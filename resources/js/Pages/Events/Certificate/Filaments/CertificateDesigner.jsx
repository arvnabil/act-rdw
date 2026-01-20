import React, { useState, useRef, useEffect, useMemo } from "react";
import { Head, router } from "@inertiajs/react";
import DraggableElement from "@/Components/Events/Certificate/DraggableElement";
import Ruler from "@/Components/Events/Certificate/Ruler";
import Toast from "@/Components/Common/Toast";
import MediaManager from "@/Components/Events/Certificate/MediaManager";

export default function CertificateDesigner({ certificate, event }) {
    // Parse initial layout: legacy array or new object
    const initialLayout = certificate.content_layout || {};
    const isLegacy = Array.isArray(initialLayout);

    const [elements, setElements] = useState(
        isLegacy ? initialLayout : initialLayout.elements || [],
    );
    const [canvasSize, setCanvasSize] = useState(
        !isLegacy && initialLayout.canvas
            ? initialLayout.canvas
            : { width: 1056, height: 816 },
    );
    const [bgConfig, setBgConfig] = useState(
        !isLegacy && initialLayout.background
            ? initialLayout.background
            : { x: 0, y: 0, scale: 1 },
    );
    const [bgUrl, setBgUrl] = useState(certificate.certificate_background);
    const [isBgEditMode, setIsBgEditMode] = useState(false);

    const elementsRef = useRef(elements);
    elementsRef.current = elements;
    const [selectedIds, setSelectedIds] = useState(new Set());
    const [saving, setSaving] = useState(false);
    const [zoom, setZoom] = useState(1);
    const [uploadingSig, setUploadingSig] = useState(false);
    const [guides, setGuides] = useState({ x: null, y: null });
    const [isSidebarOpen, setIsSidebarOpen] = useState(true);

    // Theme State - Default: Dark (Tech)
    const [isDarkMode, setIsDarkMode] = useState(true);

    const [mediaFiles, setMediaFiles] = useState([]);
    const [showMediaManager, setShowMediaManager] = useState(false);

    // Initial Fetch of Media
    // Initial Fetch of Media
    const fetchMedia = () => {
        fetch(route("events.certificates.get-media", certificate.id))
            .then((res) => res.json())
            .then((data) => setMediaFiles(data))
            .catch((e) => console.error("Failed to load media", e));
    };

    useEffect(() => {
        fetchMedia();
    }, []);

    const canvasRef = useRef(null);
    const containerRef = useRef(null);

    // --- History / Undo / Redo ---
    const [history, setHistory] = useState([certificate.content_layout || []]);
    const [future, setFuture] = useState([]);

    const addToHistory = (newElements) => {
        setHistory((prev) => {
            const last = prev[prev.length - 1];
            if (JSON.stringify(last) === JSON.stringify(newElements)) {
                return prev;
            }
            return [...prev, newElements];
        });
        setFuture([]); // Clear redo stack on new action
    };

    const undo = () => {
        if (history.length > 1) {
            const current = history[history.length - 1];
            const previous = history[history.length - 2];
            setFuture((prev) => [current, ...prev]);
            setHistory((prev) => prev.slice(0, -1));
            setElements(previous);
        }
    };

    const redo = () => {
        if (future.length > 0) {
            const next = future[0];
            setHistory((prev) => [...prev, next]);
            setFuture((prev) => prev.slice(1));
            setElements(next);
        }
    };

    // Helper wrapper to update elements and save state
    const updateElements = (newElements, save = true) => {
        setElements(newElements);
        if (save) addToHistory(newElements);
    };

    // --- Fonts ---
    const fonts = [
        { label: "Cinzel", value: "'Cinzel', serif" },
        { label: "Montserrat", value: "'Montserrat', sans-serif" },
        { label: "Playfair Display", value: "'Playfair Display', serif" },
        { label: "Great Vibes", value: "'Great Vibes', cursive" },
        { label: "Lato", value: "'Lato', sans-serif" },
        { label: "Pinyon Script", value: "'Pinyon Script', cursive" },
        { label: "Cormorant Garamond", value: "'Cormorant Garamond', serif" },
        { label: "Open Sans", value: "'Open Sans', sans-serif" },
        { label: "Bebas Neue", value: "'Bebas Neue', display" },
        { label: "Alex Brush", value: "'Alex Brush', cursive" },
    ];

    const primarySelectedId = useMemo(() => {
        if (selectedIds.size === 0) return null;
        return [...selectedIds][selectedIds.size - 1];
    }, [selectedIds]);
    const primaryElement = elements.find((el) => el.id === primarySelectedId);

    const [persistentGuides, setPersistentGuides] = useState([]); // { type: 'x'|'y', pos: number }
    const [draggingGuide, setDraggingGuide] = useState(null); // { type, currentPos }

    // Figma-style Layout Grid State
    const [layoutGrid, setLayoutGrid] = useState({
        enabled: false,
        columns: 12,
        margin: 40,
        gutter: 20,
        color: "#FF0000",
        opacity: 0.1,
    });
    const [showGridSettings, setShowGridSettings] = useState(false);

    // Toast State
    const [toast, setToast] = useState(null); // { message, type: 'success'|'danger' }

    useEffect(() => {
        const link = document.createElement("link");

        // ...
    }, []); // This useEffect seems to be cut off in view, I will insert before it.

    const previewData = useMemo(
        () => ({
            participant_name: "John Doe",
            event_title: event.title || "Event Title",
            date: new Date().toLocaleDateString("en-GB", {
                day: "numeric",
                month: "long",
                year: "numeric",
            }),
            certificate_code: "CERT-SAMPLE-123",
        }),
        [event.title],
    );

    useEffect(() => {
        const link = document.createElement("link");
        link.href =
            "https://fonts.googleapis.com/css2?family=Alex+Brush&family=Bebas+Neue&family=Cinzel:wght@400;700&family=Cormorant+Garamond:wght@400;600&family=Great+Vibes&family=Lato:wght@300;400;700&family=Montserrat:wght@300;400;600&family=Open+Sans:wght@400;600&family=Pinyon+Script&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap";
        link.rel = "stylesheet";
        document.head.appendChild(link);

        const clearGuides = () => setGuides({ x: null, y: null });
        window.addEventListener("mouseup", clearGuides);

        // Guide Dragging Handlers

        const handleGuideMove = (e) => {
            setDraggingGuide((prev) => {
                if (!prev) return null;
                if (!canvasRef.current) return prev;
                const rect = canvasRef.current.getBoundingClientRect();

                let currentPos = 0;
                if (prev.type === "horizontal") {
                    currentPos = (e.clientY - rect.top) / zoom;
                } else {
                    currentPos = (e.clientX - rect.left) / zoom;
                }
                return { ...prev, currentPos };
            });
        };

        const handleGuideUp = (e) => {
            setDraggingGuide((prev) => {
                if (!prev) return null;

                const isOutOfBounds = prev.currentPos < -20;

                setPersistentGuides((old) => {
                    const newGuides = [...old];
                    if (prev.index !== undefined && prev.index !== -1) {
                        // Moving existing guide
                        if (isOutOfBounds) {
                            newGuides.splice(prev.index, 1);
                        } else {
                            newGuides[prev.index] = {
                                ...newGuides[prev.index],
                                pos: prev.currentPos,
                            };
                        }
                        return newGuides;
                    } else {
                        // New guide
                        if (!isOutOfBounds) {
                            return [
                                ...old,
                                {
                                    type:
                                        prev.type === "horizontal" ? "y" : "x",
                                    pos: prev.currentPos,
                                },
                            ];
                        }
                        return old;
                    }
                });
                return null;
            });
        };

        window.addEventListener("mousemove", handleGuideMove);
        window.addEventListener("mouseup", handleGuideUp);

        return () => {
            document.head.removeChild(link);
            window.removeEventListener("mouseup", clearGuides);
            window.removeEventListener("mousemove", handleGuideMove);
            window.removeEventListener("mouseup", handleGuideUp);
        };
    }, [zoom]);

    const handleGuideDragStart = (type, e, index = -1) => {
        e.preventDefault();
        e.stopPropagation();
        if (!canvasRef.current) return;
        const rect = canvasRef.current.getBoundingClientRect();

        // Map types correctly
        let dragType = type;
        if (type === "y") dragType = "horizontal";
        if (type === "x") dragType = "vertical";

        let startPos = 0;
        if (dragType === "horizontal") {
            startPos = (e.clientY - rect.top) / zoom;
        } else {
            startPos = (e.clientX - rect.left) / zoom;
        }
        setDraggingGuide({ type: dragType, currentPos: startPos, index });
    };

    const handleRulerDragStart = (type, e) => {
        handleGuideDragStart(type, e, -1);
    };

    // --- Actions ---

    const addElements = (items) => {
        // Normalize input to array
        const configs = Array.isArray(items) ? items : [items];

        const newElements = configs.map((config) => {
            const type = config.type;
            const newElement = {
                id: Date.now() + Math.random(), // Ensure unique IDs for bulk insert
                type: type,
                x: 50 + Math.random() * 20, // Slight offset to prevent perfect stacking
                y: 50 + Math.random() * 20,
                ...config,
            };

            if (type === "text" || type === "variable") {
                newElement.text = config.text || "New Text";
                newElement.fontSize = 28;
                newElement.color = "#1e293b";
                newElement.fontWeight = "normal";
                newElement.fontFamily = "'Montserrat', sans-serif";
                newElement.textAlign = "center";
            } else if (type === "qrcode") {
                newElement.width = 100;
            } else if (type === "shape") {
                newElement.width = 150;
                newElement.height = 100;
                newElement.color = "#0f172a";
                newElement.shapeType = config.shapeType || "rectangle";
            } else if (type === "line") {
                newElement.width = 300;
                newElement.height = 3;
                newElement.color = "#000000";
            } else if (type === "signature" || type === "image") {
                newElement.width = 200;
                newElement.src = config.src || "";
            }
            return newElement;
        });

        // Functional update to ensure we always have the latest state
        updateElements([...elements, ...newElements]);

        // Select all new elements
        setSelectedIds(new Set(newElements.map((el) => el.id)));
    };

    // Keep addElement for backward compatibility if needed, but wrap addElements
    const addElement = (type, config = {}) => {
        addElements([{ type, ...config }]);
    };
    // Undo
    useEffect(() => {
        const handleKeyDown = (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === "z") {
                e.preventDefault();
                undo();
            }
        };
        window.addEventListener("keydown", handleKeyDown);
        return () => window.removeEventListener("keydown", handleKeyDown);
    }, [undo]);
    const updateSelected = (changes) => {
        // Properties update - we might want to debate if this pushes history every time.
        // For inputs, probably yes for "final" change, but typing?
        // Let's use history for now to be safe, though chatty.
        const newElements = elements.map((el) =>
            selectedIds.has(el.id) ? { ...el, ...changes } : el,
        );
        updateElements(newElements);
    };

    const deleteElement = (id) => {
        const newElements = elements.filter((el) => el.id !== id);
        updateElements(newElements);
        const newSet = new Set(selectedIds);
        newSet.delete(id);
        setSelectedIds(newSet);
    };

    const duplicateSelected = () => {
        const clones = elements
            .filter((el) => selectedIds.has(el.id))
            .map((el) => ({
                ...el,
                id: Date.now() + Math.random(),
                x: el.x + 2,
                y: el.y + 2,
            }));
        updateElements([...elements, ...clones]);
        setSelectedIds(new Set(clones.map((c) => c.id)));
    };

    const handleUploadMedia = (file) => {
        if (!file) return;

        // Generate ID for progress tracking
        const tempId = Date.now();
        const url = URL.createObjectURL(file);

        // Add initial file with uploading state
        setMediaFiles((prev) => [
            ...prev,
            {
                id: tempId,
                name: file.name,
                path: url,
                url: url,
                size: file.size,
                type: file.type,
                uploading: true,
                progress: 0,
            },
        ]);

        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            setMediaFiles((prev) =>
                prev.map((f) => {
                    if (f.id === tempId) {
                        return { ...f, progress: progress };
                    }
                    return f;
                }),
            );

            if (progress >= 100) {
                clearInterval(interval);
                setMediaFiles((prev) =>
                    prev.map((f) => {
                        if (f.id === tempId) {
                            return { ...f, uploading: false };
                        }
                        return f;
                    }),
                );
                setToast({
                    message: "Image uploaded successfully",
                    type: "success",
                });
                setTimeout(() => setToast(null), 3000);
            }
        }, 200);
    };

    const handleDeleteMedia = (path) => {
        setMediaFiles((prev) => prev.filter((f) => f.path !== path));
    };

    const handleRenameMedia = (path, newName) => {
        console.log("Renaming implementation pending", path, newName);
    };

    const handleDrag = (deltaX, deltaY) => {
        if (!canvasRef.current || !containerRef.current) return;

        const rect = canvasRef.current.getBoundingClientRect();
        const scaleX = 100 / rect.width;
        const scaleY = 100 / rect.height;
        const dXPercent = deltaX * scaleX;
        const dYPercent = deltaY * scaleY;

        // Snapping Disabled per request
        // We just update position raw. Guides are visual reference only.

        setElements((prev) =>
            prev.map((el) => {
                if (selectedIds.has(el.id)) {
                    return {
                        ...el,
                        x: Math.max(0, Math.min(100, el.x + dXPercent)),
                        y: Math.max(0, Math.min(100, el.y + dYPercent)),
                    };
                }
                return el;
            }),
        );
    };

    const handleResize = (id, deltaX, deltaY, handle) => {
        if (!canvasRef.current) return;
        const rect = canvasRef.current.getBoundingClientRect();
        const scaleX = 100 / rect.width;
        const scaleY = 100 / rect.height;

        setElements((prev) =>
            prev.map((el) => {
                if (el.id !== id) return el;

                const sDeltaX = deltaX / zoom;
                const sDeltaY = deltaY / zoom;

                let newW = el.width || 100;
                let newH = el.height || 100;
                let newX = el.x;
                let newY = el.y;

                // Delta for position (converted to %)
                const dXPercent = (deltaX / zoom) * scaleX;
                const dYPercent = (deltaY / zoom) * scaleY;

                if (handle.includes("e")) {
                    newW += sDeltaX;
                }
                if (handle.includes("w")) {
                    newW -= sDeltaX;
                    newX += dXPercent;
                }
                if (handle.includes("s")) {
                    newH += sDeltaY;
                }
                if (handle.includes("n")) {
                    newH -= sDeltaY;
                    newY += dYPercent;
                }

                return {
                    ...el,
                    x: newX,
                    y: newY,
                    width: Math.max(10, newW),
                    height: Math.max(10, newH),
                };
            }),
        );
    };

    const handleMediaUpload = async (file) => {
        if (!file) return;
        setUploadingSig(true);

        const formData = new FormData();
        formData.append("file", file);

        try {
            const response = await fetch(
                route("events.certificates.upload-media", certificate.id),
                {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                },
            );

            if (response.ok) {
                const newFile = await response.json();
                setMediaFiles((prev) => [...prev, newFile]);
                setToast({
                    message: "File uploaded successfully",
                    type: "success",
                });
            } else {
                console.error("Upload failed");
                setToast({ message: "Upload failed", type: "danger" });
            }
        } catch (error) {
            console.error("Error uploading media:", error);
            setToast({ message: "Error uploading media", type: "danger" });
        } finally {
            setUploadingSig(false);
        }
    };

    // --- Background Adjustment ---
    const handleBgMouseDown = (e) => {
        if (!isBgEditMode) return;
        e.preventDefault();
        e.stopPropagation();

        const startX = e.clientX;
        const startY = e.clientY;
        const startBgX = bgConfig.x;
        const startBgY = bgConfig.y;

        const handleMove = (ev) => {
            const dx = (ev.clientX - startX) / zoom;
            const dy = (ev.clientY - startY) / zoom;
            setBgConfig((prev) => ({
                ...prev,
                x: startBgX + dx,
                y: startBgY + dy,
            }));
        };

        const handleUp = () => {
            window.removeEventListener("mousemove", handleMove);
            window.removeEventListener("mouseup", handleUp);
        };

        window.addEventListener("mousemove", handleMove);
        window.addEventListener("mouseup", handleUp);
    };

    const handleMediaDelete = async (pathOrPaths) => {
        const isArray = Array.isArray(pathOrPaths);
        const body = isArray ? { paths: pathOrPaths } : { path: pathOrPaths };
        const pathsToDelete = isArray ? pathOrPaths : [pathOrPaths];

        try {
            const response = await fetch(
                route("events.certificates.delete-media", certificate.id),
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify(body),
                },
            );

            if (response.ok) {
                setMediaFiles((prev) =>
                    prev.filter((f) => !pathsToDelete.includes(f.path)),
                );

                // Sync Deletion: Remove elements using these images
                setElements((prev) =>
                    prev.filter((el) => {
                        if (el.type !== "image" && el.type !== "signature")
                            return true;
                        // Check if src contains any of the deleted paths
                        return !pathsToDelete.some((p) => el.src.includes(p));
                    }),
                );

                // Sync Deletion: Clear background if it matches
                if (bgUrl && pathsToDelete.some((p) => bgUrl.includes(p))) {
                    setBgUrl(null);
                }

                setToast({
                    message: "File(s) deleted successfully",
                    type: "success",
                });
            } else {
                console.error("Delete failed");
                setToast({ message: "Delete failed", type: "danger" });
            }
        } catch (error) {
            console.error("Error deleting media:", error);
            setToast({ message: "Error deleting media", type: "danger" });
        }
    };

    const handleMediaRename = async (path, newName) => {
        try {
            const response = await fetch(
                route("events.certificates.rename-media", certificate.id),
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify({ old_path: path, new_name: newName }),
                },
            );

            if (response.ok) {
                const data = await response.json();
                setMediaFiles((prev) =>
                    prev.map((f) => (f.path === path ? data : f)),
                );
                setToast({
                    message: "File renamed successfully",
                    type: "success",
                });
            } else {
                const err = await response.json();
                setToast({
                    message: err.error || "Rename failed",
                    type: "danger",
                });
            }
        } catch (error) {
            console.error(error);
            setToast({ message: "Rename error", type: "danger" });
        }
    };

    const handleSave = () => {
        setSaving(true);
        router.post(
            route("events.certificates.update-design", certificate.id),
            {
                content_layout: {
                    elements: elements,
                    canvas: canvasSize,
                    background: bgConfig,
                },
                image_files: mediaFiles,
                certificate_background: bgUrl, // Persist current background
            },
            {
                onFinish: () => setSaving(false),
                preserveScroll: true,
                onSuccess: () => {
                    setToast({
                        message: "Design saved successfully!",
                        type: "success",
                    });
                },
                onError: (errors) => {
                    console.error("Save failed:", errors);
                    // Extract first error message
                    const firstError = Object.values(errors)[0];
                    setToast({
                        message: `Failed: ${firstError || "Check console"}`,
                        type: "error",
                    });
                },
            },
        );
    };

    // --- Theming Strategy ---
    const theme = isDarkMode
        ? {
              bgApp: "bg-[#0f172a]",
              bgPanel: "bg-[#1e293b]",
              bgInput: "bg-[#1e293b]", // Match panel
              border: "border-slate-700",
              borderLight: "border-slate-600",
              textMain: "text-[#e1e4e5]", // Lighter gray as requested
              textMuted: "text-slate-400",
              textInverse: "text-slate-900",
              glassHdr: "glass-panel-dark",
              glassSide: "glass-panel-dark",
              highlight: "text-info",
              btnHover: "hover:bg-slate-200",
              grid: "grid-bg-dark",
          }
        : {
              bgApp: "bg-slate-50",
              bgPanel: "bg-white",
              bgInput: "bg-white",
              border: "border-slate-200",
              borderLight: "border-slate-300",
              textMain: "text-slate-700",
              textMuted: "text-slate-500",
              textInverse: "text-slate-100",
              glassHdr: "glass-panel-light",
              glassSide: "glass-panel-light",
              highlight: "text-success",
              btnHover: "hover:bg-slate-100",
              grid: "grid-bg-light",
          };

    return (
        <div
            className={`d-flex flex-column vh-100 ${theme.bgApp} ${theme.textMain} transition-colors duration-300`}
            style={{ fontFamily: "'Inter', sans-serif" }}
        >
            <Head title={`Designer - ${event.title}`} />
            <style>{`
                /* Transition for smooth theme switch */
                .transition-colors { transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease; }

                ::-webkit-scrollbar { width: 6px; height: 6px; }
                ::-webkit-scrollbar-track { background: transparent; }
                ::-webkit-scrollbar-thumb { background: ${
                    isDarkMode ? "#334155" : "#cbd5e1"
                }; border-radius: 3px; }

                .glass-panel-dark {
                    background: rgba(30, 41, 59, 0.95); /* More opaque for professional look */
                    backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.05);
                }
                .glass-panel-light {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(12px);
                    border: 1px solid rgba(0, 0, 0, 0.05);
                }

                .grid-bg-dark {
                    background-image: radial-gradient(rgba(51, 65, 85, 0.4) 1px, transparent 1px);
                    background-size: 24px 24px;
                }
                .grid-bg-light {
                    background-image: radial-gradient(rgba(148, 163, 184, 0.4) 1px, transparent 1px);
                    background-size: 24px 24px;
                }

                .btn-tech {
                    background: linear-gradient(135deg, ${
                        isDarkMode ? "#06b6d4" : "#3b82f6"
                    } 0%, ${isDarkMode ? "#3b82f6" : "#2563eb"} 100%);
                    color: white; border: none;
                    box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
                    transition: all 0.2s;
                }
                .btn-tech:hover { transform: translateY(-1px); box-shadow: 0 8px 10px -2px rgba(59, 130, 246, 0.4); }

                /* Sidebar Buttons */
                .sidebar-btn {
                    transition: all 0.2s;
                    border: 1px solid #dee2e6;
                    color: ${isDarkMode ? "#e1e4e5" : "#334155"};
                    background: ${
                        isDarkMode ? "rgba(255, 255, 255, 0.05)" : "transparent"
                    };
                }
                .sidebar-btn:hover {
                    background: ${
                        isDarkMode ? "rgba(255, 255, 255, 0.1)" : "#f1f5f9"
                    }; /* Theme color hover */
                    border-color: ${theme.border};
                    transform: translateX(2px);
                }

                /* Upload Button */
                .btn-upload {
                    background: white; color: #333; border: 1px solid #ddd; transition: all 0.3s;
                }
                .btn-upload:hover {
                    background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
                    color: white; border-color: transparent;
                }

                /* Inputs - "Proper" Style */
                .form-control-proper {
                    border-radius: 4px; /* Less rounded */
                    border: 1px solid ${isDarkMode ? "#475569" : "#cbd5e1"};
                    background-color: ${
                        isDarkMode ? "#0f172a" : "#fff"
                    }; /* Input distinct bg */
                    color: ${isDarkMode ? "#fff" : "#333"};
                    padding: 0.375rem 0.75rem;
                    margin-bottom: 1rem;
                    font-size: 0.875rem;
                }
                .form-control-proper:focus {
                    border-color: #06b6d4;
                    color: ${isDarkMode ? "#333" : "#000"};
                    box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2);
                    outline: none;
                }
                .form-control-proper:hover {
                    color: ${isDarkMode ? "#333" : "#000"};
                }

                .form-select-proper {
                    margin-bottom: 1rem;
                    border-radius: 4px;
                    border: 1px solid ${isDarkMode ? "#475569" : "#cbd5e1"};
                    background-color: ${isDarkMode ? "#0f172a" : "#fff"};
                    color: ${isDarkMode ? "#fff" : "#333"};
                    padding: 0.375rem 2rem 0.375rem 0.75rem;
                    font-size: 0.875rem;
                }
            `}</style>

            {/* Header */}
            <header
                className={`px-4 py-3 d-flex justify-content-between align-items-center shadow-sm position-relative ${theme.glassHdr} border-bottom ${theme.border}`}
                style={{ zIndex: 100 }}
            >
                <div className="d-flex align-items-center gap-3">
                    <button
                        className={`btn btn-sm rounded-circle border-0 d-flex align-items-center justify-content-center ${
                            isDarkMode
                                ? "bg-slate-800 text-white"
                                : "bg-slate-100 text-dark"
                        }`}
                        onClick={() => window.close()}
                        style={{ width: 36, height: 36 }}
                    >
                        <i className="fas fa-chevron-left"></i>
                    </button>
                    <div>
                        <div
                            className={`fw-bold ls-1 ${
                                isDarkMode ? "text-white" : "text-dark"
                            }`}
                            style={{ fontSize: "1.1rem" }}
                        >
                            CERTIFICATE{" "}
                            <span
                                className={
                                    isDarkMode
                                        ? theme.highlight
                                        : theme.highlight
                                }
                            >
                                STUDIO
                            </span>
                        </div>
                        <div
                            className={`text-xs ${
                                isDarkMode ? "text-white" : "text-dark"
                            } fw-medium`}
                        >
                            {event.title}
                        </div>
                    </div>
                </div>

                <div className="d-flex align-items-center gap-3">
                    {/* Theme Toggle */}
                    <button
                        className={`btn btn-sm rounded-circle d-flex align-items-center justify-content-center border-0 ${
                            isDarkMode
                                ? "bg-slate-800 text-warning"
                                : "bg-slate-200 text-slate-600"
                        }`}
                        onClick={() => setIsDarkMode(!isDarkMode)}
                        style={{ width: 32, height: 32 }}
                    >
                        <i
                            className={`fas ${
                                isDarkMode ? "fa-sun" : "fa-moon"
                            }`}
                        ></i>
                    </button>

                    {/* Zoom Controls (White text requested) */}
                    <div
                        className={`d-flex align-items-center rounded-pill p-1 border ${theme.border} ${theme.bgInput}`}
                    >
                        <button
                            className={`btn btn-sm ${
                                isDarkMode ? "text-white" : "text-slate-600"
                            } hover-text-primary`}
                            onClick={() =>
                                setZoom((z) => Math.max(0.4, z - 0.1))
                            }
                        >
                            <i className="fas fa-minus"></i>
                        </button>
                        <span
                            className={`small px-2 fw-medium ${
                                isDarkMode ? "text-white" : "text-slate-800"
                            }`}
                            style={{ minWidth: 40, textAlign: "center" }}
                        >
                            {Math.round(zoom * 100)}%
                        </span>
                        <button
                            className={`btn btn-sm ${
                                isDarkMode ? "text-white" : "text-slate-600"
                            } hover-text-primary`}
                            onClick={() =>
                                setZoom((z) => Math.min(2.5, z + 0.1))
                            }
                        >
                            <i className="fas fa-plus"></i>
                        </button>
                    </div>
                    <div
                        className={`h-50 border-end ${theme.border} mx-1`}
                    ></div>

                    {/* Guide Controls */}
                    <div
                        className={`d-flex align-items-center rounded-pill px-2 border ${theme.border} me-2 ${theme.bgInput}`}
                    >
                        <button
                            className={`btn btn-sm ${
                                isDarkMode ? "text-white" : "text-slate-600"
                            }`}
                            onClick={() => {
                                // 2x2 Grid (Center Lines)
                                const width = canvasSize.width;
                                const height = canvasSize.height;
                                setPersistentGuides([
                                    { type: "x", pos: width / 2 },
                                    { type: "y", pos: height / 2 },
                                ]);
                            }}
                            title="Add Center Guides (2x2)"
                        >
                            <i className="fas fa-border-all"></i>
                        </button>
                        <button
                            className={`btn btn-sm ${
                                isDarkMode ? "text-white" : "text-slate-600"
                            }`}
                            onClick={() => {
                                // 3x3 Grid (Thirds)
                                const width = canvasSize.width;
                                const height = canvasSize.height;
                                setPersistentGuides([
                                    { type: "x", pos: width / 3 },
                                    { type: "x", pos: (width / 3) * 2 },
                                    { type: "y", pos: height / 3 },
                                    { type: "y", pos: (height / 3) * 2 },
                                ]);
                            }}
                            title="Add Thirds Guides (3x3)"
                        >
                            <i className="fas fa-th"></i>
                        </button>
                        <button
                            className={`btn btn-sm text-danger`}
                            onClick={() => setPersistentGuides([])}
                            title="Clear All Guides"
                        >
                            <i className="fas fa-trash-alt"></i>
                        </button>
                    </div>

                    {/* Layout Grid Toggle */}
                    <div className="position-relative me-2">
                        <button
                            className={`btn btn-sm border rounded-pill px-3 d-flex align-items-center gap-2 ${
                                layoutGrid.enabled
                                    ? "bg-primary text-white border-primary"
                                    : isDarkMode
                                      ? "text-white border-slate-600 bg-slate-800"
                                      : "text-slate-600 border-slate-300 bg-white"
                            }`}
                            onClick={() =>
                                setShowGridSettings(!showGridSettings)
                            }
                            title="Configure Layout Columns"
                        >
                            <i className="fas fa-columns"></i>
                            <span className="d-none d-md-inline small">
                                Columns
                            </span>
                        </button>

                        {showGridSettings && (
                            <div
                                className={`position-absolute top-100 end-0 mt-2 p-3 rounded shadow-lg`}
                                onClick={(e) => e.stopPropagation()}
                                style={{
                                    width: "280px",
                                    zIndex: 2000,
                                    backgroundColor: isDarkMode
                                        ? "#1e293b"
                                        : "#ffffff",
                                    borderColor: isDarkMode
                                        ? "#334155"
                                        : "#e2e8f0",
                                    borderWidth: "1px",
                                    borderStyle: "solid",
                                }}
                            >
                                <div className="d-flex justify-content-between align-items-center mb-3">
                                    <span
                                        className={`fw-bold text-sm ${
                                            isDarkMode
                                                ? "text-white"
                                                : "text-slate-800"
                                        }`}
                                    >
                                        Layout Grid
                                    </span>
                                    <div className="form-check form-switch m-0 d-flex align-items-center">
                                        <input
                                            className="form-check-input mt-0"
                                            type="checkbox"
                                            checked={layoutGrid.enabled}
                                            onChange={(e) =>
                                                setLayoutGrid((p) => ({
                                                    ...p,
                                                    enabled: e.target.checked,
                                                }))
                                            }
                                            id="gridToggle"
                                            style={{
                                                cursor: "pointer",
                                                width: "2.5rem",
                                                height: "1.25rem",
                                                border: "2px solid rgba(255,255,255,0.2)",
                                                backgroundColor:
                                                    layoutGrid.enabled
                                                        ? "#ef4444"
                                                        : "#64748b", // Explicit colors
                                                backgroundImage: "none", // Override bootstrap icon if needed
                                            }}
                                        />
                                        <label
                                            className="form-check-label ms-2 cursor-pointer small text-muted"
                                            htmlFor="gridToggle"
                                        >
                                            {layoutGrid.enabled ? "On" : "Off"}
                                        </label>
                                    </div>
                                </div>

                                <div className="mb-3">
                                    <label
                                        className={`text-xs fw-semibold d-block mb-1 ${
                                            isDarkMode
                                                ? "text-slate-400"
                                                : "text-slate-500"
                                        }`}
                                    >
                                        Columns (Count)
                                    </label>
                                    <input
                                        type="number"
                                        className="form-control form-control-sm"
                                        value={layoutGrid.columns}
                                        onChange={(e) => {
                                            const val = parseInt(
                                                e.target.value,
                                            );
                                            setLayoutGrid((p) => ({
                                                ...p,
                                                enabled: true,
                                                columns: isNaN(val)
                                                    ? ""
                                                    : Math.max(
                                                          1,
                                                          Math.min(24, val),
                                                      ),
                                            }));
                                        }}
                                        min="1"
                                        max="24"
                                        style={{
                                            height: "32px",
                                            fontSize: "14px",
                                        }}
                                    />
                                </div>

                                <div className="row g-2 mb-3">
                                    <div className="col-6">
                                        <label
                                            className={`text-xs fw-semibold d-block mb-1 ${
                                                isDarkMode
                                                    ? "text-slate-400"
                                                    : "text-slate-500"
                                            }`}
                                        >
                                            Margin (px)
                                        </label>
                                        <input
                                            type="number"
                                            className="form-control form-control-sm"
                                            value={layoutGrid.margin}
                                            onChange={(e) => {
                                                const val = parseInt(
                                                    e.target.value,
                                                );
                                                setLayoutGrid((p) => ({
                                                    ...p,
                                                    enabled: true,
                                                    margin: isNaN(val)
                                                        ? ""
                                                        : Math.max(0, val),
                                                }));
                                            }}
                                            style={{
                                                height: "32px",
                                                fontSize: "14px",
                                            }}
                                        />
                                    </div>
                                    <div className="col-6">
                                        <label
                                            className={`text-xs fw-semibold d-block mb-1 ${
                                                isDarkMode
                                                    ? "text-slate-400"
                                                    : "text-slate-500"
                                            }`}
                                        >
                                            Gutter (px)
                                        </label>
                                        <input
                                            type="number"
                                            className="form-control form-control-sm"
                                            value={layoutGrid.gutter}
                                            onChange={(e) => {
                                                const val = parseInt(
                                                    e.target.value,
                                                );
                                                setLayoutGrid((p) => ({
                                                    ...p,
                                                    enabled: true,
                                                    gutter: isNaN(val)
                                                        ? ""
                                                        : Math.max(0, val),
                                                }));
                                            }}
                                            style={{
                                                height: "32px",
                                                fontSize: "14px",
                                            }}
                                        />
                                    </div>
                                </div>

                                <div className="mb-1">
                                    <div className="d-flex justify-content-between font-sm mb-1">
                                        <label
                                            className={`text-xs fw-semibold ${
                                                isDarkMode
                                                    ? "text-slate-400"
                                                    : "text-slate-500"
                                            }`}
                                        >
                                            Opacity
                                        </label>
                                        <span
                                            className={`text-xs ${
                                                isDarkMode
                                                    ? "text-slate-400"
                                                    : "text-slate-500"
                                            }`}
                                        >
                                            {Math.round(
                                                layoutGrid.opacity * 100,
                                            )}
                                            %
                                        </span>
                                    </div>
                                    <input
                                        type="range"
                                        className="form-range w-100"
                                        min="0.05"
                                        max="0.5"
                                        step="0.05"
                                        value={layoutGrid.opacity}
                                        onChange={(e) =>
                                            setLayoutGrid((p) => ({
                                                ...p,
                                                enabled: true,
                                                opacity: parseFloat(
                                                    e.target.value,
                                                ),
                                            }))
                                        }
                                        style={{ accentColor: "#ef4444" }}
                                    />
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="h-100 mx-3 border-end border-secondary opacity-25"></div>

                    {/* Undo / Redo Controls */}
                    <div className="d-flex align-items-center gap-2 me-2">
                        <button
                            className={`btn btn-sm rounded-circle border-0 d-flex align-items-center justify-content-center ${
                                isDarkMode
                                    ? "bg-slate-800 text-white"
                                    : "bg-slate-200 text-dark"
                            }`}
                            onClick={undo}
                            disabled={history.length <= 1}
                            style={{ width: 32, height: 32 }}
                            title="Undo (Ctrl+Z)"
                        >
                            <i className="fas fa-undo"></i>
                        </button>
                        <button
                            className={`btn btn-sm rounded-circle border-0 d-flex align-items-center justify-content-center ${
                                isDarkMode
                                    ? "bg-slate-800 text-white"
                                    : "bg-slate-200 text-dark"
                            }`}
                            onClick={redo}
                            disabled={future.length === 0}
                            style={{ width: 32, height: 32 }}
                            title="Redo (Ctrl+Y)"
                        >
                            <i className="fas fa-redo"></i>
                        </button>
                    </div>

                    {/* Sidebar Toggle */}
                    <button
                        className={`btn btn-sm border rounded-pill px-3 d-flex align-items-center gap-2 ${
                            isSidebarOpen
                                ? "bg-slate-100 text-slate-700"
                                : "bg-slate-800 text-white"
                        }`}
                        onClick={() => setIsSidebarOpen(!isSidebarOpen)}
                        title={
                            isSidebarOpen
                                ? "Collapse Sidebar"
                                : "Expand Sidebar"
                        }
                    >
                        <i
                            className={`fas fa-${
                                isSidebarOpen ? "chevron-right" : "chevron-left"
                            }`}
                            style={{
                                color: isDarkMode ? "#fff" : "#000",
                            }}
                        ></i>
                    </button>

                    <button
                        className="btn btn-tech rounded-pill px-4 fw-medium d-flex align-items-center gap-2"
                        onClick={handleSave}
                        disabled={saving}
                    >
                        {saving ? (
                            <i className="fas fa-circle-notch fa-spin"></i>
                        ) : (
                            <i className="fas fa-save"></i>
                        )}
                        <span>Save</span>
                    </button>
                </div>
            </header>

            <div className="d-flex flex-grow-1 overflow-hidden position-relative">
                {/* Sidebar */}
                <aside
                    className={`d-flex flex-column ${theme.glassSide} border-end ${theme.border} overflow-auto`}
                    style={{ width: "280px" }}
                >
                    <div className="p-4">
                        <div
                            className={`text-xs fw-bold ${theme.highlight} text-uppercase ls-1 mb-3`}
                        >
                            <i className="fas fa-layer-group me-1"></i> Elements
                        </div>
                        <div className="d-grid gap-2">
                            <button
                                className="sidebar-btn text-start py-2 px-3 rounded-2 d-flex align-items-center gap-3"
                                onClick={() => addElement("text")}
                            >
                                <i className="fas fa-heading fa-fw opacity-50"></i>{" "}
                                <span>Headline / Text</span>
                            </button>
                            <button
                                className="sidebar-btn text-start py-2 px-3 rounded-2 d-flex align-items-center gap-3"
                                onClick={() => addElement("line")}
                            >
                                <i className="fas fa-minus fa-fw opacity-50"></i>{" "}
                                <span>Line Separator</span>
                            </button>
                            <div className="d-flex gap-2">
                                <button
                                    className="sidebar-btn flex-grow-1 py-3 rounded-2"
                                    onClick={() =>
                                        addElement("shape", {
                                            shapeType: "rectangle",
                                        })
                                    }
                                >
                                    <i className="far fa-square fa-lg"></i>
                                </button>
                                <button
                                    className="sidebar-btn flex-grow-1 py-3 rounded-2"
                                    onClick={() =>
                                        addElement("shape", {
                                            shapeType: "circle",
                                        })
                                    }
                                >
                                    <i className="far fa-circle fa-lg"></i>
                                </button>
                                <button
                                    className="sidebar-btn flex-grow-1 py-3 rounded-2"
                                    onClick={() => addElement("qrcode")}
                                >
                                    <i className="fas fa-qrcode fa-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* PAGE SETUP */}
                    <div className={`p-4 border-top ${theme.border}`}>
                        <div
                            className={`text-xs fw-bold ${theme.highlight} text-uppercase ls-1 mb-3`}
                        >
                            <i className="fas fa-file-alt me-1"></i> Page Setup
                        </div>
                        <div className="d-flex gap-2 mb-3">
                            <button
                                className={`btn btn-sm w-50 ${
                                    canvasSize.width > canvasSize.height
                                        ? "btn-tech"
                                        : isDarkMode
                                          ? "btn-outline-secondary"
                                          : "btn-outline-dark"
                                }`}
                                onClick={() =>
                                    setCanvasSize({ width: 1056, height: 816 })
                                }
                            >
                                <div className="d-flex flex-column align-items-center">
                                    <span>
                                        <i className="far fa-file-image me-1"></i>{" "}
                                        Landscape
                                    </span>
                                    <span
                                        className="mt-1"
                                        style={{
                                            fontSize: "0.65rem",
                                            opacity: 0.8,
                                        }}
                                    >
                                        1056 x 816 px
                                    </span>
                                </div>
                            </button>
                            <button
                                className={`btn btn-sm w-50 ${
                                    canvasSize.width < canvasSize.height
                                        ? "btn-tech"
                                        : isDarkMode
                                          ? "btn-outline-secondary"
                                          : "btn-outline-dark"
                                }`}
                                onClick={() =>
                                    setCanvasSize({ width: 816, height: 1056 })
                                }
                            >
                                <div className="d-flex flex-column align-items-center">
                                    <span>
                                        <i className="far fa-file me-1"></i>{" "}
                                        Portrait
                                    </span>
                                    <span
                                        className="mt-1"
                                        style={{
                                            fontSize: "0.65rem",
                                            opacity: 0.8,
                                        }}
                                    >
                                        816 x 1056 px
                                    </span>
                                </div>
                            </button>
                        </div>
                        <div className="d-flex align-items-center justify-content-between mb-2">
                            <span className="text-xs">Background Edit</span>
                            <div className="form-check form-switch">
                                <input
                                    className="form-check-input"
                                    type="checkbox"
                                    checked={isBgEditMode}
                                    onChange={() =>
                                        setIsBgEditMode(!isBgEditMode)
                                    }
                                />
                            </div>
                        </div>
                        {isBgEditMode && (
                            <div className="mb-2">
                                <label className="text-xs text-muted mb-1">
                                    Scale: {Math.round(bgConfig.scale * 100)}%
                                </label>
                                <input
                                    type="range"
                                    className="form-range"
                                    min="0.5"
                                    max="3"
                                    step="0.1"
                                    value={bgConfig.scale}
                                    onChange={(e) =>
                                        setBgConfig((prev) => ({
                                            ...prev,
                                            scale: parseFloat(e.target.value),
                                        }))
                                    }
                                />
                            </div>
                        )}
                    </div>

                    <div className={`p-4 border-top ${theme.border}`}>
                        <div
                            className={`text-xs fw-bold ${theme.highlight} text-uppercase ls-1 mb-3`}
                        >
                            <i className="fas fa-code-branch me-1"></i>{" "}
                            Variables
                        </div>
                        <div className="d-grid gap-2">
                            {[
                                "Participant Name",
                                "Event Title",
                                "Date",
                                "Certificate Code",
                            ].map((label, i) => (
                                <button
                                    key={label}
                                    className="sidebar-btn w-100 text-start py-2 px-3 rounded-2 small d-flex align-items-center"
                                    onClick={() =>
                                        addElement("text", {
                                            text: `{{ ${label
                                                .toLowerCase()
                                                .replace(" ", "_")} }}`,
                                        })
                                    }
                                >
                                    {/* Fixed Badge Layout */}
                                    <div
                                        className={`d-flex align-items-center justify-content-center ${
                                            isDarkMode
                                                ? "bg-slate-700 text-slate-300"
                                                : "bg-slate-200 text-dark"
                                        } me-3 rounded-1`}
                                        style={{
                                            minWidth: "40px",
                                            height: "22px",
                                            fontSize: "10px",
                                            fontWeight: "bold",
                                        }}
                                    >
                                        VAR
                                    </div>
                                    <span className="fw-medium">{label}</span>
                                </button>
                            ))}
                        </div>
                    </div>

                    <div
                        className={`p-4 border-top ${theme.border} flex-grow-1`}
                    >
                        <div className="mb-4">
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <div
                                    className={`text-xs fw-bold ${theme.highlight} text-uppercase ls-1`}
                                >
                                    <i className="fas fa-photo-film me-1"></i>{" "}
                                    Media Files
                                </div>
                            </div>

                            <button
                                className={`sidebar-btn w-100 mb-2 text-start py-2 px-3 rounded-2 small d-flex align-items-center`}
                                onClick={() => setShowMediaManager(true)}
                            >
                                <i className="fas fa-images fa-lg me-2"></i>
                                <span className="fw-medium">
                                    Manage Media Library
                                </span>
                            </button>

                            <div
                                className={`text-xs text-center ${theme.textMuted} mb-3`}
                            >
                                {mediaFiles.length} files available
                            </div>
                        </div>
                    </div>
                </aside>

                {/* Ruler & Canvas Area */}
                <div className="flex-grow-1 d-flex flex-column overflow-hidden position-relative">
                    {/* Top Ruler Row */}
                    <div
                        className="d-flex"
                        style={{
                            height: "24px",
                            flexShrink: 0,
                            zIndex: 50,
                            background: isDarkMode ? "#1e293b" : "#f1f5f9",
                        }}
                    >
                        <div
                            style={{
                                width: "24px",
                                flexShrink: 0,
                                borderRight: `1px solid ${
                                    isDarkMode ? "#334155" : "#cbd5e1"
                                }`,
                                borderBottom: `1px solid ${
                                    isDarkMode ? "#334155" : "#cbd5e1"
                                }`,
                            }}
                        />
                        <div className="flex-grow-1 position-relative overflow-hidden">
                            <Ruler
                                type="horizontal"
                                zoom={zoom}
                                offset={guides.rulerX || 0}
                                theme={{
                                    bg: isDarkMode ? "#1e293b" : "#f1f5f9",
                                    borderColor: isDarkMode
                                        ? "#334155"
                                        : "#cbd5e1",
                                    tickColor: isDarkMode
                                        ? "#94a3b8"
                                        : "#94a3b8",
                                    textColor: isDarkMode
                                        ? "#94a3b8"
                                        : "#64748b",
                                }}
                                onDragStart={handleRulerDragStart}
                            />
                        </div>
                    </div>

                    <div className="d-flex flex-grow-1 overflow-hidden position-relative">
                        {/* Left Ruler */}
                        <div
                            style={{
                                width: "24px",
                                flexShrink: 0,
                                zIndex: 50,
                                overflow: "hidden",
                                background: isDarkMode ? "#1e293b" : "#f1f5f9",
                            }}
                        >
                            <Ruler
                                type="vertical"
                                zoom={zoom}
                                offset={guides.rulerY || 0}
                                theme={{
                                    bg: isDarkMode ? "#1e293b" : "#f1f5f9",
                                    borderColor: isDarkMode
                                        ? "#334155"
                                        : "#cbd5e1",
                                    tickColor: isDarkMode
                                        ? "#94a3b8"
                                        : "#94a3b8",
                                    textColor: isDarkMode
                                        ? "#94a3b8"
                                        : "#64748b",
                                }}
                                onDragStart={handleRulerDragStart}
                            />
                        </div>

                        {/* Canvas Scroll Container */}
                        <main
                            className={`flex-grow-1 position-relative d-flex align-items-center justify-content-center overflow-hidden ${theme.grid}`}
                            id="canvas-scroll-container"
                            onClick={() => setSelectedIds(new Set())}
                            style={{ overflow: "auto" }} // Ensure scrolling usually happens here
                        >
                            {/* Snap Guides - visual only */}
                            {guides.x !== null && (
                                <div
                                    className="position-absolute h-100"
                                    style={{
                                        left: `${guides.x}%`,
                                        width: "1px",
                                        background: isDarkMode
                                            ? "#06b6d4"
                                            : "#3b82f6",
                                        boxShadow: "0 0 8px currentColor",
                                        zIndex: 100,
                                    }}
                                ></div>
                            )}
                            {guides.y !== null && (
                                <div
                                    className="position-absolute w-100"
                                    style={{
                                        top: `${guides.y}%`,
                                        height: "1px",
                                        background: isDarkMode
                                            ? "#06b6d4"
                                            : "#3b82f6",
                                        boxShadow: "0 0 8px currentColor",
                                        zIndex: 100,
                                    }}
                                ></div>
                            )}

                            {/* Draggable Guides (Ghost) */}
                            {draggingGuide && (
                                <div
                                    className="position-absolute"
                                    style={{
                                        zIndex: 101, // Above everything
                                        pointerEvents: "none",
                                        background: "#10b981", // Emerald for active guide
                                        ...(draggingGuide.type === "horizontal"
                                            ? {
                                                  left: 0,
                                                  right: 0,
                                                  top: 0, // Will use transform?
                                                  height: "1px",
                                                  transform: `translateY(${
                                                      guides.rulerY +
                                                      draggingGuide.currentPos *
                                                          zoom
                                                  }px)`,
                                              }
                                            : {
                                                  top: 0,
                                                  bottom: 0,
                                                  left: 0,
                                                  width: "1px",
                                                  transform: `translateX(${
                                                      guides.rulerX +
                                                      draggingGuide.currentPos *
                                                          zoom
                                                  }px)`,
                                              }),
                                    }}
                                >
                                    {/* Label for position */}
                                    <span
                                        className="position-absolute bg-success text-white px-1 text-xs"
                                        style={{ top: 2, left: 2 }}
                                    >
                                        {Math.round(draggingGuide.currentPos)}
                                    </span>
                                </div>
                            )}

                            <div
                                className="overflow-auto w-100 h-100 d-flex align-items-center justify-content-center p-5"
                                ref={containerRef}
                                onScroll={() => {
                                    if (
                                        canvasRef.current &&
                                        containerRef.current
                                    ) {
                                        const canvasRect =
                                            canvasRef.current.getBoundingClientRect();
                                        const containerRect =
                                            containerRef.current.getBoundingClientRect();
                                        setGuides((prev) => ({
                                            ...prev,
                                            rulerX:
                                                canvasRect.left -
                                                containerRect.left,
                                            rulerY:
                                                canvasRect.top -
                                                containerRect.top,
                                        }));
                                    }
                                }}
                            >
                                <div
                                    ref={canvasRef}
                                    className="position-relative bg-white shadow-sm transition-all duration-300"
                                    style={{
                                        width: `${canvasSize.width}px`,
                                        height: `${canvasSize.height}px`,
                                        minWidth: `${canvasSize.width}px`,
                                        minHeight: `${canvasSize.height}px`,
                                        transform: `scale(${zoom})`,
                                        transformOrigin: "center center",
                                        boxShadow: isDarkMode
                                            ? "0 0 50px rgba(0,0,0,0.5)"
                                            : "0 0 30px rgba(0,0,0,0.1)",
                                        overflow: "hidden",
                                    }}
                                    onDoubleClick={(e) => {
                                        if (
                                            e.target === canvasRef.current ||
                                            e.target.classList?.contains(
                                                "bg-layer",
                                            )
                                        ) {
                                            setIsBgEditMode(!isBgEditMode);
                                            setToast({
                                                message: !isBgEditMode
                                                    ? "Background Edit Mode: Drag to move"
                                                    : "Background Locked",
                                                type: "info",
                                            });
                                        }
                                    }}
                                >
                                    {/* Background Layer */}
                                    <div
                                        className={`bg-layer position-absolute w-100 h-100 start-0 top-0 ${
                                            isBgEditMode ? "cursor-move" : ""
                                        }`}
                                        style={{
                                            zIndex: 0,
                                            backgroundImage: bgUrl
                                                ? `url("/storage/${bgUrl}")`
                                                : "none",
                                            backgroundSize: "cover",
                                            backgroundPosition: "center",
                                            transform: `translate(${bgConfig.x}px, ${bgConfig.y}px) scale(${bgConfig.scale})`,
                                            transformOrigin: "center center",
                                            opacity: 1,
                                            pointerEvents: isBgEditMode
                                                ? "auto"
                                                : "none",
                                        }}
                                        onMouseDown={handleBgMouseDown}
                                    />

                                    {isBgEditMode && (
                                        <div className="position-absolute top-0 start-0 m-2 badge bg-warning text-dark z-index-10">
                                            Background Edit Mode
                                        </div>
                                    )}
                                    {/* Render Persistent Guides inside the Scaled Canvas */}
                                    {layoutGrid.enabled && (
                                        <div
                                            className="position-absolute top-0 start-0 w-100 h-100"
                                            style={{
                                                zIndex: 89,
                                                pointerEvents: "none", // Explicitly disable pointer events
                                                padding: `0 ${layoutGrid.margin}px`,
                                                display: "flex",
                                                gap: `${
                                                    layoutGrid.margin > 0
                                                        ? 0
                                                        : 0
                                                }px`, // Gap handled by grid/flex
                                            }}
                                        >
                                            {/* Columns Render */}
                                            <div
                                                className="w-100 h-100 d-flex"
                                                style={{
                                                    gap: `${layoutGrid.gutter}px`,
                                                    pointerEvents: "none",
                                                }}
                                            >
                                                {[
                                                    ...Array(
                                                        layoutGrid.columns,
                                                    ),
                                                ].map((_, i) => (
                                                    <div
                                                        key={i}
                                                        className="flex-grow-1 h-100"
                                                        style={{
                                                            backgroundColor:
                                                                layoutGrid.color,
                                                            opacity:
                                                                layoutGrid.opacity,
                                                            pointerEvents:
                                                                "none",
                                                        }}
                                                    />
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    {persistentGuides.map((g, i) => (
                                        <div
                                            key={i}
                                            className="position-absolute guide-line"
                                            onMouseDown={(e) =>
                                                handleGuideDragStart(
                                                    g.type,
                                                    e,
                                                    i,
                                                )
                                            }
                                            style={{
                                                zIndex: 90,
                                                pointerEvents: "auto",
                                                cursor:
                                                    g.type === "y"
                                                        ? "row-resize"
                                                        : "col-resize",
                                                // Create a larger hit area
                                                ...(g.type === "y"
                                                    ? {
                                                          left: 0,
                                                          right: 0,
                                                          top: `${g.pos - 2}px`, // Shift up by half hit area
                                                          height: "5px", // 5px hit area
                                                          borderTop:
                                                              "2px solid transparent",
                                                          borderBottom:
                                                              "2px solid transparent",
                                                          backgroundColor:
                                                              "transparent", // Background handled by inner or pseudo?
                                                          // Let's use a child for the visible line
                                                      }
                                                    : {
                                                          top: 0,
                                                          bottom: 0,
                                                          left: `${
                                                              g.pos - 2
                                                          }px`,
                                                          width: "5px",
                                                          borderLeft:
                                                              "2px solid transparent",
                                                          borderRight:
                                                              "2px solid transparent",
                                                          backgroundColor:
                                                              "transparent",
                                                      }),
                                            }}
                                        >
                                            {/* Visible Line */}
                                            <div
                                                style={{
                                                    width:
                                                        g.type === "y"
                                                            ? "100%"
                                                            : "1px",
                                                    height:
                                                        g.type === "y"
                                                            ? "1px"
                                                            : "100%",
                                                    backgroundColor: "#06b6d4",
                                                    position: "absolute",
                                                    top:
                                                        g.type === "y"
                                                            ? "2px"
                                                            : 0,
                                                    left:
                                                        g.type === "y"
                                                            ? 0
                                                            : "2px",
                                                    boxShadow:
                                                        "0 0 2px rgba(255,255,255,0.5)", // Contrast
                                                }}
                                            />
                                        </div>
                                    ))}
                                    {/* Elements Render */}
                                    {elements.map((el) => (
                                        <DraggableElement
                                            key={el.id}
                                            element={el}
                                            previewData={previewData}
                                            isSelected={selectedIds.has(el.id)}
                                            onMouseDown={(e) => {
                                                if (e.shiftKey) {
                                                    const newSet = new Set(
                                                        selectedIds,
                                                    );
                                                    if (newSet.has(el.id))
                                                        newSet.delete(el.id);
                                                    else newSet.add(el.id);
                                                    setSelectedIds(newSet);
                                                } else {
                                                    if (!selectedIds.has(el.id))
                                                        setSelectedIds(
                                                            new Set([el.id]),
                                                        );
                                                }
                                            }}
                                            onDrag={handleDrag}
                                            onResize={handleResize}
                                            onDragStop={() =>
                                                updateElements(
                                                    elementsRef.current,
                                                )
                                            }
                                            onDelete={deleteElement}
                                        />
                                    ))}
                                </div>
                            </div>
                        </main>
                    </div>
                </div>

                {/* Properties Panel */}
                <aside
                    className={`${theme.glassSide} border-start ${theme.border} d-flex flex-column shadow-lg z-20 transition-all duration-300 ease-in-out`}
                    style={{
                        width: isSidebarOpen ? "300px" : "0px",
                        overflow: "hidden",
                        opacity: isSidebarOpen ? 1 : 0,
                    }}
                >
                    {primaryElement && selectedIds.size > 0 ? (
                        <>
                            <div
                                className={`p-4 ${theme.bgPanel} border-bottom ${theme.border} text-center`}
                                style={{ minWidth: "300px" }} // Prevent content squishing during collapse
                            >
                                <div
                                    className={`text-xs fw-bold ${theme.highlight} text-uppercase ls-1`}
                                >
                                    Properties
                                </div>
                            </div>
                            <div
                                className="p-4 flex-grow-1 overflow-auto"
                                style={{ minWidth: "300px" }}
                            >
                                {(primaryElement.type === "text" ||
                                    primaryElement.type === "variable") && (
                                    <div className="vstack gap-5">
                                        {" "}
                                        {/* Increased Gap */}
                                        <div>
                                            <label
                                                className={`text-xs fw-bold ${theme.textMuted} text-uppercase mb-2`}
                                            >
                                                Content
                                            </label>
                                            <textarea
                                                className="form-control-proper w-100"
                                                rows="3"
                                                value={primaryElement.text}
                                                onChange={(e) =>
                                                    updateSelected({
                                                        text: e.target.value,
                                                    })
                                                }
                                            />
                                        </div>
                                        <div>
                                            <label
                                                className={`text-xs fw-bold ${theme.textMuted} text-uppercase mb-2`}
                                            >
                                                Typography
                                            </label>
                                            <div className="vstack gap-3">
                                                <select
                                                    className="form-select-proper w-100"
                                                    value={
                                                        primaryElement.fontFamily
                                                    }
                                                    onChange={(e) =>
                                                        updateSelected({
                                                            fontFamily:
                                                                e.target.value,
                                                        })
                                                    }
                                                >
                                                    {fonts.map((f) => (
                                                        <option
                                                            key={f.value}
                                                            value={f.value}
                                                        >
                                                            {f.label}
                                                        </option>
                                                    ))}
                                                </select>
                                                <div className="row g-2 align-items-center">
                                                    <div className="col-8">
                                                        <div className="input-group input-group-sm">
                                                            <input
                                                                type="number"
                                                                className="form-control-proper w-100"
                                                                value={
                                                                    primaryElement.fontSize
                                                                }
                                                                onChange={(e) =>
                                                                    updateSelected(
                                                                        {
                                                                            fontSize:
                                                                                parseInt(
                                                                                    e
                                                                                        .target
                                                                                        .value,
                                                                                ),
                                                                        },
                                                                    )
                                                                }
                                                                placeholder="Size"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div className="col-4">
                                                        <div
                                                            className="rounded overflow-hidden border border-secondary"
                                                            style={{
                                                                height: "31px",
                                                                width: "100%",
                                                                position:
                                                                    "relative",
                                                                backgroundColor:
                                                                    primaryElement.color, // Show color here
                                                            }}
                                                        >
                                                            <input
                                                                type="color"
                                                                className="position-absolute"
                                                                style={{
                                                                    top: "-10px",
                                                                    left: "-10px",
                                                                    width: "200%",
                                                                    height: "200%",
                                                                    cursor: "pointer",
                                                                    opacity: 0, // Hide input, show parent bg
                                                                }}
                                                                value={
                                                                    primaryElement.color
                                                                }
                                                                onChange={(e) =>
                                                                    updateSelected(
                                                                        {
                                                                            color: e
                                                                                .target
                                                                                .value,
                                                                        },
                                                                    )
                                                                }
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                className={`text-xs fw-bold ${theme.textMuted} text-uppercase mb-2`}
                                            >
                                                Style
                                            </label>
                                            <div className="btn-group w-100 mb-3">
                                                <button
                                                    className={`btn btn-sm btn-outline-secondary ${
                                                        isDarkMode
                                                            ? "text-white"
                                                            : ""
                                                    } ${
                                                        primaryElement.fontWeight ===
                                                        "bold"
                                                            ? isDarkMode
                                                                ? "btn-tech border-0"
                                                                : "bg-info border-info text-white"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        updateSelected({
                                                            fontWeight:
                                                                primaryElement.fontWeight ===
                                                                "bold"
                                                                    ? "normal"
                                                                    : "bold",
                                                        })
                                                    }
                                                >
                                                    <i className="fas fa-bold"></i>
                                                </button>
                                                <button
                                                    className={`btn btn-sm btn-outline-secondary ${
                                                        isDarkMode
                                                            ? "text-white"
                                                            : ""
                                                    } ${
                                                        primaryElement.fontStyle ===
                                                        "italic"
                                                            ? isDarkMode
                                                                ? "btn-tech border-0"
                                                                : "bg-info border-info text-white"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        updateSelected({
                                                            fontStyle:
                                                                primaryElement.fontStyle ===
                                                                "italic"
                                                                    ? "normal"
                                                                    : "italic",
                                                        })
                                                    }
                                                >
                                                    <i className="fas fa-italic"></i>
                                                </button>
                                                <button
                                                    className={`btn btn-sm btn-outline-secondary ${
                                                        isDarkMode
                                                            ? "text-white"
                                                            : ""
                                                    } ${
                                                        primaryElement.textDecoration ===
                                                        "underline"
                                                            ? isDarkMode
                                                                ? "btn-tech border-0"
                                                                : "bg-info border-info text-white"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        updateSelected({
                                                            textDecoration:
                                                                primaryElement.textDecoration ===
                                                                "underline"
                                                                    ? "none"
                                                                    : "underline",
                                                        })
                                                    }
                                                >
                                                    <i className="fas fa-underline"></i>
                                                </button>
                                            </div>
                                            <div className="btn-group w-100">
                                                <button
                                                    className={`btn btn-sm btn-outline-secondary ${
                                                        isDarkMode
                                                            ? "text-white"
                                                            : ""
                                                    } ${
                                                        primaryElement.textAlign ===
                                                        "left"
                                                            ? isDarkMode
                                                                ? "btn-tech border-0"
                                                                : "bg-info border-info text-white"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        updateSelected({
                                                            textAlign: "left",
                                                        })
                                                    }
                                                >
                                                    <i className="fas fa-align-left"></i>
                                                </button>
                                                <button
                                                    className={`btn btn-sm btn-outline-secondary ${
                                                        isDarkMode
                                                            ? "text-white"
                                                            : ""
                                                    } ${
                                                        primaryElement.textAlign ===
                                                        "center"
                                                            ? isDarkMode
                                                                ? "btn-tech border-0"
                                                                : "bg-info border-info text-white"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        updateSelected({
                                                            textAlign: "center",
                                                        })
                                                    }
                                                >
                                                    <i className="fas fa-align-center"></i>
                                                </button>
                                                <button
                                                    className={`btn btn-sm btn-outline-secondary ${
                                                        isDarkMode
                                                            ? "text-white"
                                                            : ""
                                                    } ${
                                                        primaryElement.textAlign ===
                                                        "right"
                                                            ? isDarkMode
                                                                ? "btn-tech border-0"
                                                                : "bg-info border-info text-white"
                                                            : ""
                                                    }`}
                                                    onClick={() =>
                                                        updateSelected({
                                                            textAlign: "right",
                                                        })
                                                    }
                                                >
                                                    <i className="fas fa-align-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {(primaryElement.type === "line" ||
                                    primaryElement.type === "shape") && (
                                    <div className="vstack gap-3">
                                        <div className="row g-2">
                                            <div className="col-6">
                                                <label
                                                    className={`text-xs fw-bold ${theme.textMuted}`}
                                                >
                                                    Width
                                                </label>
                                                <input
                                                    type="number"
                                                    className="form-control-proper w-100"
                                                    value={primaryElement.width}
                                                    onChange={(e) =>
                                                        updateSelected({
                                                            width: parseInt(
                                                                e.target.value,
                                                            ),
                                                        })
                                                    }
                                                />
                                            </div>
                                            <div className="col-6">
                                                <label
                                                    className={`text-xs fw-bold ${theme.textMuted}`}
                                                >
                                                    {primaryElement.type ===
                                                    "line"
                                                        ? "Thickness"
                                                        : "Height"}
                                                </label>
                                                <input
                                                    type="number"
                                                    className="form-control-proper w-100"
                                                    value={
                                                        primaryElement.height
                                                    }
                                                    onChange={(e) =>
                                                        updateSelected({
                                                            height: parseInt(
                                                                e.target.value,
                                                            ),
                                                        })
                                                    }
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                className={`text-xs fw-bold ${theme.textMuted} text-uppercase mb-1`}
                                            >
                                                Color
                                            </label>
                                            <div
                                                className="rounded overflow-hidden border border-secondary"
                                                style={{
                                                    height: "36px",
                                                    width: "100%",
                                                    position: "relative",
                                                }}
                                            >
                                                <input
                                                    type="color"
                                                    className="position-absolute"
                                                    style={{
                                                        top: "-10px",
                                                        left: "-10px",
                                                        width: "200%",
                                                        height: "200%",
                                                        cursor: "pointer",
                                                        border: "none",
                                                    }}
                                                    value={primaryElement.color}
                                                    onChange={(e) =>
                                                        updateSelected({
                                                            color: e.target
                                                                .value,
                                                        })
                                                    }
                                                />
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                            <div
                                className={`p-4 border-top ${theme.border} mt-auto ${theme.bgPanel}`}
                            >
                                <button
                                    className={`btn btn-outline-secondary w-100 mb-2 rounded-2 ${
                                        isDarkMode
                                            ? "text-white border-slate-600"
                                            : ""
                                    }`}
                                    onClick={duplicateSelected}
                                >
                                    <i className="far fa-clone me-2"></i>{" "}
                                    Duplicate
                                </button>
                                <button
                                    className="btn btn-outline-danger w-100 rounded-2"
                                    onClick={() => {
                                        setElements(
                                            elements.filter(
                                                (el) => !selectedIds.has(el.id),
                                            ),
                                        );
                                        setSelectedIds(new Set());
                                    }}
                                >
                                    <i className="far fa-trash-alt me-2"></i>{" "}
                                    Delete
                                </button>
                            </div>
                        </>
                    ) : (
                        <div
                            className={`d-flex flex-column align-items-center justify-content-center h-100 ${theme.textMuted} p-4 text-center`}
                        >
                            <i className="fas fa-mouse-pointer fa-3x mb-3 opacity-25"></i>
                            <div className="text-sm">
                                Select an element to resize or edit
                            </div>
                        </div>
                    )}
                </aside>
            </div>

            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type === "error" ? "danger" : toast?.type}
                onClose={() => setToast(null)}
            />
            <MediaManager
                show={showMediaManager}
                onClose={() => setShowMediaManager(false)}
                files={mediaFiles}
                onUpload={handleUploadMedia}
                onDelete={handleDeleteMedia}
                onRename={handleRenameMedia}
                onSelect={(files) => {
                    if (Array.isArray(files)) {
                        // Multi-select
                        addElements(
                            files.map((file) => ({
                                type: "image",
                                src: file.url,
                                name: file.name,
                                path: file.path, // Store path for reference
                            })),
                        );
                    } else {
                        // Single select (fallback)
                        addElements([
                            {
                                type: "image",
                                src: files.url,
                                name: files.name,
                                path: files.path,
                            },
                        ]);
                    }
                    setShowMediaManager(false);
                }}
                uploadUrl={route(
                    "events.certificates.upload-media",
                    certificate.id,
                )}
                onUploadSuccess={() => fetchMedia()}
            />
        </div>
    );
}
