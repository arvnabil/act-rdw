import React, { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ServiceSectionHeader from "@/Components/Sections/ServiceDetail/ServiceSectionHeader";
import ServiceFilterBar from "@/Components/Sections/ServiceDetail/ServiceFilterBar";
import ServiceRoomGrid from "@/Components/Sections/ServiceDetail/ServiceRoomGrid";
import Seo from "@/Components/Common/Seo";
import { getImageUrl } from "@/Utils/image";

const ServiceDetail = ({ service, seo }) => {
    return (
        <MainLayout>
            <Seo seo={seo} />

            <Breadcrumb
                title={service.title}
                subtitle={
                    service.hero_subtitle ||
                    "Professional Solutions for Your Business"
                }
                bgImage={service.featured_image} // Pass dynamic hero image if supported
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    { label: service.title },
                ]}
            />

            <div className="case-area space-bottom space-top">
                <div className="container">
                    {/* Modern Premium Card Header */}
                    <div
                        className="service-intro-card bg-white p-4 p-lg-5 mb-60"
                        style={{
                            borderRadius: "24px",
                            border: "1px solid #f2f2f2",
                            boxShadow: "0 15px 40px rgba(0,0,0,0.04)",
                        }}
                    >
                        <div className="row align-items-center gy-4">
                            <div className="col-lg-5">
                                <div className="title-area mb-0 text-center text-lg-start">
                                    <span className="sub-title style1">
                                        <span className="squre-shape left me-3"></span>
                                        {service.hero_subtitle}
                                    </span>
                                    <h2 className="sec-title mb-0" style={{ fontSize: "40px", fontWeight: "700" }}>
                                        {service.grid_title || "Explore Our Solutions"}
                                    </h2>
                                </div>
                            </div>
                            <div className="col-lg-7">
                                {service.content && (
                                    <div className="ps-lg-5 position-relative">
                                        <div className="d-none d-lg-block position-absolute start-0 top-50 translate-middle-y bg-light" style={{ width: "2px", height: "80%" }}></div>

                                        <div
                                            className="service-content rich-text-content"
                                            style={{
                                                fontSize: "16px",
                                                lineHeight: "1.7",
                                                color: "#555",
                                                textAlign: "justify"
                                            }}
                                            dangerouslySetInnerHTML={{
                                                __html: service.content,
                                            }}
                                        />
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Unified Solutions Area: Filters + Grid Content */}
                    <div className="unified-solutions-block">
                        <div className="text-center mb-60">
                            <ServiceFilterBar filters={service.filters} />
                        </div>

                        <div className="solutions-content-area">
                            <ServiceRoomGrid
                                rooms={service.rooms}
                                serviceId={service.id}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
};

export default ServiceDetail;
