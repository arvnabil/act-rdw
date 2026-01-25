import React, { useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProductToolbar from "@/Components/Sections/Products/ProductToolbar";
import ProductList from "@/Components/Sections/Products/ProductList";
import ProductPagination from "@/Components/Sections/Products/ProductPagination";

export default function Products({
    products,
    filters,
    brands,
    solutions,
    categories,
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

    // Handle Filter Changes
    const handleBrandChange = (e) => {
        const value = e.target.value;
        setSelectedBrand(value);
        router.get(
            "/products",
            {
                search,
                orderby,
                brand: value,
                solution: selectedSolution,
                category: selectedCategory,
            },
            { preserveState: true },
        );
    };

    const handleSolutionChange = (e) => {
        const value = e.target.value;
        setSelectedSolution(value);
        router.get(
            "/products",
            {
                search,
                orderby,
                brand: selectedBrand,
                solution: value,
                category: selectedCategory,
            },
            { preserveState: true },
        );
    };

    const handleCategoryChange = (e) => {
        const value = e.target.value;
        setSelectedCategory(value);
        router.get(
            "/products",
            {
                search,
                orderby,
                brand: selectedBrand,
                solution: selectedSolution,
                category: value,
            },
            { preserveState: true },
        );
    };

    // Handle Remove Single Filter
    const handleRemoveFilter = (key) => {
        let newSearch = search;
        let newBrand = selectedBrand;
        let newSolution = selectedSolution;
        let newCategory = selectedCategory;

        if (key === "search") {
            setSearch("");
            newSearch = "";
        } else if (key === "brand") {
            setSelectedBrand("");
            newBrand = "";
        } else if (key === "solution") {
            setSelectedSolution("");
            newSolution = "";
        } else if (key === "category") {
            setSelectedCategory("");
            newCategory = "";
        }

        router.get(
            "/products",
            {
                search: newSearch,
                orderby,
                brand: newBrand,
                solution: newSolution,
                category: newCategory,
            },
            { preserveState: true },
        );
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
            <Head title="Our Products" />

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
                        // Filters
                        brands={brands}
                        solutions={solutions}
                        categories={categories}
                        selectedBrand={selectedBrand}
                        selectedSolution={selectedSolution}
                        selectedCategory={selectedCategory}
                        handleBrandChange={handleBrandChange}
                        handleSolutionChange={handleSolutionChange}
                        handleCategoryChange={handleCategoryChange}
                        // Active Filters
                        handleRemoveFilter={handleRemoveFilter}
                        handleResetFilters={handleResetFilters}
                    />

                    <ProductList products={products} viewMode={viewMode} />

                    <ProductPagination links={products.links} />
                </div>
            </section>
        </MainLayout>
    );
}
