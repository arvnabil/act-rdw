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
    pageSettings,
    onUpdateSettings,
}) {
    const [activeTab, setActiveTab] = useState("layers"); // layers, add, settings

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

    const renderContent = () => {
        switch (activeTab) {
            case "add":
                return (
                    <div className="grid grid-cols-2 gap-3">
                        {Object.entries(sectionRegistry).map(([key, item]) => (
                            <button
                                key={key}
                                onClick={() => {
                                    onAddSection(key);
                                    setActiveTab("layers");
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
                );
            case "settings":
                return (
                    <div className="space-y-6">
                        {/* Publishing Status */}
                        <div>
                            <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                                Publishing
                            </label>
                            <div className="space-y-2">
                                <button
                                    onClick={() =>
                                        onUpdateSettings({ status: "draft" })
                                    }
                                    className={`w-full flex items-center justify-between p-3 rounded-lg border transition ${
                                        pageSettings.status === "draft"
                                            ? "border-act-primary bg-act-primary/5 text-act-primary"
                                            : "border-gray-200 hover:border-gray-300 text-gray-700"
                                    }`}
                                >
                                    <div className="flex items-center">
                                        <i className="fa-regular fa-file-pen mr-3"></i>
                                        <span className="text-sm font-medium">
                                            Draft
                                        </span>
                                    </div>
                                    {pageSettings.status === "draft" && (
                                        <i className="fa-solid fa-circle-check"></i>
                                    )}
                                </button>
                                <button
                                    onClick={() =>
                                        onUpdateSettings({
                                            status: "published",
                                        })
                                    }
                                    className={`w-full flex items-center justify-between p-3 rounded-lg border transition ${
                                        pageSettings.status === "published"
                                            ? "border-green-600 bg-green-50 text-green-700"
                                            : "border-gray-200 hover:border-gray-300 text-gray-700"
                                    }`}
                                >
                                    <div className="flex items-center">
                                        <i className="fa-regular fa-globe mr-3"></i>
                                        <span className="text-sm font-medium">
                                            Published
                                        </span>
                                    </div>
                                    {pageSettings.status === "published" && (
                                        <i className="fa-solid fa-circle-check"></i>
                                    )}
                                </button>
                            </div>
                        </div>

                        <div className="h-px bg-gray-100"></div>

                        {/* Breadcrumb Settings */}
                        <div>
                            <div className="flex items-center justify-between mb-3">
                                <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Breadcrumb
                                </label>
                                <button
                                    onClick={() =>
                                        onUpdateSettings({
                                            show_breadcrumb:
                                                !pageSettings.show_breadcrumb,
                                        })
                                    }
                                    className={`w-10 h-5 rounded-full transition-colors relative ${
                                        pageSettings.show_breadcrumb
                                            ? "bg-act-primary"
                                            : "bg-gray-300"
                                    }`}
                                >
                                    <div
                                        className={`absolute top-1 left-1 w-3 h-3 rounded-full bg-white transition-transform ${
                                            pageSettings.show_breadcrumb
                                                ? "translate-x-5"
                                                : "translate-x-0"
                                        }`}
                                    />
                                </button>
                            </div>

                            {pageSettings.show_breadcrumb && (
                                <div className="space-y-4 animate-in fade-in slide-in-from-top-2 duration-300">
                                    <div>
                                        <label className="block text-[11px] font-medium text-gray-500 mb-2">
                                            Custom Background
                                        </label>
                                        <div className="relative group">
                                            {pageSettings.breadcrumb_image ? (
                                                <div className="relative rounded-lg overflow-hidden border border-gray-200 aspect-video bg-gray-50">
                                                    <img
                                                        src={
                                                            pageSettings.breadcrumb_image.startsWith(
                                                                "http",
                                                            )
                                                                ? pageSettings.breadcrumb_image
                                                                : `/storage/${pageSettings.breadcrumb_image}`
                                                        }
                                                        alt="Breadcrumb preview"
                                                        className="w-full h-full object-cover"
                                                    />
                                                    <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                                        <button
                                                            onClick={() =>
                                                                onUpdateSettings({
                                                                    breadcrumb_image: null,
                                                                })
                                                            }
                                                            className="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 transition"
                                                            title="Remove Image"
                                                        >
                                                            <i className="fa-regular fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            ) : (
                                                <button
                                                    onClick={() => {
                                                        const url = prompt(
                                                            "Enter Image Path (relative to storage/ or full URL):",
                                                            "services/default-breadcrumb.jpg",
                                                        );
                                                        if (url)
                                                            onUpdateSettings({
                                                                breadcrumb_image:
                                                                    url,
                                                            });
                                                    }}
                                                    className="w-full aspect-video rounded-lg border-2 border-dashed border-gray-200 hover:border-act-primary hover:bg-act-primary/5 transition flex flex-col items-center justify-center text-gray-400 gap-2"
                                                >
                                                    <i className="fa-regular fa-image text-2xl"></i>
                                                    <span className="text-xs font-medium">
                                                        Set Background
                                                    </span>
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                );
            case "layers":
            default:
                return (
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
                                        onClick={() => setActiveTab("add")}
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
                );
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
                        setActiveTab("add");
                    }}
                    className="w-10 h-10 rounded-lg bg-act-primary/10 text-act-primary hover:bg-act-primary/20 flex items-center justify-center transition"
                    title="Add Section"
                >
                    <i className="fa-regular fa-plus"></i>
                </button>

                <button
                    onClick={() => {
                        onToggle();
                        setActiveTab("settings");
                    }}
                    className="w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 transition"
                    title="Page Settings"
                >
                    <i className="fa-regular fa-gear"></i>
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
                        className={`flex-1 py-3 text-xs font-medium border-b-2 text-center transition ${activeTab === "layers" ? "border-act-primary text-act-primary bg-white" : "border-transparent text-gray-500 hover:text-gray-700"}`}
                        onClick={() => setActiveTab("layers")}
                    >
                        <i className="fa-regular fa-layer-group mb-1 block text-base"></i>
                        Layers
                    </button>
                    <button
                        className={`flex-1 py-3 text-xs font-medium border-b-2 text-center transition ${activeTab === "add" ? "border-act-primary text-act-primary bg-white" : "border-transparent text-gray-500 hover:text-gray-700"}`}
                        onClick={() => setActiveTab("add")}
                    >
                        <i className="fa-regular fa-plus mb-1 block text-base"></i>
                        Add
                    </button>
                    <button
                        className={`flex-1 py-3 text-xs font-medium border-b-2 text-center transition ${activeTab === "settings" ? "border-act-primary text-act-primary bg-white" : "border-transparent text-gray-500 hover:text-gray-700"}`}
                        onClick={() => setActiveTab("settings")}
                    >
                        <i className="fa-regular fa-gear mb-1 block text-base"></i>
                        Settings
                    </button>
                </div>
                <button
                    onClick={onToggle}
                    className="w-8 h-8 rounded hover:bg-gray-100 flex items-center justify-center text-gray-400 ml-1"
                >
                    <i className="fa-solid fa-angles-left"></i>
                </button>
            </div>

            <div className="h-px bg-gray-200 w-full"></div>

            {/* Content Area */}
            <div className="flex-1 overflow-y-auto p-4 custom-scrollbar">
                {renderContent()}
            </div>
        </div>
    );
}
