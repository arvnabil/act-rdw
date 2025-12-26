import React, { useRef, useEffect } from "react";

export default function DraggableElement({
    element,
    isSelected,
    onSelect,
    onChange,
}) {
    const elementRef = useRef(null);
    const isDragging = useRef(false);
    const dragOffset = useRef({ x: 0, y: 0 });

    // We keep track of the listeners to remove them safely
    useEffect(() => {
        return () => {
            // Cleanup if component unmounts while dragging
            document.removeEventListener("mousemove", handleMouseMove);
            document.removeEventListener("mouseup", handleMouseUp);
        };
    }, []);

    const handleMouseMove = (e) => {
        if (!isDragging.current) return;

        e.preventDefault();

        const parent = elementRef.current.parentElement;
        const parentRect = parent.getBoundingClientRect();

        let x = e.clientX - parentRect.left - dragOffset.current.x;
        let y = e.clientY - parentRect.top - dragOffset.current.y;

        // Calculate percentage positions
        const xPercent = (x / parentRect.width) * 100;
        const yPercent = (y / parentRect.height) * 100;

        onChange(element.id, { x: xPercent, y: yPercent });
    };

    const handleMouseUp = () => {
        isDragging.current = false;
        document.removeEventListener("mousemove", handleMouseMove);
        document.removeEventListener("mouseup", handleMouseUp);
    };

    const handleMouseDown = (e) => {
        e.stopPropagation();
        onSelect(element.id);

        isDragging.current = true;
        const rect = elementRef.current.getBoundingClientRect();
        dragOffset.current = {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top,
        };

        document.addEventListener("mousemove", handleMouseMove);
        document.addEventListener("mouseup", handleMouseUp);
    };

    const handleClick = (e) => {
        e.stopPropagation(); // Critical: Prevent canvas onClick from deselecting
    };

    return (
        <div
            ref={elementRef}
            onMouseDown={handleMouseDown}
            onClick={handleClick}
            style={{
                position: "absolute",
                left: `${element.x}%`,
                top: `${element.y}%`,
                fontSize: `${element.fontSize || 14}px`,
                color: element.color || "#000000",
                fontWeight: element.fontWeight || "normal",
                fontFamily: element.fontFamily || "Arial",
                cursor: isDragging.current ? "grabbing" : "grab",
                border: isSelected
                    ? "1px dashed #3b82f6"
                    : "1px solid transparent",
                padding: "4px",
                userSelect: "none",
                whiteSpace: "nowrap",
                transform: "translate(0, 0)",
            }}
            className="hover:border-blue-300"
        >
            {element.text}
        </div>
    );
}
