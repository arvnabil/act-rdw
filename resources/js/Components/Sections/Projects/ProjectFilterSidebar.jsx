import React, { useState } from "react";

export default function ProjectFilterSidebar({
    showFilters,
    filters, // { brand, industry, solution }
    handleFilterChange, // (key, value) => void
    industries,
    brands,
    solutions,
}) {
    const [openDropdown, setOpenDropdown] = useState(null); // 'industry', 'brand', 'solution'
    
    const [industrySearch, setIndustrySearch] = useState("");
    const [brandSearch, setBrandSearch] = useState("");
    const [solutionSearch, setSolutionSearch] = useState("");

    const [visibleIndustriesCount, setVisibleIndustriesCount] = useState(5);
    const [visibleBrandsCount, setVisibleBrandsCount] = useState(5);
    const [visibleSolutionsCount, setVisibleSolutionsCount] = useState(5);

    if (!showFilters) return null;

    const themeGreen = "#4ac15e";

    // Filtering logic
    const filteredIndustries = (industries || []).filter(ind => 
        ind.toLowerCase().includes(industrySearch.toLowerCase())
    );

    const filteredBrands = (brands || []).filter(brand => 
        brand.name.toLowerCase().includes(brandSearch.toLowerCase())
    );

    const filteredSolutions = (solutions || []).filter(sol => 
        sol.title.toLowerCase().includes(solutionSearch.toLowerCase())
    );

    const toggleDropdown = (name) => {
        setOpenDropdown(openDropdown === name ? null : name);
    };

    const getActiveIndustryName = () => {
        if (!filters.industry) return "Semua Industri";
        return filters.industry;
    };

    const getActiveBrandName = () => {
        if (!filters.brand) return "Semua Brand";
        return brands?.find(b => b.id == filters.brand)?.name || "Brand";
    };

    const getActiveSolutionName = () => {
        if (!filters.solution) return "Semua Solusi";
        return solutions?.find(s => s.id == filters.solution)?.title || "Solusi";
    };

    return (
        <div className="col-lg-3 mb-4 mb-lg-0">
            <style>{`
                .widget_title {
                    font-size: 18px !important;
                    font-weight: 700 !important;
                    margin-bottom: 20px !important;
                    color: #0b1422;
                }
                .widget_categories ul {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                }
                .widget_categories ul li {
                    margin-bottom: 10px;
                }
                .widget_categories ul li a {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 12px 20px;
                    background: #f8fafc;
                    border-radius: 12px;
                    color: #64748b;
                    font-weight: 500;
                    font-size: 14px;
                    transition: all 0.3s ease;
                    text-decoration: none;
                }
                .widget_categories ul li a:hover,
                .widget_categories ul li a.active {
                    background: ${themeGreen};
                    color: #fff;
                    transform: translateX(5px);
                }
                .th-radius {
                    border-radius: 20px !important;
                }
                .filter-search-input {
                    font-size: 13px !important;
                    height: 40px !important;
                    border: 1px solid #e2e8f0 !important;
                    padding-left: 15px !important;
                    border-radius: 10px !important;
                    background: #fcfcfc !important;
                    margin-bottom: 15px !important;
                }
                .filter-search-input:focus {
                    border-color: ${themeGreen} !important;
                    background: #fff !important;
                    box-shadow: 0 0 0 3px ${themeGreen}15 !important;
                }
                .btn-show-more {
                    color: ${themeGreen};
                    font-size: 13px;
                    font-weight: 700;
                    background: transparent;
                    border: none;
                    padding: 5px 10px;
                    transition: all 0.2s ease;
                }
                .btn-show-more:hover {
                    text-decoration: underline;
                    opacity: 0.8;
                }
                .rotate-180 {
                    transform: rotate(180deg);
                }
                .filter-dropdown-menu {
                    transition: all 0.3s ease;
                    z-index: 1000;
                }
            `}</style>

            {/* MOBILE DROPDOWNS (Matching Product Style) */}
            <div className="d-block d-lg-none px-2 mb-4">
                {/* Industry Dropdown */}
                {industries && industries.length > 0 && (
                    <div className="mb-3 position-relative">
                        <button
                            onClick={() => toggleDropdown('industry')}
                            className="w-100 d-flex align-items-center justify-content-between px-3 py-3 rounded-4 border-0 shadow-sm bg-white"
                        >
                            <div className="d-flex align-items-center gap-2">
                                <div className="rounded-circle d-flex align-items-center justify-content-center"
                                    style={{ width: '32px', height: '32px', backgroundColor: `${themeGreen}15` }}>
                                    <i className="fa-regular fa-building" style={{ fontSize: '14px', color: themeGreen }}></i>
                                </div>
                                <span className="fw-bold text-dark" style={{ fontSize: '14px' }}>{getActiveIndustryName()}</span>
                            </div>
                            <i className={`fa-regular fa-chevron-down transition-transform duration-300 ${openDropdown === 'industry' ? 'rotate-180' : ''}`}
                                style={{ fontSize: '12px', color: '#64748B' }}></i>
                        </button>
                        {openDropdown === 'industry' && (
                            <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden filter-dropdown-menu" style={{ zIndex: 1100 }}>
                                <div className="p-2 d-flex flex-column gap-1" style={{ maxHeight: '300px', overflowY: 'auto' }}>
                                    <button
                                        onClick={() => { handleFilterChange("industry", ""); setOpenDropdown(null); }}
                                        className={`px-3 py-2.5 rounded-3 border-0 text-start ${!filters.industry ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                        style={{ fontSize: '13.5px' }}
                                    >
                                        Semua Industri
                                    </button>
                                    {industries.map((ind) => (
                                        <button
                                            key={ind}
                                            onClick={() => { handleFilterChange("industry", ind); setOpenDropdown(null); }}
                                            className={`px-3 py-2.5 rounded-3 border-0 text-start ${filters.industry == ind ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                            style={{ fontSize: '13.5px' }}
                                        >
                                            {ind}
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
                            <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden filter-dropdown-menu" style={{ zIndex: 1100 }}>
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
                            <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden filter-dropdown-menu" style={{ zIndex: 1100 }}>
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
                {/* Industry Filter */}
                {industries && industries.length > 0 && (
                    <div className="widget widget_categories th-radius shadow-sm p-4 border mb-4 bg-white">
                        <h3 className="widget_title">By Industry</h3>
                        
                        {/* Search Input */}
                        <div className="position-relative">
                            <input
                                type="text"
                                className="form-control filter-search-input shadow-none"
                                placeholder="Cari Industri..."
                                value={industrySearch}
                                onChange={(e) => {
                                    setIndustrySearch(e.target.value);
                                    setVisibleIndustriesCount(5);
                                }}
                            />
                            <i className="fa-light fa-magnifying-glass position-absolute end-0 top-0 mt-2 pt-1 me-3 text-slate-400"></i>
                        </div>

                        <ul>
                            <li>
                                <a
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleFilterChange("industry", "");
                                    }}
                                    className={!filters.industry ? "active" : ""}
                                >
                                    All Industries
                                </a>
                            </li>
                            {filteredIndustries.slice(0, visibleIndustriesCount).map((ind) => (
                                <li key={ind}>
                                    <a
                                        href="#"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            handleFilterChange("industry", ind);
                                        }}
                                        className={filters.industry == ind ? "active" : ""}
                                    >
                                        {ind}
                                    </a>
                                </li>
                            ))}
                        </ul>

                        {filteredIndustries.length > visibleIndustriesCount && (
                            <div className="text-center mt-2">
                                <button 
                                    onClick={() => setVisibleIndustriesCount(prev => prev + 10)}
                                    className="btn-show-more"
                                >
                                    Lihat selengkapnya <i className="fa-regular fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        )}
                        
                        {filteredIndustries.length === 0 && (
                            <div className="text-center py-2 text-slate-400 text-xs">Industri tidak ditemukan</div>
                        )}
                    </div>
                )}

                {/* Brand Filter */}
                {brands && brands.length > 0 && (
                    <div className="widget widget_categories th-radius shadow-sm p-4 border mb-4 bg-white">
                        <h3 className="widget_title">By Brand</h3>

                        {/* Search Input */}
                        <div className="position-relative">
                            <input
                                type="text"
                                className="form-control filter-search-input shadow-none"
                                placeholder="Cari Brand..."
                                value={brandSearch}
                                onChange={(e) => {
                                    setBrandSearch(e.target.value);
                                    setVisibleBrandsCount(5);
                                }}
                            />
                            <i className="fa-light fa-magnifying-glass position-absolute end-0 top-0 mt-2 pt-1 me-3 text-slate-400"></i>
                        </div>

                        <ul>
                            <li>
                                <a
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleFilterChange("brand", "");
                                    }}
                                    className={!filters.brand ? "active" : ""}
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
                                        className={filters.brand == brand.id ? "active" : ""}
                                    >
                                        {brand.name}
                                    </a>
                                </li>
                            ))}
                        </ul>

                        {filteredBrands.length > visibleBrandsCount && (
                            <div className="text-center mt-2">
                                <button 
                                    onClick={() => setVisibleBrandsCount(prev => prev + 10)}
                                    className="btn-show-more"
                                >
                                    Lihat selengkapnya <i className="fa-regular fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        )}

                        {filteredBrands.length === 0 && (
                            <div className="text-center py-2 text-slate-400 text-xs">Brand tidak ditemukan</div>
                        )}
                    </div>
                )}

                {/* Solution Filter */}
                {solutions && solutions.length > 0 && (
                    <div className="widget widget_categories th-radius shadow-sm p-4 border bg-white">
                        <h3 className="widget_title">By Solution</h3>

                        {/* Search Input */}
                        <div className="position-relative">
                            <input
                                type="text"
                                className="form-control filter-search-input shadow-none"
                                placeholder="Cari Solusi..."
                                value={solutionSearch}
                                onChange={(e) => {
                                    setSolutionSearch(e.target.value);
                                    setVisibleSolutionsCount(5);
                                }}
                            />
                            <i className="fa-light fa-magnifying-glass position-absolute end-0 top-0 mt-2 pt-1 me-3 text-slate-400"></i>
                        </div>

                        <ul>
                            <li>
                                <a
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleFilterChange("solution", "");
                                    }}
                                    className={!filters.solution ? "active" : ""}
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
                                        className={filters.solution == sol.id ? "active" : ""}
                                    >
                                        {sol.title}
                                    </a>
                                </li>
                            ))}
                        </ul>

                        {filteredSolutions.length > visibleSolutionsCount && (
                            <div className="text-center mt-2">
                                <button 
                                    onClick={() => setVisibleSolutionsCount(prev => prev + 10)}
                                    className="btn-show-more"
                                >
                                    Lihat selengkapnya <i className="fa-regular fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        )}

                        {filteredSolutions.length === 0 && (
                            <div className="text-center py-2 text-slate-400 text-xs">Solusi tidak ditemukan</div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
