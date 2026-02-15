import { Head, usePage } from "@inertiajs/react";
import React from "react";
import { getWhatsAppLink } from "@/Utils/whatsapp";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ServiceAboutSection from "@/Components/Sections/ServiceItem/ServiceAboutSection";
import RelatedCategoriesSection from "@/Components/Sections/ServiceItem/RelatedCategoriesSection";
import WorkShowcaseSection from "@/Components/Sections/Common/WorkShowcaseSection";

const ServiceItemDetail = ({ item }) => {
    const { settings } = usePage().props;
    const waLink = getWhatsAppLink(settings?.whatsapp_number || "62811400262", {
        message: item.wa_message || `I am interested in ${item.title}`,
        cta_position: 'service_detail',
        cta_label: 'Service About Section WA',
        entity_type: item.entity_type || 'service',
        entity_id: item.id,
        entity_slug: item.slug || item.id
    });

    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) {
            return path;
        }
        return `/storage/${path}`;
    };

    const staticShowcase = [
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
        {
            title: "Purnomo",
            category: "Logitech",
            image: "/assets/img/project/project_3_9_.jpg",
        },
    ];

    return (
        <MainLayout>
            <Head title={item.title} />

            <Breadcrumb
                title={item.title}
                subtitle={`${item.parent_title} Solution`}
                bgImage={item.thumbnail}
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    {
                        label: item.parent_title,
                        link: `/services/${item.parent_service}`,
                    },
                    { label: item.title },
                ]}
            />

            <ServiceAboutSection
                item={item}
                getImageUrl={getImageUrl}
                waLink={waLink}
            />

            <RelatedCategoriesSection item={item} getImageUrl={getImageUrl} />

            {/* Work Showcase Section */}
            {item.show_showcase !== false && (
                <WorkShowcaseSection staticShowcase={staticShowcase} />
            )}
        </MainLayout>
    );
};

export default ServiceItemDetail;
