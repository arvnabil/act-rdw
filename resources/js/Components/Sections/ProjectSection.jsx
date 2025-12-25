import React from 'react';
import { Link } from '@inertiajs/react';

export default function ProjectSection() {
    return (
        <section className="space overflow-hidden">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>Recent Projects<span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span className="scroll-text-ani">Work Showcase</span>
                            </h2>
                        </div>
                    </div>
                    <div className="col-auto">
                        <div className="sec-btn">
                            <div className="icon-box">
                                <button data-slider-prev="#projectSlider3" className="slider-arrow style2 default">
                                    <i className="far fa-arrow-left"></i>
                                </button>
                                <button data-slider-next="#projectSlider3" className="slider-arrow style2 default">
                                    <i className="far fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="slider-area project-slider3 slider-drag-wrap">
                    <div className="swiper th-slider" id="projectSlider3" data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"1"},"992":{"slidesPerView":"1","centeredSlides":true,"centeredSlidesBounds":true},"1200":{"slidesPerView":"4.5","centeredSlidesBounds":true},"1921":{"slidesPerView":"4.5","centeredSlidesBounds":true}}}'>
                        <div className="swiper-wrapper">
                            {['3_9_', '3_9_', '3_9_', '3_9_', '3_9_', '3_9_', '3_9_', '3_9_', '3_9'].map((img, index) => (
                                <div className="swiper-slide" key={index}>
                                    <div className="project-card3">
                                        <div className="box-img">
                                            <img src={`/assets/img/project/project_${img}.jpg`} alt="image" />
                                        </div>
                                        <div className="project-content">
                                            <div className="box-content">
                                                <h3 className="box-title">
                                                    <Link href="/projects">Project for Marketing</Link>
                                                </h3>
                                                <span className="sub-title">IT TECHNOLOGY</span>
                                            </div>
                                            <Link href="/projects" className="icon-btn">
                                                <i className="fa-light fa-arrow-right"></i>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
