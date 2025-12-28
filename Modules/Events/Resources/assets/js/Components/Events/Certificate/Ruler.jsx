import React, { useMemo } from "react";

const Ruler = ({
    type = "horizontal",
    length = 3000,
    zoom = 1,
    theme,
    offset = 0,
    onDragStart,
}) => {
    const isHorizontal = type === "horizontal";
    const majorTick = 100;
    const minorTick = 10;

    // We need to render ticks that cover the visible area and some buffer.
    // For simplicity, we'll render a fixed large range for now, but shifted by offset.
    // Ideally, length should be the "viewport size" and we render ticks based on offset.

    // Let's stick to the previous simple approach but use transform for offset.
    // AND we need to render ticks starting from negative to cover the area before 0 if canvas is pushed right.

    const ticks = useMemo(() => {
        const items = [];
        // Calculate logical start/end based on 0 being at 'offset' pixels from left.
        // If offset is 100px, then 0 is at 100px.
        // We want to render ticks from -offset/zoom to (length - offset)/zoom basically.

        // Simplified: Render a huge range, from -2000 to +3000?
        // Or just renders 0 to Size and shift the wrapper?
        // If we shift wrapper by +offset, then 0 is at +offset.

        const limit = 3000;
        for (let i = 0; i <= limit; i += minorTick) {
            items.push({ pos: i, isMajor: i % majorTick === 0, label: i });
        }
        return items;
    }, []);

    const rulerStyle = isHorizontal
        ? {
              width: "100%",
              height: "24px",
              borderBottom: `1px solid ${theme?.borderColor || "#ccc"}`,
              backgroundColor: theme?.bg || "#f8f9fa",
              overflow: "hidden",
          }
        : {
              width: "24px",
              height: "100%",
              borderRight: `1px solid ${theme?.borderColor || "#ccc"}`,
              backgroundColor: theme?.bg || "#f8f9fa",
              overflow: "hidden",
          };

    // The inner container that moves
    const innerStyle = {
        position: "absolute",
        [isHorizontal ? "left" : "top"]: offset, // Shift the 0 point
        [isHorizontal ? "top" : "left"]: 0,
        transformOrigin: "0 0",
        // Note: We don't scale the ruler container itself because strokes would get thick.
        // We scale positions in the map.
    };

    // Draggable Guide Logic
    const handleMouseDown = (e) => {
        // Prevent default browser drag
        e.preventDefault();
        // Emit drag start
        if (onDragStart) {
            onDragStart(type, e);
        }
    };

    return (
        <div
            className={`ruler-${type} d-flex position-relative user-select-none`}
            style={rulerStyle}
            onMouseDown={handleMouseDown}
        >
            <div style={innerStyle}>
                {ticks.map(({ pos, isMajor, label }) => (
                    <div
                        key={pos}
                        className="position-absolute"
                        style={{
                            [isHorizontal ? "left" : "top"]: `${pos * zoom}px`,
                            [isHorizontal ? "top" : "left"]: 0,
                            width: isHorizontal
                                ? "1px"
                                : isMajor
                                ? "12px"
                                : "6px",
                            height: isHorizontal
                                ? isMajor
                                    ? "12px"
                                    : "6px"
                                : "1px",
                            backgroundColor: theme?.tickColor || "#94a3b8", // Increased contrast
                        }}
                    >
                        {isMajor && (
                            <span
                                className="position-absolute fs-10" // Force small size
                                style={{
                                    fontSize: "9px",
                                    color: theme?.textColor || "#64748b",
                                    fontWeight: "600",
                                    // Adjust label positioning
                                    [isHorizontal ? "left" : "top"]: "4px",
                                    [isHorizontal ? "top" : "left"]: "2px",
                                    transform: isHorizontal
                                        ? "none"
                                        : "rotate(-90deg) translateX(-100%)", // Rotate and push back
                                    transformOrigin: "top left",
                                    whiteSpace: "nowrap",
                                }}
                            >
                                {label}
                            </span>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
};

export default Ruler;
