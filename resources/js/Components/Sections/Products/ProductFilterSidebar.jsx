import React, { useState } from "react";

export default function ProductFilterSidebar({
    showFilters,
    filters, // { brand, category, solution }
    handleFilterChange, // (key, value) => void
    categories,
    brands,
    solutions,
}) {
    const [openDropdown, setOpenDropdown] = useState(null); // 'category', 'brand', 'solution'
    const [categorySearch, setCategorySearch] = useState("");
    const [visibleCategoriesCount, setVisibleCategoriesCount] = useState(10);

    // Brand State
    const [brandSearch, setBrandSearch] = useState("");
    const [visibleBrandsCount, setVisibleBrandsCount] = useState(10);

    // Solution State
    const [solutionSearch, setSolutionSearch] = useState("");
    const [visibleSolutionsCount, setVisibleSolutionsCount] = useState(10);

    const filteredCategories = categories.filter(cat =>
        cat.name.toLowerCase().includes(categorySearch.toLowerCase())
    );

    const filteredBrands = brands.filter(brand =>
        brand.name.toLowerCase().includes(brandSearch.toLowerCase())
    );

    const filteredSolutions = solutions.filter(sol =>
        sol.title.toLowerCase().includes(solutionSearch.toLowerCase())
    );

    if (!showFilters) return null;

    const themeGreen = "#4ac15e";

    const toggleDropdown = (name) => {
        setOpenDropdown(openDropdown === name ? null : name);
    };

    const getActiveCategoryName = () => {
        if (!filters.category) return "Semua Kategori";
        return categories.find(c => c.id == filters.category)?.name || "Kategori";
    };

    const getActiveBrandName = () => {
        if (!filters.brand) return "Semua Brand";
        return brands.find(b => b.id == filters.brand)?.name || "Brand";
    };

    const getActiveSolutionName = () => {
        if (!filters.solution) return "Semua Solusi";
        return solutions.find(s => s.id == filters.solution)?.title || "Solusi";
    };

    return (
        <div className="col-lg-3 mb-4 mb-lg-0 wow fadeInUp">
            <style>{`
                .sidebar-active-item {
                    color: ${themeGreen} !important;
                }
                .sidebar-active-item:hover {
                    color: #ffffff !important;
                }
                .rotate-180 {
                    transform: rotate(180deg);
                }
                .filter-dropdown-menu {
                    transition: all 0.3s ease;
                    z-index: 1000;
                }
                .widget_title {
                    font-size: 18px !important;
                    font-weight: 700 !important;
                    margin-bottom: 20px !important;
                }
            `}</style>

            {/* MOBILE DROPDOWNS */}
            <div className="d-block d-lg-none px-2 mb-4">
                {/* Category Dropdown */}
                {categories && categories.length > 0 && (
                    <div className="mb-3 position-relative">
                        <button
                            onClick={() => toggleDropdown('category')}
                            className="w-100 d-flex align-items-center justify-content-between px-3 py-3 rounded-4 border-0 shadow-sm bg-white"
                            style={{ transition: 'all 0.3s ease' }}
                        >
                            <div className="d-flex align-items-center gap-2">
                                <div className="rounded-circle d-flex align-items-center justify-content-center"
                                    style={{ width: '32px', height: '32px', backgroundColor: `${themeGreen}15` }}>
                                    <i className="fa-regular fa-grid-2" style={{ fontSize: '14px', color: themeGreen }}></i>
                                </div>
                                <span className="fw-bold text-dark" style={{ fontSize: '14px' }}>{getActiveCategoryName()}</span>
                            </div>
                            <i className={`fa-regular fa-chevron-down transition-transform duration-300 ${openDropdown === 'category' ? 'rotate-180' : ''}`}
                                style={{ fontSize: '12px', color: '#64748B' }}></i>
                        </button>
                        {openDropdown === 'category' && (
                            <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden filter-dropdown-menu">
                                <div className="p-2 d-flex flex-column gap-1" style={{ maxHeight: '300px', overflowY: 'auto' }}>
                                    <button
                                        onClick={() => { handleFilterChange("category", ""); setOpenDropdown(null); }}
                                        className={`px-3 py-2.5 rounded-3 border-0 transition-all text-start ${!filters.category ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                        style={{ fontSize: '13.5px' }}
                                    >
                                        Semua Kategori
                                    </button>
                                    {categories.map((cat) => (
                                        <button
                                            key={cat.id}
                                            onClick={() => { handleFilterChange("category", cat.id); setOpenDropdown(null); }}
                                            className={`px-3 py-2.5 rounded-3 border-0 transition-all text-start ${filters.category == cat.id ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                            style={{ fontSize: '13.5px' }}
                                        >
                                            {cat.name}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                )}

                {/* Brand Dropdown */}
                {brands && brands.length > 0 && (
                    <div className="mb-3 position-relative">
                        <button
                            onClick={() => toggleDropdown('brand')}
                            className="w-100 d-flex align-items-center justify-content-between px-3 py-3 rounded-4 border-0 shadow-sm bg-white"
                        >
                            <div className="d-flex align-items-center gap-2">
                                <div className="rounded-circle d-flex align-items-center justify-content-center"
                                    style={{ width: '32px', height: '32px', backgroundColor: `${themeGreen}15` }}>
                                    <i className="fa-regular fa-tag" style={{ fontSize: '14px', color: themeGreen }}></i>
                                </div>
                                <span className="fw-bold text-dark" style={{ fontSize: '14px' }}>{getActiveBrandName()}</span>
                            </div>
                            <i className={`fa-regular fa-chevron-down transition-transform duration-300 ${openDropdown === 'brand' ? 'rotate-180' : ''}`}
                                style={{ fontSize: '12px', color: '#64748B' }}></i>
                        </button>
                        {openDropdown === 'brand' && (
                            <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden filter-dropdown-menu">
                                <div className="p-2 d-flex flex-column gap-1" style={{ maxHeight: '300px', overflowY: 'auto' }}>
                                    <button
                                        onClick={() => { handleFilterChange("brand", ""); setOpenDropdown(null); }}
                                        className={`px-3 py-2.5 rounded-3 border-0 text-start ${!filters.brand ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                        style={{ fontSize: '13.5px' }}
                                    >
                                        Semua Brand
                                    </button>
                                    {brands.map((brand) => (
                                        <button
                                            key={brand.id}
                                            onClick={() => { handleFilterChange("brand", brand.id); setOpenDropdown(null); }}
                                            className={`px-3 py-2.5 rounded-3 border-0 text-start ${filters.brand == brand.id ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                            style={{ fontSize: '13.5px' }}
                                        >
                                            {brand.name}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                )}

                {/* Solution Dropdown */}
                {solutions && solutions.length > 0 && (
                    <div className="mb-3 position-relative">
                        <button
                            onClick={() => toggleDropdown('solution')}
                            className="w-100 d-flex align-items-center justify-content-between px-3 py-3 rounded-4 border-0 shadow-sm bg-white"
                        >
                            <div className="d-flex align-items-center gap-2">
                                <div className="rounded-circle d-flex align-items-center justify-content-center"
                                    style={{ width: '32px', height: '32px', backgroundColor: `${themeGreen}15` }}>
                                    <i className="fa-regular fa-lightbulb" style={{ fontSize: '14px', color: themeGreen }}></i>
                                </div>
                                <span className="fw-bold text-dark" style={{ fontSize: '14px' }}>{getActiveSolutionName()}</span>
                            </div>
                            <i className={`fa-regular fa-chevron-down transition-transform duration-300 ${openDropdown === 'solution' ? 'rotate-180' : ''}`}
                                style={{ fontSize: '12px', color: '#64748B' }}></i>
                        </button>
                        {openDropdown === 'solution' && (
                            <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden filter-dropdown-menu">
                                <div className="p-2 d-flex flex-column gap-1" style={{ maxHeight: '300px', overflowY: 'auto' }}>
                                    <button
                                        onClick={() => { handleFilterChange("solution", ""); setOpenDropdown(null); }}
                                        className={`px-3 py-2.5 rounded-3 border-0 text-start ${!filters.solution ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                        style={{ fontSize: '13.5px' }}
                                    >
                                        Semua Solusi
                                    </button>
                                    {solutions.map((sol) => (
                                        <button
                                            key={sol.id}
                                            onClick={() => { handleFilterChange("solution", sol.id); setOpenDropdown(null); }}
                                            className={`px-3 py-2.5 rounded-3 border-0 text-start ${filters.solution == sol.id ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                            style={{ fontSize: '13.5px' }}
                                        >
                                            {sol.title}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </div>

            {/* DESKTOP SIDEBAR */}
            <div className="d-none d-lg-block">
                {/* Widget: Categories */}
                {categories && categories.length > 0 && (
                    <div className="widget widget_categories th-radius shadow-sm p-4 border mb-4 bg-white">
                        <h3 className="widget_title">Categories</h3>

                        {/* Search Input */}
                        <div className="mb-3 position-relative">
                            <input
                                type="text"
                                className="form-control rounded-pill pe-5"
                                placeholder="Cari Kategori..."
                                value={categorySearch}
                                onChange={(e) => {
                                    setCategorySearch(e.target.value);
                                    setVisibleCategoriesCount(10); // Reset pagination on search
                                }}
                                style={{
                                    fontSize: '14px',
                                    height: '40px',
                                    border: '1px solid #e2e8f0',
                                    paddingLeft: '15px'
                                }}
                            />
                            <i className="fa-light fa-magnifying-glass position-absolute"
                                style={{
                                    right: '15px',
                                    top: '50%',
                                    transform: 'translateY(-50%)',
                                    color: '#94a3b8'
                                }}></i>
                        </div>

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
                            {filteredCategories.slice(0, visibleCategoriesCount).map((cat) => (
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

                        {/* Load More Button */}
                        {filteredCategories.length > visibleCategoriesCount && (
                            <div className="text-center mt-3">
                                <button
                                    onClick={() => setVisibleCategoriesCount(prev => prev + 10)}
                                    className="btn btn-sm btn-link text-decoration-none fw-bold"
                                    style={{ color: themeGreen, fontSize: '13px' }}
                                >
                                    Lihat selengkapnya <i className="fa-regular fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        )}

                        {/* No results message */}
                        {filteredCategories.length === 0 && (
                            <div className="text-center py-3 text-muted" style={{ fontSize: '13px' }}>
                                Kategori tidak ditemukan
                            </div>
                        )}
                    </div>
                )}

                {/* Widget: Brands */}
                {brands && brands.length > 0 && (
                    <div className="widget widget_categories th-radius shadow-sm p-4 border mb-4 bg-white">
                        <h3 className="widget_title">Brands</h3>

                        {/* Search Input */}
                        <div className="mb-3 position-relative">
                            <input
                                type="text"
                                className="form-control rounded-pill pe-5"
                                placeholder="Cari Brand..."
                                value={brandSearch}
                                onChange={(e) => {
                                    setBrandSearch(e.target.value);
                                    setVisibleBrandsCount(10); // Reset pagination on search
                                }}
                                style={{
                                    fontSize: '14px',
                                    height: '40px',
                                    border: '1px solid #e2e8f0',
                                    paddingLeft: '15px'
                                }}
                            />
                            <i className="fa-light fa-magnifying-glass position-absolute"
                                style={{
                                    right: '15px',
                                    top: '50%',
                                    transform: 'translateY(-50%)',
                                    color: '#94a3b8'
                                }}></i>
                        </div>

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
                            {filteredBrands.slice(0, visibleBrandsCount).map((brand) => (
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

                        {/* Load More Button */}
                        {filteredBrands.length > visibleBrandsCount && (
                            <div className="text-center mt-3">
                                <button
                                    onClick={() => setVisibleBrandsCount(prev => prev + 10)}
                                    className="btn btn-sm btn-link text-decoration-none fw-bold"
                                    style={{ color: themeGreen, fontSize: '13px' }}
                                >
                                    Lihat selengkapnya <i className="fa-regular fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        )}

                        {/* No results message */}
                        {filteredBrands.length === 0 && (
                            <div className="text-center py-3 text-muted" style={{ fontSize: '13px' }}>
                                Brand tidak ditemukan
                            </div>
                        )}
                    </div>
                )}

                {/* Widget: Solutions */}
                {solutions && solutions.length > 0 && (
                    <div className="widget widget_categories th-radius shadow-sm p-4 border bg-white">
                        <h3 className="widget_title">Solutions</h3>

                        {/* Search Input */}
                        <div className="mb-3 position-relative">
                            <input
                                type="text"
                                className="form-control rounded-pill pe-5"
                                placeholder="Cari Solusi..."
                                value={solutionSearch}
                                onChange={(e) => {
                                    setSolutionSearch(e.target.value);
                                    setVisibleSolutionsCount(10); // Reset pagination on search
                                }}
                                style={{
                                    fontSize: '14px',
                                    height: '40px',
                                    border: '1px solid #e2e8f0',
                                    paddingLeft: '15px'
                                }}
                            />
                            <i className="fa-light fa-magnifying-glass position-absolute"
                                style={{
                                    right: '15px',
                                    top: '50%',
                                    transform: 'translateY(-50%)',
                                    color: '#94a3b8'
                                }}></i>
                        </div>

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
                            {filteredSolutions.slice(0, visibleSolutionsCount).map((sol) => (
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

                        {/* Load More Button */}
                        {filteredSolutions.length > visibleSolutionsCount && (
                            <div className="text-center mt-3">
                                <button
                                    onClick={() => setVisibleSolutionsCount(prev => prev + 10)}
                                    className="btn btn-sm btn-link text-decoration-none fw-bold"
                                    style={{ color: themeGreen, fontSize: '13px' }}
                                >
                                    Lihat selengkapnya <i className="fa-regular fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        )}

                        {/* No results message */}
                        {filteredSolutions.length === 0 && (
                            <div className="text-center py-3 text-muted" style={{ fontSize: '13px' }}>
                                Solusi tidak ditemukan
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
