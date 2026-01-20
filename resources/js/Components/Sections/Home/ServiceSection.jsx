import { Link } from "@inertiajs/react";
import React from "react";

export default function ServiceSection({
    services,
    title,
    subtitle,
    cta_text,
    cta_url,
}) {
    const [activeIndex, setActiveIndex] = React.useState(0);

    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) {
            return path;
        }
        return `/storage/${path}`;
    };

    if (!services || services.length === 0) return null;

    const t = title || "Explore Our Services";
    const st = subtitle || "What we Offer";
    const btnText = cta_text || "View Details";
    // cta_url is not used for the *main* button in the current design,
    // but individual service items have their own links.
    // However, the request asked for "cta_text" and "cta_url" as editable meta.
    // There is no main CTA button at the bottom of the section in the current design, ONLY inside items.
    // I will assume cta_text implies the text on the cards if desired, OR I should add a main CTA.
    // Looking at the design, there IS no main CTA.
    // But "cta_text" usually implies "View All Services".
    // I will stick to just title/subtitle for now unless I see a place to put a main CTA.
    // Wait, the design has "View Details" on each card.
    // Let's use `cta_text` to override "View Details" on cards if provided?
    // OR create a main button if `cta_url` is provided. The user contract says "Editable: cta_text, cta_url".
    // I'll add a main button at the bottom if cta_url is present.

    return (
        <div className="service-area space-bottom" id="service-sec">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>
                                {st}
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
                                    {t}
                                </span>
                            </h2>
                        </div>
                    </div>
                    {cta_url && (
                        <div className="col-auto">
                            <div className="sec-btn">
                                <Link
                                    href={cta_url}
                                    className="th-btn th-radius style4"
                                >
                                    {cta_text || "View All Services"}
                                </Link>
                            </div>
                        </div>
                    )}
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
                                            service.thumbnail,
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
                                        {btnText}{" "}
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
}
