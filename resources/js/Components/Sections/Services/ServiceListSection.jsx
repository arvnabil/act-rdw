import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";
import { getImageUrl as globalGetImageUrl } from "@/Utils/image";

export default function ServiceListSection({ services, getImageUrl }) {
    const resolveImage = getImageUrl || globalGetImageUrl;
    return (

        <section
            className="position-relative bg-top-center overflow-hidden space space-bottom"
            id="service-sec"
        >
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-lg-8">
                        <SectionTitle
                            subTitle="What We’re Offering"
                            title="Dealing in all professional IT services"
                            align="title-area service-title-box text-center"
                        />
                        <div className="text-center">
                            <p
                                className="sec-text mb-50 wow fadeInUp"
                                data-wow-delay=".3s"
                            >
                                IT solutions refer to a broad range of services
                                and technologies designed to address <br />
                                specific business needs, streamline operations,
                                and drive growth.
                            </p>
                        </div>
                    </div>
                </div>
                <div className="service-area">
                    <div className="row gy-30 justify-content-center">
                        {services && services.length > 0 ? (
                            services.map((service, index) => (
                                <div className="col-xl-4 col-md-6" key={index}>
                                    <div className="service-box service-style-1">
                                        <div className="service-img">
                                            <Link
                                                href={`/services/${service.slug}`}
                                            >
                                                <img
                                                    src={resolveImage(
                                                        service.thumbnail
                                                    )}
                                                    alt={service.name}
                                                />
                                            </Link>
                                        </div>
                                        <div className="service-content">
                                            <h3 className="box-title">
                                                <Link
                                                    href={`/services/${service.slug}`}
                                                >
                                                    {service.name}
                                                </Link>
                                            </h3>
                                            <p className="service-box_text">
                                                {service.description
                                                    ? service.description.substring(
                                                        0,
                                                        100
                                                    ) + "..."
                                                    : ""}
                                            </p>
                                            <Link
                                                className="th-btn style4"
                                                href={`/services/${service.slug}`}
                                            >
                                                Read More{" "}
                                                <i className="fa-light fa-arrow-right-long"></i>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="text-center">No services found.</p>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
