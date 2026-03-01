import React, { useState, useEffect } from "react";
import SchemaForm from "./Components/SchemaForm";
import { sectionRegistry } from "./utils/sectionRegistry";

export default function InspectorPanel({
    section,
    onUpdate,
    isOpen,
    onToggle,
    onClose,
}) {
    const [configJson, setConfigJson] = useState("");
    const [error, setError] = useState(null);
    const [useVisualEditor, setUseVisualEditor] = useState(true);

    const registryItem = section ? sectionRegistry[section.section_key] : null;
    const schema = registryItem?.schema;

    // Sync local state when section changes
    useEffect(() => {
        if (section) {
            setConfigJson(JSON.stringify(section.config || {}, null, 2));
            setError(null);
        }
    }, [section?.id]);

    // Collapsed State (Vertical strip)
    if (!isOpen) {
        return (
            <div className="w-10 bg-white border-l border-gray-200 h-full flex flex-col shrink-0 z-20 shadow-sm items-center py-4">
                <button
                    onClick={onToggle}
                    className="w-8 h-8 rounded hover:bg-gray-100 flex items-center justify-center text-gray-400 transition"
                    title="Expand Inspector"
                >
                    <i className="fa-solid fa-sidebar-flip"></i>
                </button>
            </div>
        );
    }

    // Expanded State but No Section
    if (!section) {
        return (
            <div className="w-80 bg-white border-l border-gray-200 h-full flex flex-col shrink-0 z-20 shadow-sm transition-all duration-300">
                <div className="p-4 border-b border-gray-100 flex items-center justify-between">
                    <span className="text-xs font-bold text-gray-400 uppercase tracking-widest">
                        Properties
                    </span>
                    <button
                        onClick={onToggle}
                        className="text-gray-400 hover:text-gray-600"
                    >
                        <i className="fa-solid fa-angles-right"></i>
                    </button>
                </div>
                <div className="flex-1 flex flex-col items-center justify-center text-gray-400 text-center p-6">
                    <div className="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mb-4">
                        <i className="fa-regular fa-hand-pointer text-2xl text-gray-300"></i>
                    </div>
                    <p className="text-sm font-medium text-gray-500">
                        No Section Selected
                    </p>
                    <p className="text-xs mt-1">
                        Select a section in the canvas or sidebar to edit its
                        properties.
                    </p>
                </div>
            </div>
        );
    }

    const handleJsonChange = (e) => {
        const newValue = e.target.value;
        setConfigJson(newValue);

        try {
            const parsed = JSON.parse(newValue);
            setError(null);
            onUpdate({
                ...section,
                config: parsed,
            });
        } catch (err) {
            setError(err.message);
        }
    };

    const handleFormChange = (newConfig) => {
        onUpdate({
            ...section,
            config: newConfig,
        });
        setConfigJson(JSON.stringify(newConfig, null, 2));
    };

    return (
        <div className="w-96 bg-white border-l border-gray-200 h-full overflow-y-auto shrink-0 flex flex-col z-20 shadow-sm transition-all duration-300">
            <div className="p-4 border-b border-gray-200">
                <div className="flex items-start justify-between mb-2">
                    <div>
                        <span className="text-xs font-bold text-gray-400 uppercase tracking-widest block mb-1">
                            Properties
                        </span>
                        <div
                            className="text-base font-bold text-gray-800 capitalize truncate w-40"
                            title={section.section_key}
                        >
                            {registryItem?.label ||
                                section.section_key?.replace(/_/g, " ")}
                        </div>
                    </div>
                    <div className="flex items-center gap-1">
                        <button
                            onClick={onClose}
                            className="w-7 h-7 rounded hover:bg-gray-100 flex items-center justify-center text-gray-400 transition"
                            title="Deselect"
                        >
                            <i className="fa-solid fa-times"></i>
                        </button>
                        <button
                            onClick={onToggle}
                            className="w-7 h-7 rounded hover:bg-gray-100 flex items-center justify-center text-gray-400 transition"
                            title="Collapse"
                        >
                            <i className="fa-solid fa-angles-right"></i>
                        </button>
                    </div>
                </div>

                {schema && (
                    <div className="flex bg-gray-100 p-1 rounded text-xs font-medium">
                        <button
                            onClick={() => setUseVisualEditor(true)}
                            className={`flex-1 py-1 rounded transition ${useVisualEditor ? "bg-white shadow text-act-primary" : "text-gray-500 hover:text-gray-700"}`}
                        >
                            Visual
                        </button>
                        <button
                            onClick={() => setUseVisualEditor(false)}
                            className={`flex-1 py-1 rounded transition ${!useVisualEditor ? "bg-white shadow text-act-primary" : "text-gray-500 hover:text-gray-700"}`}
                        >
                            Code (JSON)
                        </button>
                    </div>
                )}
            </div>

            <div className="p-4 flex-1">
                {useVisualEditor && schema ? (
                    <SchemaForm
                        schema={schema}
                        data={section.config || {}}
                        onChange={handleFormChange}
                    />
                ) : (
                    <div className="mb-4">
                        <div className="flex items-center justify-between mb-2">
                            <label className="text-sm font-medium text-gray-700">
                                Configuration (JSON)
                            </label>
                            {error && (
                                <span className="text-xs text-red-500">
                                    Invalid JSON
                                </span>
                            )}
                        </div>
                        <textarea
                            value={configJson}
                            onChange={handleJsonChange}
                            className={`w-full h-96 text-xs font-mono p-3 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-act-primary focus:border-act-primary outline-none resize-none ${
                                error
                                    ? "border-red-300 bg-red-50"
                                    : "border-gray-200"
                            }`}
                            spellCheck="false"
                        />
                        <p className="mt-2 text-xs text-gray-500">
                            Directly editing the configuration JSON.
                        </p>
                    </div>
                )}

                {!schema && !useVisualEditor && (
                    <div className="mb-6 p-3 bg-blue-50 border border-blue-100 rounded text-xs text-blue-700">
                        No visual schema available for this section. Using JSON
                        editor.
                    </div>
                )}

                <div className="pt-4 border-t border-gray-200 mt-4">
                    <label className="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            checked={section.is_active !== false}
                            onChange={(e) =>
                                onUpdate({
                                    ...section,
                                    is_active: e.target.checked,
                                })
                            }
                            className="rounded border-gray-300 text-act-primary focus:ring-act-primary"
                        />
                        <span className="text-sm font-medium text-gray-700">
                            Section Active
                        </span>
                    </label>
                </div>
            </div>
        </div>
    );
}
