import { Link } from "@inertiajs/react";
import React from "react";
import { getImageUrl } from "@/Utils/image";

export default function ServiceSection({
    services,
    title,
    subtitle,
    cta_text,
    cta_url,
    isBuilder = false,
}) {
    const [activeIndex, setActiveIndex] = React.useState(0);


    if (!services || services.length === 0) return null;

    const t = title || "Jelajahi Layanan Kami";
    const st = subtitle || "Apa yang Kami Tawarkan";
    const btnText = cta_text || "Lihat Detail";
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

    // CSS Overrides to force desktop layout in Builder
    const builderStyles = `
        /* Force container to simulate desktop width */
        .container {
            min-width: 1200px !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }
        /* Force horizontal layout for the list area */
        .service-list-area {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 24px !important;
        }
        /* Reset item width/flex properties to default desktop behavior (allow shrinking) */
        .service-list-wrap {
            width: auto !important;
            min-width: 0 !important; /* Critical: allow items to shrink to fit container */
            flex: 1 1 auto !important; /* Ensure they share space */
        }
        /* Active item expansion */
        .service-list-wrap.active {
            flex: 3 1 auto !important; /* Give more space to active item */
        }

        /* Force list height to matching desktop */
        .service-list {
            height: 636px !important;
            max-height: none !important;
        }

        /* Force positioning of content for inactive items (vertical text mode) */
        .service-list .service-content {
            position: absolute !important;
            bottom: 157px !important;
            right: -50px !important;
            left: auto !important;
            top: auto !important;
            transform: rotate(-90deg) !important;
            width: auto !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Active item content positioning (normal text mode) */
        .service-list-wrap.active .service-content {
            left: 40px !important;
            bottom: 40px !important;
            right: auto !important;
            transform: none !important;
        }

        /* Force icons to be visible */
        .service-list .service-icon {
            opacity: 1 !important;
            visibility: visible !important;
            display: block !important;
            top: 24px !important;
            left: 35px !important;
            width: 100px !important; /* Correct width per design */
            height: 100px !important;
        }
        .service-list-wrap.active .service-icon {
            top: 61% !important; /* Approximate desktop position for active */
        }
    `;

    return (
        <div className="service-area space-bottom" id="service-sec">
            {isBuilder && <style>{builderStyles}</style>}
            <div className="container">
                <div
                    className={`row align-items-center ${isBuilder ? "justify-content-between" : "justify-content-lg-between justify-content-center"}`}
                >
                    <div
                        className={`${isBuilder ? "col-6" : "col-lg-6"} mb-n2 mb-lg-0`}
                    >
                        <div
                            className={`title-area ${isBuilder ? "text-start" : "text-center text-lg-start"}`}
                        >
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
                                    {cta_text || "Lihat Semua Layanan"}
                                </Link>
                            </div>
                        </div>
                    )}
                </div>
                <div className="row">
                    <div className="service-list-area">
                        {" "}
                        {services.map((service, index) => (
                            <div
                                key={service.id}
                                className={`service-list-wrap sv-list2 service7-list ${index === activeIndex ? "active" : ""
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
