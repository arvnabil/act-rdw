import React from "react";
import { Link } from "@inertiajs/react";
import ProductCard from "@/Components/Common/ProductCard";

export default function RelatedProducts({ relatedProducts }) {
    if (!relatedProducts || relatedProducts.length === 0) return null;

    return (
        <div className="space-extra-top mb-30">
            <div className="row">
                <div className="title-area mb-25 text-center">
                    <span className="sub-title">Similar Products</span>
                    <h2 className="sec-title">Recently Solution Products</h2>
                </div>
            </div>
            <div
                className="swiper th-slider has-shadow"
                id="productSlider1"
                data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"2"},"768":{"slidesPerView":"2"},"992":{"slidesPerView":"3"},"1200":{"slidesPerView":"4"}}}'
            >
                <div className="swiper-wrapper">
                    {relatedProducts.map((related, index) => (
                        <div className="swiper-slide style2" key={related.id}>
                            <ProductCard product={related} />
                        </div>
                    ))}
                </div>
                <div className="d-block d-md-none mt-40 text-center">
                    <div className="icon-box">
                        <button
                            data-slider-prev="#productSlider1"
                            className="slider-arrow default"
                        >
                            <i className="far fa-arrow-left"></i>
                        </button>
                        <button
                            data-slider-next="#productSlider1"
                            className="slider-arrow default"
                        >
                            <i className="far fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
