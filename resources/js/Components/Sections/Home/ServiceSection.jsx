import { Link } from "@inertiajs/react";
import React from "react";

const ServiceSection = ({ services }) => {
    const [activeIndex, setActiveIndex] = React.useState(0);

    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) {
            return path;
        }
        return `/storage/${path}`;
    };

    if (!services || services.length === 0) return null;

    return (
        <div className="service-area space-bottom" id="service-sec">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>
                                What we Offer
                                <span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span
                                    className="scroll-text-ani"
                                    style={{
                                        backgroundImage:
                                            "linear-gradient(to right, rgb(11, 20, 34) 100%, rgb(213, 215, 218) 100%)",
                                    }}
                                >
                                    Explore Our Services
                                </span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className="service-list-area">
                        {services.map((service, index) => (
                            <div
                                key={service.id}
                                className={`service-list-wrap sv-list2 service7-list ${
                                    index === activeIndex ? "active" : ""
                                }`}
                                onMouseEnter={() => setActiveIndex(index)}
                            >
                                <div
                                    className="service-list background-image"
                                    style={{
                                        backgroundImage: `url(${getImageUrl(
                                            service.thumbnail
                                        )})`,
                                    }}
                                >
                                    <span className="service-icon">
                                        <img
                                            src={getImageUrl(service.icon)}
                                            alt={service.name}
                                        />
                                    </span>
                                    <div className="service-content">
                                        <h4 className="box-title">
                                            <Link
                                                href={`/services/${service.slug}`}
                                            >
                                                {service.name}
                                            </Link>
                                        </h4>
                                        <span className="service-subtitle">
                                            {service.description}
                                        </span>
                                    </div>
                                    <Link
                                        href={`/services/${service.slug}`}
                                        className="th-btn th-radius style2"
                                    >
                                        View Details{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ServiceSection;
