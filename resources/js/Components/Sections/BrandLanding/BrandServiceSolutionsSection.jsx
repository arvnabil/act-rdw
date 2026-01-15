import React from "react";
import { Link } from "@inertiajs/react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination } from "swiper/modules";
import SectionTitleLight from "@/Components/Common/SectionTitleLight";
import "swiper/css";

export default function BrandServiceSolutionsSection({
    relatedServices,
    getImageUrl,
}) {
    if (!relatedServices || relatedServices.length === 0) return null;

    return (
        <>
            {relatedServices.map((service, sIndex) => (
                <section
                    key={sIndex}
                    className="position-relative space-top space-extra-bottom"
                    id={`service-${service.slug}`}
                    style={{
                        backgroundImage: `url(${
                            service.image
                                ? getImageUrl(service.image)
                                : "/assets/img/bg/bg_overlay_1.png"
                        })`,
                        backgroundSize: "cover",
                        backgroundPosition: "center",
                    }}
                >
                    <div
                        className="position-absolute top-0 start-0 w-100 h-100"
                        style={{
                            background:
                                "linear-gradient(to bottom, #0A141E 0%, rgba(10, 20, 30, 0.85) 15%, rgba(10, 20, 30, 0.85) 85%, #0A141E 100%)",
                            zIndex: 1,
                        }}
                    ></div>
                    <div
                        className="container th-container position-relative"
                        style={{ zIndex: 2 }}
                    >
                        <div className="row justify-content-center">
                            <div className="col-xl-8">
                                <div className="title-area text-center mb-55">
                                    <SectionTitleLight
                                        subTitle={service.sub_title}
                                        title={service.title}
                                        align="title-area service-title-box text-center"
                                        subTitleStyle={{
                                            backgroundColor: "transparent",
                                            color: "#20B2AA",
                                        }}
                                    />
                                </div>
                            </div>
                        </div>

                        <div className="slider-area">
                            <Swiper
                                modules={[Navigation, Pagination]}
                                spaceBetween={30}
                                slidesPerView={1}
                                pagination={{
                                    el: `.service-pagination-${sIndex}`,
                                    type: "progressbar",
                                }}
                                navigation={{
                                    prevEl: `.service-prev-${sIndex}`,
                                    nextEl: `.service-next-${sIndex}`,
                                }}
                                onBeforeInit={(swiper) => {
                                    swiper.params.navigation.prevEl = `.service-prev-${sIndex}`;
                                    swiper.params.navigation.nextEl = `.service-next-${sIndex}`;
                                }}
                                breakpoints={{
                                    576: { slidesPerView: 1 },
                                    768: { slidesPerView: 2 },
                                    992: { slidesPerView: 3 },
                                    1200: { slidesPerView: 3 },
                                }}
                                className="service-slider"
                            >
                                {service.solutions.map((solution, i) => (
                                    <SwiperSlide key={i}>
                                        <div className="room-card position-relative overflow-hidden rounded-20 bg-dark">
                                            <div
                                                className="room-img position-relative overflow-hidden room-img-height"
                                                style={{ borderRadius: "20px" }}
                                            >
                                                <img
                                                    src={
                                                        solution.image
                                                            ? getImageUrl(
                                                                  solution.image
                                                              )
                                                            : "/assets/img/logo/activ-logo-white.png"
                                                    }
                                                    onError={(e) => {
                                                        e.target.onerror = null;
                                                        e.target.src =
                                                            "https://placehold.co/600x400?text=No+Image";
                                                    }}
                                                    alt={solution.title}
                                                    className="w-100 h-100 object-fit-cover"
                                                    style={{
                                                        transition:
                                                            "transform 0.5s ease",
                                                    }}
                                                />
                                                <div
                                                    className="position-absolute bottom-0 start-0 w-100 p-4"
                                                    style={{
                                                        background:
                                                            "linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 60%, transparent 100%)",
                                                        zIndex: 2,
                                                    }}
                                                >
                                                    <h3 className="text-white mb-2 h4 fw-bold">
                                                        {solution.title}
                                                    </h3>
                                                    <p className="text-white-50 mb-4 fs-xs">
                                                        {solution.desc}
                                                    </p>
                                                    <Link
                                                        href={`/services/${service.slug}/${solution.slug}`}
                                                        className="th-btn style3 th-radius th-icon room-btn btn-ghost-green"
                                                    >
                                                        Show Products{" "}
                                                        <i className="fa-regular fa-arrow-right ms-2"></i>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                ))}
                            </Swiper>

                            <div className="d-none d-md-flex align-items-center justify-content-center gap-4 mt-5">
                                <button
                                    className={`service-prev-${sIndex} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`}
                                >
                                    <i className="fa-regular fa-arrow-left"></i>
                                </button>
                                <div
                                    className={`service-pagination-${sIndex} position-relative`}
                                    style={{
                                        width: "150px",
                                        height: "2px",
                                        background: "rgba(255,255,255,0.2)",
                                        borderRadius: "2px",
                                        overflow: "hidden",
                                    }}
                                ></div>
                                <button
                                    className={`service-next-${sIndex} icon-btn style2 bg-transparent border rounded-circle border-white text-white hover-white`}
                                >
                                    <i className="fa-regular fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            ))}
        </>
    );
}
