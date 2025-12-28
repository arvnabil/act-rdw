import React, { useRef, useState, useMemo, useEffect } from "react";
import Swal from "sweetalert2";
import axios from "axios";

export default function MediaManager({
    show,
    onClose,
    files = [],
    onSelect,
    uploadUrl,
    onUploadSuccess,
    onDelete,
    onRename,
}) {
    const fileInputRef = useRef(null);
    const [searchQuery, setSearchQuery] = useState("");
    const [selectedFiles, setSelectedFiles] = useState(new Set());
    const [uploadQueue, setUploadQueue] = useState([]); // { id, name, progress: 0-100, status: 'pending'|'uploading'|'success'|'error', errorMsg }
    const [isDragging, setIsDragging] = useState(false);

    const filteredFiles = useMemo(() => {
        if (!searchQuery) return files;
        return files.filter((file) =>
            file.name.toLowerCase().includes(searchQuery.toLowerCase())
        );
    }, [files, searchQuery]);

    useEffect(() => {
        if (!show) {
            setSelectedFiles(new Set());
            setUploadQueue([]); // Clear queue on close? Or keep? Let's clear for now
        }
    }, [show]);

    if (!show) return null;

    // --- Upload Logic ---

    const processUpload = async (fileObj) => {
        const formData = new FormData();
        formData.append("file", fileObj.file);

        try {
            // Update status to uploading
            setUploadQueue((prev) =>
                prev.map((item) =>
                    item.id === fileObj.id
                        ? { ...item, status: "uploading" }
                        : item
                )
            );

            const response = await axios.post(uploadUrl, formData, {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round(
                        (progressEvent.loaded * 100) / progressEvent.total
                    );
                    setUploadQueue((prev) =>
                        prev.map((item) =>
                            item.id === fileObj.id
                                ? { ...item, progress: percentCompleted }
                                : item
                        )
                    );
                },
            });

            // Success
            setUploadQueue((prev) =>
                prev.map((item) =>
                    item.id === fileObj.id
                        ? { ...item, status: "success", progress: 100 }
                        : item
                )
            );

            // Refresh list
            onUploadSuccess();

            // Auto-remove success items after delay (optional, but good for UX)
            setTimeout(() => {
                setUploadQueue((prev) =>
                    prev.filter((item) => item.id !== fileObj.id)
                );
            }, 3000);
        } catch (error) {
            console.error("Upload failed", error);
            let errorMsg = "Upload failed";
            if (error.response?.data?.message) {
                errorMsg = error.response.data.message;
            } else if (error.response?.status === 422) {
                errorMsg = "Validation error";
            } else if (error.message) {
                errorMsg = error.message;
            }

            setUploadQueue((prev) =>
                prev.map((item) =>
                    item.id === fileObj.id
                        ? { ...item, status: "error", errorMsg: errorMsg }
                        : item
                )
            );
        }
    };

    const handleFilesAdded = (newFiles) => {
        const queueItems = [];
        Array.from(newFiles).forEach((file) => {
            // Validation: 2MB
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: "error",
                    title: "File too large",
                    text: `${file.name} exceeds 2MB limit.`,
                    customClass: { popup: "rounded-xl shadow-2xl border-0" },
                });
                return;
            }

            const id = Math.random().toString(36).substring(7);
            const item = {
                id,
                file,
                name: file.name,
                progress: 0,
                status: "pending",
                errorMsg: null,
            };
            queueItems.push(item);

            // Trigger upload immediately
            processUpload(item);
        });

        if (queueItems.length > 0) {
            setUploadQueue((prev) => [...prev, ...queueItems]);
        }
    };

    // --- Drag & Drop ---

    const handleDragOver = (e) => {
        e.preventDefault();
        setIsDragging(true);
    };

    const handleDragLeave = (e) => {
        e.preventDefault();
        setIsDragging(false);
    };

    const handleDrop = (e) => {
        e.preventDefault();
        setIsDragging(false);
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            handleFilesAdded(e.dataTransfer.files);
        }
    };

    // --- Selection & Actions ---

    const handleToggleSelect = (e, file) => {
        e.stopPropagation();
        const newSelected = new Set(selectedFiles);
        if (newSelected.has(file.path)) {
            newSelected.delete(file.path);
        } else {
            newSelected.add(file.path);
        }
        setSelectedFiles(newSelected);
    };

    const handleInsertSelected = () => {
        const selected = files.filter((f) => selectedFiles.has(f.path));
        onSelect(selected);
        setSelectedFiles(new Set());
    };

    const handleRenameClick = (e, file) => {
        e.stopPropagation();
        const currentName = file.name;
        const namePart =
            currentName.substring(0, currentName.lastIndexOf(".")) ||
            currentName;

        Swal.fire({
            title: "Rename File",
            text: `Enter new name for "${currentName}"`,
            input: "text",
            inputValue: namePart,
            showCancelButton: true,
            confirmButtonText: "Rename",
            confirmButtonColor: "#3b82f6",
            cancelButtonColor: "#6b7280",
            inputValidator: (value) => {
                if (!value) {
                    return "You need to write something!";
                }
            },
            customClass: {
                popup: "rounded-xl shadow-2xl border-0",
                title: "text-lg font-bold text-gray-800",
                confirmButton:
                    "bg-blue-500 hover:bg-blue-600 border-0 rounded-lg px-4 py-2",
                cancelButton:
                    "bg-gray-500 hover:bg-gray-600 border-0 rounded-lg px-4 py-2",
                input: "form-control shadow-none border-gray-300 rounded-lg w-75 mx-auto",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                onRename(file.path, result.value);
            }
        });
    };

    const handleDeleteClick = (e, filePath) => {
        e.stopPropagation();
        Swal.fire({
            title: "Delete this file?",
            text: "This action cannot be undone.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#6b7280",
            confirmButtonText: "Delete",
            cancelButtonText: "Cancel",
            customClass: {
                popup: "rounded-xl shadow-2xl border-0",
                title: "text-lg font-bold text-gray-800",
                confirmButton:
                    "bg-red-500 hover:bg-red-600 border-0 rounded-lg px-4 py-2",
                cancelButton:
                    "bg-gray-500 hover:bg-gray-600 border-0 rounded-lg px-4 py-2",
            },
        }).then((result) => {
            if (result.isConfirmed) {
                onDelete(filePath);
                const newSelected = new Set(selectedFiles);
                if (newSelected.has(filePath)) {
                    newSelected.delete(filePath);
                    setSelectedFiles(newSelected);
                }
            }
        });
    };

    return (
        <div
            className="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
            style={{
                zIndex: 3000,
                backgroundColor: "rgba(0,0,0,0.6)",
                backdropFilter: "blur(4px)",
            }}
            onClick={onClose}
        >
            <style>{`
                .swal2-container { z-index: 10000 !important; }
                /* Overlay Styles */
                .media-actions { opacity: 0; transition: opacity 0.2s ease; background-color: rgba(0, 0, 0, 0.4); }
                .media-card:hover .media-actions { opacity: 1; }
                .media-card.selected .media-actions { opacity: 1; background-color: rgba(0, 0, 0, 0.1); }
                
                .media-card { transition: all 0.2s ease; }
                .media-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
                .custom-scrollbar::-webkit-scrollbar { width: 6px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }
                .button-gradient { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border: none; }
                .text-shadow { text-shadow: 0 1px 2px rgba(0,0,0,0.3); }
                .drop-zone-active { border: 2px dashed #6366f1; background-color: rgba(99, 102, 241, 0.05); }
            `}</style>

            <div
                className="bg-white rounded-4 shadow-2xl overflow-hidden d-flex flex-column"
                style={{ width: "900px", height: "85vh", maxWidth: "95%" }}
                onClick={(e) => e.stopPropagation()}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
            >
                {/* Header */}
                <div className="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-white sticky-top">
                    <div className="d-flex align-items-center gap-3">
                        <h5 className="m-0 fw-bold text-dark ls-tight">
                            Media Library
                        </h5>
                        <div className="bg-light rounded-pill px-3 py-1 text-xs fw-bold text-secondary">
                            {files.length} Assets
                        </div>
                    </div>
                    <button
                        className="btn btn-icon btn-light rounded-circle shadow-sm"
                        onClick={onClose}
                        style={{
                            width: "32px",
                            height: "32px",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                        }}
                    >
                        <i className="fas fa-times"></i>
                    </button>
                </div>

                {/* Toolbar */}
                <div className="px-4 py-3 bg-white border-bottom d-flex gap-3 align-items-center">
                    <div className="position-relative flex-grow-1">
                        <i className="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input
                            type="text"
                            className="form-control rounded-pill ps-5 bg-light border-0"
                            placeholder="Search your uploads..."
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            style={{ height: "42px" }}
                        />
                    </div>

                    <input
                        type="file"
                        ref={fileInputRef}
                        className="d-none"
                        accept="image/*"
                        multiple
                        onChange={(e) => {
                            if (e.target.files && e.target.files.length > 0) {
                                handleFilesAdded(e.target.files);
                            }
                            e.target.value = ""; // Reset
                        }}
                    />
                    <button
                        className="btn btn-primary rounded-pill px-4 fw-medium d-flex align-items-center gap-2 shadow-sm button-gradient"
                        onClick={() => fileInputRef.current.click()}
                        style={{ height: "42px" }}
                    >
                        <i className="fas fa-cloud-upload-alt"></i>
                        <span>Upload Media</span>
                    </button>
                </div>

                {/* Body - Grid */}
                <div
                    className={`p-4 overflow-auto grow bg-light custom-scrollbar position-relative ${
                        isDragging ? "drop-zone-active" : ""
                    }`}
                >
                    {/* Drag Overlay */}
                    {isDragging && (
                        <div
                            className="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                            style={{
                                zIndex: 50,
                                backgroundColor: "rgba(255,255,255,0.8)",
                                backdropFilter: "blur(2px)",
                            }}
                        >
                            <div className="text-center text-primary animate-bounce">
                                <i className="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                <h4 className="fw-bold">
                                    Drop files to upload
                                </h4>
                            </div>
                        </div>
                    )}

                    {/* Check if Empty (and no uploads) */}
                    {filteredFiles.length === 0 && uploadQueue.length === 0 ? (
                        <div
                            className="h-100 d-flex flex-column align-items-center justify-content-center text-center opacity-75"
                            onClick={() => fileInputRef.current.click()}
                            style={{ cursor: "pointer" }}
                        >
                            {searchQuery ? (
                                <>
                                    <div className="mb-3 p-3 bg-white rounded-circle shadow-sm text-secondary">
                                        <i className="fas fa-search fs-2"></i>
                                    </div>
                                    <h6 className="fw-bold text-dark">
                                        No matches found
                                    </h6>
                                    <p className="text-secondary small">
                                        Try a different search term
                                    </p>
                                </>
                            ) : (
                                <>
                                    <div className="mb-4 text-muted">
                                        <i className="fas fa-cloud-upload-alt fa-4x mb-3 text-primary opacity-50"></i>
                                    </div>
                                    <h6 className="fw-bold text-dark mb-2">
                                        Library is Empty
                                    </h6>
                                    <p className="text-secondary small mb-4">
                                        Drag & drop images here or click to
                                        browse
                                    </p>
                                </>
                            )}
                        </div>
                    ) : (
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns:
                                    "repeat(auto-fill, minmax(140px, 1fr))",
                                gap: "1.25rem",
                            }}
                        >
                            {/* Upload Queue Items */}
                            {uploadQueue.map((item) => (
                                <div
                                    key={item.id}
                                    className="bg-white rounded-3 p-3 border shadow-sm d-flex flex-column justify-content-center position-relative overflow-hidden"
                                    style={{ aspectRatio: "1" }}
                                >
                                    <div className="text-center mb-2">
                                        <i
                                            className={`fas ${
                                                item.status === "error"
                                                    ? "fa-exclamation-circle text-danger"
                                                    : "fa-image text-muted"
                                            } fs-3`}
                                        ></i>
                                    </div>
                                    <div className="text-xs text-truncate text-center mb-2 fw-medium">
                                        {item.name}
                                    </div>

                                    {item.status === "error" ? (
                                        <div className="text-danger text-xs text-center">
                                            {item.errorMsg}
                                        </div>
                                    ) : (
                                        <div
                                            className="w-100 bg-light rounded-pill overflow-hidden"
                                            style={{ height: "6px" }}
                                        >
                                            <div
                                                className="bg-primary h-100 transition-all duration-300"
                                                style={{
                                                    width: `${item.progress}%`,
                                                }}
                                            ></div>
                                        </div>
                                    )}
                                    {item.status === "success" && (
                                        <div className="position-absolute top-0 end-0 p-1 text-success bg-white rounded-circle m-2 shadow-sm">
                                            <i className="fas fa-check-circle"></i>
                                        </div>
                                    )}
                                </div>
                            ))}

                            {/* Existing Files */}
                            {filteredFiles.map((file) => {
                                const isSelected = selectedFiles.has(file.path);
                                return (
                                    <div
                                        key={file.path}
                                        className={`media-card position-relative bg-white rounded-3 overflow-hidden border ${
                                            isSelected
                                                ? "selected border-primary ring-2 ring-primary ring-opacity-50"
                                                : "border-light"
                                        }`}
                                        style={{
                                            aspectRatio: "1",
                                            cursor: "pointer",
                                            transition: "all 0.2s",
                                            transform: isSelected
                                                ? "scale(0.98)"
                                                : "scale(1)",
                                        }}
                                        onClick={(e) =>
                                            handleToggleSelect(e, file)
                                        }
                                    >
                                        {/* Checkbox */}
                                        <div
                                            className="position-absolute top-0 start-0 p-2"
                                            style={{ zIndex: 20 }}
                                        >
                                            <div
                                                className={`rounded-circle d-flex align-items-center justify-content-center shadow-sm border ${
                                                    isSelected
                                                        ? "bg-primary border-primary text-white"
                                                        : "bg-white border-secondary"
                                                }`}
                                                style={{
                                                    width: "24px",
                                                    height: "24px",
                                                    transition: "all 0.2s",
                                                }}
                                            >
                                                {isSelected && (
                                                    <i
                                                        className="fas fa-check"
                                                        style={{
                                                            fontSize: "12px",
                                                        }}
                                                    ></i>
                                                )}
                                            </div>
                                        </div>

                                        {/* Image Wrapper */}
                                        <div
                                            className="w-100 h-100 p-3 d-flex align-items-center justify-content-center"
                                            style={{
                                                backgroundColor: "#f8f9fa",
                                                backgroundImage:
                                                    "radial-gradient(#cbd5e1 1px, transparent 1px)",
                                                backgroundSize: "10px 10px",
                                            }}
                                        >
                                            <img
                                                src={file.url}
                                                alt={file.name}
                                                className="mw-100 mh-100 object-fit-contain shadow-sm rounded-2"
                                                onError={(e) => {
                                                    e.target.onerror = null;
                                                    e.target.src =
                                                        "https://via.placeholder.com/150?text=Error";
                                                }}
                                            />
                                        </div>

                                        {/* Overlay */}
                                        <div className="media-actions position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <div
                                                className="position-absolute top-0 end-0 p-2 d-flex gap-2"
                                                style={{ zIndex: 20 }}
                                            >
                                                <button
                                                    className="btn btn-light btn-sm rounded-circle shadow-sm text-primary d-flex align-items-center justify-content-center"
                                                    style={{
                                                        width: "32px",
                                                        height: "32px",
                                                    }}
                                                    onClick={(e) =>
                                                        handleRenameClick(
                                                            e,
                                                            file
                                                        )
                                                    }
                                                    title="Rename"
                                                >
                                                    <i
                                                        className="fas fa-pen"
                                                        style={{
                                                            fontSize: "14px",
                                                        }}
                                                    ></i>
                                                </button>
                                                <button
                                                    className="btn btn-light btn-sm rounded-circle shadow-sm text-danger d-flex align-items-center justify-content-center"
                                                    style={{
                                                        width: "32px",
                                                        height: "32px",
                                                    }}
                                                    onClick={(e) =>
                                                        handleDeleteClick(
                                                            e,
                                                            file.path
                                                        )
                                                    }
                                                    title="Delete"
                                                >
                                                    <i
                                                        className="fas fa-trash-alt"
                                                        style={{
                                                            fontSize: "14px",
                                                        }}
                                                    ></i>
                                                </button>
                                            </div>
                                        </div>

                                        {/* Label */}
                                        <div className="position-absolute bottom-0 w-100 bg-white border-top p-2">
                                            <div className="text-truncate text-xs fw-medium text-secondary text-center">
                                                {file.name}
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}
                </div>

                {/* Footer */}
                <div className="bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
                    <div className="d-flex align-items-center gap-3">
                        <small className="text-muted text-xs">
                            {selectedFiles.size} selected
                        </small>
                        {selectedFiles.size > 0 && (
                            <button
                                className="btn btn-danger btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-2"
                                onClick={(e) => {
                                    e.stopPropagation();
                                    Swal.fire({
                                        title: `Delete ${selectedFiles.size} files?`,
                                        text: "This action cannot be undone.",
                                        icon: "warning",
                                        showCancelButton: true,
                                        confirmButtonColor: "#ef4444",
                                        cancelButtonColor: "#6b7280",
                                        confirmButtonText: "Yes, delete",
                                        customClass: {
                                            popup: "rounded-xl shadow-2xl border-0",
                                            confirmButton:
                                                "bg-red-500 hover:bg-red-600 border-0 rounded-lg px-4 py-2",
                                        },
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            onDelete(Array.from(selectedFiles));
                                            setSelectedFiles(new Set());
                                        }
                                    });
                                }}
                            >
                                <i className="fas fa-trash-alt"></i>
                                <span>Delete Selected</span>
                            </button>
                        )}
                    </div>

                    <button
                        className={`btn btn-primary rounded-pill px-4 fw-bold shadow-sm ${
                            selectedFiles.size === 0
                                ? "disabled opacity-50"
                                : "button-gradient"
                        }`}
                        onClick={handleInsertSelected}
                        disabled={selectedFiles.size === 0}
                    >
                        Insert Selected ({selectedFiles.size})
                    </button>
                </div>
            </div>
        </div>
    );
}
