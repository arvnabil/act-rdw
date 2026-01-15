import React from "react";
import { Link } from "@inertiajs/react";

export default function ProductList({ products, viewMode }) {
    return (
        <div className="row gy-40">
            <div className="tab-content" id="nav-tabContent">
                <div
                    className={`tab-pane fade ${
                        viewMode === "grid" ? "active show" : ""
                    }`}
                    id="tab-grid"
                >
                    <div className="row gy-40">
                        {products.data.map((product) => (
                            <div className="col-xl-4 col-sm-6" key={product.id}>
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
                                            {product.service?.name || "General"}
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
                        {products.data.map((product) => (
                            <div className="col-md-6" key={product.id}>
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
                                            {product.service?.name || "General"}
                                        </span>
                                        <h3 className="product-title">
                                            <Link
                                                href={`/products/${product.slug}`}
                                            >
                                                {product.name}
                                            </Link>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
