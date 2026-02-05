import React, { useState } from "react";
import { Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProductToolbar from "@/Components/Sections/Products/ProductToolbar";
import ProductList from "@/Components/Sections/Products/ProductList";
import ProductPagination from "@/Components/Sections/Products/ProductPagination";
import ProductFilterSidebar from "@/Components/Sections/Products/ProductFilterSidebar";
import Seo from "@/Components/Common/Seo";

export default function Products({
    products,
    filters,
    brands,
    solutions,
    categories,
    seo,
}) {
    // State for View Mode (Grid vs List)
    const [viewMode, setViewMode] = useState("grid"); // 'grid' or 'list'
    const [search, setSearch] = useState(filters?.search || "");
    const [orderby, setOrderby] = useState(filters?.orderby || "menu_order");
    const [selectedBrand, setSelectedBrand] = useState(filters?.brand || "");
    const [selectedSolution, setSelectedSolution] = useState(
        filters?.solution || "",
    );
    const [selectedCategory, setSelectedCategory] = useState(
        filters?.category || "",
    );
    const [showFilters, setShowFilters] = useState(false);

    // Unified Filter Change Handler
    const handleFilterChange = (key, value) => {
        let newBrand = selectedBrand;
        let newSolution = selectedSolution;
        let newCategory = selectedCategory;

        if (key === "brand") {
            setSelectedBrand(value);
            newBrand = value;
        }
        if (key === "solution") {
            setSelectedSolution(value);
            newSolution = value;
        }
        if (key === "category") {
            setSelectedCategory(value);
            newCategory = value;
        }

        router.get(
            "/products",
            {
                search,
                orderby,
                brand: newBrand,
                solution: newSolution,
                category: newCategory,
            },
            { preserveState: true },
        );
    };

    // Handle Search Submit
    const handleSearch = (e) => {
        e.preventDefault();
        router.get(
            "/products",
            {
                search,
                orderby,
                brand: selectedBrand,
                solution: selectedSolution,
                category: selectedCategory,
            },
            { preserveState: true },
        );
    };

    // Handle Sort Change
    const handleSortChange = (e) => {
        const value = e.target.value;
        setOrderby(value);
        router.get(
            "/products",
            {
                search,
                orderby: value,
                brand: selectedBrand,
                solution: selectedSolution,
                category: selectedCategory,
            },
            { preserveState: true },
        );
    };

    // Keep legacy handlers for backward compatibility if needed, map to unified
    const handleBrandChange = (e) =>
        handleFilterChange("brand", e.target.value);
    const handleSolutionChange = (e) =>
        handleFilterChange("solution", e.target.value);
    const handleCategoryChange = (e) =>
        handleFilterChange("category", e.target.value);

    // Handle Remove Single Filter
    const handleRemoveFilter = (key) => {
        if (key === "search") {
            setSearch("");
            router.get(
                "/products",
                {
                    search: "",
                    orderby,
                    brand: selectedBrand,
                    solution: selectedSolution,
                    category: selectedCategory,
                },
                { preserveState: true },
            );
        } else {
            handleFilterChange(key, "");
        }
    };

    // Handle Reset All Filters
    const handleResetFilters = () => {
        setSearch("");
        setSelectedBrand("");
        setSelectedSolution("");
        setSelectedCategory("");
        router.get("/products", { orderby }, { preserveState: true });
    };

    return (
        <MainLayout>
            <Seo seo={seo} />

            <Breadcrumb
                title="All Products"
                items={[
                    { label: "Home", link: "/" },
                    { label: "All Products" },
                ]}
            />

            {/* Product Area */}
            <section className="space-extra-bottom space">
                <div className="container">
                    <ProductToolbar
                        search={search}
                        setSearch={setSearch}
                        handleSearch={handleSearch}
                        viewMode={viewMode}
                        setViewMode={setViewMode}
                        orderby={orderby}
                        handleSortChange={handleSortChange}
                        showFilters={showFilters}
                        setShowFilters={setShowFilters}
                        // Filters
                        brands={brands}
                        solutions={solutions}
                        categories={categories}
                        selectedBrand={selectedBrand}
                        selectedSolution={selectedSolution}
                        selectedCategory={selectedCategory}
                        // Handlers
                        handleBrandChange={handleBrandChange}
                        handleSolutionChange={handleSolutionChange}
                        handleCategoryChange={handleCategoryChange}
                        handleRemoveFilter={handleRemoveFilter}
                        handleResetFilters={handleResetFilters}
                    />

                    <div className="row">
                        <ProductFilterSidebar
                            showFilters={showFilters}
                            filters={{
                                brand: selectedBrand,
                                category: selectedCategory,
                                solution: selectedSolution,
                            }}
                            handleFilterChange={handleFilterChange}
                            categories={categories}
                            brands={brands}
                            solutions={solutions}
                        />

                        <div className={showFilters ? "col-lg-9" : "col-lg-12"}>
                            <ProductList
                                products={products}
                                viewMode={viewMode}
                            />
                            <ProductPagination links={products.links} />
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
