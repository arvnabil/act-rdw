import React from "react";

export default function BrandSection({
    brands,
    title,
    subtitle,
    elementId = "brandSlider1",
    show_button = true,
    button_text = "Lihat Semua Partner",
    button_url = "/partners",
}) {
    // Normalize show_button to boolean
    const shouldShowButton =
        show_button === true ||
        show_button === "true" ||
        show_button === 1 ||
        show_button === "1";

    const list = brands || [];

    // Only render title area if title or subtitle is provided
    const showHeader = title || subtitle;

    return (
        <div className="brand-area overflow-hidden space-bottom">
            <div className="container th-container">
                {showHeader && (
                    <div className="row justify-content-lg-between justify-content-center align-items-center mb-4">
                        <div className="col-lg-6 mb-n2 mb-lg-0">
                            <div className="title-area text-center text-lg-start">
                                {subtitle && (
                                    <span className="sub-title">
                                        <span className="squre-shape left me-3"></span>
                                        {subtitle}
                                        <span className="squre-shape d-lg-none right ms-3"></span>
                                    </span>
                                )}
                                {title && (
                                    <h2 className="sec-title">
                                        <span className="scroll-text-ani">
                                            {title}
                                        </span>
                                    </h2>
                                )}
                            </div>
                        </div>
                    </div>
                )}
                <div
                    className="swiper th-slider brandSlider1"
                    id={elementId}
                    data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"2"},"768":{"slidesPerView":"3"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"5"},"1400":{"slidesPerView":"6"}}}'
                >
                    <div className="swiper-wrapper">
                        {list.map((brand, index) => (
                            <div className="swiper-slide" key={index}>
                                <div className="brand-box">
                                    <a
                                        href="#"
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        <img
                                            className="original"
                                            src={brand.image}
                                            alt={brand.name || "Brand Logo"}
                                        />
                                        <img
                                            className="gray"
                                            src={brand.image}
                                            alt={brand.name || "Brand Logo"}
                                        />
                                    </a>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            {shouldShowButton && (
                <div className="text-center mt-5">
                    <div className="d-inline-block">
                        <a
                            href={button_url}
                            className="th-btn th-radius th-icon"
                        >
                            {button_text}{" "}
                            <i className="fa-light fa-arrow-right-long"></i>
                        </a>
                    </div>
                </div>
            )}
        </div>
    );
}
