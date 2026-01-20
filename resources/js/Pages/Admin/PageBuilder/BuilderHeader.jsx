import React from "react";
import { Link } from "@inertiajs/react";

export default function BuilderHeader({
    page,
    onSave,
    isSaving,
    previewDevice,
    setPreviewDevice,
    onUndo,
    onRedo,
    canUndo,
    canRedo,
}) {
    // Helper helper for device button styles
    const getDeviceBtnClass = (device) =>
        `p-2 rounded hover:shadow-sm transition ${
            previewDevice === device
                ? "bg-white text-act-primary shadow-sm font-medium"
                : "text-gray-500 hover:bg-white/50 hover:text-gray-700"
        }`;

    return (
        <div className="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 sticky top-0 z-50 shadow-sm">
            <div className="flex items-center gap-4">
                <Link
                    href="/admin/pages"
                    className="text-gray-500 hover:text-gray-700"
                >
                    <i className="fa-solid fa-arrow-left"></i>
                </Link>
                <div>
                    <div className="text-sm font-semibold text-gray-600">
                        {page.title}
                    </div>
                    <span className="text-[10px] text-gray-400 font-mono">
                        {page.slug}
                    </span>
                </div>
            </div>

            <div className="flex items-center gap-3">
                {/* Undo / Redo */}
                <div className="flex items-center bg-gray-100/50 rounded-lg p-1 mr-2 border border-gray-200/50">
                    <button
                        onClick={onUndo}
                        disabled={!canUndo}
                        className="p-2 text-gray-500 hover:text-act-primary disabled:opacity-30 disabled:hover:text-gray-500 transition"
                        title="Undo (Ctrl+Z)"
                    >
                        <i className="fa-solid fa-rotate-left"></i>
                    </button>
                    <button
                        onClick={onRedo}
                        disabled={!canRedo}
                        className="p-2 text-gray-500 hover:text-act-primary disabled:opacity-30 disabled:hover:text-gray-500 transition"
                        title="Redo (Ctrl+Shift+Z)"
                    >
                        <i className="fa-solid fa-rotate-right"></i>
                    </button>
                </div>

                <div className="bg-gray-100 p-1 rounded-lg flex gap-1">
                    <button
                        onClick={() => setPreviewDevice("desktop")}
                        className={getDeviceBtnClass("desktop")}
                        title="Desktop (100%)"
                    >
                        <i className="fa-regular fa-desktop"></i>
                    </button>
                    <button
                        onClick={() => setPreviewDevice("tablet")}
                        className={getDeviceBtnClass("tablet")}
                        title="Tablet (768px)"
                    >
                        <i className="fa-regular fa-tablet"></i>
                    </button>
                    <button
                        onClick={() => setPreviewDevice("mobile")}
                        className={getDeviceBtnClass("mobile")}
                        title="Mobile (375px)"
                    >
                        <i className="fa-regular fa-mobile"></i>
                    </button>
                </div>

                <div className="h-6 w-px bg-gray-300 mx-2"></div>

                <a
                    href={`/${page.slug}`}
                    target="_blank"
                    className="text-sm font-medium text-gray-600 hover:text-act-primary flex items-center gap-2"
                >
                    Live View <i className="fa-regular fa-external-link"></i>
                </a>

                <button
                    onClick={onSave}
                    disabled={isSaving}
                    className="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    {isSaving ? (
                        <>
                            <i className="fa-solid fa-spinner fa-spin"></i>{" "}
                            Saving...
                        </>
                    ) : (
                        <>
                            <i className="fa-solid fa-save"></i> Save Changes
                        </>
                    )}
                </button>
            </div>
        </div>
    );
}
