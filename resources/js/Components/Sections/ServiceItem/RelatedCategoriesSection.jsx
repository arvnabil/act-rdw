import React from "react";
import { Link } from "@inertiajs/react";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/css";

export default function RelatedCategoriesSection({ item, getImageUrl }) {
    return (
        <section
            className="category-area3 space overflow-hidden"
            id="brand-sec"
        >
            <div className="container th-container">
                <div className="row justify-content-center">
                    <div className="col-xl-8">
                        <div className="title-area text-center mb-55">
                            <span className="sub-title text-theme">
                                Partner Teknologi
                            </span>
                            <h2 className="sec-title text-capitalize">
                                Pilihan Brand Terbaik
                            </h2>
                            <p
                                className="sec-text mt-2 mx-auto"
                                style={{ maxWidth: "600px" }}
                            >
                                Kami bekerjasama dengan brand teknologi
                                terkemuka. Pilih brand di bawah ini untuk
                                melihat daftar produk{" "}
                                <strong>{item.title}</strong> yang kompatibel.
                            </p>
                        </div>
                    </div>
                </div>
                <div className="slider-area category-style-3">
                    <Swiper
                        className="category-slider3"
                        centerInsufficientSlides={true}
                        spaceBetween={24}
                        slidesPerView={1}
                        autoplay={{ delay: 3000, disableOnInteraction: false }}
                        breakpoints={{
                            576: { slidesPerView: 2 },
                            768: { slidesPerView: 3 },
                            992: { slidesPerView: 4 },
                            1200: { slidesPerView: 5 },
                        }}
                    >
                        {item.brands.map((brand, index) => (
                            <SwiperSlide key={index}>
                                <div
                                    className="brand-card-tech wow fadeInUp"
                                    data-wow-delay={`${index * 0.1}s`}
                                >
                                    <Link
                                        href={`/${brand.slug || "#"}/products?service_item=${item.slug}`}
                                        className="brand-link"
                                    >
                                        <div className="brand-logo-wrapper">
                                            <img
                                                src={getImageUrl(brand.thumbnail)}
                                                alt={brand.name}
                                                className="brand-logo"
                                            />
                                        </div>
                                        <div className="brand-info">
                                            <h3 className="brand-name">
                                                {brand.name}
                                            </h3>
                                            <span className="brand-cta">
                                                Lihat Produk{" "}
                                                <i className="fa-regular fa-arrow-right ms-1"></i>
                                            </span>
                                        </div>
                                    </Link>
                                </div>
                            </SwiperSlide>
                        ))}
                    </Swiper>
                </div>
            </div>
            <style jsx="true">{`
                .category-slider3 {
                    padding: 20px 12px 40px 12px !important;
                }

                .brand-card-tech {
                    background: #fff;
                    border: 1px solid #eee;
                    border-radius: 16px;
                    padding: 24px;
                    text-align: center;
                    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                    position: relative;
                    z-index: 1;
                    overflow: hidden;
                    height: 100%;
                }

                .brand-card-tech::before {
                    content: "";
                    position: absolute;
                    inset: 0;
                    border-radius: 16px; 
                    padding: 2px; 
                    background: linear-gradient(135deg, var(--theme-color, #2C9E8E), #00d2ff); 
                    -webkit-mask: 
                        linear-gradient(#fff 0 0) content-box, 
                        linear-gradient(#fff 0 0);
                    -webkit-mask-composite: xor; 
                    mask-composite: exclude; 
                    opacity: 0;
                    transition: opacity 0.4s ease;
                }
                
                .brand-card-tech:hover {
                    border-color: transparent;
                    transform: translateY(-8px);
                    box-shadow: 0 15px 40px rgba(44, 158, 142, 0.12);
                }

                .brand-card-tech:hover::before {
                    opacity: 1;
                }

                .brand-logo-wrapper {
                    height: 80px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 20px;
                }

                .brand-logo {
                    max-height: 100%;
                    max-width: 100%;
                    object-fit: contain;
                    transition: transform 0.3s ease;
                }

                .brand-card-tech:hover .brand-logo {
                    transform: scale(1.05);
                }

                .brand-name {
                    font-size: 18px;
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: #1a1a1a;
                }

                .brand-cta {
                    font-size: 14px;
                    color: var(--theme-color, #2c9e8e);
                    font-weight: 500;
                    opacity: 0;
                    transform: translateY(10px);
                    transition: all 0.3s ease;
                    display: inline-block;
                }

                .brand-card-tech:hover .brand-cta {
                    opacity: 1;
                    transform: translateY(0);
                }

                .text-theme {
                    color: var(--theme-color, #2c9e8e);
                }
            `}</style>
        </section>
    );
}
