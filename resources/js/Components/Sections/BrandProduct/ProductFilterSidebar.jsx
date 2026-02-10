import React, { useState } from "react";

export default function ProductFilterSidebar({
    showFilters,
    filters,
    handleFilterChange,
    categories,
    serviceSolutions,
    serviceItemLabel,
}) {
    const [openDropdown, setOpenDropdown] = useState(null); // 'category', 'service'
    const [categorySearch, setCategorySearch] = useState("");
    const [visibleCategoriesCount, setVisibleCategoriesCount] = useState(10);

    const [serviceSearch, setServiceSearch] = useState("");
    const [visibleServiceCount, setVisibleServiceCount] = useState(10);

    const filteredCategories = categories.filter(cat =>
        cat.name.toLowerCase().includes(categorySearch.toLowerCase())
    );

    const filteredServiceSolutions = serviceSolutions.filter(sol =>
        sol.name.toLowerCase().includes(serviceSearch.toLowerCase())
    );

    if (!showFilters) return null;

    const themeGreen = "#4ac15e";

    const toggleDropdown = (name) => {
        setOpenDropdown(openDropdown === name ? null : name);
    };

    const getActiveCategoryName = () => {
        if (!filters.category) return "Semua Kategori";
        return categories.find(c => c.slug === filters.category)?.name || "Kategori";
    };

    const getActiveServiceName = () => {
        if (!filters.service_item) return `Semua ${serviceItemLabel}`;
        return serviceSolutions.find(s => s.slug === filters.service_item)?.name || serviceItemLabel;
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
                                        className={`d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 transition-all ${!filters.category ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                        style={{ textAlign: 'left', fontSize: '13.5px' }}
                                    >
                                        <span>Semua Kategori</span>
                                    </button>
                                    {categories.map((cat) => (
                                        <button
                                            key={cat.id}
                                            onClick={() => { handleFilterChange("category", cat.slug); setOpenDropdown(null); }}
                                            className={`d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 transition-all ${filters.category === cat.slug ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                            style={{ textAlign: 'left', fontSize: '13.5px' }}
                                        >
                                            <span className="text-truncate">{cat.name}</span>
                                            <span className="badge rounded-pill bg-light text-dark px-2 py-1" style={{ fontSize: '10px' }}>({cat.count})</span>
                                        </button>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>
                )}

                {/* Service Dropdown */}
                {serviceSolutions.length > 0 && (
                    <div className="mb-3 position-relative">
                        <button
                            onClick={() => toggleDropdown('service')}
                            className="w-100 d-flex align-items-center justify-content-between px-3 py-3 rounded-4 border-0 shadow-sm bg-white"
                        >
                            <div className="d-flex align-items-center gap-2">
                                <div className="rounded-circle d-flex align-items-center justify-content-center"
                                    style={{ width: '32px', height: '32px', backgroundColor: `${themeGreen}15` }}>
                                    <i className="fa-regular fa-lightbulb" style={{ fontSize: '14px', color: themeGreen }}></i>
                                </div>
                                <span className="fw-bold text-dark" style={{ fontSize: '14px' }}>{getActiveServiceName()}</span>
                            </div>
                            <i className={`fa-regular fa-chevron-down transition-transform duration-300 ${openDropdown === 'service' ? 'rotate-180' : ''}`}
                                style={{ fontSize: '12px', color: '#64748B' }}></i>
                        </button>
                        {openDropdown === 'service' && (
                            <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden filter-dropdown-menu">
                                <div className="p-2 d-flex flex-column gap-1" style={{ maxHeight: '300px', overflowY: 'auto' }}>
                                    <button
                                        onClick={() => { handleFilterChange("service_item", ""); setOpenDropdown(null); }}
                                        className={`d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 transition-all ${!filters.service_item ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                        style={{ textAlign: 'left', fontSize: '13.5px' }}
                                    >
                                        <span>Semua {serviceItemLabel}</span>
                                    </button>
                                    {serviceSolutions.map((sol) => (
                                        <button
                                            key={sol.id}
                                            onClick={() => { handleFilterChange("service_item", sol.slug); setOpenDropdown(null); }}
                                            className={`d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 transition-all ${filters.service_item === sol.slug ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"}`}
                                            style={{ textAlign: 'left', fontSize: '13.5px' }}
                                        >
                                            <span className="text-truncate">{sol.name}</span>
                                            <span className="badge rounded-pill bg-light text-dark px-2 py-1" style={{ fontSize: '10px' }}>({sol.count})</span>
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
                                setVisibleCategoriesCount(10);
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
                                        handleFilterChange("category", cat.slug);
                                    }}
                                    className={
                                        filters.category === cat.slug
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
                {serviceSolutions.length > 0 && (
                    <div className="widget widget_categories th-radius shadow-sm p-4 border bg-white">
                        <h3 className="widget_title">{serviceItemLabel}</h3>

                        {/* Search Input */}
                        <div className="mb-3 position-relative">
                            <input
                                type="text"
                                className="form-control rounded-pill pe-5"
                                placeholder={`Cari ${serviceItemLabel}...`}
                                value={serviceSearch}
                                onChange={(e) => {
                                    setServiceSearch(e.target.value);
                                    setVisibleServiceCount(10);
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
                                        handleFilterChange("service_item", "");
                                    }}
                                    className={
                                        !filters.service_item
                                            ? "active fw-bold sidebar-active-item"
                                            : ""
                                    }
                                >
                                    All {serviceItemLabel}
                                </a>
                            </li>
                            {filteredServiceSolutions.slice(0, visibleServiceCount).map((sol) => (
                                <li key={sol.id}>
                                    <a
                                        href="#"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            handleFilterChange(
                                                "service_item",
                                                sol.slug
                                            );
                                        }}
                                        className={
                                            filters.service_item === sol.slug
                                                ? "active fw-bold sidebar-active-item"
                                                : ""
                                        }
                                    >
                                        {sol.name}
                                    </a>
                                </li>
                            ))}
                        </ul>

                        {/* Load More Button */}
                        {filteredServiceSolutions.length > visibleServiceCount && (
                            <div className="text-center mt-3">
                                <button
                                    onClick={() => setVisibleServiceCount(prev => prev + 10)}
                                    className="btn btn-sm btn-link text-decoration-none fw-bold"
                                    style={{ color: themeGreen, fontSize: '13px' }}
                                >
                                    Lihat selengkapnya <i className="fa-regular fa-chevron-down ms-1"></i>
                                </button>
                            </div>
                        )}

                        {/* No results message */}
                        {filteredServiceSolutions.length === 0 && (
                            <div className="text-center py-3 text-muted" style={{ fontSize: '13px' }}>
                                {serviceItemLabel} tidak ditemukan
                            </div>
                        )}
                    </div>
                )}
            </div>
        </div>
    );
}
