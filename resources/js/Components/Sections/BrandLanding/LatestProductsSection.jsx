import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Autoplay } from "swiper/modules";
import SectionTitle from "@/Components/Common/SectionTitle";
import "swiper/css";

export default function LatestProductsSection({ products, getImageUrl }) {
    return (
        <section
            className="space-top space-extra-bottom bg-white"
            id="latest-products"
        >
            <div className="container th-container">
                <SectionTitle
                    subTitle="New"
                    title="Products"
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
                                    className="product-card-simple latest-product-height position-relative overflow-hidden w-100"
                                    style={{
                                        borderRadius: "30px",
                                        boxShadow:
                                            "0 10px 30px rgba(0,0,0,0.3)",
                                        cursor: "pointer",
                                    }}
                                >
                                    <img
                                        src={getImageUrl(product.image_path)}
                                        alt={product.name}
                                        className="w-100 h-100 object-fit-cover"
                                        style={{
                                            transition: "transform 0.5s ease",
                                        }}
                                    />
                                    <div
                                        className="product-content position-absolute bottom-0 start-0 w-100 p-4 d-flex flex-column justify-content-end"
                                        style={{
                                            height: "100%",
                                            background:
                                                "linear-gradient(to top, #111 0%, rgba(17,17,17,0.8) 30%, transparent 100%)",
                                        }}
                                    >
                                        <h5 className="text-white mb-1 fw-bold">
                                            {product.name}
                                        </h5>
                                        {product.category && (
                                            <p className="text-white-50 fs-xs mb-0 fw-medium">
                                                {product.category}
                                            </p>
                                        )}
                                    </div>
                                </div>
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
