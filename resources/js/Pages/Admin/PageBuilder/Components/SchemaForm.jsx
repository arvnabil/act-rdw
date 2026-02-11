import React, { useState } from "react";
import RepeaterEditor from "./RepeaterEditor";

/**
 * SchemaForm
 *
 * Dynamically renders form fields based on the registry schema.
 * Supports: text, textarea, select, image, and simplified nested repeaters.
 */
export default function SchemaForm({ schema, data, onChange }) {
    const handleChange = (name, value) => {
        onChange({
            ...data,
            [name]: value,
        });
    };

    const renderField = (field, currentData, onFieldChange, keyPrefix = "") => {
        const fieldName = field.name;
        // Ensure we safely access the value, defaulting to empty string or array depending on type
        const value =
            currentData?.[fieldName] !== undefined
                ? currentData[fieldName]
                : field.type === "repeater"
                    ? []
                    : "";
        const uniqueKey = keyPrefix + fieldName;

        switch (field.type) {
            case "text":
                return (
                    <div key={uniqueKey} className="mb-4">
                        <label className="block text-xs font-medium text-gray-700 mb-1">
                            {field.label}
                        </label>
                        <input
                            type="text"
                            value={value}
                            onChange={(e) =>
                                onFieldChange(fieldName, e.target.value)
                            }
                            className="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-act-primary focus:ring focus:ring-act-primary/20 bg-gray-50 text-gray-800"
                        />
                    </div>
                );

            case "textarea":
                return (
                    <div key={uniqueKey} className="mb-4">
                        <label className="block text-xs font-medium text-gray-700 mb-1">
                            {field.label}
                        </label>
                        <textarea
                            value={value}
                            onChange={(e) =>
                                onFieldChange(fieldName, e.target.value)
                            }
                            rows={3}
                            className="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-act-primary focus:ring focus:ring-act-primary/20 bg-gray-50 resize-y text-gray-800"
                        />
                    </div>
                );

            case "image":
                return (
                    <div key={uniqueKey} className="mb-4">
                        <label className="block text-xs font-medium text-gray-700 mb-1">
                            {field.label}
                        </label>
                        <div className="flex items-start gap-3">
                            <div className="w-16 h-16 bg-gray-100 rounded border border-gray-200 overflow-hidden flex-shrink-0 relative group">
                                {value ? (
                                    <img
                                        src={value}
                                        alt="Preview"
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center text-gray-400">
                                        <i className="fa-regular fa-image"></i>
                                    </div>
                                )}
                            </div>
                            <div className="flex-1 space-y-2">
                                {/* Direct URL Input */}
                                <input
                                    type="text"
                                    value={value}
                                    onChange={(e) =>
                                        onFieldChange(fieldName, e.target.value)
                                    }
                                    placeholder="Image URL..."
                                    className="w-full text-xs border-gray-300 rounded shadow-sm focus:border-act-primary focus:ring focus:ring-act-primary/20 bg-gray-50"
                                />

                                {/* File Upload Input */}
                                <div className="relative">
                                    <input
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) => {
                                            const file = e.target.files[0];
                                            if (!file) return;

                                            const formData = new FormData();
                                            formData.append("file", file);

                                            // Show uploading state
                                            const btn = e.target.nextElementSibling;
                                            if (btn) btn.innerText = "Uploading...";

                                            fetch("/admin/upload-media", {
                                                method: "POST",
                                                body: formData,
                                                headers: {
                                                    "X-CSRF-TOKEN": document
                                                        .querySelector(
                                                            'meta[name="csrf-token"]',
                                                        )
                                                        ?.getAttribute(
                                                            "content",
                                                        ),
                                                },
                                            })
                                                .then((res) => res.json())
                                                .then((data) => {
                                                    if (data.url) {
                                                        onFieldChange(
                                                            fieldName,
                                                            data.url,
                                                        );
                                                    }
                                                })
                                                .catch((err) =>
                                                    console.error(
                                                        "Upload failed",
                                                        err,
                                                    ),
                                                )
                                                .finally(() => {
                                                    if (btn)
                                                        btn.innerText =
                                                            "Choose File";
                                                    e.target.value = null; // reset
                                                });
                                        }}
                                        className="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    />
                                    <button className="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-xs py-1.5 px-3 rounded shadow-sm flex items-center justify-center gap-2 transition">
                                        <i className="fa-solid fa-cloud-arrow-up"></i>
                                        <span>Choose File</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                );

            case "select":
                return (
                    <div key={uniqueKey} className="mb-4">
                        <label className="block text-xs font-medium text-gray-700 mb-1">
                            {field.label}
                        </label>
                        <select
                            value={value}
                            onChange={(e) =>
                                onFieldChange(fieldName, e.target.value)
                            }
                            className="w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-act-primary focus:ring focus:ring-act-primary/20 bg-gray-50 text-gray-800"
                        >
                            {field.options?.map((opt) => (
                                <option key={opt.value} value={opt.value}>
                                    {opt.label}
                                </option>
                            ))}
                        </select>
                    </div>
                );

            case "repeater":
                // Recursively render nested schemas using the new RepeaterEditor
                return (
                    <RepeaterEditor
                        key={uniqueKey}
                        label={field.label}
                        items={value}
                        schema={field.schema}
                        onChange={(newArray) =>
                            onFieldChange(fieldName, newArray)
                        }
                    />
                );

            default:
                return null;
        }
    };

    if (!schema || schema.length === 0) {
        return (
            <div className="p-4 bg-gray-50 border border-gray-100 rounded-lg text-center">
                <div className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                    <i className="fa-regular fa-lock"></i>
                </div>
                <h4 className="text-xs font-semibold text-gray-600 mb-1">
                    Read Only Section
                </h4>
                <p className="text-xs text-gray-500 leading-relaxed">
                    This section uses legacy content and is not editable.
                </p>
            </div>
        );
    }

    return (
        <div className="space-y-1">
            {schema.map((field) => renderField(field, data, handleChange))}
        </div>
    );
}
