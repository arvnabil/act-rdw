import { Head, Link } from "@inertiajs/react";
import React, { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ServiceSectionHeader from "@/Components/Sections/ServiceDetail/ServiceSectionHeader";
import ServiceFilterBar from "@/Components/Sections/ServiceDetail/ServiceFilterBar";
import ServiceRoomGrid from "@/Components/Sections/ServiceDetail/ServiceRoomGrid";

const ServiceDetail = ({ service, seo }) => {
    return (
        <MainLayout>
            <Head title={seo?.title || service.title}>
                {seo?.description && (
                    <meta name="description" content={seo.description} />
                )}
                {seo?.image && <meta property="og:image" content={seo.image} />}
            </Head>

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
                    <ServiceSectionHeader
                        title={service.grid_title}
                        subtitle={service.hero_subtitle}
                    />

                    {/* Dynamic Service Content */}
                    {service.content && (
                        <div
                            className="service-content mb-50 rich-text-content"
                            dangerouslySetInnerHTML={{
                                __html: service.content,
                            }}
                        />
                    )}

                    <ServiceFilterBar filters={service.filters} />

                    <ServiceRoomGrid
                        rooms={service.rooms}
                        serviceId={service.id}
                    />
                </div>
            </div>
        </MainLayout>
    );
};

export default ServiceDetail;
