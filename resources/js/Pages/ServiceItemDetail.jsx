import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import { Swiper, SwiperSlide } from "swiper/react";
import {
    Navigation,
    Pagination,
    Autoplay,
    EffectCoverflow,
} from "swiper/modules";

// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/effect-coverflow";

export default function ServiceItemDetail({ item: service }) {
    return (
        <MainLayout>
            <Head title={service.title} />
            <Breadcrumb
                title={service.title}
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    {
                        label: service.parent_title,
                        link: `/services/${service.parent_service}`,
                    },
                    { label: service.title },
                ]}
            />

            {/* About Area */}
            <div
                className="about-area smoke-bg p-5 position-relative overflow-hidden"
                id="about-sec"
            >
                <div className="container">
                    <div className="row">
                        <div className="col-xl-6">
                            <div className="img-box6">
                                <div className="img1 reveal">
                                    <img src={service.images[0]} alt="About" />
                                </div>
                                <div className="img2 reveal">
                                    <img src={service.images[1]} alt="About" />
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-6">
                            <div className="ps-xl-3 ms-xl-3 ps-xl-5 ms-xxl-5">
                                <div className="title-area about-7-titlebox mb-20">
                                    <span className="sub-title style1 text-anime-style-2">
                                        {service.title}
                                    </span>
                                    <h2 className="sec-title mb-20 text-anime-style-3">
                                        {service.subtitle}
                                    </h2>
                                    <p
                                        className="sec-text mb-30 wow fadeInUp"
                                        data-wow-delay=".4s"
                                    >
                                        {service.description}
                                    </p>
                                </div>
                                <div className="about-item-wrap">
                                    {service.features.map((feature, index) => (
                                        <div
                                            className="about-item wow fadeInUp"
                                            data-wow-delay={`.${5 + index}s`}
                                            key={index}
                                        >
                                            <div className="about-item_img">
                                                <img
                                                    src={feature.icon}
                                                    alt=""
                                                />
                                            </div>
                                            <div className="about-item_centent">
                                                <h5 className="box-title">
                                                    {feature.title}
                                                </h5>
                                                <p className="about-item_text">
                                                    {feature.text}
                                                </p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                <div
                                    className="mt-35 wow fadeInUp"
                                    data-wow-delay=".7s"
                                >
                                    <Link
                                        href="/contact"
                                        className="th-btn black-btn th-radius th-icon"
                                    >
                                        Consultation Now{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Category Area (Brand Configurator) */}
            <section className="category-area3 space">
                <div className="container th-container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="title-area text-center mb-55">
                                <span className="sub-title text-anime-style-2">
                                    Solution Configurator
                                </span>
                                <h2 className="sec-title text-anime-style-3">
                                    Select a Brand to Start Configuration
                                </h2>
                            </div>
                        </div>
                    </div>

                    <div className="slider-area category-style-3">
                        <Swiper
                            modules={[Navigation, Autoplay]}
                            spaceBetween={30}
                            slidesPerView={1}
                            autoplay={{
                                delay: 3000,
                                disableOnInteraction: false,
                            }}
                            breakpoints={{
                                576: { slidesPerView: 1 },
                                768: { slidesPerView: 2 },
                                992: { slidesPerView: 3 },
                                1200: { slidesPerView: 4 }, // Adjusted for better fit
                                1400: { slidesPerView: 5 },
                            }}
                            className="category-slider3 has-shadow"
                        >
                            {service.brands.map((brand, index) => (
                                <SwiperSlide key={index}>
                                    <div className="category-card single2">
                                        <div className="box-img global-img">
                                            <Link
                                                href={`${
                                                    service.configurator_route ||
                                                    "/room-configurator"
                                                }?brand=${
                                                    brand.name
                                                        .toLowerCase()
                                                        .split(" ")[0]
                                                }`}
                                            >
                                                <img
                                                    src={brand.image}
                                                    alt={brand.name}
                                                />
                                            </Link>
                                        </div>
                                        <h3 className="box-title">
                                            <Link
                                                href={`${
                                                    service.configurator_route ||
                                                    "/room-configurator"
                                                }?brand=${
                                                    brand.name
                                                        .toLowerCase()
                                                        .split(" ")[0]
                                                }`}
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
                            {/* Duplicating brands to simulate infinite feel if items are few */}
                            {service.brands.map((brand, index) => (
                                <SwiperSlide key={`dup-${index}`}>
                                    <div className="category-card single2">
                                        <div className="box-img global-img">
                                            <Link
                                                href={`${
                                                    service.configurator_route ||
                                                    "/room-configurator"
                                                }?brand=${
                                                    brand.name
                                                        .toLowerCase()
                                                        .split(" ")[0]
                                                }`}
                                            >
                                                <img
                                                    src={brand.image}
                                                    alt={brand.name}
                                                />
                                            </Link>
                                        </div>
                                        <h3 className="box-title">
                                            <Link
                                                href={`${
                                                    service.configurator_route ||
                                                    "/room-configurator"
                                                }?brand=${
                                                    brand.name
                                                        .toLowerCase()
                                                        .split(" ")[0]
                                                }`}
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

            {/* Work Showcase Area */}
            <div className="case-area3 position-relative overflow-hidden space-bottom">
                <div className="container th-container">
                    <div className="title-area text-center">
                        <span className="sub-title text-anime-style-2">
                            Project
                        </span>
                        <h2 className="sec-title text-anime-style-3">
                            Our Work Showcase
                        </h2>
                    </div>
                    <div className="slider-area">
                        <Swiper
                            modules={[Navigation, EffectCoverflow, Autoplay]}
                            effect={"coverflow"}
                            grabCursor={true}
                            centeredSlides={true}
                            slidesPerView={"auto"}
                            coverflowEffect={{
                                rotate: -17,
                                stretch: -8,
                                depth: 330,
                                modifier: 1,
                                slideShadows: false,
                            }}
                            loop={true}
                            autoplay={{
                                delay: 2500,
                                disableOnInteraction: false,
                            }}
                            breakpoints={{
                                0: { slidesPerView: 1 },
                                576: { slidesPerView: 2 },
                                992: { slidesPerView: 3 },
                            }}
                            className="case-slider3"
                        >
                            {service.projects.map((project, index) => (
                                <SwiperSlide
                                    key={index}
                                    className="case-box3-slide"
                                >
                                    <div className="case-box3">
                                        <div className="case-img position-relative">
                                            <img
                                                src={project.image}
                                                alt="case"
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
                                </SwiperSlide>
                            ))}
                            {/* Add some more dummy slides to ensure effect works if few items */}
                            {service.projects.map((project, index) => (
                                <SwiperSlide
                                    key={`dup-${index}`}
                                    className="case-box3-slide"
                                >
                                    <div className="case-box3">
                                        <div className="case-img position-relative">
                                            <img
                                                src={project.image}
                                                alt="case"
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
                                </SwiperSlide>
                            ))}
                        </Swiper>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
