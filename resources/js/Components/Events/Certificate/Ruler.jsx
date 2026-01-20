import React, { useRef, useEffect } from "react";

export default function Ruler({ type, zoom, offset = 0, theme, onDragStart }) {
    const canvasRef = useRef(null);

    // Default theme if not provided
    const colors = {
        bg: theme?.bg || "#f1f5f9",
        borderColor: theme?.borderColor || "#cbd5e1",
        tickColor: theme?.tickColor || "#94a3b8",
        textColor: theme?.textColor || "#64748b",
    };

    useEffect(() => {
        const canvas = canvasRef.current;
        if (!canvas) return;

        const ctx = canvas.getContext("2d");
        const dpr = window.devicePixelRatio || 1;

        // Logical size
        const width = canvas.offsetWidth;
        const height = canvas.offsetHeight;

        // Actual size
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        ctx.scale(dpr, dpr);

        // Clear
        ctx.fillStyle = colors.bg;
        ctx.fillRect(0, 0, width, height);

        // Border
        ctx.strokeStyle = colors.borderColor;
        ctx.lineWidth = 1;

        // Text settings
        ctx.fillStyle = colors.textColor;
        ctx.font = "10px sans-serif";

        ctx.strokeStyle = colors.tickColor;

        if (type === "horizontal") {
            ctx.textAlign = "left";
            ctx.textBaseline = "top";

            // Bottom border
            ctx.beginPath();
            ctx.moveTo(0, height);
            ctx.lineTo(width, height);
            ctx.stroke();

            const step = 50 * zoom;
            const startX = -(offset % step);

            for (let x = startX; x < width; x += 10 * zoom) {
                // Determine loop index to find if major or minor
                // abs position
                const absIdx = Math.round(
                    (x - startX + (offset % step) + offset) / (10 * zoom),
                );
                const isMajor = absIdx % 5 === 0;

                ctx.beginPath();
                ctx.moveTo(x, height);
                ctx.lineTo(x, height - (isMajor ? 12 : 6));
                ctx.stroke();

                if (isMajor) {
                    const val = Math.round(
                        (x - startX + offset) / zoom + (startX < 0 ? 0 : 0),
                    );
                    // Simple label
                    // Actually calculate exact value relative to offset
                    const exactVal = Math.round((x + offset) / zoom);
                    if (exactVal >= 0) {
                        ctx.fillText(exactVal.toString(), x + 2, 2);
                    }
                }
            }
        } else {
            // Vertical Ruler
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";

            // Right border
            ctx.beginPath();
            ctx.moveTo(width, 0);
            ctx.lineTo(width, height);
            ctx.stroke();

            const step = 50 * zoom;
            const startY = -(offset % step);

            for (let y = startY; y < height; y += 10 * zoom) {
                // Determine if major
                // Logic: calculate absolute Y value
                // The 'y' loop increments by 10*zoom.
                // We need to know if this specific 'y' corresponds to a multiple of 50.
                // current absolute pixel Y = y + offset
                // current logical unit Y = (y + offset) / zoom

                // Using modulo on the loop variable + remainder logic
                // x is relative to canvas 0.
                // AbsY = y + offset.
                const val = (y + offset) / zoom;
                const isMajor = Math.abs(val % 50) < 1; // approx check for float precision

                ctx.beginPath();
                ctx.moveTo(width, y);
                ctx.lineTo(width - (isMajor ? 12 : 6), y);
                ctx.stroke();

                if (isMajor) {
                    const labelVal = Math.round(val);
                    // Vertical text: Rotate -90 degrees?
                    // Or just standard text if it fits? 24px is narrow.
                    // 3 digits "100" fits in 24px? Sans-serif 10px is roughly 6px wide per char -> 18px. It fits.
                    // Let's try drawing horizontally centered in the column

                    ctx.save();
                    // Rotate -90 deg for standard vertical ruler look
                    ctx.translate(width - 14, y);
                    ctx.rotate(-Math.PI / 2);
                    ctx.fillText(labelVal.toString(), 0, 0);
                    ctx.restore();
                }
            }
        }
    }, [zoom, offset, type, colors]);

    return (
        <canvas
            ref={canvasRef}
            className={`ruler ${type}`}
            style={{
                height: type === "horizontal" ? "24px" : "100%",
                width: type === "vertical" ? "24px" : "100%",
                display: "block",
                cursor: type === "horizontal" ? "row-resize" : "col-resize",
            }}
            onMouseDown={(e) =>
                onDragStart && onDragStart(type === "horizontal" ? "y" : "x", e)
            }
        />
    );
}
