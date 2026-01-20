import React, { useEffect } from "react";
import { Link } from "@inertiajs/react";
import Swiper from "swiper";
import { Navigation, Pagination, EffectFade, Autoplay } from "swiper/modules";
// Import Swiper styles if not globally imported, but app.css has them.
// We rely on app.css for custom template styles but ensuring modules load is key.

export default function SliderSection({ slides, elementId = "heroSlide2" }) {
    const defaultSlides = [
        {
            bg_image: "/assets/img/hero/hero_bg_3_1.png",
            title: "Seamless ICT Integration for Business",
            description:
                "Your trusted partner for digital transformation. We specialize in delivering tailored technology across Video Conferencing, Data Infrastructure, and Security systems.",
            buttons: [
                {
                    text: "Explore Our Services",
                    url: "/services",
                    style: "style7",
                },
                { text: "Get In Touch", url: "/contact", style: "style2" },
            ],
        },
        {
            bg_image: "/assets/img/hero/hero_bg_2_1.png",
            title: "Connect Your Teams, Wherever They Are",
            description:
                "Experience professional video conferencing for every space. Whether it’s a small huddle room or a large auditorium, we ensure crystal-clear communication.",
            buttons: [
                {
                    text: "Meeting Room Solutions",
                    url: "/contact",
                    style: "style7",
                },
                { text: "View Products", url: "/services", style: "style2" },
            ],
        },
        {
            bg_image: "/assets/img/hero/hero_bg_2_2.png",
            title: "A Strong Foundation for Your Data",
            description:
                "Keep your operations running smoothly with reliable servers and scalable storage. We provide the performance and capacity your business needs to stay ahead.",
            buttons: [
                {
                    text: "Infrastructure Solutions",
                    url: "/contact",
                    style: "style7",
                },
                { text: "Contact Us", url: "/services", style: "style2" },
            ],
        },
        {
            bg_image: "/assets/img/hero/hero_bg_4_1.png",
            title: "Smart Security for Peace of Mind",
            description:
                "Protect what matters most with advanced surveillance systems. Monitor your environment in real-time and ensure safety around the clock with our integrated solutions.",
            buttons: [
                {
                    text: "See Security Systems",
                    url: "/contact",
                    style: "style7",
                },
                { text: "Contact Sales", url: "/services", style: "style2" },
            ],
        },
    ];

    const items = slides && slides.length > 0 ? slides : defaultSlides;

    useEffect(() => {
        const swiperInstance = new Swiper(`#${elementId}`, {
            modules: [Navigation, Pagination, EffectFade, Autoplay],
            effect: "fade", // Revert to fade to fix blank screen
            fadeEffect: {
                crossFade: true, // Restore crossFade for non-overlapping transitions
            },
            loop: true, // Keep loop enabled as requested
            speed: 1000,
            observer: true,
            observeParents: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: `[data-slider-next="#${elementId}"]`,
                prevEl: `[data-slider-prev="#${elementId}"]`,
            },
        });

        return () => {
            if (swiperInstance) swiperInstance.destroy();
        };
    }, [elementId, items]);

    return (
        <div className="hero-2" id="hero">
            <div
                className="hero2-overlay"
                data-bg-src="/assets/img/bg/line-pattern.png"
            ></div>
            <div className="swiper hero-slider-2" id={elementId}>
                <div className="swiper-wrapper">
                    {items.map((slide, index) => (
                        <div className="swiper-slide" key={index}>
                            <div
                                className="hero-inner"
                                style={{ minHeight: "900px" }}
                            >
                                <div
                                    className="th-hero-bg"
                                    data-bg-src={slide.bg_image}
                                    style={{
                                        backgroundImage: `url(${slide.bg_image})`,
                                        backgroundSize: "cover",
                                        backgroundPosition: "center",
                                    }}
                                ></div>
                                <div className="container">
                                    <div className="hero-style2 text-center text-md-start">
                                        <h1
                                            className="hero-title mb-20"
                                            data-ani="slideinup"
                                            data-ani-delay="0.4s"
                                        >
                                            {slide.title}
                                        </h1>
                                        <p
                                            className="hero-desc"
                                            data-ani="slideinup"
                                            data-ani-delay="0.5s"
                                        >
                                            {slide.description}
                                        </p>
                                        <div
                                            className="btn-group text-center text-md-start"
                                            data-ani="slideinup"
                                            data-ani-delay="0.8s"
                                        >
                                            {slide.buttons?.map(
                                                (btn, btnIndex) => (
                                                    <Link
                                                        key={btnIndex}
                                                        href={btn.url}
                                                        className={`th-btn th-radius ${btn.style || "style2"} th-icon`}
                                                    >
                                                        {btn.text}
                                                        <i className="fa-light fa-arrow-right-long"></i>
                                                    </Link>
                                                ),
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
                <div className="th-swiper-custom">
                    <div className="swiper-pagination"></div>
                    <div className="hero-icon">
                        <button
                            data-slider-prev={`#${elementId}`}
                            className="hero-arrow slider-prev"
                        >
                            <img
                                src="/assets/img/icon/hero-arrow-left.svg"
                                alt=""
                            />
                        </button>
                        <button
                            data-slider-next={`#${elementId}`}
                            className="hero-arrow slider-next"
                        >
                            <img
                                src="/assets/img/icon/hero-arrow-right.svg"
                                alt=""
                            />
                        </button>
                    </div>
                </div>
                <div className="scroll-down">
                    <a href="#about-sec" className="scroll-wrap">
                        <span>
                            <img src="/assets/img/icon/down-arrow.svg" alt="" />
                        </span>
                        Scroll Down
                    </a>
                </div>
            </div>
        </div>
    );
}
