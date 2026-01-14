import { Head, Link } from "@inertiajs/react";
import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import { Swiper, SwiperSlide } from "swiper/react";
import {
    Navigation,
    EffectFade,
    Thumbs,
    Autoplay,
    EffectCoverflow,
    Pagination,
} from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/effect-fade";
import "swiper/css/thumbs";
import "swiper/css/autoplay";
import "swiper/css/effect-coverflow";
import "swiper/css/pagination";
import SectionTitle from "@/Components/Common/SectionTitle";
import SectionTitleLight from "@/Components/Common/SectionTitleLight";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

const BrandLanding = ({
    brand,
    products,
    categories,
    pageSite,
    relatedServices,
}) => {
    const [thumbsSwiper, setThumbsSwiper] = React.useState(null);
    const [lightboxImage, setLightboxImage] = React.useState(null);

    React.useEffect(() => {
        const elements = document.querySelectorAll(".scroll-text-ani");
        elements.forEach((line) => {
            gsap.to(line, {
                backgroundImage:
                    "linear-gradient(to right, #0B1422 100%, #D5D7DA 100%)",
                ease: "none",
                scrollTrigger: {
                    trigger: line,
                    start: "top bottom",
                    end: "top center",
                    scrub: true,
                },
            });
        });
    }, []);

    // Helper for images
    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) {
            return path;
        }
        return `/storage/${path}`;
    };

    const getBrandSlug = (b) => b.slug || b.name.toLowerCase();

    // Dummy Data for PageSite (until module is ready)
    const pageData = pageSite || {
        hero_styles: {
            background_color: "#444A572C", // Dark Slate Blue (Darkened "Use background not pink")
            background_image:
                "https://activ.co.id/wp-content/uploads/2023/11/Group-97-1.png?id=5807",
        },
    };

    const staticShowcase = [
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Project Alpha",
            category: "Yealink",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Meeting Room B",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Conference Hall",
            category: "Jabra",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Huddle Space",
            category: "Maxhub",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Executive Office",
            category: "Zoom",
            image: "/assets/img/project/project_3_9_.jpg",
        },
    ];

    const heroSlides = [
        {
            subtitle: "Business Solution",
            title: "VIDEO CONFERENCING",
            desc: "Explore video conferencing products, including conference cameras, room solutions, webcams, headsets, collaboration tools, and accessories.",
            image: "https://activ.co.id/wp-content/uploads/2023/11/Banner-Utama-2.png",
        },
    ];

    return (
        <MainLayout>
            <Head title={brand.name} />

            {/* Hero Section */}
            <section>
                {/* ... existing hero code ... */}
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
                                        style={{
                                            letterSpacing: "2px",
                                        }} // Matching the category color
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
                                        solutions, webcams, headsets,
                                        collaboration tools, and accessories.
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
                                                            objectFit:
                                                                "contain",
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

                                if (
                                    relatedServices &&
                                    relatedServices.length > 0
                                ) {
                                    relatedServices.forEach((s) => {
                                        navItems.push({
                                            name: s.name,
                                            link: `#service-${s.slug}`,
                                        });
                                    });
                                } else {
                                    // Fallback
                                    navItems.push({
                                        name: "Solutions",
                                        link: "#room-solutions",
                                    });
                                }

                                navItems.push(
                                    {
                                        name: "Kategori",
                                        link: "#all-categories",
                                    },
                                    {
                                        name: "New Product",
                                        link: "#latest-products",
                                    },
                                    {
                                        name: "Project",
                                        link: "#project-showcase",
                                    }
                                );

                                const handleScroll = (e, id) => {
                                    e.preventDefault();
                                    const element = document.querySelector(id);
                                    if (element) {
                                        const offset = 100;
                                        const bodyRect =
                                            document.body.getBoundingClientRect()
                                                .top;
                                        const elementRect =
                                            element.getBoundingClientRect().top;
                                        const elementPosition =
                                            elementRect - bodyRect;
                                        const offsetPosition =
                                            elementPosition - offset;

                                        window.scrollTo({
                                            top: offsetPosition,
                                            behavior: "smooth",
                                        });
                                    }
                                };

                                return navItems.map((item, i) => (
                                    <a
                                        key={i}
                                        href={item.link}
                                        onClick={(e) =>
                                            handleScroll(e, item.link)
                                        }
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

            {/* Dynamic Service Solutions Sections */}
            {relatedServices && relatedServices.length > 0
                ? relatedServices.map((service, sIndex) => {
                      return (
                          <section
                              key={sIndex}
                              className="position-relative space-top space-extra-bottom"
                              id={`service-${service.slug}`}
                              style={{
                                  backgroundImage: `url(${
                                      service.image
                                          ? getImageUrl(service.image)
                                          : "/assets/img/bg/bg_overlay_1.png" // Fallback background
                                  })`,
                                  backgroundSize: "cover",
                                  backgroundPosition: "center",
                              }}
                          >
                              <div
                                  className="position-absolute top-0 start-0 w-100 h-100"
                                  style={{
                                      background:
                                          "linear-gradient(to bottom, #0A141E 0%, rgba(10, 20, 30, 0.85) 15%, rgba(10, 20, 30, 0.85) 85%, #0A141E 100%)",
                                      zIndex: 1,
                                  }}
                              ></div>
                              <div
                                  className="container th-container position-relative"
                                  style={{ zIndex: 2 }}
                              >
                                  <div className="row justify-content-center">
                                      <div className="col-xl-8">
                                          <div className="title-area text-center mb-55">
                                              <SectionTitleLight
                                                  subTitle={service.sub_title}
                                                  title={service.title}
                                                  align="title-area service-title-box text-center"
                                                  subTitleStyle={{
                                                      backgroundColor:
                                                          "transparent",
                                                      color: "#20B2AA",
                                                  }}
                                              />
                                          </div>
                                      </div>
                                  </div>

                                  <div className="slider-area">
                                      <Swiper
                                          modules={[Navigation, Pagination]}
                                          spaceBetween={30}
                                          slidesPerView={1}
                                          pagination={{
                                              el: `.service-pagination-${sIndex}`,
                                              type: "progressbar",
                                          }}
                                          navigation={{
                                              prevEl: `.service-prev-${sIndex}`,
                                              nextEl: `.service-next-${sIndex}`,
                                          }}
                                          onBeforeInit={(swiper) => {
                                              swiper.params.navigation.prevEl = `.service-prev-${sIndex}`;
                                              swiper.params.navigation.nextEl = `.service-next-${sIndex}`;
                                          }}
                                          breakpoints={{
                                              576: { slidesPerView: 1 },
                                              768: { slidesPerView: 2 },
                                              992: { slidesPerView: 3 },
                                              1200: { slidesPerView: 3 },
                                          }}
                                          className="service-slider"
                                      >
                                          {service.solutions.map(
                                              (solution, i) => (
                                                  <SwiperSlide key={i}>
                                                      <div className="room-card position-relative overflow-hidden rounded-20 bg-dark">
                                                          <div
                                                              className="room-img position-relative overflow-hidden room-img-height"
                                                              style={{
                                                                  borderRadius:
                                                                      "20px",
                                                              }}
                                                          >
                                                              <img
                                                                  src={
                                                                      solution.image
                                                                          ? getImageUrl(
                                                                                solution.image
                                                                            )
                                                                          : "/assets/img/logo/activ-logo-white.png" // Using a generic logo or placeholder available in assets
                                                                  }
                                                                  onError={(
                                                                      e
                                                                  ) => {
                                                                      e.target.onerror =
                                                                          null;
                                                                      e.target.src =
                                                                          "https://placehold.co/600x400?text=No+Image";
                                                                  }}
                                                                  alt={
                                                                      solution.title
                                                                  }
                                                                  className="w-100 h-100 object-fit-cover"
                                                                  style={{
                                                                      transition:
                                                                          "transform 0.5s ease",
                                                                  }}
                                                              />
                                                              <div
                                                                  className="position-absolute bottom-0 start-0 w-100 p-4"
                                                                  style={{
                                                                      background:
                                                                          "linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 60%, transparent 100%)",
                                                                      zIndex: 2,
                                                                  }}
                                                              >
                                                                  <h3 className="text-white mb-2 h4 fw-bold">
                                                                      {
                                                                          solution.title
                                                                      }
                                                                  </h3>
                                                                  <p className="text-white-50 mb-4 fs-xs">
                                                                      {
                                                                          solution.desc
                                                                      }
                                                                  </p>
                                                                  <Link
                                                                      href={`/services/${service.slug}/${solution.slug}`}
                                                                      className="th-btn style3 th-radius th-icon room-btn btn-ghost-green"
                                                                  >
                                                                      Show
                                                                      Products{" "}
                                                                      <i className="fa-regular fa-arrow-right ms-2"></i>
                                                                  </Link>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </SwiperSlide>
                                              )
                                          )}
                                      </Swiper>

                                      {/* Navigation Controls (Desktop Only) */}
                                      <div className="d-none d-md-flex align-items-center justify-content-center gap-4 mt-5">
                                          <button
                                              className={`service-prev-${sIndex} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`}
                                          >
                                              <i className="fa-regular fa-arrow-left"></i>
                                          </button>
                                          <div
                                              className={`service-pagination-${sIndex} position-relative`}
                                              style={{
                                                  width: "150px",
                                                  height: "2px",
                                                  background:
                                                      "rgba(255,255,255,0.2)",
                                                  borderRadius: "2px",
                                                  overflow: "hidden",
                                              }}
                                          ></div>
                                          <button
                                              className={`service-next-${sIndex} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`}
                                          >
                                              <i className="fa-regular fa-arrow-right"></i>
                                          </button>
                                      </div>
                                  </div>
                              </div>
                          </section>
                      );
                  })
                : null}

            {/* All Categories Section */}
            <section className="space" id="all-categories">
                <div className="container th-container">
                    <div className="title-area text-start mb-40">
                        <SectionTitle
                            subTitle="Kategori"
                            title="Semua Kategori"
                            align="title-area service-title-box text-center"
                        />
                    </div>

                    <div className="row justify-content-center gy-4 row-cols-2 row-cols-md-4 row-cols-lg-6">
                        {categories && categories.length > 0 ? (
                            categories.map((cat, index) => (
                                <div key={index} className="col">
                                    <Link
                                        href={`/${getBrandSlug(
                                            brand
                                        )}/products?category=${
                                            cat.slug || cat.id
                                        }`}
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
                                                }}
                                            >
                                                <img
                                                    src={getImageUrl(cat.image)}
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
                            // Fallback if no categories
                            <div className="col-12 text-center text-muted">
                                No categories found.
                            </div>
                        )}
                    </div>
                </div>
            </section>

            {/* Latest Products Section */}
            {/* Latest Products Section - Updated Design */}
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
                                            src={getImageUrl(
                                                product.image_path
                                            )}
                                            alt={product.name}
                                            className="w-100 h-100 object-fit-cover"
                                            style={{
                                                transition:
                                                    "transform 0.5s ease",
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

            {/* Work Showcase Section */}
            <div
                className="case-area3 position-relative overflow-hidden space-bottom"
                id="project-showcase"
            >
                <div className="container th-container">
                    <SectionTitle
                        subTitle="Project"
                        title="Our Work Showcase"
                        align="text-center"
                    />
                    <div className="slider-area">
                        <Swiper
                            modules={[EffectCoverflow]}
                            effect={"coverflow"}
                            grabCursor={true}
                            centeredSlides={true}
                            loop={true}
                            slidesPerView={"auto"}
                            coverflowEffect={{
                                rotate: -17,
                                stretch: -8,
                                depth: 330,
                                modifier: 1,
                                slideShadows: false,
                            }}
                            breakpoints={{
                                0: { slidesPerView: 1 },
                                576: { slidesPerView: 2 },
                                992: { slidesPerView: 3 },
                                1200: { slidesPerView: 3 },
                            }}
                            className="case-slider3"
                        >
                            {staticShowcase.map((project, index) => (
                                <SwiperSlide key={index}>
                                    <div className="case-box3 gsap-cursor">
                                        <div className="case-box3-inner">
                                            <div
                                                className="case-img position-relative"
                                                style={{
                                                    height: "400px",
                                                    overflow: "hidden",
                                                }}
                                            >
                                                {" "}
                                                {/* Added height constraint */}
                                                <img
                                                    src={project.image}
                                                    alt="case image"
                                                    className="w-100 h-100 object-fit-cover rounded-3"
                                                />
                                                <div className="case-content">
                                                    <h3 className="case-title">
                                                        <a href="#">
                                                            {project.title}
                                                        </a>
                                                    </h3>
                                                    <span className="case-categ">
                                                        {project.category}
                                                    </span>
                                                    <a
                                                        href="#"
                                                        className="case-icon"
                                                    >
                                                        <i className="fa-light fa-arrow-right-long"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    </div>
                </div>
            </div>
            <style>{`
                .hover-opacity:hover {
                    opacity: 0.7;
                }
                .hover-white:hover {
                    background-color: rgba(255, 255, 255, 0.1) !important;
                    border-color: #ffffff !important;
                    color: #ffffff !important;
                }
                .hover-dark:hover {
                    background-color: rgba(0, 0, 0, 0.1) !important;
                    border-color: #000000 !important;
                    color: #000000 !important;
                }
                .room-card:hover img {
                    transform: scale(1.1);
                }
                .room-card {
                    overflow: hidden;
                    backface-visibility: hidden;
                }
                .category-card:hover {
                    background-color: #E5E7EB !important;
                    transform: translateY(-5px);
                }
                .product-card-simple:hover img {
                    transform: scale(1.05) !important;
                }
                .room-img-height {
                    height: 500px;
                }
                .room-btn {
                    padding: 10px 25px;
                }
                .latest-product-height {
                    height: 400px;
                }
                @media (max-width: 576px) {
                    .room-img-height {
                        height: 380px;
                    }
                    .room-btn {
                        padding: 8px 15px !important;
                        font-size: 14px;
                        width: 100%;
                        justify-content: center;
                    }
                    .latest-product-height {
                        height: 350px;
                    }
                }
                .swiper-pagination-progressbar-fill {
                    background-color: #ffffff !important;
                }
                .swiper-pagination-lock {
                    display: none !important;
                }
                .btn-ghost-green {
                    background-color: transparent !important;
                    border: 1px solid #ffffff !important;
                    color: #ffffff !important;
                    transition: all 0.3s ease;
                    position: relative;
                    z-index: 1;
                }
                .btn-ghost-green:hover {
                    background-color: #28a745 !important;
                    border-color: #28a745 !important;
                    color: #ffffff !important;
                }
                .btn-ghost-green::before,
                .btn-ghost-green::after {
                    display: none !important;
                }
                .scroll-text-ani {
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                }
             `}</style>

            {/* Lightbox Overlay */}
            {lightboxImage && (
                <div
                    className="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                    style={{
                        zIndex: 9999,
                        backgroundColor: "rgba(0,0,0,0.9)",
                    }}
                    onClick={() => setLightboxImage(null)}
                >
                    <button
                        className="btn-close btn-close-white position-absolute top-0 end-0 m-4"
                        onClick={() => setLightboxImage(null)}
                        aria-label="Close"
                        style={{
                            width: "2rem",
                            height: "2rem",
                            backgroundSize: "contain",
                        }}
                    ></button>
                    <img
                        src={lightboxImage}
                        alt="Award Full View"
                        className="img-fluid"
                        style={{
                            maxHeight: "90vh",
                            maxWidth: "90vw",
                            objectFit: "contain",
                            animation: "zoomIn 0.3s ease-out",
                        }}
                        onClick={(e) => e.stopPropagation()} // Prevent closing when clicking image
                    />
                    <style>{`
                        @keyframes zoomIn {
                            from { transform: scale(0.9); opacity: 0; }
                            to { transform: scale(1); opacity: 1; }
                        }
                    `}</style>
                </div>
            )}
        </MainLayout>
    );
};

export default BrandLanding;
