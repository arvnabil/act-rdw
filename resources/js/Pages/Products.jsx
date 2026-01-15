import React, { useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProductToolbar from "@/Components/Sections/Products/ProductToolbar";
import ProductList from "@/Components/Sections/Products/ProductList";
import ProductPagination from "@/Components/Sections/Products/ProductPagination";

export default function Products({ products, filters }) {
    // State for View Mode (Grid vs List)
    const [viewMode, setViewMode] = useState("grid"); // 'grid' or 'list'
    const [search, setSearch] = useState(filters?.search || "");
    const [orderby, setOrderby] = useState(filters?.orderby || "menu_order");

    // Handle Search Submit
    const handleSearch = (e) => {
        e.preventDefault();
        router.get("/products", { search, orderby }, { preserveState: true });
    };

    // Handle Sort Change
    const handleSortChange = (e) => {
        const value = e.target.value;
        setOrderby(value);
        router.get(
            "/products",
            { search, orderby: value },
            { preserveState: true }
        );
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
                    />

                    <ProductList products={products} viewMode={viewMode} />

                    <ProductPagination links={products.links} />
                </div>
            </section>
        </MainLayout>
    );
}
