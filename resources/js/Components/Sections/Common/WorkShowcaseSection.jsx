import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { EffectCoverflow } from "swiper/modules";
import SectionTitle from "@/Components/Common/SectionTitle";
import "swiper/css";
import "swiper/css/effect-coverflow";

export default function WorkShowcaseSection({
    staticShowcase,
    config,
    getImageUrl,
}) {
    // Helper to resolve image if getImageUrl is not passed (fallback for shared component)
    const resolveImg = (img) => (getImageUrl ? getImageUrl(img) : img);

    return (
        <div
            className="case-area3 position-relative overflow-hidden space-bottom"
            id="project-showcase"
        >
            <div className="container th-container">
                <SectionTitle
                    subTitle="Proyek"
                    title={config?.title || "Portofolio Proyek Kami"}
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
                            rotate: 0,
                            stretch: 0,
                            depth: 100,
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
                                                overflow: "hidden",
                                            }}
                                        >
                                            <img
                                                src={resolveImg(project.image)}
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
            <style jsx="true">{`
                .case-img {
                    height: 480px;
                }
                @media (max-width: 991px) {
                    .case-img {
                        height: 380px;
                    }
                }
                @media (max-width: 767px) {
                    .case-img {
                        height: 320px;
                    }
                    .case-title {
                        font-size: 20px !important;
                    }
                    .case-area3 {
                        padding-bottom: 40px !important;
                    }
                }
            `}</style>
        </div>
    );
}
