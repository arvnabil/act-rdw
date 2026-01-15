import React from "react";
import { router } from "@inertiajs/react";

export default function ProductFilterTopBar({
    showFilters,
    setShowFilters,
    filters,
    handleFilterChange,
    categories,
    serviceSolutions,
    products,
    brand,
}) {
    return (
        <div className="controls-area mb-5">
            {/* Row 1: Buttons */}
            <div className="d-flex justify-content-between align-items-center mb-3">
                <button
                    onClick={() => setShowFilters(!showFilters)}
                    className="th-btn style1 th-radius th-icon text-capitalize"
                    style={{ padding: "12px 30px" }}
                >
                    <i className="fa-regular fa-filter me-2"></i> All Filters
                </button>

                <div className="sort-dropdown" style={{ minWidth: "200px" }}>
                    <select
                        className="form-select th-radius bg-white border shadow-sm"
                        style={{
                            cursor: "pointer",
                            padding: "12px 20px",
                            backgroundImage: `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e")`, // Custom chevron
                            backgroundPosition: "right 1rem center",
                            backgroundSize: "16px 12px",
                        }}
                        value={filters.sort || "newest"}
                        onChange={(e) =>
                            handleFilterChange("sort", e.target.value)
                        }
                    >
                        <option value="newest">Sort By: Newest</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                </div>
            </div>

            {/* Row 2: Active Filters Chips */}
            <div className="active-filters d-flex align-items-center flex-wrap gap-2 mb-3">
                {(filters.category || filters.service_item) && (
                    <>
                        {/* Category Chip */}
                        {filters.category && (
                            <span className="filter-chip bg-white border rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm">
                                <span className="fw-bold text-uppercase fs-xs me-2">
                                    {categories.find(
                                        (c) => c.slug === filters.category
                                    )?.name || filters.category}
                                </span>
                                <button
                                    onClick={() =>
                                        handleFilterChange("category", "")
                                    }
                                    className="bg-transparent border-0 p-0 text-muted hover-red"
                                >
                                    <i className="fa-regular fa-times"></i>
                                </button>
                            </span>
                        )}

                        {/* Service Item Chip */}
                        {filters.service_item && (
                            <span className="filter-chip bg-white border rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm">
                                <span className="fw-bold text-uppercase fs-xs me-2">
                                    {serviceSolutions.find(
                                        (s) => s.slug === filters.service_item
                                    )?.name || filters.service_item}
                                </span>
                                <button
                                    onClick={() =>
                                        handleFilterChange("service_item", "")
                                    }
                                    className="bg-transparent border-0 p-0 text-muted hover-red"
                                >
                                    <i className="fa-regular fa-times"></i>
                                </button>
                            </span>
                        )}

                        {/* Clear All */}
                        <button
                            onClick={() => {
                                router.get(
                                    route("brand.products", {
                                        brandSlug: brand.slug,
                                    }),
                                    {},
                                    { preserveScroll: true }
                                );
                            }}
                            className="btn-clear bg-light border-0 rounded-pill px-3 py-2 fw-bold text-uppercase fs-xs text-muted hover-dark"
                        >
                            Hapus Semua
                        </button>
                    </>
                )}
            </div>

            {/* Row 3: Result Count */}
            <div className="result-count">
                <span className="text-muted">
                    Menampilkan {products.data.length} dari {products.total}{" "}
                    produk
                </span>
            </div>
        </div>
    );
}
