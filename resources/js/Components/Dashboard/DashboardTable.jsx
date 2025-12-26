import React from "react";

export default function DashboardTable({ columns = [], children }) {
    return (
        <div
            className="dashboard-table-container"
            style={{
                border: "1px solid #eee",
                borderRadius: "20px",
                overflowX: "auto", // Enable horizontal scrolling
            }}
        >
            {/* Scrollable Content Wrapper */}
            <div style={{ minWidth: "900px" }}>
                {/* Header */}
                {columns.length > 0 && (
                    <div className="row g-0 bg-light p-3 fw-bold small text-muted text-uppercase border-bottom">
                        {columns.map((col, index) => (
                            <div
                                key={index}
                                className={`${col.className || "col"}`}
                            >
                                {col.label}
                            </div>
                        ))}
                    </div>
                )}

                {/* Body */}
                <div className="dashboard-table-body">{children}</div>
            </div>
        </div>
    );
}

export function DashboardTableRow({ children, className = "", style = {} }) {
    return (
        <div
            className={`row g-0 p-3 align-items-center border-bottom transition-all hover-bg-light ${className}`}
            style={{
                backgroundColor: "#fff", // Default white, can be overridden
                ...style,
            }}
        >
            {children}
        </div>
    );
}
