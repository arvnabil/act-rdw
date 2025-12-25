import React from 'react';

export default function BrandSection() {
    return (
        <div className="brand-area overflow-hidden space-bottom">
            <div className="container th-container">
                <div className="swiper th-slider brandSlider1" id="brandSlider1" data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"2"},"768":{"slidesPerView":"3"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"5"},"1400":{"slidesPerView":"6"}}}'>
                    <div className="swiper-wrapper">
                        {['1_1', '1_2', '1_3', '1_4', '1_5', '1_6', '1_7', '1_8', '1_9', '2_1'].map((brand, index) => (
                            <div className="swiper-slide" key={index}>
                                <div className="brand-box">
                                    <a href="https://www.logitech.com" target="_blank" rel="noreferrer">
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
    );
}
