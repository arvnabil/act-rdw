import React from "react";

export default function ProductFilterSidebar({
    showFilters,
    filters, // { brand, category, solution }
    handleFilterChange, // (key, value) => void
    categories,
    brands,
    solutions,
}) {
    if (!showFilters) return null;

    return (
        <div className="col-lg-3 mb-4 mb-lg-0 wow fadeInUp">
            <style>{`
                .sidebar-active-item {
                    color: var(--theme-color) !important;
                }
                .sidebar-active-item:hover {
                    color: #ffffff !important;
                }
            `}</style>
            {/* Widget: Categories */}
            {categories && categories.length > 0 && (
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
                                        ? "active fw-bold sidebar-active-item"
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
                                        handleFilterChange("category", cat.id);
                                    }}
                                    className={
                                        filters.category == cat.id
                                            ? "active fw-bold sidebar-active-item"
                                            : ""
                                    }
                                >
                                    {cat.name}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            {/* Widget: Brands */}
            {brands && brands.length > 0 && (
                <div className="widget widget_categories th-radius shadow-sm p-4 border mb-4">
                    <h3 className="widget_title">Brands</h3>
                    <ul>
                        <li>
                            <a
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault();
                                    handleFilterChange("brand", "");
                                }}
                                className={
                                    !filters.brand
                                        ? "active fw-bold sidebar-active-item"
                                        : ""
                                }
                            >
                                All Brands
                            </a>
                        </li>
                        {brands.map((brand) => (
                            <li key={brand.id}>
                                <a
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleFilterChange("brand", brand.id);
                                    }}
                                    className={
                                        filters.brand == brand.id
                                            ? "active fw-bold sidebar-active-item"
                                            : ""
                                    }
                                >
                                    {brand.name}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            {/* Widget: Solutions */}
            {solutions && solutions.length > 0 && (
                <div className="widget widget_categories th-radius shadow-sm p-4 border">
                    <h3 className="widget_title">Solutions</h3>
                    <ul>
                        <li>
                            <a
                                href="#"
                                onClick={(e) => {
                                    e.preventDefault();
                                    handleFilterChange("solution", "");
                                }}
                                className={
                                    !filters.solution
                                        ? "active fw-bold sidebar-active-item"
                                        : ""
                                }
                            >
                                All Solutions
                            </a>
                        </li>
                        {solutions.map((sol) => (
                            <li key={sol.id}>
                                <a
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleFilterChange("solution", sol.id);
                                    }}
                                    className={
                                        filters.solution == sol.id
                                            ? "active fw-bold sidebar-active-item"
                                            : ""
                                    }
                                >
                                    {sol.title}
                                </a>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </div>
    );
}
