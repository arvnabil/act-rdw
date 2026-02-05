import React from "react";
import { Link } from "@inertiajs/react";

export default function ClientSection({
    clients,
    title,
    subtitle,
    show_button,
    button_text,
    button_url,
}) {
    const defaultClients = [
        "1_1",
        "1_2",
        "1_3",
        "1_4",
        "1_5",
        "1_6",
        "1_7",
        "1_1",
        "1_4",
        "1_3",
        "1_2",
        "1_1",
        "1_1",
        "1_1",
    ];

    const list = clients && clients.length > 0 ? clients : defaultClients;
    const t = title || "Klien Kami";
    const st = subtitle || "Klien";

    // Normalize show_button
    const shouldShowButton =
        show_button === true ||
        show_button === "true" ||
        show_button === 1 ||
        show_button === "1";

    const btnText = button_text || "Lihat Semua Klien";
    const btnUrl = button_url || "/clients";

    return (
        <section className="overflow-hidden space-top">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>
                                {st}
                                <span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span className="scroll-text-ani">{t}</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="brand-area overflow-hidden">
                    <div className="container th-container">
                        <div
                            className="swiper th-slider brandSlider1"
                            id="brandSlider2"
                            data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"2"},"768":{"slidesPerView":"3"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"5"},"1400":{"slidesPerView":"6"}}}'
                        >
                            <div className="swiper-wrapper">
                                {list.map((brand, index) => (
                                    <div className="swiper-slide" key={index}>
                                        <div
                                            className="brand-box d-flex justify-content-center align-items-center"
                                            style={{ minHeight: "100px" }}
                                        >
                                            <a
                                                href="#"
                                                className="d-flex justify-content-center align-items-center w-100 h-100"
                                                onClick={(e) =>
                                                    e.preventDefault()
                                                }
                                            >
                                                <img
                                                    className="original"
                                                    src={
                                                        typeof brand ===
                                                            "string"
                                                            ? `/assets/img/brand/brand_${brand}.svg`
                                                            : brand.image ||
                                                            brand
                                                    }
                                                    alt="Brand Logo"
                                                    style={{
                                                        objectFit: "contain",
                                                        width: "auto",
                                                        height: "auto",
                                                        maxWidth: "100%",
                                                        maxHeight: "90px", // Limit height to prevent cropping
                                                    }}
                                                />
                                                <img
                                                    className="gray"
                                                    src={
                                                        typeof brand ===
                                                            "string"
                                                            ? `/assets/img/brand/brand_${brand}.svg`
                                                            : brand.image ||
                                                            brand
                                                    }
                                                    alt="Brand Logo"
                                                    style={{
                                                        objectFit: "contain",
                                                        width: "auto",
                                                        height: "auto",
                                                        maxWidth: "100%",
                                                        maxHeight: "90px", // Limit height to prevent cropping
                                                    }}
                                                />
                                            </a>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                {shouldShowButton && (
                    <div className="text-center mt-3">
                        <Link href={btnUrl} className="th-btn th-radius">
                            {btnText}
                        </Link>
                    </div>
                )}
            </div>
        </section>
    );
}
