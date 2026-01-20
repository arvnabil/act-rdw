import React, { useState, useRef } from "react";

export default function MediaManager({
    show,
    onClose,
    files = [],
    onSelect,
    onUpload,
    onDelete,
    onRename,
}) {
    const [isDragging, setIsDragging] = useState(false);
    const fileInputRef = useRef(null);

    if (!show) return null;

    const handleDragEnter = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(true);
    };

    const handleDragLeave = (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (e.currentTarget.contains(e.relatedTarget)) return;
        setIsDragging(false);
    };

    const handleDragOver = (e) => {
        e.preventDefault();
        e.stopPropagation();
    };

    const handleDrop = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);

        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            if (onUpload) {
                Array.from(e.dataTransfer.files).forEach((file) =>
                    onUpload(file),
                );
            }
        }
    };

    const triggerUpload = () => {
        if (fileInputRef.current) {
            fileInputRef.current.click();
        }
    };

    return (
        <div
            className="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm"
            onClick={onClose}
        >
            <div
                className="bg-white w-full max-w-4xl h-[80vh] rounded-xl shadow-2xl flex flex-col overflow-hidden animate-fade-in-up relative font-sans"
                onClick={(e) => e.stopPropagation()}
                onDragEnter={handleDragEnter}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                {/* Drag Overlay */}
                {isDragging && (
                    <div className="absolute inset-0 z-50 bg-purple-50/95 flex flex-col items-center justify-center border-4 border-dashed border-purple-400 m-4 rounded-xl transition-all">
                        <i className="fas fa-cloud-upload-alt text-6xl text-purple-600 mb-4 animate-bounce"></i>
                        <h3 className="text-2xl font-bold text-gray-800">
                            Drop files to upload
                        </h3>
                        <p className="text-gray-500 mt-2">
                            Release to upload instantly
                        </p>
                    </div>
                )}

                {/* Modal Header */}
                <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white">
                    <div>
                        <h3 className="text-xl font-bold text-gray-800">
                            Media Library
                        </h3>
                        <p className="text-sm text-gray-500 mt-1">
                            {files.length} items • Drag & Drop supported
                        </p>
                    </div>
                    <div className="flex items-center gap-3">
                        <input
                            type="file"
                            ref={fileInputRef}
                            className="hidden"
                            accept="image/*"
                            multiple
                            onChange={(e) => {
                                if (e.target.files && onUpload) {
                                    Array.from(e.target.files).forEach((file) =>
                                        onUpload(file),
                                    );
                                }
                                e.target.value = null;
                            }}
                        />
                        {/* Canva Style Button */}
                        <button
                            onClick={triggerUpload}
                            className="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-full flex items-center gap-2 transition-all shadow-md hover:shadow-lg font-medium text-sm transform active:scale-95"
                        >
                            <i className="fas fa-cloud-upload-alt"></i>
                            Upload Media
                        </button>
                        <button
                            onClick={onClose}
                            className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors cursor-pointer"
                        >
                            <i className="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {/* Modal Content */}
                <div className="flex-1 overflow-y-auto p-6 bg-gray-50/50">
                    {files.length === 0 ? (
                        // Empty State
                        <div className="h-full flex flex-col items-center justify-center border border-dashed border-gray-300 rounded-xl bg-white m-4">
                            <div className="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mb-6">
                                <i className="fas fa-images text-4xl text-purple-400"></i>
                            </div>
                            <h4 className="text-xl font-semibold text-gray-800 mb-2">
                                No media files
                            </h4>
                            <p className="text-gray-500 mb-6 max-w-sm text-center">
                                Upload images to use them. Drag and drop works
                                too!
                            </p>
                            <button
                                onClick={triggerUpload}
                                className="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-full font-medium transition-all shadow-md hover:shadow-lg transform active:scale-95"
                            >
                                Select Files
                            </button>
                        </div>
                    ) : (
                        // Grid Layout
                        <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                            {files.map((file, index) => (
                                <div
                                    key={file.id || index}
                                    className="group relative aspect-square bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-purple-300 transition-all overflow-hidden cursor-pointer"
                                    onClick={() =>
                                        !file.uploading && onSelect([file])
                                    }
                                >
                                    {/* Image */}
                                    <div className="w-full h-full p-2 flex items-center justify-center bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmIi8+CjxwYXRoIGQ9Ik0wIDBMODCA4Wk04IDBMMCA4WiIgc3Ryb2tlPSIjZjNmNGY2IiBzdHJva2Utd2lkdGg9IjEiLz4KPC9zdmc+')]">
                                        <img
                                            src={file.url}
                                            alt={file.name}
                                            className={`max-w-full max-h-full object-contain ${
                                                file.uploading
                                                    ? "opacity-50 blur-sm"
                                                    : ""
                                            }`}
                                        />
                                    </div>

                                    {/* Upload Progress */}
                                    {file.uploading && (
                                        <div className="absolute inset-0 flex flex-col items-center justify-center bg-black/40 z-10 p-2">
                                            <div className="w-full max-w-[80%] bg-white/50 rounded-full h-1.5 mb-1 overflow-hidden">
                                                <div
                                                    className="bg-purple-500 h-full transition-all duration-200 ease-out"
                                                    style={{
                                                        width: `${file.progress || 0}%`,
                                                    }}
                                                />
                                            </div>
                                            <span className="text-white text-[9px] font-bold drop-shadow">
                                                {Math.round(file.progress || 0)}
                                                %
                                            </span>
                                        </div>
                                    )}

                                    {/* Hover Overlay */}
                                    {!file.uploading && (
                                        <div className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex items-end justify-between">
                                            <span className="text-white text-[11px] truncate max-w-[70%] font-medium">
                                                {file.name}
                                            </span>
                                            <button
                                                onClick={(e) => {
                                                    e.stopPropagation();
                                                    if (
                                                        confirm(
                                                            "Delete this file?",
                                                        )
                                                    )
                                                        onDelete(file.path);
                                                }}
                                                className="text-white/80 hover:text-red-400 transition-colors"
                                                title="Delete"
                                            >
                                                <i className="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
