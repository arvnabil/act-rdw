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
            title: "Integrasi TIK yang Mulus untuk Bisnis",
            description:
                "Mitra tepercaya Anda untuk transformasi digital. Kami berspesialisasi dalam menghadirkan teknologi yang disesuaikan untuk Konferensi Video, Infrastruktur Data, dan sistem Keamanan.",
            buttons: [
                {
                    text: "Jelajahi Layanan Kami",
                    url: "/services",
                    style: "style7",
                },
                { text: "Hubungi Kami", url: "/contact", style: "style2" },
            ],
        },
        {
            bg_image: "/assets/img/hero/hero_bg_2_1.png",
            title: "Hubungkan Tim Anda, di Mana Saja Mereka Berada",
            description:
                "Rasakan konferensi video profesional untuk setiap ruang. Baik itu ruang berkumpul kecil atau auditorium besar, kami memastikan komunikasi yang jernih.",
            buttons: [
                {
                    text: "Solusi Ruang Rapat",
                    url: "/contact",
                    style: "style7",
                },
                { text: "Lihat Produk", url: "/services", style: "style2" },
            ],
        },
        {
            bg_image: "/assets/img/hero/hero_bg_2_2.png",
            title: "Fondasi Kuat untuk Data Anda",
            description:
                "Jaga kelancaran operasional Anda dengan server yang andal dan penyimpanan yang skalabel. Kami memberikan kinerja dan kapasitas yang dibutuhkan bisnis Anda untuk tetap unggul.",
            buttons: [
                {
                    text: "Solusi Infrastruktur",
                    url: "/contact",
                    style: "style7",
                },
                { text: "Hubungi Kami", url: "/services", style: "style2" },
            ],
        },
        {
            bg_image: "/assets/img/hero/hero_bg_4_1.png",
            title: "Keamanan Cerdas untuk Ketenangan Pikiran",
            description:
                "Lindungi hal yang paling berarti dengan sistem pengawasan canggih. Pantau lingkungan Anda secara real-time dan pastikan keamanan sepanjang waktu dengan solusi terintegrasi kami.",
            buttons: [
                {
                    text: "Lihat Sistem Keamanan",
                    url: "/contact",
                    style: "style7",
                },
                { text: "Hubungi Sales", url: "/services", style: "style2" },
            ],
        },
    ];

    const items = slides && slides.length > 0 ? slides : defaultSlides;

    // Swiper initialization is now handled globally in useTemplateInit.js
    // to ensure animation logic and navigation events are consolidated.
    // Double initialization was causing conflict and animation glitches.

    // useEffect(() => {
    //     const swiperInstance = new Swiper(`#${elementId}`, { ... });
    //     return () => { if (swiperInstance) swiperInstance.destroy(); };
    // }, [elementId, items]);

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
                                                (btn, btnIndex) => {
                                                    const isExternal =
                                                        btn.url?.startsWith(
                                                            "http",
                                                        );
                                                    const openNewTab =
                                                        btn.open_new_tab ||
                                                        btn.url?.startsWith(
                                                            "http",
                                                        ); // Auto new tab for external if not specified

                                                    if (
                                                        isExternal ||
                                                        openNewTab
                                                    ) {
                                                        return (
                                                            <a
                                                                key={btnIndex}
                                                                href={btn.url}
                                                                target={
                                                                    openNewTab
                                                                        ? "_blank"
                                                                        : "_self"
                                                                }
                                                                rel={
                                                                    openNewTab
                                                                        ? "noopener noreferrer"
                                                                        : undefined
                                                                }
                                                                className={`th-btn th-radius ${btn.style || "style2"} th-icon`}
                                                            >
                                                                {btn.text}
                                                                <i className="fa-light fa-arrow-right-long"></i>
                                                            </a>
                                                        );
                                                    }

                                                    return (
                                                        <Link
                                                            key={btnIndex}
                                                            href={btn.url}
                                                            className={`th-btn th-radius ${btn.style || "style2"} th-icon`}
                                                        >
                                                            {btn.text}
                                                            <i className="fa-light fa-arrow-right-long"></i>
                                                        </Link>
                                                    );
                                                },
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
                        Gulir ke Bawah
                    </a>
                </div>
            </div>
        </div>
    );
}
