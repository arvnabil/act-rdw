import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { EffectCoverflow } from "swiper/modules";
import SectionTitle from "@/Components/Common/SectionTitle";
import "swiper/css";
import "swiper/css/effect-coverflow";

export default function WorkShowcaseSection({ staticShowcase, config }) {
    return (
        <div
            className="case-area3 position-relative overflow-hidden space-bottom"
            id="project-showcase"
        >
            <div className="container th-container">
                <SectionTitle
                    subTitle="Project"
                    title={config?.title || "Our Work Showcase"}
                    align="text-center"
                />
                <div className="slider-area">
                    <Swiper
                        modules={[EffectCoverflow]}
                        effect={"coverflow"}
                        grabCursor={true}
                        centeredSlides={true}
                        loop={true}
                        slidesPerView={"auto"}
                        coverflowEffect={{
                            rotate: -17,
                            stretch: -8,
                            depth: 330,
                            modifier: 1,
                            slideShadows: false,
                        }}
                        breakpoints={{
                            0: { slidesPerView: 1 },
                            576: { slidesPerView: 2 },
                            992: { slidesPerView: 3 },
                            1200: { slidesPerView: 3 },
                        }}
                        className="case-slider3"
                    >
                        {staticShowcase.map((project, index) => (
                            <SwiperSlide key={index}>
                                <div className="case-box3 gsap-cursor">
                                    <div className="case-box3-inner">
                                        <div
                                            className="case-img position-relative"
                                            style={{
                                                height: "400px",
                                                overflow: "hidden",
                                            }}
                                        >
                                            <img
                                                src={project.image}
                                                alt="case image"
                                                className="w-100 h-100 object-fit-cover rounded-3"
                                            />
                                            <div className="case-content">
                                                <h3 className="case-title">
                                                    <a href="#">
                                                        {project.title}
                                                    </a>
                                                </h3>
                                                <span className="case-categ">
                                                    {project.category}
                                                </span>
                                                <a
                                                    href="#"
                                                    className="case-icon"
                                                >
                                                    <i className="fa-light fa-arrow-right-long"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </SwiperSlide>
                        ))}
                    </Swiper>
                </div>
            </div>
        </div>
    );
}
