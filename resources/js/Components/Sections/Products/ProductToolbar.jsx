import React from "react";

export default function ProductToolbar({
    search,
    setSearch,
    handleSearch,
    viewMode,
    setViewMode,
    orderby,
    handleSortChange,
    brands,
    solutions,
    categories,
    selectedBrand,
    selectedSolution,
    selectedCategory,
    handleBrandChange,
    handleSolutionChange,
    handleCategoryChange,
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
        search || selectedBrand || selectedSolution || selectedCategory;

    return (
        <>
            <div className="th-sort-bar">
                <div className="row justify-content-between align-items-center">
                    <div className="col-md-4">
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
                    <div className="col-md-auto mt-3 mt-md-0">
                        <div className="sorting-filter-wrap d-flex align-items-center justify-content-center justify-content-md-end flex-wrap gap-3">
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

                            {/* Filter by Category */}
                            <form
                                className="woocommerce-ordering"
                                onSubmit={(e) => e.preventDefault()}
                            >
                                <select
                                    name="category"
                                    className="orderby nice-select"
                                    aria-label="Filter by Category"
                                    value={selectedCategory}
                                    onChange={handleCategoryChange}
                                    style={{ display: "block" }}
                                >
                                    <option value="">All Categories</option>
                                    {categories &&
                                        categories.map((cat) => (
                                            <option key={cat.id} value={cat.id}>
                                                {cat.name}
                                            </option>
                                        ))}
                                </select>
                            </form>

                            {/* Filter by Brand */}
                            <form
                                className="woocommerce-ordering"
                                onSubmit={(e) => e.preventDefault()}
                            >
                                <select
                                    name="brand"
                                    className="orderby nice-select"
                                    aria-label="Filter by Brand"
                                    value={selectedBrand}
                                    onChange={handleBrandChange}
                                    style={{ display: "block" }}
                                >
                                    <option value="">All Brands</option>
                                    {brands &&
                                        brands.map((brand) => (
                                            <option
                                                key={brand.id}
                                                value={brand.id}
                                            >
                                                {brand.name}
                                            </option>
                                        ))}
                                </select>
                            </form>

                            {/* Filter by Solution */}
                            <form
                                className="woocommerce-ordering"
                                onSubmit={(e) => e.preventDefault()}
                            >
                                <select
                                    name="solution"
                                    className="orderby nice-select"
                                    aria-label="Filter by Solution"
                                    value={selectedSolution}
                                    onChange={handleSolutionChange}
                                    style={{ display: "block" }}
                                >
                                    <option value="">All Solutions</option>
                                    {solutions &&
                                        solutions.map((solution) => (
                                            <option
                                                key={solution.id}
                                                value={solution.id}
                                            >
                                                {solution.title}
                                            </option>
                                        ))}
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {/* Active Filters Tags */}
            {hasActiveFilters && (
                <div className="active-filters mb-4">
                    <div className="d-flex flex-wrap align-items-center">
                        <span className="fw-bold me-2">Active Filters:</span>

                        {search && (
                            <span className="d-inline-flex align-items-center border rounded px-3 py-1 bg-white text-dark gap-2 me-2">
                                Search: {search}
                                <i
                                    className="fa-regular fa-times cursor-pointer"
                                    onClick={() => handleRemoveFilter("search")}
                                    style={{ cursor: "pointer" }}
                                ></i>
                            </span>
                        )}

                        {selectedCategory && (
                            <span className="d-inline-flex align-items-center border rounded px-3 py-1 bg-white text-dark gap-2 me-2">
                                Category: {getCategoryName(selectedCategory)}
                                <i
                                    className="fa-regular fa-times cursor-pointer"
                                    onClick={() =>
                                        handleRemoveFilter("category")
                                    }
                                    style={{ cursor: "pointer" }}
                                ></i>
                            </span>
                        )}

                        {selectedBrand && (
                            <span className="d-inline-flex align-items-center border rounded px-3 py-1 bg-white text-dark gap-2 me-2">
                                Brand: {getBrandName(selectedBrand)}
                                <i
                                    className="fa-regular fa-times cursor-pointer"
                                    onClick={() => handleRemoveFilter("brand")}
                                    style={{ cursor: "pointer" }}
                                ></i>
                            </span>
                        )}

                        {selectedSolution && (
                            <span className="d-inline-flex align-items-center border rounded px-3 py-1 bg-white text-dark gap-2 me-2">
                                Solution: {getSolutionName(selectedSolution)}
                                <i
                                    className="fa-regular fa-times cursor-pointer"
                                    onClick={() =>
                                        handleRemoveFilter("solution")
                                    }
                                    style={{ cursor: "pointer" }}
                                ></i>
                            </span>
                        )}

                        <button
                            className="btn btn-sm btn-outline-danger ms-2"
                            onClick={handleResetFilters}
                        >
                            Reset Filter
                        </button>
                    </div>
                </div>
            )}
        </>
    );
}
