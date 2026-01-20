import React from "react";
import { Link } from "@inertiajs/react";

export default function ProjectSection({
    projects,
    title,
    subtitle,
    cta_text,
    cta_url,
}) {
    const list =
        projects && projects.length > 0
            ? projects
            : [
                  "3_9_",
                  "3_9_",
                  "3_9_",
                  "3_9_",
                  "3_9_",
                  "3_9_",
                  "3_9_",
                  "3_9_",
                  "3_9",
              ];
    const heading = title || "Work Showcase";
    const subHeading = subtitle || "Recent Projects";
    const btnText = cta_text || "View Details";

    return (
        <section className="space overflow-hidden">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>
                                {subHeading}
                                <span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span className="scroll-text-ani">
                                    {heading}
                                </span>
                            </h2>
                        </div>
                    </div>
                    <div className="col-auto">
                        <div className="sec-btn">
                            <div className="icon-box">
                                <button
                                    data-slider-prev="#projectSlider3"
                                    className="slider-arrow style2 default"
                                >
                                    <i className="far fa-arrow-left"></i>
                                </button>
                                <button
                                    data-slider-next="#projectSlider3"
                                    className="slider-arrow style2 default"
                                >
                                    <i className="far fa-arrow-right"></i>
                                </button>
                            </div>
                            {cta_url && (
                                <Link
                                    href={cta_url}
                                    className="th-btn th-radius style4 ms-4"
                                >
                                    {cta_text || "View All Projects"}
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
                <div className="slider-area project-slider3 slider-drag-wrap">
                    <div
                        className="swiper th-slider"
                        id="projectSlider3"
                        data-slider-options='{"breakpoints":{"0":{"slidesPerView":1},"576":{"slidesPerView":"1"},"768":{"slidesPerView":"1"},"992":{"slidesPerView":"1","centeredSlides":true,"centeredSlidesBounds":true},"1200":{"slidesPerView":"4.5","centeredSlidesBounds":true},"1921":{"slidesPerView":"4.5","centeredSlidesBounds":true}}}'
                    >
                        <div className="swiper-wrapper">
                            {list.map((item, index) => {
                                // Handle both object (dynamic) and string (static) item
                                const isObj = typeof item === "object";
                                const imgPath = isObj
                                    ? item.image
                                    : `/assets/img/project/project_${item}.jpg`;
                                const itemTitle = isObj
                                    ? item.title
                                    : "Project for Marketing";
                                const sub = isObj
                                    ? item.category || item.subtitle
                                    : "IT TECHNOLOGY";
                                const link = isObj
                                    ? item.link || `/projects/${item.slug}`
                                    : "/projects";

                                return (
                                    <div className="swiper-slide" key={index}>
                                        <div className="project-card3">
                                            <div className="box-img">
                                                <img
                                                    src={imgPath}
                                                    alt="image"
                                                />
                                            </div>
                                            <div className="project-content">
                                                <div className="box-content">
                                                    <h3 className="box-title">
                                                        <Link href={link}>
                                                            {itemTitle}
                                                        </Link>
                                                    </h3>
                                                    <span className="sub-title">
                                                        {sub}
                                                    </span>
                                                </div>
                                                <Link
                                                    href={link}
                                                    className="icon-btn"
                                                >
                                                    <i className="fa-light fa-arrow-right"></i>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
