import React from 'react';
import { Link } from '@inertiajs/react';

export default function ClientSection() {
    return (
        <section className="overflow-hidden">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>Clients<span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span className="scroll-text-ani">Our Clients</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="brand-area overflow-hidden space-bottom">
                    <div className="container th-container">
                        <div className="swiper th-slider brandSlider1" id="brandSlider2" data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"2"},"768":{"slidesPerView":"3"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"5"},"1400":{"slidesPerView":"6"}}}'>
                            <div className="swiper-wrapper">
                                {['1_1', '1_2', '1_3', '1_4', '1_5', '1_6', '1_7', '1_1', '1_4', '1_3', '1_2', '1_1', '1_1', '1_1'].map((brand, index) => (
                                    <div className="swiper-slide" key={index}>
                                        <div className="brand-box">
                                            <a href="#" onClick={(e) => e.preventDefault()}>
                                                <img className="original" src={`/assets/img/brand/brand_${brand}.svg`} alt="Brand Logo" />
                                                <img className="gray" src={`/assets/img/brand/brand_${brand}.svg`} alt="Brand Logo" />
                                            </a>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
