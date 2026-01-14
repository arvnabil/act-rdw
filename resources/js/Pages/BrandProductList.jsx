import { Head, Link, router } from "@inertiajs/react";
import React, { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

const BrandProductList = ({
    brand,
    products,
    categories,
    serviceSolutions = [],
    serviceItemLabel = "Solutions",
    filters = {},
}) => {
    const [showFilters, setShowFilters] = useState(false);

    // Helpers
    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) return path;
        return `/storage/${path}`;
    };

    const handleFilterChange = (key, value) => {
        const newFilters = { ...filters, [key]: value };
        // Reset page on filter change
        if (key !== "page") delete newFilters.page;

        router.get(
            route("brand.products", { brandSlug: brand.slug }),
            newFilters,
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    // Find current category for Hero Section
    const currentCategorySlug = filters.category;
    const currentCategory = categories.find(
        (c) => c.slug === currentCategorySlug
    );

    // Default Hero Content (if no category selected, show Brand general info)
    const heroContent = currentCategory
        ? {
              title: currentCategory.name,
              desc: `Explore ${currentCategory.name} solutions from ${brand.name}.`,
              image: getImageUrl(brand.logo_path), //Ideally a category specific image if available
          }
        : {
              title: `${brand.name} Products`,
              desc: `Browse our extensive collection of ${brand.name} technology solutions.`,
              image: getImageUrl(brand.logo_path),
          };

    return (
        <MainLayout>
            <Head title={`${brand.name} Products`} />

            <Breadcrumb
                title={heroContent.title}
                subtitle={`${brand.name} Products`}
                items={[
                    { label: "Home", link: "/" },
                    {
                        label: brand.name,
                        link: `/${brand.slug || brand.name.toLowerCase()}`,
                    },
                    { label: "Products" },
                ]}
            />

            <div className="product-list-area space-top space-extra-bottom">
                <div className="container th-container">
                    {/* Controls Component */}
                    <div className="controls-area mb-5">
                        {/* Row 1: Buttons */}
                        <div className="d-flex justify-content-between align-items-center mb-3">
                            <button
                                onClick={() => setShowFilters(!showFilters)}
                                className="th-btn style1 th-radius th-icon text-capitalize"
                                style={{ padding: "12px 30px" }}
                            >
                                <i className="fa-regular fa-filter me-2"></i>{" "}
                                All Filters
                            </button>

                            <div
                                className="sort-dropdown"
                                style={{ minWidth: "200px" }}
                            >
                                <select
                                    className="form-select th-radius bg-white border shadow-sm"
                                    style={{
                                        cursor: "pointer",
                                        padding: "12px 20px",
                                        backgroundImage: `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e")`, // Custom chevron
                                        backgroundPosition: "right 1rem center",
                                        backgroundSize: "16px 12px",
                                    }}
                                    value={filters.sort || "newest"}
                                    onChange={(e) =>
                                        handleFilterChange(
                                            "sort",
                                            e.target.value
                                        )
                                    }
                                >
                                    <option value="newest">
                                        Sort By: Newest
                                    </option>
                                    <option value="price_asc">
                                        Price: Low to High
                                    </option>
                                    <option value="price_desc">
                                        Price: High to Low
                                    </option>
                                </select>
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
                                                    (c) =>
                                                        c.slug ===
                                                        filters.category
                                                )?.name || filters.category}
                                            </span>
                                            <button
                                                onClick={() =>
                                                    handleFilterChange(
                                                        "category",
                                                        ""
                                                    )
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
                                                    (s) =>
                                                        s.slug ===
                                                        filters.service_item
                                                )?.name || filters.service_item}
                                            </span>
                                            <button
                                                onClick={() =>
                                                    handleFilterChange(
                                                        "service_item",
                                                        ""
                                                    )
                                                }
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
                                                { preserveScroll: true }
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
                                Menampilkan {products.data.length} dari{" "}
                                {products.total} produk
                            </span>
                        </div>
                    </div>

                    <div className="row">
                        {/* Filter Sidebar (Conditional or always visible on large screens? Requirements imply specific "All Filter" button like Logitech. Logitech uses an offcanvas or push down. We'll use a simple toggle column for now to be cleaner) */}
                        {showFilters && (
                            <div className="col-lg-3 mb-4 mb-lg-0 wow fadeInUp">
                                <div className="widget widget_categories th-radius shadow-sm p-4 border mb-4">
                                    <h3 className="widget_title">Categories</h3>
                                    <ul>
                                        <li>
                                            <a
                                                href="#"
                                                onClick={(e) => {
                                                    e.preventDefault();
                                                    handleFilterChange(
                                                        "category",
                                                        ""
                                                    );
                                                }}
                                                className={
                                                    !filters.category
                                                        ? "active fw-bold text-theme"
                                                        : ""
                                                }
                                            >
                                                All Categories
                                            </a>
                                        </li>
                                        {categories.map((cat) => (
                                            <li key={cat.id}>
                                                <a
                                                    href="#"
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        handleFilterChange(
                                                            "category",
                                                            cat.slug
                                                        );
                                                    }}
                                                    className={
                                                        filters.category ===
                                                        cat.slug
                                                            ? "active fw-bold text-theme"
                                                            : ""
                                                    }
                                                >
                                                    {cat.name}{" "}
                                                    <span className="float-end text-muted">
                                                        ({cat.count})
                                                    </span>
                                                </a>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                {serviceSolutions.length > 0 && (
                                    <div className="widget widget_categories th-radius shadow-sm p-4 border">
                                        <h3 className="widget_title">
                                            {serviceItemLabel}
                                        </h3>
                                        <ul>
                                            <li>
                                                <a
                                                    href="#"
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        handleFilterChange(
                                                            "service_item",
                                                            ""
                                                        );
                                                    }}
                                                    className={
                                                        !filters.service_item
                                                            ? "active fw-bold text-theme"
                                                            : ""
                                                    }
                                                >
                                                    All {serviceItemLabel}
                                                </a>
                                            </li>
                                            {serviceSolutions.map((sol) => (
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
                                                            filters.service_item ===
                                                            sol.slug
                                                                ? "active fw-bold text-theme"
                                                                : ""
                                                        }
                                                    >
                                                        {sol.name}{" "}
                                                        <span className="float-end text-muted">
                                                            ({sol.count})
                                                        </span>
                                                    </a>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                )}
                            </div>
                        )}

                        <div className={showFilters ? "col-lg-9" : "col-lg-12"}>
                            {/* Hero Card for Category */}
                            <div
                                className="category-hero th-radius mb-50 position-relative overflow-hidden d-flex align-items-center"
                                style={{
                                    backgroundColor: "#333", // Dark background like Logitech
                                    minHeight: "300px",
                                    padding: "40px",
                                }}
                            >
                                <div className="row align-items-center w-100 m-0">
                                    <div className="col-md-6 z-index-common">
                                        <h2 className="text-white mb-3 text-capitalize">
                                            {heroContent.title}
                                        </h2>
                                        <p className="text-white-50 mb-0 fs-5">
                                            {heroContent.desc}
                                        </p>
                                    </div>
                                    <div className="col-md-6 position-relative text-center">
                                        {/* Abstract circle or geometric shape background if possible */}
                                        <div
                                            className="position-absolute top-50 start-50 translate-middle rounded-circle"
                                            style={{
                                                width: "250px",
                                                height: "250px",
                                                backgroundColor:
                                                    "rgba(255,255,255,0.1)",
                                            }}
                                        ></div>
                                        <img
                                            src={heroContent.image}
                                            alt={heroContent.title}
                                            className="position-relative z-index-common"
                                            style={{
                                                maxHeight: "200px",
                                                objectFit: "contain",
                                            }}
                                        />
                                    </div>
                                </div>
                            </div>

                            {/* Product Grid */}
                            <div className="row gy-4">
                                {products.data.map((product, index) => (
                                    <div
                                        key={product.id}
                                        className="col-xl-4 col-lg-6 col-md-6 col-sm-6"
                                    >
                                        <div className="th-product product-grid-style2 th-radius border h-100 d-flex flex-column justify-content-between p-0 overflow-hidden bg-white">
                                            {/* Like Button */}
                                            <div className="position-absolute top-0 end-0 p-3 z-index-common">
                                                <button className="icon-btn bg-transparent text-body border-0 shadow-none">
                                                    <i className="fa-light fa-heart"></i>
                                                </button>
                                            </div>

                                            <div
                                                className="product-img text-center p-4"
                                                style={{
                                                    height: "280px",
                                                    display: "flex",
                                                    alignItems: "center",
                                                    justifyContent: "center",
                                                    backgroundColor: "#F8F9FA",
                                                }}
                                            >
                                                <Link
                                                    href={route(
                                                        "products.show",
                                                        product.slug ||
                                                            product.id
                                                    )}
                                                >
                                                    <img
                                                        src={getImageUrl(
                                                            product.image_path
                                                        )}
                                                        alt={product.name}
                                                        style={{
                                                            maxHeight: "200px",
                                                            maxWidth: "100%",
                                                            objectFit:
                                                                "contain",
                                                        }}
                                                    />
                                                </Link>
                                            </div>
                                            <div className="product-content p-4 text-center">
                                                <div className="color-swatches mb-3 d-flex justify-content-center gap-2">
                                                    {[
                                                        "#333",
                                                        "#ccc",
                                                        "#e91e63",
                                                    ].map(
                                                        (
                                                            color,
                                                            i // Dummy Colors for visual likeness
                                                        ) => (
                                                            <span
                                                                key={i}
                                                                className="rounded-circle d-inline-block border"
                                                                style={{
                                                                    width: "12px",
                                                                    height: "12px",
                                                                    backgroundColor:
                                                                        color,
                                                                }}
                                                            ></span>
                                                        )
                                                    )}
                                                </div>
                                                <h3 className="product-title fs-5 mb-2">
                                                    <Link
                                                        href={route(
                                                            "products.show",
                                                            product.slug ||
                                                                product.id
                                                        )}
                                                        className="text-inherit"
                                                    >
                                                        {product.name}
                                                    </Link>
                                                </h3>
                                                <p className="product-category text-muted small mb-0">
                                                    {product.service?.name}
                                                </p>

                                                {/* Price - Dummy if not available */}
                                                {/* <span className="price d-block mt-2 fw-bold text-dark">Rp 3.500.000</span> */}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* Pagination */}
                            {products.links && products.links.length > 3 && (
                                <div className="th-pagination text-center mt-50">
                                    <ul>
                                        {products.links.map((link, i) => (
                                            <li key={i}>
                                                {link.url ? (
                                                    <Link
                                                        href={link.url}
                                                        className={
                                                            link.active
                                                                ? "active"
                                                                : ""
                                                        }
                                                        dangerouslySetInnerHTML={{
                                                            __html: link.label,
                                                        }}
                                                    ></Link>
                                                ) : (
                                                    <span
                                                        className="text-muted"
                                                        style={{
                                                            display:
                                                                "inline-block", // Match typical pagination item display
                                                            padding: "0 15px", // Match padding if necessary, or just rely on parent CSS
                                                        }}
                                                        dangerouslySetInnerHTML={{
                                                            __html: link.label,
                                                        }}
                                                    ></span>
                                                )}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .active.text-theme { color: #E8B4B4 !important; } /* Logitech Pinkish Accents */
                .product-grid-style2 { transition: box-shadow 0.3s ease; }
                .product-grid-style2:hover { box-shadow: 0 10px 30px rgba(0,0,0,0.08); }

                /* Filter Chip Styles */
                .fs-xs { font-size: 11px; }
                .hover-red:hover { color: #dc3545 !important; } /* Bootstrap Danger Red */
                .hover-dark:hover { background-color: #212529 !important; color: #fff !important; }
                .cursor-pointer { cursor: pointer; }
                .filter-chip { transition: all 0.2s; }
                .filter-chip:hover { border-color: #dee2e6 !important; background-color: #f8f9fa !important; }
                .sort-dropdown .form-select:focus { border-color: #E8B4B4; box-shadow: 0 0 0 0.25rem rgba(232, 180, 180, 0.25); }
            `}</style>
        </MainLayout>
    );
};

export default BrandProductList;
