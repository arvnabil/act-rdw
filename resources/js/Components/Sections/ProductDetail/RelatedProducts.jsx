import React from "react";
import { Link } from "@inertiajs/react";

export default function RelatedProducts({ relatedProducts }) {
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
                            <div className="th-product">
                                <div className="product-img">
                                    <img
                                        src={related.image}
                                        onError={(e) =>
                                            (e.target.src =
                                                "/assets/img/product/logitech meetup.jpg")
                                        }
                                        alt={related.name}
                                    />
                                    {related.tag && (
                                        <span className="product-tag">
                                            {related.tag}
                                        </span>
                                    )}
                                    <div className="actions">
                                        <Link
                                            href={`/products/${related.slug}`}
                                            className="icon-btn"
                                        >
                                            <i className="far fa-eye"></i>
                                        </Link>
                                        <a
                                            href="#"
                                            className="icon-btn"
                                            onClick={(e) => e.preventDefault()}
                                        >
                                            <i className="far fa-phone"></i>
                                        </a>
                                    </div>
                                </div>
                                <div className="product-content">
                                    <span className="price">
                                        {related.category}
                                    </span>
                                    <h3 className="product-title">
                                        <Link
                                            href={`/products/${related.slug}`}
                                        >
                                            {related.name}
                                        </Link>
                                    </h3>
                                </div>
                            </div>
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
