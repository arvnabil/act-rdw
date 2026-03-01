import React, { useState } from "react";
import {
    DndContext,
    closestCenter,
    KeyboardSensor,
    PointerSensor,
    useSensor,
    useSensors,
} from "@dnd-kit/core";
import {
    arrayMove,
    SortableContext,
    sortableKeyboardCoordinates,
    verticalListSortingStrategy,
    useSortable,
} from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import SchemaForm from "./SchemaForm"; // Recursive usage

/**
 * SortableItem
 * Individual collapsible item within the repeater.
 */
function SortableItem({
    id,
    index,
    data,
    schema,
    onRemove,
    onChange,
    expandedId,
    onToggleExpand,
}) {
    const {
        attributes,
        listeners,
        setNodeRef,
        transform,
        transition,
        isDragging,
    } = useSortable({ id });

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        opacity: isDragging ? 0.5 : 1,
        position: "relative",
        zIndex: isDragging ? 999 : "auto",
    };

    const isExpanded = expandedId === id;

    // Helper to get a preview title (e.g. from 'title', 'name', or 'text' field)
    const getPreviewTitle = () => {
        if (typeof data !== "object") return data;
        return (
            data.title ||
            data.name ||
            data.text ||
            data.label ||
            `Item #${index + 1}`
        );
    };

    return (
        <div
            ref={setNodeRef}
            style={style}
            className="bg-white border border-gray-200 rounded-lg mb-2 shadow-sm group"
        >
            {/* Header / Drag Handle */}
            <div className="flex items-center justify-between p-2 bg-gray-50 border-b border-gray-100 rounded-t-lg select-none">
                <div className="flex items-center gap-2 flex-1 min-w-0">
                    <div
                        {...attributes}
                        {...listeners}
                        className="cursor-grab active:cursor-grabbing text-gray-400 p-1 hover:text-gray-600"
                    >
                        <i className="fa-solid fa-grip-lines"></i>
                    </div>
                    <button
                        type="button"
                        onClick={() => onToggleExpand(id)}
                        className="text-xs font-semibold text-gray-700 truncate flex-1 text-left"
                    >
                        {isExpanded ? (
                            <i className="fa-solid fa-chevron-down mr-2 text-[10px] text-gray-400"></i>
                        ) : (
                            <i className="fa-solid fa-chevron-right mr-2 text-[10px] text-gray-400"></i>
                        )}
                        {getPreviewTitle()}
                    </button>
                </div>

                <div className="flex items-center gap-1">
                    <button
                        type="button"
                        onClick={() => onRemove(index)}
                        className="w-6 h-6 rounded flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition"
                        title="Remove Item"
                    >
                        <i className="fa-regular fa-trash text-xs"></i>
                    </button>
                </div>
            </div>

            {/* Body (Collapsible) */}
            {isExpanded && (
                <div className="p-3 bg-white rounded-b-lg">
                    {schema ? (
                        <SchemaForm
                            schema={schema}
                            data={data}
                            onChange={(updatedData) =>
                                onChange(index, updatedData)
                            }
                        />
                    ) : (
                        // Fallback for simple array of strings
                        <input
                            type="text"
                            value={data}
                            onChange={(e) => onChange(index, e.target.value)}
                            className="w-full text-sm border-gray-300 rounded"
                        />
                    )}
                </div>
            )}
        </div>
    );
}

/**
 * RepeaterEditor
 * Main container for managing list of items.
 */
