import { Head, Link } from "@inertiajs/react";
import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

const ServiceItemDetail = ({ item }) => {
    const waNumber = "62811400262"; // Replace with actual number
    const waLink = `https://wa.me/${waNumber}?text=${encodeURIComponent(
        item.wa_message || `I am interested in ${item.title}`
    )}`;

    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) {
            return path;
        }
        return `/storage/${path}`;
    };

    const staticShowcase = [
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
    ];

    return (
        <MainLayout>
            <Head title={item.title} />

            <Breadcrumb
                title={item.title}
                subtitle={`${item.parent_title} Solution`}
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    {
                        label: item.parent_title,
                        link: `/services/${item.parent_service}`,
                    },
                    { label: item.title },
                ]}
            />

            <div
                className="about-area smoke-bg p-5 position-relative overflow-hidden space-top space-bottom"
                id="about-sec"
            >
                <div className="container">
                    <div className="row">
                        <div className="col-xl-6">
                            <div className="img-box6">
                                <div className="img1 reveal">
                                    <img
                                        src={getImageUrl(item.images[0])}
                                        alt={item.title}
                                    />
                                </div>
                                {item.images[1] && (
                                    <div className="img2 reveal">
                                        <img
                                            src={getImageUrl(item.images[1])}
                                            alt={item.title}
                                        />
                                    </div>
                                )}
                            </div>
                        </div>
                        <div className="col-xl-6">
                            <div className="ps-xl-3 ms-xl-3 ps-xl-5 ms-xxl-5">
                                <div className="title-area about-7-titlebox mb-20">
                                    <span className="sub-title style1 text-anime-style-2">
                                        {item.title}
                                    </span>
                                    <h2 className="sec-title mb-20 text-anime-style-3">
                                        {item.subtitle || item.title}
                                    </h2>
                                    <p className="sec-text mb-30 wow fadeInUp">
                                        {item.description}
                                    </p>
                                </div>
                                <div className="about-item-wrap">
                                    {item.features.map((feature, index) => (
                                        <div
                                            key={index}
                                            className="about-item wow fadeInUp"
                                        >
                                            <div className="about-item_img">
                                                <img
                                                    src={getImageUrl(
                                                        feature.icon
                                                    )}
                                                    alt="icon"
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
                                {item.wa_message && (
                                    <div className="mt-35 wow fadeInUp">
                                        <a
                                            href={waLink}
                                            target="_blank"
                                            className="th-btn black-btn th-radius th-icon"
                                        >
                                            Consultation Now{" "}
                                            <i className="fa-light fa-arrow-right-long"></i>
                                        </a>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* <section className="category-area3 space">
                <div className="container th-container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="title-area text-center mb-55">
                                <span className="sub-title text-anime-style-2">
                                    Room Configurator
                                </span>
                                <h2 className="sec-title text-anime-style-3">
                                    Select a Brand to Start Configuration
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div className="slider-area category-style-3">
                        <div
                            className="swiper th-slider has-shadow category-slider3"
                            id="categorySlider3"
                            data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"3"},"1400":{"slidesPerView":"5"}}}'
                        >
                            <div className="swiper-wrapper">
                                {item.brands.map((brand, index) => (
                                    <div key={index} className="swiper-slide">
                                        <div className="category-card single2 text-center">
                                            <div className="box-img global-img mb-3">
                                                <Link
                                                    href={`${
                                                        item.configurator_route
                                                    }?brand=${brand.name
                                                        .toLowerCase()
                                                        .replace(
                                                            /\s+/g,
                                                            "-"
                                                        )}&type=${item.id}`}
                                                >
                                                    <img
                                                        src={getImageUrl(
                                                            brand.image
                                                        )}
                                                        alt={brand.name}
                                                        style={{
                                                            borderRadius:
                                                                "10px",
                                                        }}
                                                    />
                                                </Link>
                                            </div>
                                            <h3 className="box-title">
                                                <Link
                                                    href={`${
                                                        item.configurator_route
                                                    }?brand=${brand.name
                                                        .toLowerCase()
                                                        .replace(
                                                            /\s+/g,
                                                            "-"
                                                        )}&type=${item.id}`}
                                                >
                                                    {brand.name}
                                                </Link>
                                            </h3>
                                            <span className="category-text">
                                                {brand.desc}
                                            </span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section> */}

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
                        <div
                            className="swiper th-slider has-shadow category-slider3"
                            id="categorySlider3"
                            data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"3"},"1400":{"slidesPerView":"5"}}}'
                        >
                            <div className="swiper-wrapper">
                                {item.brands.map((brand, index) => (
                                    <div key={index} className="swiper-slide">
                                        <div className="category-card single2 text-center">
                                            <div className="box-img global-img mb-3">
                                                <Link
                                                    href={`/${brand.name
                                                        .toLowerCase()
                                                        .replace(/\s+/g, "-")}`}
                                                >
                                                    <img
                                                        src={getImageUrl(
                                                            brand.image
                                                        )}
                                                        alt={brand.name}
                                                        style={{
                                                            borderRadius:
                                                                "10px",
                                                        }}
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
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Work Showcase Section */}
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
                        <div
                            className="swiper th-slider case-slider3 slider-drag-wrap"
                            id="caseSlider1"
                            data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"3"}},"effect":"coverflow","coverflowEffect":{"rotate":"-17","stretch":"-8","depth":"330","modifier":"1","slideShadows":"false"},"centeredSlides":"true"}'
                        >
                            <div className="swiper-wrapper">
                                {staticShowcase.map((project, index) => (
                                    <div key={index} className="swiper-slide">
                                        <div className="case-box3 gsap-cursor">
                                            <div className="case-img position-relative">
                                                <img
                                                    src={project.image}
                                                    alt="case image"
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
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
};

export default ServiceItemDetail;
