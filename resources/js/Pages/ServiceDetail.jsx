import { Head, Link } from "@inertiajs/react";
import React, { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ServiceSectionHeader from "@/Components/Sections/ServiceDetail/ServiceSectionHeader";
import ServiceFilterBar from "@/Components/Sections/ServiceDetail/ServiceFilterBar";
import ServiceRoomGrid from "@/Components/Sections/ServiceDetail/ServiceRoomGrid";

const ServiceDetail = ({ service }) => {
    return (
        <MainLayout>
            <Head title={service.title} />

            <Breadcrumb
                title={service.title}
                subtitle={
                    service.hero_subtitle ||
                    "Professional Solutions for Your Business"
                }
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
