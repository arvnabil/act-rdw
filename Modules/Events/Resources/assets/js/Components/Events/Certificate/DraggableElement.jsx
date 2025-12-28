import React, { useRef, useEffect } from "react";

export default function DraggableElement({
    element,
    isSelected,
    previewData,
    onMouseDown,
    onDrag,
    onResize, // New prop for resizing
    onDragStop, // New prop for history
    onDelete,
}) {
    const elementRef = useRef(null);
    const isDragging = useRef(false);
    const isResizing = useRef(false);
    const lastPos = useRef({ x: 0, y: 0 });
    const resizeHandle = useRef(null); // 'se', 'sw', 'ne', 'nw'

    useEffect(() => {
        return () => {
            document.removeEventListener("mousemove", handleMouseMove);
            document.removeEventListener("mouseup", handleMouseUp);
        };
    }, []);

    const handleMouseMove = (e) => {
        e.preventDefault();
        const currentX = e.clientX;
        const currentY = e.clientY;
        const deltaX = currentX - lastPos.current.x;
        const deltaY = currentY - lastPos.current.y;

        if (isResizing.current) {
            onResize(element.id, deltaX, deltaY, resizeHandle.current);
            lastPos.current = { x: currentX, y: currentY };
        } else if (isDragging.current) {
            onDrag(deltaX, deltaY);
            lastPos.current = { x: currentX, y: currentY };
        }
    };

    const handleMouseUp = () => {
        if (isDragging.current || isResizing.current) {
            if (onDragStop) onDragStop(element.id);
        }
        isDragging.current = false;
        isResizing.current = false;
        resizeHandle.current = null;
        document.removeEventListener("mousemove", handleMouseMove);
        document.removeEventListener("mouseup", handleMouseUp);
    };

    const handleStart = (e) => {
        e.stopPropagation();
        // If clicking a handle, don't start move
        if (e.target.dataset.handle) return;

        onMouseDown(e, element.id); // Select

        isDragging.current = true;
        isResizing.current = false;
        lastPos.current = { x: e.clientX, y: e.clientY };

        document.addEventListener("mousemove", handleMouseMove);
        document.addEventListener("mouseup", handleMouseUp);
    };

    const handleResizeStart = (e, handle) => {
        e.stopPropagation();
        e.preventDefault();

        isResizing.current = true;
        isDragging.current = false;
        resizeHandle.current = handle;
        lastPos.current = { x: e.clientX, y: e.clientY };

        document.addEventListener("mousemove", handleMouseMove);
        document.addEventListener("mouseup", handleMouseUp);
    };

    const handleDelete = (e) => {
        e.stopPropagation();
        e.preventDefault();
        onDelete(element.id);
    };

    // Calculate dimensions for inline display if needed (mostly handled by parent props)
    // But we need to apply width/height to the styles

    const commonStyle = {
        position: "absolute",
        left: `${element.x}%`,
        top: `${element.y}%`,
        cursor: isDragging.current ? "grabbing" : "grab",
        border: isSelected ? "1px solid #06b6d4" : "1px solid transparent", // Thinner, sharper border
        boxShadow: isSelected ? "0 0 0 1px rgba(6, 182, 212, 1)" : "none", // Sharp outline
        userSelect: "none",
        transform: "translate(0, 0)",
        zIndex: isSelected ? 50 : 10,
        // No transition during interaction
        transition:
            isDragging.current || isResizing.current
                ? "none"
                : "border 0.2s, box-shadow 0.2s",
    };

    const renderHandles = () => {
        if (!isSelected) return null;

        // Allow resizing for all standard types
        const isResizable = [
            "shape",
            "image",
            "signature",
            "qrcode",
            "text",
            "variable",
        ].includes(element.type);
        if (!isResizable) return null;

        const baseHandleStyle = {
            width: "10px",
            height: "10px",
            background: "#fff",
            border: "1px solid #7c3aed", // Purple like image
            borderRadius: "50%",
            position: "absolute",
            zIndex: 51,
        };

        const handles = [
            {
                id: "se",
                cursor: "nwse-resize",
                style: { bottom: "-5px", right: "-5px" },
            },
            {
                id: "sw",
                cursor: "nesw-resize",
                style: { bottom: "-5px", left: "-5px" },
            },
            {
                id: "ne",
                cursor: "nesw-resize",
                style: { top: "-5px", right: "-5px" },
            },
            {
                id: "nw",
                cursor: "nwse-resize",
                style: { top: "-5px", left: "-5px" },
            },
            {
                id: "e",
                cursor: "ew-resize",
                style: {
                    top: "50%",
                    right: "-4px",
                    transform: "translateY(-50%)",
                    height: "16px",
                    width: "6px",
                    borderRadius: "4px",
                    backgroundColor: "#7c3aed", // Solid color typical for side handles or white with border? Image looks filled/border. Let's do white w/ border but pill shape.
                    background: "#fff",
                    border: "1px solid #7c3aed",
                },
            },
            {
                id: "w",
                cursor: "ew-resize",
                style: {
                    top: "50%",
                    left: "-4px",
                    transform: "translateY(-50%)",
                    height: "16px",
                    width: "6px",
                    borderRadius: "4px",
                    background: "#fff",
                    border: "1px solid #7c3aed",
                },
            },
        ];

        return (
            <>
                {handles.map((h) => (
                    <div
                        key={h.id}
                        data-handle={h.id}
                        onMouseDown={(e) => handleResizeStart(e, h.id)}
                        style={{
                            ...baseHandleStyle,
                            ...h.style,
                            cursor: h.cursor,
                        }}
                    ></div>
                ))}
            </>
        );
    };

    const renderContent = () => {
        const style = { width: "100%", height: "100%" };

        if (element.type === "qrcode") {
            return (
                <div
                    style={{
                        ...style,
                        width: element.width,
                        height: element.width,
                        background: "#fff",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        fontSize: "9px",
                        color: "#000",
                        textAlign: "center",
                        padding: "5px",
                        borderRadius: "8px",
                    }}
                >
                    <div className="d-flex flex-column align-items-center justify-content-center h-100 p-1 bg-white border">
                        <i className="fas fa-qrcode fa-3x mb-1"></i>
                        <div
                            style={{
                                fontSize: "0.6em",
                                textTransform: "uppercase",
                                fontWeight: "bold",
                            }}
                        >
                            SCAN ME
                        </div>
                    </div>
                </div>
            );
        }
        if (element.type === "shape") {
            return (
                <div
                    style={{
                        ...style,
                        width: element.width,
                        height: element.height,
                        backgroundColor: element.color || "#000000",
                        borderRadius:
                            element.shapeType === "circle" ? "50%" : "4px",
                    }}
                ></div>
            );
        }
        if (element.type === "line") {
            return (
                <div
                    style={{
                        width: element.width,
                        height: element.height,
                        backgroundColor: element.color || "#000000",
                    }}
                ></div>
            );
        }
        if (element.type === "image" || element.type === "signature") {
            return (
                <div style={{ width: element.width }}>
                    <img
                        src={element.src}
                        style={{ width: "100%", pointerEvents: "none" }}
                    />
                </div>
            );
        }

        // Text Processing with Preview
        let textToDisplay = element.text;
        if (
            previewData &&
            !isSelected &&
            (element.type === "text" || element.type === "variable")
        ) {
            Object.keys(previewData).forEach((key) => {
                const regex = new RegExp(`{{\\s*${key}\\s*}}`, "gi");
                textToDisplay = textToDisplay.replace(regex, previewData[key]);
            });
        }

        const isTextFixed = element.width && element.width > 0;

        return (
            <div
                style={{
                    fontSize: `${element.fontSize || 14}px`,
                    color: element.color || "#000000",
                    fontWeight: element.fontWeight || "normal",
                    fontFamily: element.fontFamily || "Arial",
                    fontStyle: element.fontStyle || "normal",
                    textDecoration: element.textDecoration || "none",
                    textAlign: element.textAlign || "left",
                    // Width Logic
                    width: isTextFixed ? `${element.width}px` : "auto",
                    whiteSpace: isTextFixed ? "normal" : "nowrap",
                    wordWrap: isTextFixed ? "break-word" : "normal",

                    padding: "4px 8px",
                    lineHeight: 1.2,
                    border:
                        isSelected && isTextFixed
                            ? "1px dashed #cbd5e1"
                            : "none", // visual aid for text box
                }}
            >
                {textToDisplay}
            </div>
        );
    };

    return (
        <div
            ref={elementRef}
            onMouseDown={handleStart}
            onClick={(e) => e.stopPropagation()}
            style={commonStyle}
            className="group draggable-item"
        >
            {/* Delete Button (Canva style: floating above) */}
            {isSelected && (
                <div
                    onClick={handleDelete}
                    className="position-absolute rounded-circle bg-danger text-white shadow-sm border"
                    style={{
                        width: "15px",
                        height: "15px",
                        top: "-18px",
                        right: "-16px",
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        fontSize: "10px",
                        cursor: "pointer",
                        zIndex: 60,
                        padding: "0",
                        margin: "0",
                    }}
                    title="Delete"
                >
                    <i className="fas fa-times"></i>
                </div>
            )}

            {renderContent()}
            {renderHandles()}
        </div>
    );
}
