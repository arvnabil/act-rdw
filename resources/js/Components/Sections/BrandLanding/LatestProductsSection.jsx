import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Autoplay } from "swiper/modules";
import SectionTitle from "@/Components/Common/SectionTitle";
import "swiper/css";

export default function LatestProductsSection({
    products,
    getImageUrl,
    config,
}) {
    if (products && config?.count) {
        products = products.slice(0, config.count);
    }
    return (
        <section
            className="space-top space-extra-bottom bg-white"
            id="latest-products"
        >
            <div className="container th-container">
                <SectionTitle
                    subTitle="New"
                    title={config?.title || "Products"}
                    align="text-center"
                />

                <Swiper
                    modules={[Navigation, Autoplay]}
                    spaceBetween={24}
                    slidesPerView={1}
                    autoplay={{
                        delay: 3000,
                        disableOnInteraction: false,
                    }}
                    breakpoints={{
                        575: { slidesPerView: 2 },
                        992: { slidesPerView: 3 },
                        1200: { slidesPerView: 4 },
                    }}
                    className="latest-products-slider"
                    style={{ paddingBottom: "40px" }}
                >
                    {products && products.length > 0 ? (
                        products.map((product, i) => (
                            <SwiperSlide key={i}>
                                <div
                                    className="product-card-gradient position-relative overflow-hidden w-100"
                                    style={{
                                        borderRadius: "30px",
                                        aspectRatio: "4/5",
                                        cursor: "pointer",
                                        background:
                                            "linear-gradient(180deg, #F3F4F6 0%, #D1D5DB 40%, #4B5563 100%)",
                                        boxShadow:
                                            "0 10px 30px rgba(0,0,0,0.1)",
                                    }}
                                >
                                    <div
                                        className="img-wrapper w-100 h-100 d-flex align-items-center justify-content-center p-4"
                                        style={{
                                            position: "absolute",
                                            top: 0,
                                            left: 0,
                                            zIndex: 1,
                                        }}
                                    >
                                        <div
                                            className="d-flex align-items-center justify-content-center"
                                            style={{
                                                width: "80%",
                                                height: "80%",
                                                backgroundColor: !product.image_path ? "white" : "transparent",
                                                borderRadius: "20px",
                                                padding: !product.image_path ? "20px" : "0",
                                                boxShadow: !product.image_path ? "0 10px 20px rgba(0,0,0,0.1)" : "none"
                                            }}
                                        >
                                            <img
                                                src={getImageUrl(
                                                    product.image_path,
                                                    "/assets/default.png"
                                                )}
                                                alt={product.name}
                                                className="w-100 h-100"
                                                style={{
                                                    objectFit: "contain",
                                                    transition:
                                                        "transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)",
                                                    filter: product.image_path
                                                        ? "drop-shadow(0 10px 20px rgba(0,0,0,0.2))"
                                                        : "none",
                                                }}
                                            />
                                        </div>
                                    </div>

                                    {/* Content */}
                                    <div
                                        className="product-content position-absolute bottom-0 start-0 w-100 p-4"
                                        style={{ zIndex: 2 }}
                                    >
                                        {product.category && (
                                            <span
                                                className="d-block text-white-50 text-uppercase fw-bold mb-2"
                                                style={{
                                                    fontSize: "0.7rem",
                                                    letterSpacing: "2px",
                                                }}
                                            >
                                                {product.category}
                                            </span>
                                        )}
                                        <h5
                                            className="text-white fw-bold mb-0"
                                            style={{
                                                fontSize: "1.5rem",
                                                lineHeight: "1.2",
                                                textShadow:
                                                    "0 2px 4px rgba(0,0,0,0.3)",
                                            }}
                                        >
                                            {product.name}
                                        </h5>
                                    </div>
                                </div>
                                <style jsx="true">{`
                                    .product-card-gradient:hover img {
                                        transform: scale(1.1) translateY(-10px);
                                    }
                                `}</style>
                            </SwiperSlide>
                        ))
                    ) : (
                        <div className="text-muted">
                            No latest products found.
                        </div>
                    )}
                </Swiper>
            </div>
        </section>
    );
}
