import React from "react";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { sectionRegistry } from "./utils/sectionRegistry";

export default function SortableSectionItem({
    section,
    isSelected,
    onClick,
    onRemove,
}) {
    const {
        attributes,
        listeners,
        setNodeRef,
        transform,
        transition,
        isDragging,
    } = useSortable({ id: section.id });

    const style = {
        transform: CSS.Transform.toString(transform),
        transition,
        opacity: isDragging ? 0.5 : 1,
    };

    const registryItem = sectionRegistry[section.section_key] || {};

    return (
        <div
            ref={setNodeRef}
            style={style}
            className={`p-3 border rounded-lg mb-2 flex items-center gap-3 bg-white group cursor-pointer transition-all ${
                isSelected
                    ? "border-act-primary shadow-sm ring-1 ring-act-primary/20"
                    : "border-gray-200 hover:border-gray-300"
            }`}
            onClick={onClick}
        >
            {/* Drag Handle */}
            <div
                {...attributes}
                {...listeners}
                className="text-gray-400 hover:text-gray-600 cursor-grab active:cursor-grabbing p-1"
            >
                <i className="fa-solid fa-grip-lines"></i>
            </div>

            {/* Icon */}
            <div
                className={`w-8 h-8 rounded flex items-center justify-center text-xs ${isSelected ? "bg-act-primary/10 text-act-primary" : "bg-gray-100 text-gray-500"}`}
            >
                <i className={registryItem.icon || "fa-regular fa-cube"}></i>
            </div>

            {/* Info */}
            <div className="flex-1 min-w-0">
                <p
                    className="text-sm font-medium text-gray-800 truncate"
                    title={registryItem.label || section.section_key}
                >
                    {registryItem.label || section.section_key}
                </p>
                <div className="flex items-center gap-2">
                    <span
                        className={`w-2 h-2 rounded-full ${section.is_active ? "bg-green-500" : "bg-gray-300"}`}
                    ></span>
                    <span className="text-xs text-gray-400 truncate">
                        {section.is_active ? "Active" : "Hidden"}
                    </span>
                </div>
            </div>

            {/* Active Indicator Arrow */}
            {isSelected && (
                <div className="text-act-primary">
                    <i className="fa-solid fa-chevron-right"></i>
                </div>
            )}

            {/* Remove Button */}
            {!registryItem.lock && (
                <button
                    onClick={(e) => {
                        e.stopPropagation();
                        onRemove(section.id);
                    }}
                    className="text-gray-400 hover:text-red-500 transition px-2"
                    title="Remove Section"
                >
                    <i className="fa-regular fa-trash"></i>
                </button>
            )}
        </div>
    );
}
