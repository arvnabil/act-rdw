import React from "react";

export default function TestimonialSection({
    title = "What Client Say About us",
    subtitle = "Testimonials",
    testimonials = [
        {
            name: "Sarah Rahman",
            role: "UI/UX Designer",
            quote: "Cybersecurity is more critical than ever in a world increasingly reliant on digital technologies. By investing in robust cybersecurity measures, individuals and organizations can protect themselves from threats and secure their digital assets for the future.",
            rating: 5,
            image: "/assets/img/testimonial/testi_4_1.png",
        },
        {
            name: "Angelina Rose",
            role: "App Developer",
            quote: "Cybersecurity is more critical than ever in a world increasingly reliant on digital technologies. By investing in robust cybersecurity measures, individuals and organizations can protect themselves from threats and secure their digital assets for the future.",
            rating: 5,
            image: "/assets/img/testimonial/testi_4_2.png",
        },
        {
            name: "Michel Smith",
            role: "Software Engineer",
            quote: "Cybersecurity is more critical than ever in a world increasingly reliant on digital technologies. By investing in robust cybersecurity measures, individuals and organizations can protect themselves from threats and secure their digital assets for the future.",
            rating: 5,
            image: "/assets/img/testimonial/testi_4_3.png",
        },
        {
            name: "Jesmen",
            role: "Graphics Designer",
            quote: "Cybersecurity is more critical than ever in a world increasingly reliant on digital technologies. By investing in robust cybersecurity measures, individuals and organizations can protect themselves from threats and secure their digital assets for the future.",
            rating: 5,
            image: "/assets/img/testimonial/testi_4_4.png",
        },
    ],
}) {
    // Helper helper to render stars
    const renderStars = (rating) => {
        return [...Array(rating || 5)].map((_, i) => (
            <i key={i} className="fa-solid fa-star"></i>
        ));
    };

    return (
        <section
            className="testi-area5 bg-smoke overflow-hidden space"
            id="testi-sec"
        >
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-6">
                        <div className="testi-image-wrapp">
                            <div className="testi-img">
                                <img
                                    src="/assets/img/testimonial/testi-img-10.jpg"
                                    alt="Testimonial 1"
                                />
                            </div>
                            <div className="testi-img style2">
                                <img
                                    src="/assets/img/testimonial/testi-img-20.jpg"
                                    alt="Testimonial 2"
                                />
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6">
                        <div className="title-area testi5-titlebox mb-40 mt-40">
                            <span className="sub-title text-anime-style-2">
                                <span className="squre-shape left me-2"></span>
                                {subtitle}
                                <span className="squre-shape right ms-2"></span>
                            </span>
                            <h2 className="sec-title text-anime-style-3">
                                {title}
                            </h2>
                        </div>

                        <div
                            className="swiper th-slider testiSlide5"
                            id="testiSlide5"
                            data-slider-options='{"effect":"slide","loop":false,"thumbs":{"swiper":".testi-grid2-thumb"}}'
                        >
                            <div className="swiper-wrapper">
                                {testimonials.map((item, index) => (
                                    <div key={index} className="swiper-slide">
                                        <div className="testi-grid2">
                                            <div className="box-content">
                                                <p className="box-text">
                                                    “{item.quote}”
                                                </p>
                                                <h6 className="box-title">
                                                    {item.name}
                                                </h6>
                                                <span className="box-desig">
                                                    {item.role}
                                                </span>
                                                <div className="box-review">
                                                    {renderStars(item.rating)}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div
                            className="swiper th-slider testi-grid2-thumb mt-4"
                            id="testiSlide6"
                            data-slider-options='{"effect":"slide","slidesPerView":"6","spaceBetween":2,"loop":false}'
                        >
                            <div className="swiper-wrapper">
                                {testimonials.map((item, index) => (
                                    <div key={index} className="swiper-slide">
                                        <div className="box-img">
                                            <img
                                                src={
                                                    item.image ||
                                                    "/assets/img/icon/user.svg"
                                                }
                                                alt={item.name}
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>
                            <div className="icon-box justify-content-start">
                                <button
                                    data-slider-prev="#testiSlide5, #testiSlide6"
                                    className="slider-arrow default"
                                    style={{
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "center",
                                    }}
                                >
                                    <img
                                        src="/assets/img/icon/left-arrow2.svg"
                                        alt=""
                                        style={{ margin: 0 }}
                                    />
                                </button>
                                <button
                                    data-slider-next="#testiSlide5, #testiSlide6"
                                    className="slider-arrow default"
                                    style={{
                                        display: "flex",
                                        alignItems: "center",
                                        justifyContent: "center",
                                    }}
                                >
                                    <img
                                        src="/assets/img/icon/right-arrow2.svg"
                                        alt=""
                                        style={{ margin: 0 }}
                                    />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
