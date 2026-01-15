import React from "react";
import { Link } from "@inertiajs/react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";

export default function RelatedCategoriesSection({ item, getImageUrl }) {
    return (
        <section className="category-area3 space">
            <div className="container th-container">
                <div className="row justify-content-center">
                    <div className="col-xl-6">
                        <div className="title-area text-center mb-55">
                            <span className="sub-title text-anime-style-2">
                                Set Brand
                            </span>
                            <h2 className="sec-title text-anime-style-3">
                                Select a Brand
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="slider-area category-style-3">
                    <Swiper
                        className="category-slider3 has-shadow"
                        spaceBetween={24}
                        slidesPerView={1}
                        breakpoints={{
                            576: { slidesPerView: 1 },
                            768: { slidesPerView: 2 },
                            992: { slidesPerView: 3 },
                            1200: { slidesPerView: 3 },
                            1400: { slidesPerView: 5 },
                        }}
                    >
                        {item.brands.map((brand, index) => (
                            <SwiperSlide key={index}>
                                <div className="category-card single2 text-center">
                                    <div className="box-img global-img mb-3">
                                        <Link
                                            href={`/${brand.name
                                                .toLowerCase()
                                                .replace(/\s+/g, "-")}`}
                                        >
                                            <img
                                                src={getImageUrl(brand.image)}
                                                alt={brand.name}
                                                style={{ borderRadius: "10px" }}
                                            />
                                        </Link>
                                    </div>
                                    <h3 className="box-title">
                                        <Link
                                            href={`/${brand.name
                                                .toLowerCase()
                                                .replace(/\s+/g, "-")}`}
                                        >
                                            {brand.name}
                                        </Link>
                                    </h3>
                                    <span className="category-text">
                                        {brand.desc}
                                    </span>
                                </div>
                            </SwiperSlide>
                        ))}
                    </Swiper>
                </div>
            </div>
        </section>
    );
}
