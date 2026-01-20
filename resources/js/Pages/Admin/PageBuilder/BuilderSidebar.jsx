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
} from "@dnd-kit/sortable";
import SortableSectionItem from "./SortableSectionItem";
import { sectionRegistry } from "./utils/sectionRegistry";

export default function BuilderSidebar({
    sections,
    onReorder,
    onSelect,
    selectedSectionId,
    onAddSection,
    onRemove,
    isOpen,
    onToggle,
}) {
    const [isLibraryOpen, setIsLibraryOpen] = useState(false);

    const sensors = useSensors(
        useSensor(PointerSensor),
        useSensor(KeyboardSensor, {
            coordinateGetter: sortableKeyboardCoordinates,
        }),
    );

    const handleDragEnd = (event) => {
        const { active, over } = event;

        if (active.id !== over.id) {
            const oldIndex = sections.findIndex((s) => s.id === active.id);
            const newIndex = sections.findIndex((s) => s.id === over.id);
            onReorder(arrayMove(sections, oldIndex, newIndex));
        }
    };

    if (!isOpen) {
        return (
            <div className="w-16 bg-white border-r border-gray-200 h-full flex flex-col flex-shrink-0 z-20 shadow-sm items-center py-4 gap-4">
                <button
                    onClick={onToggle}
                    className="w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 transition"
                    title="Expand Sidebar"
                >
                    <i className="fa-solid fa-sidebar"></i>
                </button>

                <div className="w-8 h-px bg-gray-200"></div>

                <button
                    onClick={() => {
                        onToggle();
                        setIsLibraryOpen(true);
                    }}
                    className="w-10 h-10 rounded-lg bg-act-primary/10 text-act-primary hover:bg-act-primary/20 flex items-center justify-center transition"
                    title="Add Section"
                >
                    <i className="fa-regular fa-plus"></i>
                </button>
            </div>
        );
    }

    return (
        <div className="w-80 bg-white border-r border-gray-200 h-full flex flex-col flex-shrink-0 z-20 shadow-sm transition-all duration-300">
            {/* Header / Tabs */}
            <div className="flex items-center justify-between px-2 pt-2 pb-0">
                <div className="flex flex-1">
                    <button
                        className={`flex-1 py-3 text-sm font-medium border-b-2 text-center transition ${!isLibraryOpen ? "border-act-primary text-act-primary bg-white" : "border-transparent text-gray-500 hover:text-gray-700"}`}
                        onClick={() => setIsLibraryOpen(false)}
                    >
                        <i className="fa-regular fa-layer-group mr-2"></i>{" "}
                        Layers
                    </button>
                    <button
                        className={`flex-1 py-3 text-sm font-medium border-b-2 text-center transition ${isLibraryOpen ? "border-act-primary text-act-primary bg-white" : "border-transparent text-gray-500 hover:text-gray-700"}`}
                        onClick={() => setIsLibraryOpen(true)}
                    >
                        <i className="fa-regular fa-plus mr-2"></i> Add
                    </button>
                </div>
                <button
                    onClick={onToggle}
                    className="w-8 h-8 rounded hover:bg-gray-100 flex items-center justify-center text-gray-400 ml-1"
                >
                    <i className="fa-solid fa-angles-left"></i>
                </button>
            </div>

            <div className="h-px bg-gray-200 w-full mb-2"></div>

            {/* Content Area */}
            <div className="flex-1 overflow-y-auto p-4 custom-scrollbar">
                {isLibraryOpen ? (
                    // Library View
                    <div className="grid grid-cols-2 gap-3">
                        {Object.entries(sectionRegistry).map(([key, item]) => (
                            <button
                                key={key}
                                onClick={() => {
                                    onAddSection(key);
                                    setIsLibraryOpen(false); // Switch back to layers after adding
                                }}
                                className="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:border-act-primary hover:bg-act-primary/5 hover:shadow-sm transition aspect-square text-center group"
                            >
                                <div className="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 mb-3 group-hover:bg-white group-hover:text-act-primary">
                                    <i
                                        className={`${item.icon || "fa-solid fa-cube"} text-lg`}
                                    ></i>
                                </div>
                                <span className="text-xs font-medium text-gray-700 group-hover:text-gray-900 leading-tight">
                                    {item.label}
                                </span>
                            </button>
                        ))}
                    </div>
                ) : (
                    // Layers (Sortable) View
                    <DndContext
                        sensors={sensors}
                        collisionDetection={closestCenter}
                        onDragEnd={handleDragEnd}
                    >
                        <SortableContext
                            items={sections.map((s) => s.id)}
                            strategy={verticalListSortingStrategy}
                        >
                            {sections.length === 0 ? (
                                <div className="text-center py-10 text-gray-400">
                                    <p className="text-sm mb-3">
                                        No sections yet
                                    </p>
                                    <button
                                        onClick={() => setIsLibraryOpen(true)}
                                        className="text-act-primary hover:underline text-sm font-medium"
                                    >
                                        Add your first section
                                    </button>
                                </div>
                            ) : (
                                sections.map((section) => (
                                    <SortableSectionItem
                                        key={section.id}
                                        section={section}
                                        isSelected={
                                            selectedSectionId === section.id
                                        }
                                        onClick={() => onSelect(section.id)}
                                        onRemove={onRemove}
                                    />
                                ))
                            )}
                        </SortableContext>
                    </DndContext>
                )}
            </div>
        </div>
    );
}
