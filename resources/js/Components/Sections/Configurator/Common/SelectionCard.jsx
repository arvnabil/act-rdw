import React from "react";

export default function SelectionCard({
    selected,
    onClick,
    icon,
    image,
    title,
    subtitle,
    children,
    className = "",
    themeColor = "#4AC15E",
    style = {},
}) {
    const activeStyle = { borderColor: themeColor, backgroundColor: "#ECFDF5" };
    const defaultStyle = { borderColor: "#E5E7EB", backgroundColor: "#fff" };

    return (
        <div
            className={`selection-card p-4 rounded-3 text-center h-100 transition-all ${className}`}
            style={{
                border: "2px solid",
                ...(selected ? activeStyle : defaultStyle),
                cursor: "pointer",
                ...style,
            }}
            onClick={onClick}
        >
            {/* Optional Check Badge */}
            {selected && (
                <div
                    className="position-absolute top-0 end-0 m-3"
                    style={{ color: themeColor }}
                >
                    <i className="fa-solid fa-circle-check fa-lg"></i>
                </div>
            )}

            {(icon || image) && (
                <div className="mb-3">
                    <img
                        src={icon || image}
                        alt={title}
                        style={{ height: "50px", objectFit: "contain" }}
                    />
                </div>
            )}

            {title && <h5 className="h6 mb-2">{title}</h5>}
            {subtitle && (
                <small className="text-muted d-block">{subtitle}</small>
            )}

            {children}
        </div>
    );
}
