import React from "react";

export default function ProductToolbar({
    search,
    setSearch,
    handleSearch,
    viewMode,
    setViewMode,
    orderby,
    handleSortChange,
}) {
    return (
        <div className="th-sort-bar">
            <div className="row justify-content-between align-items-center">
                <div className="col-md-4">
                    <div className="search-form-area">
                        <form className="search-form" onSubmit={handleSearch}>
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
                <div className="col-md-auto">
                    <div className="sorting-filter-wrap">
                        <div className="nav" role="tablist">
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
                                <option value="name">Sort by Name (A-Z)</option>
                                <option value="date">Sort by Latest</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}
