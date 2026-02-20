import React from "react";

export default function ProjectToolbar({
    search,
    setSearch,
    handleSearch,
    orderby,
    handleSortChange,
    showFilters,
    setShowFilters,
    // Data for lookups
    brands,
    solutions,
    industries,
    // Selected values
    selectedBrand,
    selectedSolution,
    selectedIndustry,
    // Handlers
    handleRemoveFilter,
    handleResetFilters,
}) {
    // Helper to get name
    const getBrandName = (id) => brands?.find((b) => b.id == id)?.name || id;
    const getSolutionName = (id) =>
        solutions?.find((s) => s.id == id)?.title || id;

    const hasActiveFilters =
        selectedBrand || selectedSolution || selectedIndustry;

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
                    border-radius: 50px;
                    display: flex;
                    align-items: center;
                    position: relative;
                    width: 100%;
                    transition: all 0.3s ease;
                    padding: 4px;
                }
                .search-form:focus-within {
                    border-color: #4ac15e;
                    background: #ffffff;
                    box-shadow: 0 0 0 4px rgba(74, 193, 94, 0.1);
                }
                .search-form input {
                    border: none !important;
                    background: transparent !important;
                    padding: 0 50px 0 20px !important;
                    height: 42px !important;
                    font-size: 14px !important;
                    flex-grow: 1 !important;
                    outline: none !important;
                    border-radius: 50px !important;
                    box-shadow: none !important;
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
                    border-radius: 50% !important;
                    position: absolute !important;
                    right: 5px !important;
                    top: 50% !important;
                    transform: translateY(-50%) !important;
                    transition: all 0.3s ease !important;
                    box-shadow: 0 2px 6px rgba(74, 193, 94, 0.3) !important;
                }
                .search-form button:hover {
                    background: #3b82f6 !important;
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
                    width: 100%;
                }
                .th-btn-filter:hover {
                    background: #3b82f6;
                    transform: translateY(-1px);
                    box-shadow: 0 6px 14px rgba(59, 130, 246, 0.3);
                }
                .th-btn-filter.active {
                    background: #0b1422;
                    box-shadow: 0 4px 10px rgba(11, 20, 34, 0.2);
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
                }
                .orderby-select:hover {
                    border-color: #cbd5e1;
                }
                .filter-chip {
                    font-size: 12px;
                    font-weight: 600;
                    color: #64748b;
                    border-color: #e2e8f0 !important;
                    transition: all 0.2s ease;
                }
                .filter-chip:hover {
                    border-color: #4ac15e !important;
                    color: #4ac15e;
                }
                .hover-red:hover {
                    color: #ef4444 !important;
                }
                @media (max-width: 767px) {
                    .product-toolbar-wrapper .th-sort-bar { padding: 16px; border-radius: 16px; }
                    .search-form-area { margin-bottom: 12px; }
                    .sorting-filter-wrap {
                        display: flex;
                        flex-direction: row;
                        justify-content: space-between;
                        gap: 12px;
                    }
                    .filter-btn-wrapper, .sort-wrapper { width: 50%; }
                    .th-btn-filter { width: 100%; border-radius: 50px; font-size: 13px; padding: 0 15px; }
                    .orderby-select { width: 100%; border-radius: 50px; background-position: right 12px center !important; padding-right: 30px !important; padding-left: 15px !important; font-size: 13px; }
                }
                @media (min-width: 768px) {
                    .search-form-area { width: 350px; }
                    .th-btn-filter { width: auto; }
                    .filter-btn-wrapper { width: auto; }
                    .sort-wrapper { width: auto; }
                }
            `}</style>

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
                                    placeholder="Search projects..."
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
                            {/* Filter Toggle */}
                            <div className="filter-btn-wrapper">
                                <button
                                    onClick={() => setShowFilters(!showFilters)}
                                    className={`th-btn-filter ${showFilters ? "active" : ""}`}
                                >
                                    <i className="fa-regular fa-sliders"></i>
                                    <span>{showFilters ? "Hide Filters" : "Show Filters"}</span>
                                </button>
                            </div>

                            {/* Sort Dropdown */}
                            <div className="sort-wrapper">
                                <select
                                    className="orderby-select w-100"
                                    value={orderby}
                                    onChange={handleSortChange}
                                >
                                    <option value="latest">Latest Projects</option>
                                    <option value="title">Project Name (A-Z)</option>
                                    <option value="client">Client Name</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Active Filters Chips */}
            {(hasActiveFilters || search) && (
                <div className="active-filters mt-4 d-flex align-items-center flex-wrap gap-2 animate-in fade-in slide-in-from-top-2 duration-300">
                    <span className="text-sm font-bold text-slate-500 me-2">
                        Active Filters:
                    </span>

                    {search && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-1.5 d-inline-flex align-items-center shadow-sm">
                            <span className="me-2">Search: {search}</span>
                            <button onClick={() => handleRemoveFilter("search")} className="hover-red border-0 bg-transparent p-0 text-slate-400">
                                <i className="fa-regular fa-times text-xs"></i>
                            </button>
                        </span>
                    )}

                    {selectedIndustry && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-1.5 d-inline-flex align-items-center shadow-sm">
                            <span className="me-2 uppercase">{selectedIndustry}</span>
                            <button onClick={() => handleRemoveFilter("industry")} className="hover-red border-0 bg-transparent p-0 text-slate-400">
                                <i className="fa-regular fa-times text-xs"></i>
                            </button>
                        </span>
                    )}

                    {selectedBrand && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-1.5 d-inline-flex align-items-center shadow-sm">
                            <span className="me-2 uppercase">{getBrandName(selectedBrand)}</span>
                            <button onClick={() => handleRemoveFilter("brand")} className="hover-red border-0 bg-transparent p-0 text-slate-400">
                                <i className="fa-regular fa-times text-xs"></i>
                            </button>
                        </span>
                    )}

                    {selectedSolution && (
                        <span className="filter-chip bg-white border rounded-pill px-3 py-1.5 d-inline-flex align-items-center shadow-sm">
                            <span className="me-2 uppercase">{getSolutionName(selectedSolution)}</span>
                            <button onClick={() => handleRemoveFilter("solution")} className="hover-red border-0 bg-transparent p-0 text-slate-400">
                                <i className="fa-regular fa-times text-xs"></i>
                            </button>
                        </span>
                    )}

                    <button
                        onClick={handleResetFilters}
                        className="text-xs font-bold text-slate-400 hover:text-slate-900 border-0 bg-transparent px-2 py-1 ms-2"
                    >
                        RESET ALL
                    </button>
                </div>
            )}
        </div>
    );
}
