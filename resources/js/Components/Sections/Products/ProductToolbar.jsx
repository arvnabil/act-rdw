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
            {/* Top Bar Area */}
            <div className="th-sort-bar">
                <div className="row justify-content-between align-items-center gy-3">
                    {/* Left: Search */}
                    <div className="col-md-auto">
                        <div
                            className="search-form-area"
                            style={{ width: "300px", maxWidth: "100%" }}
                        >
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
                    <div className="col-md-auto">
                        <div className="sorting-filter-wrap d-flex align-items-center justify-content-center justify-content-md-end flex-wrap gap-3">
                            {/* All Filters Button */}
                            <button
                                onClick={() => setShowFilters(!showFilters)}
                                className={`th-btn style1 th-radius th-icon text-capitalize ${showFilters ? "active" : ""}`}
                                style={{
                                    padding: "10px 25px",
                                    height: "50px",
                                    display: "flex",
                                    alignItems: "center",
                                }}
                            >
                                <i className="fa-regular fa-filter me-2"></i>{" "}
                                All Filters
                            </button>

                            {/* View Mode */}
                            <div className="nav me-2" role="tablist">
                                <a
                                    className={`nav-link d-flex align-items-center justify-content-center ${
                                        viewMode === "grid" ? "active" : ""
                                    }`}
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        setViewMode("grid");
                                    }}
                                    aria-label="Grid View"
                                >
                                    <i className="fa-light fa-grid-2"></i>
                                </a>
                                <a
                                    className={`nav-link d-flex align-items-center justify-content-center ${
                                        viewMode === "list" ? "active" : ""
                                    }`}
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        setViewMode("list");
                                    }}
                                    aria-label="List View"
                                >
                                    <i className="fa-solid fa-list"></i>
                                </a>
                            </div>

                            {/* Sort Dropdown */}
                            <form
                                className="woocommerce-ordering"
                                method="get"
                                onSubmit={(e) => e.preventDefault()}
                            >
                                <select
                                    name="orderby"
                                    className="orderby nice-select"
                                    aria-label="destination order"
                                    value={orderby}
                                    onChange={handleSortChange}
                                    style={{ display: "block" }}
                                >
                                    <option value="menu_order">
                                        Default Sorting
                                    </option>
                                    <option value="name">
                                        Sort by Name (A-Z)
                                    </option>
                                    <option value="date">Sort by Latest</option>
                                </select>
                            </form>
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
