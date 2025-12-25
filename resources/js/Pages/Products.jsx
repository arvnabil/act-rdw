import React, { useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

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
                    <div className="th-sort-bar">
                        <div className="row justify-content-between align-items-center">
                            <div className="col-md-4">
                                <div className="search-form-area">
                                    <form
                                        className="search-form"
                                        onSubmit={handleSearch}
                                    >
                                        <input
                                            type="text"
                                            placeholder="Search products..."
                                            value={search}
                                            onChange={(e) =>
                                                setSearch(e.target.value)
                                            }
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
                                                viewMode === "grid"
                                                    ? "active"
                                                    : ""
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
                                                viewMode === "list"
                                                    ? "active"
                                                    : ""
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
                                            <option value="name">
                                                Sort by Name (A-Z)
                                            </option>
                                            <option value="date">
                                                Sort by Latest
                                            </option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-40">
                        <div className="tab-content" id="nav-tabContent">
                            <div
                                className={`tab-pane fade ${
                                    viewMode === "grid" ? "active show" : ""
                                }`}
                                id="tab-grid"
                            >
                                <div className="row gy-40">
                                    {products.data.map((product, index) => (
                                        <div
                                            className="col-xl-4 col-sm-6"
                                            key={product.id}
                                        >
                                            <div className="th-product product-grid">
                                                <div className="product-img">
                                                    <img
                                                        src={
                                                            product.image_path ||
                                                            "/assets/img/product/product_1_1.png"
                                                        }
                                                        alt={product.name}
                                                    />
                                                    {product.is_active && (
                                                        <span className="product-tag">
                                                            Active
                                                        </span>
                                                    )}
                                                    <div className="actions">
                                                        <Link
                                                            href={`/products/${product.slug}`}
                                                            className="icon-btn"
                                                        >
                                                            <i className="far fa-eye"></i>
                                                        </Link>
                                                        <a
                                                            href="#"
                                                            className="icon-btn"
                                                            onClick={(e) =>
                                                                e.preventDefault()
                                                            }
                                                        >
                                                            <i className="far fa-phone"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div className="product-content">
                                                    <h3 className="product-title">
                                                        <Link
                                                            href={`/products/${product.slug}`}
                                                        >
                                                            {product.name}
                                                        </Link>
                                                    </h3>
                                                    <span className="category">
                                                        {product.service
                                                            ?.name || "General"}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                            <div
                                className={`tab-pane fade ${
                                    viewMode === "list" ? "active show" : ""
                                }`}
                                id="tab-list"
                            >
                                <div className="row gy-30">
                                    {products.data.map((product, index) => (
                                        <div
                                            className="col-md-6"
                                            key={product.id}
                                        >
                                            <div className="th-product list-view">
                                                <div className="product-img">
                                                    <img
                                                        src={
                                                            product.image_path ||
                                                            "/assets/img/product/product_1_1.png"
                                                        }
                                                        alt={product.name}
                                                    />
                                                    <div className="actions">
                                                        <Link
                                                            href={`/products/${product.slug}`}
                                                            className="icon-btn"
                                                        >
                                                            <i className="far fa-eye"></i>
                                                        </Link>
                                                    </div>
                                                </div>
                                                <div className="product-content">
                                                    <span className="category">
                                                        {product.service
                                                            ?.name || "General"}
                                                    </span>
                                                    <h3 className="product-title">
                                                        <Link
                                                            href={`/products/${product.slug}`}
                                                        >
                                                            {product.name}
                                                        </Link>
                                                    </h3>
                                                    {/* Rating removed as it is not in DB yet */}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="th-pagination text-center pt-50">
                        <ul>
                            {products.links.map((link, index) => {
                                let label = link.label;
                                let className = link.active ? "active" : "";

                                if (link.label.includes("&laquo; Previous")) {
                                    label = '<i class="far fa-arrow-left"></i>';
                                } else if (
                                    link.label.includes("Next &raquo;")
                                ) {
                                    label =
                                        '<i class="far fa-arrow-right"></i>';
                                    className = "next-page";
                                }

                                return (
                                    <li key={index}>
                                        {link.url ? (
                                            <Link
                                                href={link.url}
                                                className={className}
                                                dangerouslySetInnerHTML={{
                                                    __html: label,
                                                }}
                                                preserveScroll
                                            />
                                        ) : (
                                            <a
                                                href="#"
                                                className="disabled"
                                                onClick={(e) =>
                                                    e.preventDefault()
                                                }
                                                dangerouslySetInnerHTML={{
                                                    __html: label,
                                                }}
                                            ></a>
                                        )}
                                    </li>
                                );
                            })}
                        </ul>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
