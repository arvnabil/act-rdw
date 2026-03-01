import React from "react";
import { Link } from "@inertiajs/react";
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
            <div className="container">
                <SectionTitle
                    subTitle="New Release"
                    title={config?.title || "Latest Innovation"}
                    align="text-center"
                />

                <Swiper
                    modules={[Navigation, Autoplay]}
                    spaceBetween={30}
                    slidesPerView={1}
                    centerInsufficientSlides={true}
                    autoplay={{
                        delay: 3500,
                        disableOnInteraction: false,
                    }}
                    breakpoints={{
                        575: { slidesPerView: 2 },
                        992: { slidesPerView: 3 },
                        1200: { slidesPerView: 4 },
                    }}
                    className="latest-products-slider"
                    style={{ padding: "40px 20px 60px 20px", margin: "0 -20px" }}
                >
                    {products && products.length > 0 ? (
                        products.map((product, i) => (
                            <SwiperSlide key={i}>
                                <Link
                                    href={`/products/${product.slug}`}
                                    className="text-decoration-none d-block h-100"
                                >
                                    <div className="tech-product-card h-100 group cursor-pointer">
                                        {/* Badge (Optional/Static for now or based on newness) */}
                                        {product.is_new && (
                                            <span className="new-badge">New Arrival</span>
                                        )}

                                        {/* Image Area */}
                                        <div className="product-img-area">
                                            <div className="img-overlay"></div>
                                            <img
                                                src={getImageUrl(
                                                    product.image_path,
                                                    "/assets/default.png"
                                                )}
                                                alt={product.name}
                                            />
                                        </div>

                                        {/* Content Area */}
                                        <div className="product-info">
                                            {product.category && (
                                                <div className="product-meta">
                                                    <span className="category-tag">
                                                        {product.category}
                                                    </span>
                                                </div>
                                            )}
                                            <h3 className="product-title text-dark">
                                                {product.name}
                                            </h3>
                                            <div className="hover-line"></div>
                                        </div>
                                    </div>
                                </Link>
                            </SwiperSlide>
                        ))
                    ) : (
                        <div className="text-muted text-center py-5">
                            No latest products found.
                        </div>
                    )}
                </Swiper>
            </div>
            <style jsx="true">{`
                .tech-product-card {
                    background: #ffffff;
                    border-radius: 20px;
                    padding: 25px;
                    position: relative;
                    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    border: 1px solid #f3f4f6;
                    overflow: hidden;
                    height: 100%;
                }

                /* Hover Lift & Shadow */
                .tech-product-card:hover {
                    transform: translateY(-10px);
                    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
                    border-color: transparent;
                }

                /* Gradient Border Reveal */
                .tech-product-card::before {
                    content: "";
                    position: absolute;
                    inset: 0;
                    border-radius: 20px; 
                    padding: 1.5px; 
                    background: linear-gradient(135deg, var(--theme-color, #2C9E8E), #00d2ff); 
                    -webkit-mask: 
                        linear-gradient(#fff 0 0) content-box, 
                        linear-gradient(#fff 0 0);
                    -webkit-mask-composite: xor; 
                    mask-composite: exclude; 
                    opacity: 0;
                    transition: opacity 0.4s ease;
                    pointer-events: none;
                }
                .tech-product-card:hover::before {
                    opacity: 1;
                }

                /* New Badge */
                .new-badge {
                    position: absolute;
                    top: 20px;
                    left: 20px;
                    background: rgba(44, 158, 142, 0.1);
                    color: var(--theme-color, #2C9E8E);
                    font-size: 11px;
                    font-weight: 700;
                    text-transform: uppercase;
                    padding: 4px 10px;
                    border-radius: 30px;
                    z-index: 2;
                    letter-spacing: 0.5px;
                }

                /* Image Area */
                .product-img-area {
                    position: relative;
                    height: 220px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 20px;
                    padding: 10px;
                    background: radial-gradient(circle at center, #f9fafb 0%, #ffffff 70%);
                    border-radius: 15px;
                    overflow: hidden;
                }

                .product-img-area img {
                    max-width: 90%;
                    max-height: 90%;
                    object-fit: contain;
                    transition: transform 0.5s ease;
                    filter: drop-shadow(0 5px 15px rgba(0,0,0,0.05));
                    z-index: 1;
                }

                .tech-product-card:hover .product-img-area img {
                    transform: scale(1.08) translateY(-5px);
                    filter: drop-shadow(0 10px 25px rgba(0,0,0,0.1));
                }

                .tech-product-card:hover .category-tag {
                    color: var(--theme-color, #2C9E8E);
                }

                .product-title {
                    font-size: 18px;
                    font-weight: 700;
                    color: #111827;
                    margin: 0;
                    line-height: 1.4;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    transition: color 0.3s ease;
                }

                .tech-product-card:hover .product-title {
                    color: var(--theme-color, #2C9E8E);
                }

                /* Decorative Line */
                .hover-line {
                    width: 0;
                    height: 2px;
                    background: var(--theme-color, #2C9E8E);
                    margin-top: 15px;
                    transition: width 0.4s ease;
                }
                
                .tech-product-card:hover .hover-line {
                    width: 40px;
                }
            `}</style>
        </section>
    );
}
