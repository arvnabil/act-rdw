import { Link, router } from "@inertiajs/react";
import React, { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProductFilterTopBar from "@/Components/Sections/BrandProduct/ProductFilterTopBar";
import ProductFilterSidebar from "@/Components/Sections/BrandProduct/ProductFilterSidebar";
import CategoryHero from "@/Components/Sections/BrandProduct/CategoryHero";
import ProductList from "@/Components/Sections/BrandProduct/ProductList";
import Seo from "@/Components/Common/Seo";

const BrandProductList = ({
    brand,
    products,
    categories,
    serviceSolutions = [],
    serviceItemLabel = "Solutions",
    filters = {},
    seo,
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
            },
        );
    };

    // Find current category for Hero Section
    const currentCategorySlug = filters.category;
    const currentCategory = categories.find(
        (c) => c.slug === currentCategorySlug,
    );

    // Default Hero Content (if no category selected, show Brand general info)
    const heroContent = currentCategory
        ? {
            title: currentCategory.name,
            desc: `Explore ${currentCategory.name} solutions from ${brand.name}.`,
            image: getImageUrl(currentCategory.image || brand.logo_path), // Use Category Icon if available
        }
        : {
            title: `${brand.name} Products`,
            desc: `Browse our extensive collection of ${brand.name} technology solutions.`,
            image: getImageUrl(brand.logo_path),
        };

    return (
        <MainLayout>
            <Seo seo={seo} />

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
                    {/* Hero Card for Category - Moved to Top */}
                    <div className="mb-4">
                        <CategoryHero heroContent={heroContent} />
                    </div>

                    {/* Controls Component */}
                    <ProductFilterTopBar
                        showFilters={showFilters}
                        setShowFilters={setShowFilters}
                        filters={filters}
                        handleFilterChange={handleFilterChange}
                        categories={categories}
                        serviceSolutions={serviceSolutions}
                        products={products}
                        brand={brand}
                    />

                    <div className="row">
                        {/* Filter Sidebar */}
                        <ProductFilterSidebar
                            showFilters={showFilters}
                            filters={filters}
                            handleFilterChange={handleFilterChange}
                            categories={categories}
                            serviceSolutions={serviceSolutions}
                            serviceItemLabel={serviceItemLabel}
                        />

                        <div className={showFilters ? "col-lg-9" : "col-lg-12"}>
                            {/* Product Grid */}
                            <ProductList
                                products={products}
                                getImageUrl={getImageUrl}
                            />
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
