import React, { useRef, useEffect, useState } from "react";

export default function DraggableElement({
    element,
    isSelected,
    onMouseDown,
    onDrag,
    onResize,
    onDragStop,
    onDelete,
    previewData = {},
}) {
    const nodeRef = useRef(null);
    const [isDragging, setIsDragging] = useState(false);
    const [isResizing, setIsResizing] = useState(false);
    const [dragStart, setDragStart] = useState({ x: 0, y: 0 });
    const [resizeHandle, setResizeHandle] = useState(null);

    // --- Drag Logic ---
    useEffect(() => {
        const handleMouseMove = (e) => {
            if (isDragging) {
                const deltaX = e.clientX - dragStart.x;
                const deltaY = e.clientY - dragStart.y;
                if (onDrag) onDrag(deltaX, deltaY);
                setDragStart({ x: e.clientX, y: e.clientY });
            }
            if (isResizing && resizeHandle) {
                const deltaX = e.clientX - dragStart.x;
                const deltaY = e.clientY - dragStart.y;
                if (onResize)
                    onResize(element.id, deltaX, deltaY, resizeHandle);
                setDragStart({ x: e.clientX, y: e.clientY });
            }
        };

        const handleMouseUp = () => {
            if (isDragging || isResizing) {
                setIsDragging(false);
                setIsResizing(false);
                setResizeHandle(null);
                if (onDragStop) onDragStop();
            }
        };

        if (isDragging || isResizing) {
            window.addEventListener("mousemove", handleMouseMove);
            window.addEventListener("mouseup", handleMouseUp);
        }

        return () => {
            window.removeEventListener("mousemove", handleMouseMove);
            window.removeEventListener("mouseup", handleMouseUp);
        };
    }, [
        isDragging,
        isResizing,
        dragStart,
        resizeHandle,
        onDrag,
        onResize,
        onDragStop,
        element.id,
    ]);

    const handleElementMouseDown = (e) => {
        if (isResizing) return;
        e.stopPropagation();
        if (onMouseDown) onMouseDown(e); // Select
        setIsDragging(true);
        setDragStart({ x: e.clientX, y: e.clientY });
    };

    const handleResizeMouseDown = (e, handle) => {
        e.stopPropagation();
        setIsResizing(true);
        setResizeHandle(handle);
        setDragStart({ x: e.clientX, y: e.clientY });
    };

    // --- Render Content ---
    const renderContent = () => {
        const commonStyle = {
            width: "100%",
            height: "100%",
            display: "block",
            pointerEvents: "none",
        };

        switch (element.type) {
            case "image":
                return (
                    <img
                        src={element.src || element.url}
                        alt="Element"
                        style={{ ...commonStyle, objectFit: "contain" }}
                        draggable={false}
                    />
                );
            case "qrcode":
                return (
                    <div
                        style={{
                            ...commonStyle,
                            backgroundColor: "#f0f0f0",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            border: "1px dashed #ccc",
                        }}
                    >
                        <i className="fas fa-qrcode text-4xl text-gray-400"></i>
                    </div>
                );
            case "text":
            case "variable":
                return (
                    <div
                        style={{
                            ...commonStyle,
                            fontFamily: element.fontFamily,
                            fontSize: `${element.fontSize}px`,
                            fontWeight: element.fontWeight,
                            color: element.color,
                            textAlign: element.textAlign,
                            lineHeight: 1.2,
                            whiteSpace: "pre-wrap",
                        }}
                    >
                        {element.type === "variable"
                            ? previewData[element.text] || `{${element.text}}`
                            : element.text}
                    </div>
                );
            case "shape":
                return (
                    <div
                        style={{
                            ...commonStyle,
                            backgroundColor: element.color,
                            borderRadius:
                                element.shapeType === "circle" ? "50%" : "0",
                        }}
                    />
                );
            default:
                return null;
        }
    };

    return (
        <div
            ref={nodeRef}
            className={`position-absolute group ${isSelected ? "ring-2 ring-purple-500 ring-offset-2 z-50" : "hover:ring-1 hover:ring-purple-300 z-10"}`}
            style={{
                left: `${element.x}%`,
                top: `${element.y}%`,
                width: element.width
                    ? typeof element.width === "number"
                        ? `${element.width}px`
                        : element.width
                    : "auto",
                height: element.height
                    ? typeof element.height === "number"
                        ? `${element.height}px`
                        : element.height
                    : "auto",
                minWidth: element.type === "image" ? "50px" : "auto",
                minHeight: element.type === "image" ? "50px" : "auto",
                touchAction: "none",
                cursor: isDragging ? "grabbing" : "grab",
                transform: "translate(-50%, -50%)",
            }}
            onMouseDown={handleElementMouseDown}
        >
            {/* Content */}
            {renderContent()}

            {/* Controls (Only if selected) */}
            {isSelected && (
                <>
                    {/* Delete Button */}
                    <button
                        className="absolute -top-3 -right-3 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-600 hover:scale-110 transition-all z-50 cursor-pointer"
                        onMouseDown={(e) => {
                            e.stopPropagation();
                        }}
                        onClick={(e) => {
                            e.stopPropagation();
                            if (onDelete) onDelete(element.id);
                        }}
                        title="Delete Element"
                    >
                        <i className="fas fa-times text-xs"></i>
                    </button>

                    {/* Resize Handles */}
                    {["nw", "ne", "sw", "se"].map((handle) => (
                        <div
                            key={handle}
                            className={`absolute w-3 h-3 bg-white border-2 border-purple-500 rounded-full z-40
                                ${handle === "nw" ? "-top-1.5 -left-1.5 cursor-nw-resize" : ""}
                                ${handle === "ne" ? "-top-1.5 -right-1.5 cursor-ne-resize" : ""}
                                ${handle === "sw" ? "-bottom-1.5 -left-1.5 cursor-sw-resize" : ""}
                                ${handle === "se" ? "-bottom-1.5 -right-1.5 cursor-se-resize" : ""}
                            `}
                            onMouseDown={(e) =>
                                handleResizeMouseDown(e, handle)
                            }
                        />
                    ))}
                </>
            )}
        </div>
    );
}
