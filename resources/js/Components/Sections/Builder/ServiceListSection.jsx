import React from "react";
import ServiceListSection from "@/Components/Sections/Services/ServiceListSection";

export default function BuilderServiceListSection(props) {
    // Helper to resolve image paths (copied from Services.jsx or generalized)
    // In Dynamic Page, images should already be resolved by SectionDataResolver,
    // BUT ServiceListSection expects a getImageUrl prop or handles it internally?
    // Let's check ServiceListSection.jsx again.
    // It takes { services, getImageUrl }.

    // We can define getImageUrl here or update ServiceListSection to handle default.
    // SectionDataResolver already resolves paths for 'thumbnail' and 'icon' in 'services' array.
    // So getImageUrl can be a simple pass-through or fallback.

    const getImageUrl = (path) => {
        if (!path) return "/assets/img/service/service_6_1.jpg";
        if (
            path.startsWith("http") ||
            path.startsWith("/assets") ||
            path.startsWith("/storage")
        )
            return path;
        return `/storage/${path}`;
    };

    return (
        <ServiceListSection
            services={props.services}
            getImageUrl={getImageUrl}
        />
    );
}
