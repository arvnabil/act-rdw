import React from "react";

export default function DashboardTable({ children, columns }) {
    return (
        <div className="table-responsive">
            <table className="table">
                <thead>
                    <tr>
                        {columns.map((col, idx) => (
                            <th key={idx} className={col.className}>
                                {col.label}
                            </th>
                        ))}
                    </tr>
                </thead>
                <tbody>{children}</tbody>
            </table>
        </div>
    );
}

export function DashboardTableRow({ children, style }) {
    return <tr style={style}>{children}</tr>;
}
