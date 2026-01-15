import React from "react";
import { Link } from "@inertiajs/react";

export default function ProductList({ products, getImageUrl }) {
    return (
        <div className="row gy-4">
            {products.data.map((product) => (
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
                                    product.slug || product.id
                                )}
                            >
                                <img
                                    src={getImageUrl(product.image_path)}
                                    alt={product.name}
                                    style={{
                                        maxHeight: "200px",
                                        maxWidth: "100%",
                                        objectFit: "contain",
                                    }}
                                />
                            </Link>
                        </div>
                        <div className="product-content p-4 text-center">
                            <div className="color-swatches mb-3 d-flex justify-content-center gap-2">
                                {["#333", "#ccc", "#e91e63"].map((color, i) => (
                                    <span
                                        key={i}
                                        className="rounded-circle d-inline-block border"
                                        style={{
                                            width: "12px",
                                            height: "12px",
                                            backgroundColor: color,
                                        }}
                                    ></span>
                                ))}
                            </div>
                            <h3 className="product-title fs-5 mb-2">
                                <Link
                                    href={route(
                                        "products.show",
                                        product.slug || product.id
                                    )}
                                    className="text-inherit"
                                >
                                    {product.name}
                                </Link>
                            </h3>
                            <p className="product-category text-muted small mb-0">
                                {product.service?.name}
                            </p>
                        </div>
                    </div>
                </div>
            ))}

            {/* Pagination */}
            {products.links && products.links.length > 3 && (
                <div className="th-pagination text-center mt-50">
                    <ul>
                        {products.links.map((link, i) => (
                            <li key={i}>
                                {link.url ? (
                                    <Link
                                        href={link.url}
                                        className={link.active ? "active" : ""}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    ></Link>
                                ) : (
                                    <span
                                        className="text-muted"
                                        style={{
                                            display: "inline-block",
                                            padding: "0 15px",
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
    );
}
