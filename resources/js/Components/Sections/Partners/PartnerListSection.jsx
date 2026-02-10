import React, { useState, useMemo } from "react";
import { Link } from "@inertiajs/react";

export default function PartnerListSection({ brands = [], categories = [] }) {
    const [selectedCategory, setSelectedCategory] = useState("All Partners");
    const [filters, setFilters] = useState({
        featured: false,
    });

    const filteredBrands = useMemo(() => {
        return brands.filter((brand) => {
            const matchesCategory =
                selectedCategory === "All Partners" ||
                (brand.categories && brand.categories.includes(selectedCategory));
            const matchesFeatured = !filters.featured || brand.is_featured;
            return matchesCategory && matchesFeatured;
        });
    }, [brands, selectedCategory, filters]);

    const [isDropdownOpen, setIsDropdownOpen] = useState(false);

    const totalCount = brands.length;

    // Soft backgrounds for branding boxes - Using theme-aware tints
    const bgColors = [
        'bg-[#F0FDF4]', 'bg-[#FDF2F2]', 'bg-[#F5F3FF]',
        'bg-[#F0F9FF]', 'bg-[#FFFBEB]', 'bg-[#F0FDFA]'
    ];

    const themeGreen = "#4ac15e";

    return (
        <section className="case-area space-top space-bottom bg-[#F8FAFC]">
            <style>{`
                .partner-card {
                    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                    border: 1px solid #E2E8F0 !important;
                }
                .partner-card:hover {
                    transform: translateY(-8px);
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08) !important;
                    border-color: ${themeGreen}44 !important;
                }
                .category-btn {
                    transition: all 0.3s ease;
                }
                .category-btn.active {
                    background-color: ${themeGreen} !important;
                    color: white !important;
                }
                .count-badge-active {
                    background-color: white !important;
                    color: ${themeGreen} !important;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                .fade-in-up {
                    animation: fadeInUp 0.6s ease-out both;
                }
                @keyframes fadeInUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .rotate-180 {
                    transform: rotate(180deg);
                }
            `}</style>

            <div className="container">
                <div className="row">
                    {/* Sidebar Area */}
                    <div className="col-lg-4 col-xl-3 mb-4 mb-lg-0 px-3 px-lg-0">
                        {/* Mobile Dropdown - Redesigned as Premium Custom Dropdown */}
                        <div className="d-block d-lg-none mb-3">
                            <h3 className="widget_title mb-3" style={{ fontSize: '16px', fontWeight: '700', color: '#0F172A' }}>Kategori Partner</h3>
                            <div className="position-relative">
                                <button
                                    onClick={() => setIsDropdownOpen(!isDropdownOpen)}
                                    className="w-100 d-flex align-items-center justify-content-between px-3 py-3 rounded-4 border-0 shadow-sm bg-white"
                                    style={{ transition: 'all 0.3s ease' }}
                                >
                                    <div className="d-flex align-items-center gap-2">
                                        <div className="rounded-circle d-flex align-items-center justify-content-center"
                                            style={{ width: '32px', height: '32px', backgroundColor: `${themeGreen}15` }}>
                                            <i className="fa-regular fa-handshake text-success" style={{ fontSize: '14px', color: themeGreen }}></i>
                                        </div>
                                        <span className="fw-bold text-dark" style={{ fontSize: '14px' }}>
                                            {selectedCategory === "All Partners" ? "Semua Partner" : selectedCategory}
                                        </span>
                                    </div>
                                    <i className={`fa-regular fa-chevron-down transition-transform duration-300 ${isDropdownOpen ? 'rotate-180' : ''}`}
                                        style={{ fontSize: '12px', color: '#64748B' }}></i>
                                </button>

                                {/* Dropdown Menu */}
                                {isDropdownOpen && (
                                    <div className="position-absolute w-100 mt-2 bg-white rounded-4 shadow-lg border-0 overflow-hidden fade-in-up"
                                        style={{ zIndex: 1000, animationDuration: '0.3s' }}>
                                        <div className="p-2 d-flex flex-column gap-1">
                                            <button
                                                onClick={() => {
                                                    setSelectedCategory("All Partners");
                                                    setIsDropdownOpen(false);
                                                }}
                                                className={`d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 transition-all ${selectedCategory === "All Partners" ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"
                                                    }`}
                                                style={{ textAlign: 'left', fontSize: '13.5px' }}
                                            >
                                                <span>Semua Partner</span>
                                                <span className="badge rounded-pill bg-light text-dark px-2 py-1" style={{ fontSize: '10px' }}>{totalCount}</span>
                                            </button>

                                            {categories.map((cat) => (
                                                <button
                                                    key={cat.name}
                                                    onClick={() => {
                                                        setSelectedCategory(cat.name);
                                                        setIsDropdownOpen(false);
                                                    }}
                                                    className={`d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 transition-all ${selectedCategory === cat.name ? "bg-light text-success fw-bold" : "bg-transparent text-secondary"
                                                        }`}
                                                    style={{ textAlign: 'left', fontSize: '13.5px' }}
                                                >
                                                    <span className="text-truncate">{cat.name}</span>
                                                    <span className="badge rounded-pill bg-light text-dark px-2 py-1" style={{ fontSize: '10px' }}>{cat.count}</span>
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Desktop Sidebar - Hidden on mobile */}
                        <aside className="sidebar-area pe-lg-4 d-none d-lg-block">
                            <div className="widget bg-transparent border-0 p-0">
                                <h3 className="widget_title mb-4" style={{ fontSize: '18px', fontWeight: '700', color: '#0F172A', letterSpacing: '-0.01em' }}>Categories</h3>
                                <div className="d-flex flex-column gap-1">
                                    <button
                                        onClick={() => setSelectedCategory("All Partners")}
                                        className={`category-btn d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 shadow-sm ${selectedCategory === "All Partners" ? "active font-bold" : "bg-white text-secondary hover-bg-light"
                                            }`}
                                        style={{ textAlign: 'left', fontSize: '13px' }}
                                    >
                                        <span>All Partners</span>
                                        <span className={`d-flex align-items-center justify-content-center rounded-circle fw-bold ${selectedCategory === "All Partners" ? "count-badge-active" : "bg-light text-[#64748B]"}`}
                                            style={{ width: '24px', height: '24px', fontSize: '10px' }}>
                                            {totalCount}
                                        </span>
                                    </button>

                                    {categories.map((cat) => (
                                        <button
                                            key={cat.name}
                                            onClick={() => setSelectedCategory(cat.name)}
                                            className={`category-btn d-flex align-items-center justify-content-between px-3 py-2.5 rounded-3 border-0 shadow-sm ${selectedCategory === cat.name ? "active font-bold" : "bg-white text-secondary hover-bg-light"
                                                }`}
                                            style={{ textAlign: 'left', fontSize: '13px' }}
                                        >
                                            <span className="text-truncate" style={{ maxWidth: '80%' }}>{cat.name}</span>
                                            <span className={`d-flex align-items-center justify-content-center rounded-circle fw-bold ${selectedCategory === cat.name ? "count-badge-active" : "bg-light text-[#64748B]"}`}
                                                style={{ width: '24px', height: '24px', fontSize: '10px' }}>
                                                {cat.count}
                                            </span>
                                        </button>
                                    ))}
                                </div>

                                <div className="border-top my-4 opacity-50"></div>

                                <h3 className="widget_title mb-4" style={{ fontSize: '18px', fontWeight: '700', color: '#0F172A' }}>Filters</h3>
                                <div className="ps-2">
                                    <div className="form-check custom-checkbox mb-3">
                                        <input
                                            className="form-check-input border-secondary-subtle"
                                            type="checkbox"
                                            id="featuredOnly"
                                            style={{ width: '1.1em', height: '1.1em', cursor: 'pointer', borderRadius: '4px' }}
                                            checked={filters.featured}
                                            onChange={(e) => setFilters({ ...filters, featured: e.target.checked })}
                                        />
                                        <label className="form-check-label ms-2 text-dark font-medium" htmlFor="featuredOnly" style={{ fontSize: '14px', cursor: 'pointer' }}>
                                            Featured Only
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>

                    {/* Content Area */}
                    <div className="col-lg-8 col-xl-9">
                        <div className="mb-5 d-flex align-items-end justify-content-between border-bottom pb-4">
                            <div>
                                <span className="fw-bold text-uppercase tracking-widest" style={{ fontSize: '11px', color: themeGreen }}>Partners</span>
                                <h2 className="mb-0 mt-1" style={{ fontSize: '28px', fontWeight: '800', color: '#0F172A', letterSpacing: '-0.01em' }}>
                                    {selectedCategory}
                                </h2>
                                <p className="text-secondary mt-1 mb-0" style={{ fontSize: '13.5px' }}>
                                    Showing {filteredBrands.length} professional partners
                                </p>
                            </div>
                        </div>

                        <div className="row gy-4 gx-3">
                            {filteredBrands.map((brand, idx) => (
                                <div key={brand.id} className="col-sm-6 col-md-4 col-xl-3 fade-in-up" style={{ animationDelay: `${idx * 0.1}s` }}>
                                    <div className="partner-card bg-white border shadow-sm overflow-hidden position-relative" style={{ height: '100%', borderRadius: '14px' }}>
                                        {/* Main Card Link to Brand Landing Page */}
                                        <Link
                                            href={`/${brand.slug}`}
                                            className="d-block text-decoration-none"
                                        >
                                            <div className="p-[6px]">
                                                <div className={`d-flex align-items-center justify-content-center p-3 transition-all duration-500 ${bgColors[idx % bgColors.length]}`}
                                                    style={{ width: '100%', height: '110px', borderRadius: '20px' }}>
                                                    <img
                                                        src={brand.image || "/assets/default.png"}
                                                        alt={brand.name}
                                                        className="img-fluid transition-all duration-700"
                                                        style={{ maxWidth: '80%', maxHeight: '65px', objectFit: 'contain', filter: 'drop-shadow(0 4px 6px rgba(0,0,0,0.05))' }}
                                                    />
                                                </div>
                                            </div>
                                            <div className="px-2 pt-2 pb-3 text-center">
                                                <h4 className="mb-0 text-dark fw-bold transition-colors" style={{ fontSize: '13.5px' }}>{brand.name}</h4>
                                            </div>
                                        </Link>

                                        {/* Specific Link to Brand Product Page */}
                                        <div className="text-center pb-3 opacity-0 partner-card-hover-show transition-all duration-300 transform translate-y-2">
                                            <Link
                                                href={`/${brand.slug}/products`}
                                                className="fw-bold text-decoration-none"
                                                style={{ fontSize: '10px', color: themeGreen }}
                                            >
                                                View Products <i className="fa-light fa-arrow-right ms-1"></i>
                                            </Link>
                                        </div>

                                        <style>{`
                                            .partner-card:hover .partner-card-hover-show {
                                                opacity: 1;
                                                transform: translateY(-4px);
                                            }
                                            .partner-card:hover h4 {
                                                color: ${themeGreen} !important;
                                            }
                                            .partner-card:hover img {
                                                transform: scale(1.1);
                                            }
                                        `}</style>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {filteredBrands.length === 0 && (
                            <div className="bg-white rounded-4 p-5 text-center shadow-sm mt-4 border border-dashed">
                                <div className="bg-light rounded-circle d-inline-flex p-4 mb-4">
                                    <i className="fa-light fa-search text-secondary" style={{ fontSize: '32px' }}></i>
                                </div>
                                <h4 className="text-dark fw-bold">No partners found</h4>
                                <p className="text-secondary mb-4" style={{ fontSize: '14px' }}>Try adjusting your filters or category selection.</p>
                                <button
                                    onClick={() => {
                                        setSelectedCategory("All Partners");
                                        setFilters({ featured: false });
                                    }}
                                    className="btn rounded-pill px-4 py-2 mt-2 fw-bold text-white"
                                    style={{ backgroundColor: themeGreen }}
                                >
                                    Reset Discovery
                                </button>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
