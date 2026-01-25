import React from "react";
import { Link } from "@inertiajs/react";

export default function ProductCard({ product, className = "" }) {
    // Helper to resolve image URL
    const getImageUrl = (path) => {
        if (!path) return "/assets/img/product/product_1_1.png";
        if (path.startsWith("http") || path.startsWith("/assets")) return path;
        return `/storage/${path}`;
    };

    return (
        <div
            className={`product-card-modern ${className}`}
            style={{
                borderRadius: "20px",
                background: "#ffffff",
                boxShadow: "0 4px 20px rgba(0,0,0,0.06)",
                overflow: "hidden",
                transition: "all 0.3s ease",
                height: "100%",
                display: "flex",
                flexDirection: "column",
                border: "1px solid rgba(0,0,0,0.03)",
                cursor: "pointer",
            }}
        >
            <Link
                href={`/products/${product.slug}`}
                className="d-block h-100 text-decoration-none"
            >
                {/* Image Area */}
                <div
                    className="img-wrapper position-relative"
                    style={{
                        height: "260px",
                        backgroundColor: "#F8F9FA", // Light gray background
                        display: "flex",
                        alignItems: "center",
                        justifyContent: "center",
                        padding: "20px",
                        overflow: "hidden",
                    }}
                >
                    <img
                        src={getImageUrl(product.image_path)}
                        alt={product.name}
                        className="product-img"
                        style={{
                            maxWidth: "100%",
                            maxHeight: "100%",
                            objectFit: "contain", // Ensure transparent PNGs float nicely
                            transition: "transform 0.4s ease",
                        }}
                    />

                    {/* Optional Badge */}
                    {product.is_active && (
                        <span
                            className="position-absolute top-0 start-0 m-3 px-3 py-1 bg-white rounded-pill text-success fw-bold shadow-sm"
                            style={{ fontSize: "0.75rem" }}
                        >
                            Active
                        </span>
                    )}
                </div>

                {/* Content Area */}
                <div
                    className="content-wrapper p-4 d-flex flex-column flex-grow-1"
                    style={{ backgroundColor: "#ffffff" }}
                >
                    {/* Category / Meta */}
                    <div className="mb-2">
                        <span
                            className="text-uppercase fw-bold text-muted"
                            style={{
                                fontSize: "0.75rem",
                                letterSpacing: "1px",
                            }}
                        >
                            {product.category?.name ||
                                product.service?.name ||
                                "General"}
                        </span>
                    </div>

                    {/* Title */}
                    <h3
                        className="product-title mb-3 text-dark fw-bold"
                        style={{
                            fontSize: "1.1rem",
                            lineHeight: "1.4",
                            overflow: "hidden",
                            textOverflow: "ellipsis",
                            display: "-webkit-box",
                            WebkitLineClamp: "2",
                            WebkitBoxOrient: "vertical",
                        }}
                    >
                        {product.name}
                    </h3>

                    {/* Service Badge (if applicable) */}
                    <div className="mt-auto">
                        <span
                            className="d-inline-flex align-items-center text-muted"
                            style={{ fontSize: "0.85rem" }}
                        >
                            <span
                                className="rounded-circle bg-success me-2"
                                style={{ width: "8px", height: "8px" }}
                            ></span>
                            View Details
                        </span>
                    </div>
                </div>
            </Link>

            <style jsx="true">{`
                .product-card-modern:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
                }
                .product-card-modern:hover .product-img {
                    transform: scale(1.08);
                }
                .product-card-modern:hover .product-title {
                    color: #198754 !important; /* Bootstrap success color or brand green */
                    transition: color 0.3s ease;
                }
            `}</style>
        </div>
    );
}
