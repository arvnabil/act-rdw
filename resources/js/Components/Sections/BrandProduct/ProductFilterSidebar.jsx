import React from "react";

export default function ProductFilterSidebar({
    showFilters,
    filters,
    handleFilterChange,
    categories,
    serviceSolutions,
    serviceItemLabel,
}) {
    if (!showFilters) return null;

    return (
        <div className="col-lg-3 mb-4 mb-lg-0 wow fadeInUp">
            <div className="widget widget_categories th-radius shadow-sm p-4 border mb-4">
                <h3 className="widget_title">Categories</h3>
                <ul>
                    <li>
                        <a
                            href="#"
                            onClick={(e) => {
                                e.preventDefault();
                                handleFilterChange("category", "");
                            }}
                            className={
                                !filters.category
                                    ? "active fw-bold text-theme"
                                    : ""
                            }
                        >
                            All Categories
                        </a>
                    </li>
                    {categories.map((cat) => (
                        <li key={cat.id}>
                            <a
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault();
                                    handleFilterChange("category", cat.slug);
                                }}
                                className={
                                    filters.category === cat.slug
                                        ? "active fw-bold text-theme"
                                        : ""
                                }
                            >
                                {cat.name}{" "}
                                <span className="float-end text-muted">
                                    ({cat.count})
                                </span>
                            </a>
                        </li>
                    ))}
                </ul>
            </div>
            {serviceSolutions.length > 0 && (
                <div className="widget widget_categories th-radius shadow-sm p-4 border">
                    <h3 className="widget_title">{serviceItemLabel}</h3>
                    <ul>
                        <li>
                            <a
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault();
                                    handleFilterChange("service_item", "");
                                }}
                                className={
                                    !filters.service_item
                                        ? "active fw-bold text-theme"
                                        : ""
                                }
                            >
                                All {serviceItemLabel}
                            </a>
                        </li>
                        {serviceSolutions.map((sol) => (
                            <li key={sol.id}>
                                <a
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleFilterChange(
                                            "service_item",
                                            sol.slug
                                        );
                                    }}
                                    className={
                                        filters.service_item === sol.slug
                                            ? "active fw-bold text-theme"
                                            : ""
                                    }
                                >
                                    {sol.name}{" "}
                                    <span className="float-end text-muted">
                                        ({sol.count})
                                    </span>
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </div>
    );
}
