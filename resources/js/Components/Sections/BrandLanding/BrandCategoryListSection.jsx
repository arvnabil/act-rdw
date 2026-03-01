import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";

export default function BrandCategoryListSection({
    categories,
    brand,
    getBrandSlug,
    getImageUrl,
    config,
}) {
    return (
        <section className="space position-relative" id="all-categories">
             {/* Background Decoration */}
            <div className="shape-mockup jump d-none d-xl-block" style={{ top: "10%", left: "2%" }}>
                <img src="/assets/img/shape/circle_1.png" alt="shape" style={{ opacity: "0.05" }} />
            </div>

            <div className="container">
                <div className="title-area text-center mb-50">
                    <span className="sub-title text-theme">Kategori Produk</span>
                    <h2 className="sec-title text-capitalize">Jelajahi Solusi Anda</h2>
                    <p className="sec-text mt-2 mx-auto" style={{ maxWidth: "600px" }}>
                         Temukan berbagai kategori produk {config?.title || "unggulan"} yang dirancang untuk kebutuhan teknologi Anda.
                    </p>
                </div>

                <div className="slider-area">
                    {categories && categories.length > 0 ? (
                        <Swiper
                            modules={[Navigation, Autoplay, Pagination]}
                            spaceBetween={24}
                            slidesPerView={1}
                            navigation={{
                                nextEl: ".category-next",
                                prevEl: ".category-prev",
                            }}
                            pagination={{ clickable: true, dynamicBullets: true }}
                            autoplay={{ delay: 3500, disableOnInteraction: false }}
                            breakpoints={{
                                576: { slidesPerView: 2 },
                                768: { slidesPerView: 3 },
                                992: { slidesPerView: 4 },
                                1200: { slidesPerView: 5 },
                            }}
                            className="category-slider-tech pb-5"
                            style={{ overflow: "visible" }} // Force visible overflow on Swiper
                        >
                            {categories.map((cat, index) => (
                                <SwiperSlide key={index}>
                                    <Link
                                        href={`/${getBrandSlug(brand)}/products?category=${cat.slug || cat.id}`}
                                        className="d-block h-100"
                                    >
                                        <div 
                                            className="tech-category-card h-100"
                                        >
                                            <div className="card-content">
                                                <div className="icon-wrapper">
                                                    <img
                                                        src={getImageUrl(cat.image, "/assets/default.png")}
                                                        alt={cat.name}
                                                        className="cat-icon"
                                                    />
                                                </div>
                                                <h3 className="cat-title">{cat.name}</h3>
                                                <div className="link-arrow">
                                                    <i className="fa-regular fa-arrow-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </Link>
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    ) : (
                        <div className="text-center text-muted py-5">
                            <i className="fa-light fa-box-open fa-3x mb-3 opacity-50"></i>
                            <p>Tidak ada kategori ditemukan.</p>
                        </div>
                    )}
                    
                    {/* Navigation Buttons */}
                    <div className="d-flex justify-content-center gap-3 mt-2">
                        <button className="category-prev th-btn style4 icon-only">
                            <i className="fa-regular fa-arrow-left"></i>
                        </button>
                        <button className="category-next th-btn style4 icon-only">
                            <i className="fa-regular fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <style jsx="true">{`
                .tech-category-card {
                    background: #ffffff;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    border-radius: 20px;
                    padding: 30px 20px;
                    text-align: center;
                    position: relative;
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    z-index: 1;
                    overflow: visible; /* Ensure content isn't clipped inside card */
                    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
                }

                .tech-category-card::before {
                    content: "";
                    position: absolute;
                    inset: 0;
                    border-radius: 20px; 
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

                .tech-category-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 20px 40px rgba(44, 158, 142, 0.15);
                    border-color: transparent;
                }

                .tech-category-card:hover::before {
                    opacity: 1;
                }

                .tech-category-card::after {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: radial-gradient(circle at 50% 0%, rgba(44, 158, 142, 0.05) 0%, transparent 70%);
                    opacity: 0;
                    transition: opacity 0.4s ease;
                    z-index: -1;
                }

                .tech-category-card:hover::after {
                    opacity: 1;
                }

                .card-content {
                    position: relative;
                    z-index: 2;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    height: 100%;
                }

                .icon-wrapper {
                    width: 80px;
                    height: 80px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 20px;
                    border-radius: 50%;
                    background: #f8f9fa;
                    transition: all 0.4s ease;
                    padding: 15px;
                }

                .tech-category-card:hover .icon-wrapper {
                    background: #fff;
                    transform: scale(1.1) rotate(5deg);
                    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                }

                .cat-icon {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                    transition: all 0.4s ease;
                    filter: grayscale(100%) opacity(0.8);
                }

                .tech-category-card:hover .cat-icon {
                     filter: grayscale(0%) opacity(1);
                     transform: scale(1.1);
                }

                .cat-title {
                    font-size: 16px;
                    font-weight: 600;
                    color: #1a1a1a;
                    margin-bottom: 15px;
                    line-height: 1.4;
                    transition: color 0.3s ease;
                    flex-grow: 1;
                }

                .tech-category-card:hover .cat-title {
                    color: var(--theme-color, #2C9E8E);
                }

                .link-arrow {
                    width: 32px;
                    height: 32px;
                    background: transparent;
                    border: 1px solid #e5e7eb;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #9ca3af;
                    font-size: 14px;
                    transition: all 0.3s ease;
                    margin-top: auto;
                }

                .tech-category-card:hover .link-arrow {
                    background: var(--theme-color, #2C9E8E);
                    border-color: var(--theme-color, #2C9E8E);
                    color: #fff;
                    transform: translateX(5px);
                    box-shadow: 0 4px 10px rgba(44, 158, 142, 0.3);
                }

                .text-theme {
                    color: var(--theme-color, #2c9e8e);
                }

                /* Swiper Overrides */
                :global(.category-slider-tech) {
                    padding-bottom: 80px !important;
                    padding-top: 50px !important;
                    padding-left: 20px !important;
                    padding-right: 20px !important;
                    margin: 0 -20px; /* Compensate for side padding */
                    overflow: visible; /* Attempt to allow overflow if possible */
                }

                :global(.swiper-pagination-bullet) {
                    width: 10px;
                    height: 10px;
                    background-color: #d1d5db;
                    opacity: 1;
                    transition: all 0.3s ease;
                }

                :global(.swiper-pagination-bullet-active) {
                    background-color: var(--theme-color, #2c9e8e);
                    width: 25px;
                    border-radius: 5px;
                    box-shadow: 0 4px 10px rgba(44, 158, 142, 0.3);
                }

                .icon-only {
                    width: 46px;
                    height: 46px;
                    padding: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 50%;
                }
            `}</style>
        </section>
    );
}