export default function RepeaterEditor({
    label,
    items = [],
    schema,
    onChange, // returns new array
}) {
    // We need stable IDs for dnd-kit. items might not have IDs.
    // So we'll map items to generated IDs if they don't have them, or just use index-based keys if we must,
    // but dnd-kit prefers stable IDs.
    // Strategy: Use a local state `itemKeys` to track stable IDs for indices.

    // Simplification: We'll assume we can't easily mutate data to add IDs.
    // We will generate a local mapping "ids" -> "index".
    // Wait, simpler: Just wrap items in objects with IDs locally?
    // Let's stick to using unique IDs generated on mount/change.

    // Actually, `items` comes from props. Updating it updates props.
    // We'll generate a random ID for each item *only for the UI keys* if simple index isn't enough.
    // But index changes on sort.
    // Let's use a local state that zips items with unique IDs.

    const [uiItems, setUiItems] = useState(() =>
        (items || []).map((item) => ({
            id: `item-${Math.random().toString(36).substr(2, 9)}`,
            data: item,
        })),
    );

    // Sync uiItems when props.items changes length or content externally (careful with loops)
    // Actually, for a controlled component, we should trust props.items.
    // But we need stable IDs for DND.
    // Let's Re-zipping on every render might be okay if we memoize or hash?
    // Better: Only reset if length changes or we detect external mismatch.
    // For now, let's strictly control it:

    React.useEffect(() => {
        // This is a naive sync. If `items` changes, we try to preserve IDs where possible or regenerate.
        // Since `onChange` updates `items`, this effect triggers.
        // We should probably rely on `uiItems` as the source of truth for ORDER, and `items` for DATA.

        setUiItems((prev) => {
            // If length matches, assuming it's just a data update
            if (prev.length === items.length) {
                return prev.map((ui, idx) => ({ ...ui, data: items[idx] }));
            }
            // If length changed, fully rebuild (lost drag state possibility but safe)
            return (items || []).map((item) => ({
                id: `item-${Math.random().toString(36).substr(2, 9)}`,
                data: item,
            }));
        });
    }, [items]);

    const [expandedId, setExpandedId] = useState(null);

    const sensors = useSensors(
        useSensor(PointerSensor),
        useSensor(KeyboardSensor, {
            coordinateGetter: sortableKeyboardCoordinates,
        }),
    );

    const handleDragEnd = (event) => {
        const { active, over } = event;

        if (over && active.id !== over.id) {
            setUiItems((items) => {
                const oldIndex = items.findIndex((i) => i.id === active.id);
                const newIndex = items.findIndex((i) => i.id === over.id);

                const newUiItems = arrayMove(items, oldIndex, newIndex);

                // Propagate change to parent
                const newData = newUiItems.map((i) => i.data);
                onChange(newData);

                return newUiItems;
            });
        }
    };

    const handleAddItem = () => {
        // Create new item structure
        const newItemData = {};
        if (schema) {
            schema.forEach((field) => {
                newItemData[field.name] = field.type === "repeater" ? [] : "";
            });
        }

        const newArray = [...items, newItemData];
        onChange(newArray);
        // Expanded state will be handled by useEffect sync, ideally we want to expand the new one.
        // We can't easily predict the ID here unless we generate it.
    };

    const handleRemoveItem = (index) => {
        if (!confirm("Remove this item?")) return;
        const newArray = [...items];
        newArray.splice(index, 1);
        onChange(newArray);
    };

    const handleItemChange = (index, newData) => {
        const newArray = [...items];
        newArray[index] = newData;
        onChange(newArray);
    };

    return (
        <div className="mb-6 border border-gray-200 rounded-lg overflow-hidden flex flex-col bg-gray-50/50">
            {/* Toolbar */}
            <div className="bg-gray-100 px-3 py-2 border-b border-gray-200 flex justify-between items-center group">
                <label className="text-xs font-bold uppercase text-gray-600 tracking-wider">
                    {label}
                </label>
                <button
                    type="button"
                    onClick={handleAddItem}
                    className="text-xs bg-white hover:bg-act-primary hover:text-white border border-gray-300 hover:border-act-primary text-gray-700 px-2 py-1 rounded shadow-sm transition flex items-center"
                >
                    <i className="fa-solid fa-plus mr-1"></i> Add
                </button>
            </div>

            {/* List */}
            <div className="p-2">
                <DndContext
                    sensors={sensors}
                    collisionDetection={closestCenter}
                    onDragEnd={handleDragEnd}
                >
                    <SortableContext
                        items={uiItems.map((i) => i.id)}
                        strategy={verticalListSortingStrategy}
                    >
                        {uiItems.length === 0 ? (
                            <div className="text-center py-6 text-gray-400 text-xs italic">
                                No items yet.
                            </div>
                        ) : (
                            uiItems.map((uiItem, index) => (
                                <SortableItem
                                    key={uiItem.id}
                                    id={uiItem.id}
                                    index={index}
                                    data={uiItem.data}
                                    schema={schema}
                                    onRemove={handleRemoveItem}
                                    onChange={handleItemChange}
                                    expandedId={expandedId}
                                    onToggleExpand={(id) =>
                                        setExpandedId(
                                            id === expandedId ? null : id,
                                        )
                                    }
                                />
                            ))
                        )}
                    </SortableContext>
                </DndContext>
            </div>
        </div>
    );
}
