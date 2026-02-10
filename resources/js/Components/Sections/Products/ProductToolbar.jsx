import React from "react";

export default function ProductToolbar({
    search,
    setSearch,
    handleSearch,
    viewMode,
    setViewMode,
    orderby,
    handleSortChange,
    showFilters,
    setShowFilters,
    // Data for lookups
    brands,
    solutions,
    categories,
    // Selected values
    selectedBrand,
    selectedSolution,
    selectedCategory,
    // Handlers
    handleRemoveFilter,
    handleResetFilters,
}) {
    // Helper to get name
    const getBrandName = (id) => brands?.find((b) => b.id == id)?.name || id;
    const getSolutionName = (id) =>
        solutions?.find((s) => s.id == id)?.title || id;
    const getCategoryName = (id) =>
        categories?.find((c) => c.id == id)?.name || id;

    const hasActiveFilters =
        selectedBrand || selectedSolution || selectedCategory;

    return (
        <div className="product-toolbar-wrapper mb-5">
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
                .view-mode-nav {
                    display: flex !important;
                    flex-direction: row !important; /* Force Horizontal */
                    flex-wrap: nowrap !important; /* Prevent Bootstrap .nav wrapping */
                    align-items: center !important;
                    gap: 8px;
                    background: #f8fafc;
                    padding: 4px;
                    border-radius: 12px;
                    border: 1px solid #e2e8f0;
                    width: auto !important; /* Allow it to take necessary width */
                    min-width: fit-content;
                }
                .view-mode-nav .nav-link {
                    width: 40px;
                    height: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 8px;
                    color: #94a3b8;
                    padding: 0 !important;
                    transition: all 0.2s ease;
                    border: none;
                    margin: 0 !important;
                    flex-shrink: 0 !important; /* Prevent shrinking */
                }
                .view-mode-nav .nav-link.active {
                    color: #ffffff;
                    background: #4ac15e;
                    box-shadow: 0 2px 5px rgba(74, 193, 94, 0.3);
                }
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
                        font-size: 13px; /* Slightly smaller text for 2-col */
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
                        border-radius: 50px; /* Consistent Pill Shape */
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
                    /* Ensure icons are explicitly horizontal in desktop */
                    .view-mode-nav {
                         flex-direction: row !important;
                         display: flex !important;
                    }
                }
            `}</style>
            {/* Top Bar Area */}
            <div className="th-sort-bar">
                <div className="row justify-content-between align-items-center gy-3">
                    {/* Left: Search */}
                    <div className="col-12 col-md-auto">
                        <div className="search-form-area">
                            <form
                                className="search-form"
                                onSubmit={handleSearch}
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

                            {/* Actions Group (View Mode + Sort) */}
                            <div className="sort-wrapper d-flex align-items-center gap-3">
                                {/* View Mode - Hidden on Mobile */}
                                <div className="nav view-mode-nav d-none d-md-flex" role="tablist">
                                    <a
                                        className={`nav-link ${viewMode === "grid" ? "active" : ""
                                            }`}
                                        href="#"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setViewMode("grid");
                                        }}
                                        aria-label="Grid View"
                                    >
                                        <i className="fa-regular fa-grid-2"></i>
                                    </a>
                                    <a
                                        className={`nav-link ${viewMode === "list" ? "active" : ""
                                            }`}
                                        href="#"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setViewMode("list");
                                        }}
                                        aria-label="List View"
                                    >
                                        <i className="fa-regular fa-list"></i>
                                    </a>
                                </div>

                                {/* Sort Dropdown */}
                                <form
                                    className="woocommerce-ordering w-100"
                                    method="get"
                                    onSubmit={(e) => e.preventDefault()}
                                >
                                    <select
                                        name="orderby"
                                        className="orderby-select w-100"
                                        aria-label="destination order"
                                        value={orderby}
                                        onChange={handleSortChange}
                                    >
                                        <option value="menu_order">
                                            Default
                                        </option>
                                        <option value="name">
                                            Name (A-Z)
                                        </option>
                                        <option value="date">Latest</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Active Filters Tags (Below) */}
            {(hasActiveFilters || search) && (
                <div className="active-filters mt-3 d-flex align-items-center flex-wrap gap-2">
                    <span className="fw-bold me-2 text-dark">
                        Active Filters:
                    </span>

                    {search && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm">
                            <span className="fw-bold text-muted fs-xs me-2">
                                Search: {search}
                            </span>
                            <button
                                onClick={() => handleRemoveFilter("search")}
                                className="bg-transparent border-0 p-0 text-muted hover-red"
                            >
                                <i className="fa-regular fa-times"></i>
                            </button>
                        </span>
                    )}

                    {selectedCategory && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm">
                            <span className="fw-bold text-uppercase fs-xs me-2">
                                {getCategoryName(selectedCategory)}
                            </span>
                            <button
                                onClick={() => handleRemoveFilter("category")}
                                className="bg-transparent border-0 p-0 text-muted hover-red"
                            >
                                <i className="fa-regular fa-times"></i>
                            </button>
                        </span>
                    )}

                    {selectedBrand && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm">
                            <span className="fw-bold text-uppercase fs-xs me-2">
                                {getBrandName(selectedBrand)}
                            </span>
                            <button
                                onClick={() => handleRemoveFilter("brand")}
                                className="bg-transparent border-0 p-0 text-muted hover-red"
                            >
                                <i className="fa-regular fa-times"></i>
                            </button>
                        </span>
                    )}

                    {selectedSolution && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-2 d-inline-flex align-items-center shadow-sm">
                            <span className="fw-bold text-uppercase fs-xs me-2">
                                {getSolutionName(selectedSolution)}
                            </span>
                            <button
                                onClick={() => handleRemoveFilter("solution")}
                                className="bg-transparent border-0 p-0 text-muted hover-red"
                            >
                                <i className="fa-regular fa-times"></i>
                            </button>
                        </span>
                    )}

                    {hasActiveFilters && (
                        <button
                            onClick={handleResetFilters}
                            className="btn-clear bg-light border-0 rounded-pill px-3 py-2 fw-bold text-uppercase fs-xs text-muted hover-dark ms-2"
                        >
                            Reset All
                        </button>
                    )}
                </div>
            )}
        </div>
    );
}
