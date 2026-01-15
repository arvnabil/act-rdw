import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";

export default function BrandHeroSection({
    brand,
    pageData,
    relatedServices,
    getImageUrl,
    setLightboxImage,
}) {
    const handleScroll = (e, id) => {
        e.preventDefault();
        const element = document.querySelector(id);
        if (element) {
            const offset = 100;
            const bodyRect = document.body.getBoundingClientRect().top;
            const elementRect = element.getBoundingClientRect().top;
            const elementPosition = elementRect - bodyRect;
            const offsetPosition = elementPosition - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth",
            });
        }
    };

    return (
        <section>
            <div
                className="brand-hero-area position-relative"
                style={{
                    backgroundColor:
                        pageData.hero_styles?.background_color || "#E8B4B4",
                    backgroundImage: pageData.hero_styles?.background_image
                        ? `url(${getImageUrl(
                              pageData.hero_styles.background_image
                          )})`
                        : "none",
                    backgroundSize: "cover",
                    backgroundPosition: "center",
                    padding: "100px 0",
                    minHeight: "500px",
                    display: "flex",
                    alignItems: "center",
                }}
            >
                {/* Overlay */}
                <div
                    className="position-absolute top-0 start-0 w-100 h-100"
                    style={{
                        backgroundColor: "rgba(15, 23, 42, 0.6)",
                        zIndex: 0,
                    }}
                ></div>

                <div
                    className="container th-container position-relative"
                    style={{ zIndex: 1 }}
                >
                    <div className="row align-items-center">
                        <div className="col-lg-6">
                            <div className="hero-content wow fadeInUp">
                                <span
                                    className="sub-title text-uppercase mb-20"
                                    style={{ letterSpacing: "2px" }}
                                >
                                    Business Solution
                                </span>
                                <h1
                                    className="hero-title text-anime-style-3 mb-4"
                                    style={{
                                        fontSize: "clamp(2rem, 5vw, 4rem)",
                                        fontWeight: "bold",
                                        color: "white",
                                        lineHeight: "1.2",
                                    }}
                                >
                                    {brand.name.toUpperCase()}
                                </h1>
                                <p
                                    className="hero-text mb-30"
                                    style={{
                                        fontSize: "1.2rem",
                                        maxWidth: "500px",
                                        color: "rgba(255,255,255,0.8)",
                                    }}
                                >
                                    Explore video conferencing products,
                                    including conference cameras, room
                                    solutions, webcams, headsets, collaboration
                                    tools, and accessories.
                                </p>
                                <a
                                    href="#products"
                                    className="th-btn style3 th-radius th-icon"
                                >
                                    Contact Sales{" "}
                                    <i className="fa-regular fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div
                                className="hero-scroll-cards wow fadeInRight"
                                style={{
                                    maxWidth: "800px",
                                    marginLeft: "auto",
                                }}
                            >
                                <div className="mb-4 ps-0 ps-lg-2 text-center text-lg-start">
                                    <span className="sub-title style1 text-anime-style-2 text-white">
                                        <span className="squre-shape left me-3 bg-white"></span>
                                        Award & Certified
                                        <span className="squre-shape d-lg-none right ms-3 bg-white"></span>
                                    </span>
                                </div>
                                <Swiper
                                    modules={[Autoplay]}
                                    spaceBetween={20}
                                    autoplay={{
                                        delay: 2500,
                                        disableOnInteraction: false,
                                    }}
                                    loop={true}
                                    breakpoints={{
                                        0: { slidesPerView: 1 },
                                        768: { slidesPerView: 2 },
                                        1024: { slidesPerView: 3 },
                                    }}
                                    className="hero-badge-slider"
                                >
                                    {[1, 2, 3, 4].map((item) => (
                                        <SwiperSlide key={item}>
                                            <div
                                                className="bg-white d-flex align-items-center justify-content-center shadow-lg"
                                                style={{
                                                    width: "250px",
                                                    height: "250px",
                                                    padding: "20px",
                                                    margin: "0 auto",
                                                    borderRadius: "10px",
                                                    cursor: "pointer",
                                                }}
                                                onClick={() =>
                                                    setLightboxImage(
                                                        "https://activ.co.id/wp-content/uploads/2024/11/elite-dk-360x360-1.png"
                                                    )
                                                }
                                            >
                                                <img
                                                    src="https://activ.co.id/wp-content/uploads/2024/11/elite-dk-360x360-1.png"
                                                    alt="Elite Partner"
                                                    className="img-fluid"
                                                    style={{
                                                        maxHeight: "100%",
                                                        maxWidth: "100%",
                                                        objectFit: "contain",
                                                    }}
                                                />
                                            </div>
                                        </SwiperSlide>
                                    ))}
                                </Swiper>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Dark Sub-Nav Bar */}
            <div
                className="brand-subnav py-3"
                style={{ backgroundColor: "#1DA2C0" }}
            >
                <div className="container th-container">
                    <div className="d-flex justify-content-center gap-4 flex-wrap">
                        {(() => {
                            const navItems = [];
                            if (relatedServices && relatedServices.length > 0) {
                                relatedServices.forEach((s) => {
                                    navItems.push({
                                        name: s.name,
                                        link: `#service-${s.slug}`,
                                    });
                                });
                            } else {
                                navItems.push({
                                    name: "Solutions",
                                    link: "#room-solutions",
                                });
                            }
                            navItems.push(
                                { name: "Kategori", link: "#all-categories" },
                                {
                                    name: "New Product",
                                    link: "#latest-products",
                                },
                                { name: "Project", link: "#project-showcase" }
                            );

                            return navItems.map((item, i) => (
                                <a
                                    key={i}
                                    href={item.link}
                                    onClick={(e) => handleScroll(e, item.link)}
                                    className="text-white text-uppercase fw-bold text-decoration-none fs-6 hover-opacity"
                                >
                                    {item.name}
                                </a>
                            ));
                        })()}
                    </div>
                </div>
            </div>
        </section>
    );
}
