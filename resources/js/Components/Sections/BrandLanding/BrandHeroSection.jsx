import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";

export default function BrandHeroSection({
    brand,
    bgImage,
    bgColor,
    relatedServices,
    getImageUrl,
    setLightboxImage,
    config,
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
                    backgroundColor: bgColor,
                    backgroundImage: `url(${getImageUrl(
                        bgImage,
                        "/assets/img/foto-dok-logitech-1.png",
                    )})`,
                    backgroundSize: "cover",
                    backgroundPosition: "center",
                    backgroundRepeat: "no-repeat",
                    padding: "var(--hero-padding, 100px 0)",
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
                    className="container th-container position-relative px-lg-5"
                    style={{ zIndex: 1 }}
                >
                    <div className="row align-items-center gy-5 gx-lg-5">
                        <div className={config?.awards?.length > 0 ? "col-lg-6" : "col-lg-12 text-center"}>
                            <div className="hero-content wow fadeInUp text-center text-lg-start">
                                <span
                                    className="sub-title text-uppercase mb-20 d-block"
                                    style={{ letterSpacing: "2px", color: "rgba(255,255,255,0.9)" }}
                                >
                                    {config?.eyebrow ||
                                        config?.subtitle ||
                                        "AUTHORIZED PARTNER"}
                                </span>
                                <h1
                                    className="hero-title text-anime-style-3 mb-4"
                                    style={{
                                        fontSize: "var(--hero-title-size, clamp(2.2rem, 5vw, 4rem))",
                                        fontWeight: "bold",
                                        color: "white",
                                        lineHeight: "1.1",
                                    }}
                                >
                                    {(
                                        config?.title || brand.name
                                    ).toUpperCase()}
                                </h1>
                                <p
                                    className="hero-text mb-30 mx-auto mx-lg-0"
                                    style={{
                                        fontSize: "1.1rem",
                                        maxWidth: "500px",
                                        color: "rgba(255,255,255,0.8)",
                                        lineHeight: "1.6"
                                    }}
                                >
                                    {config?.desc ||
                                        config?.subtitle ||
                                        "Explore video conferencing products, including conference cameras, room solutions, webcams, headsets, collaboration tools, and accessories."}
                                </p>
                                <div className="d-flex justify-content-center justify-content-lg-start mt-4">
                                    <a
                                        href={config?.cta_url || "#products"}
                                        className="th-btn style3 th-radius th-icon px-4"
                                    >
                                        {config?.cta_label || "Contact Sales"}{" "}
                                        <i className="fa-regular fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {config?.awards?.length > 0 && (
                            <div className="col-lg-6 px-lg-4">
                                <div
                                    className="hero-scroll-cards wow fadeInRight mt-5 mt-lg-0"
                                    style={{
                                        maxWidth: "800px",
                                        marginLeft: "auto",
                                    }}
                                >
                                    <div className="mb-5 ps-0 ps-lg-2 text-center text-lg-start">
                                        <span className="sub-title style1 text-anime-style-2 text-white justify-content-center justify-content-lg-start">
                                            <span className="squre-shape left me-4 bg-white"></span>
                                            Official Accreditations
                                            <span className="squre-shape d-lg-none right ms-4 bg-white"></span>
                                        </span>
                                    </div>
                                    <Swiper
                                        modules={[Autoplay]}
                                        spaceBetween={20}
                                        autoplay={{
                                            delay: 2500,
                                            disableOnInteraction: false,
                                        }}
                                        speed={800}
                                        grabCursor={true}
                                        loop={true}
                                        breakpoints={{
                                            0: { slidesPerView: 1.2, centeredSlides: true },
                                            576: { slidesPerView: 2 },
                                            1024: { slidesPerView: 3 },
                                        }}
                                        className="hero-badge-slider"
                                    >
                                        {config.awards.map((item, index) => (
                                            <SwiperSlide key={index}>
                                                <div
                                                    className="bg-white d-flex align-items-center justify-content-center shadow-lg"
                                                    style={{
                                                        aspectRatio: "1/1",
                                                        padding: "20px",
                                                        margin: "0 auto",
                                                        borderRadius: "15px",
                                                        cursor: "pointer",
                                                        maxWidth: "250px"
                                                    }}
                                                    onClick={() =>
                                                        setLightboxImage(
                                                            getImageUrl(item.image),
                                                        )
                                                    }
                                                >
                                                    <img
                                                        src={getImageUrl(
                                                            item.image,
                                                            "/assets/default.png"
                                                        )}
                                                        alt={
                                                            item.alt ||
                                                            "Award Badge"
                                                        }
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
                        )}
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
                                { name: "Project", link: "#project-showcase" },
                            );

                            return navItems.map((item, i) => (
                                <a
                                    key={i}
                                    href={item.link}
                                    onClick={(e) => handleScroll(e, item.link)}
                                    className="text-white text-uppercase fw-bold text-decoration-none fs-xs-small hover-opacity"
                                    style={{ fontSize: "13px" }}
                                >
                                    {item.name}
                                </a>
                            ));
                        })()}
                    </div>
                </div>
            </div>
            <style>{`
                :root {
                    --hero-padding: 120px 0;
                    --hero-title-size: clamp(2.5rem, 6vw, 4rem);
                }
                @media (max-width: 991px) {
                    :root {
                        --hero-padding: 80px 0;
                        --hero-title-size: 2.5rem;
                    }
                    .hero-content {
                        margin-bottom: 20px;
                    }
                }
                @media (max-width: 575px) {
                    :root {
                        --hero-padding: 60px 0 80px;
                        --hero-title-size: 2rem;
                    }
                    .hero-text {
                        font-size: 1rem !important;
                    }
                    .fs-xs-small {
                        font-size: 11px !important;
                    }
                }
            `}</style>
        </section>
    );
}
