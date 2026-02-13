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
    const [search, setSearch] = React.useState(filters.search || "");

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        handleFilterChange("search", search);
    };

    return (
        <div className="product-toolbar-wrapper mb-4">
            <style>{`
                .product-toolbar-wrapper .th-sort-bar {
                    background: #ffffff;
                    padding: 10px 20px;
                    border-radius: 16px;
                    border: 1px solid #e2e8f0;
                    box-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
                }
                .search-form {
                    background: #f8fafc;
                    border: 1px solid #e2e8f0;
                    border-radius: 50px; /* Pill shape */
                    display: flex;
                    align-items: center;
                    position: relative; /* For absolute positioning of button */
                    width: 100%;
                    transition: all 0.3s ease;
                    padding: 4px; /* Space for button inside */
                }
                .search-form:focus-within {
                    border-color: #4ac15e;
                    background: #ffffff;
                    box-shadow: 0 0 0 4px rgba(74, 193, 94, 0.1);
                }
                .search-form input {
                    border: none !important;
                    background: transparent !important;
                    padding: 0 50px 0 20px !important; /* Space for button on right */
                    height: 42px !important;
                    font-size: 14px !important;
                    flex-grow: 1 !important;
                    outline: none !important;
                    border-radius: 50px !important;
                }
                .search-form button {
                    background: #4ac15e !important;
                    color: #fff !important;
                    width: 40px !important;
                    height: 40px !important;
                    border: none !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    font-size: 16px !important;
                    border-radius: 50% !important; /* Circle shape */
                    position: absolute !important;
                    right: 5px !important;
                    top: 50% !important;
                    transform: translateY(-50%) !important;
                    transition: all 0.3s ease !important;
                    box-shadow: 0 2px 6px rgba(74, 193, 94, 0.3) !important;
                }
                .search-form button:hover {
                    background: #3da850 !important;
                    transform: translateY(-50%) scale(1.05) !important;
                }
                .th-btn-filter {
                    height: 48px;
                    padding: 0 24px;
                    border-radius: 12px;
                    background: #4ac15e;
                    color: white;
                    font-weight: 600;
                    font-size: 14px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    border: none;
                    white-space: nowrap;
                    transition: all 0.2s ease;
                    box-shadow: 0 4px 10px rgba(74, 193, 94, 0.2);
                    width: 100%; /* Default width (mobile) */
                }
                .th-btn-filter:hover {
                    background: #3da850;
                    transform: translateY(-1px);
                    box-shadow: 0 6px 14px rgba(74, 193, 94, 0.3);
                }
                .filter-chip { transition: all 0.2s; }
                .filter-chip:hover { border-color: #dee2e6 !important; background-color: #f8f9fa !important; }
                
                .orderby-select {
                    height: 48px !important;
                    border-radius: 12px !important;
                    border: 1px solid #e2e8f0 !important;
                    padding: 0 40px 0 20px !important;
                    font-size: 14px !important;
                    font-weight: 500 !important;
                    color: #334155 !important;
                    background: #ffffff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E") no-repeat right 16px center !important;
                    background-size: 16px !important;
                    appearance: none !important;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                }
                .orderby-select:hover {
                    border-color: #cbd5e1;
                    background-color: #f8fafc !important;
                }
                
                /* Mobile Layout Improvements */
                @media (max-width: 767px) {
                    .product-toolbar-wrapper .th-sort-bar { 
                        padding: 16px; 
                        border-radius: 16px; 
                    }
                    .search-form-area { 
                        margin-bottom: 12px; 
                    }
                    /* Side by side layout for Filter and Sort on mobile */
                    .sorting-filter-wrap {
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        gap: 12px;
                    }
                    /* Filter Button Wrapper */
                    .filter-btn-wrapper {
                        width: 50%;
                    }
                    .th-btn-filter {
                        width: 100%;
                        border-radius: 50px;
                        font-size: 13px;
                        padding: 0 15px;
                    }
                    /* Sort Wrapper */
                    .sort-wrapper {
                        width: 50%;
                    }
                    .woocommerce-ordering {
                        width: 100%;
                    }
                    .orderby-select {
                        width: 100%;
                        border-radius: 50px;
                        background-position: right 12px center !important;
                        padding-right: 30px !important;
                        padding-left: 15px !important;
                        font-size: 13px;
                    }
                }
                
                /* Desktop Layout */
                @media (min-width: 768px) {
                    .search-form-area { width: 350px; }
                    .col-md-auto { padding: 0 10px; }
                    .th-btn-filter { width: auto; }
                    .filter-btn-wrapper { width: auto; }
                    .sort-wrapper { width: auto; }
                }
            `}</style>

            {/* Top Bar Area */}
            <div className="th-sort-bar mb-4">
                <div className="row justify-content-between align-items-center gy-3">
                    {/* Left: Search */}
                    <div className="col-12 col-md-auto">
                        <div className="search-form-area">
                            <form
                                className="search-form"
                                onSubmit={handleSearchSubmit}
                            >
                                <input
                                    type="text"
                                    placeholder="Search products..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                                <button type="submit">
                                    <i className="fa-light fa-magnifying-glass"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    {/* Right: Actions */}
                    <div className="col-12 col-md-auto">
                        <div className="sorting-filter-wrap">
                            {/* Filter Button */}
                            <div className="filter-btn-wrapper">
                                <button
                                    onClick={() => setShowFilters(!showFilters)}
                                    className={`th-btn-filter ${showFilters ? "active" : ""}`}
                                >
                                    <i className="fa-regular fa-sliders"></i>
                                    <span>All Filters</span>
                                </button>
                            </div>

                            {/* Sort Dropdown */}
                            <div className="sort-wrapper">
                                <form
                                    className="woocommerce-ordering w-100"
                                    method="get"
                                    onSubmit={(e) => e.preventDefault()}
                                >
                                    <select
                                        name="orderby"
                                        className="orderby-select w-100"
                                        aria-label="Shop order"
                                        value={filters.sort || "newest"}
                                        onChange={(e) =>
                                            handleFilterChange("sort", e.target.value)
                                        }
                                    >
                                        <option value="newest">Sort By: Newest</option>
                                        <option value="price_asc">Price: Low to High</option>
                                        <option value="price_desc">Price: High to Low</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
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
                                        (c) => c.slug === filters.category,
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
                                        (s) => s.slug === filters.service_item,
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

                        {/* Search Chip */}
                        {filters.search && (
                            <span className="filter-chip bg-white border rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm">
                                <span className="fw-bold text-uppercase fs-xs me-2">
                                    Search: {filters.search}
                                </span>
                                <button
                                    onClick={() => {
                                        setSearch("");
                                        handleFilterChange("search", "");
                                    }}
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
                                    { preserveScroll: true },
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
