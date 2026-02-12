import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function BrandCategoryListSection({
    categories,
    brand,
    getBrandSlug,
    getImageUrl,
    config,
}) {
    return (
        <section className="space" id="all-categories">
            <div className="container th-container">
                <div className="title-area text-start mb-40">
                    <SectionTitle
                        subTitle="Kategori"
                        title={config?.title || "Semua Kategori"}
                        align="title-area service-title-box text-center"
                    />
                </div>

                <div className="row justify-content-center gy-4 row-cols-2 row-cols-md-4 row-cols-lg-6">
                    {categories && categories.length > 0 ? (
                        categories.map((cat, index) => (
                            <div key={index} className="col">
                                <Link
                                    href={`/${getBrandSlug(
                                        brand,
                                    )}/products?category=${cat.slug || cat.id}`}
                                >
                                    <div
                                        className="category-card py-4 px-2 text-center h-100 d-flex flex-column align-items-center justify-content-center"
                                        style={{
                                            backgroundColor: "#F3F4F6",
                                            borderRadius: "10px",
                                            transition: "all 0.3s ease",
                                            cursor: "pointer",
                                            minHeight: "220px",
                                        }}
                                    >
                                        <div
                                            className="cat-img-wrapper mb-3 d-flex align-items-center justify-content-center"
                                            style={{
                                                height: "160px",
                                                width: "100%",
                                                backgroundColor: !cat.image ? "white" : "transparent",
                                                borderRadius: "15px",
                                                padding: !cat.image ? "15px" : "0",
                                                boxShadow: !cat.image ? "0 5px 15px rgba(0,0,0,0.05)" : "none"
                                            }}
                                        >
                                            <img
                                                src={getImageUrl(cat.image, "/assets/default.png")}
                                                alt={cat.name}
                                                style={{
                                                    maxHeight: "100%",
                                                    maxWidth: "100%",
                                                    objectFit: "contain",
                                                }}
                                            />
                                        </div>
                                        <h6
                                            className="fw-medium text-dark mb-0"
                                            style={{ fontSize: "0.9rem" }}
                                        >
                                            {cat.name}
                                        </h6>
                                    </div>
                                </Link>
                            </div>
                        ))
                    ) : (
                        <div className="col-12 text-center text-muted">
                            No categories found.
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
}
