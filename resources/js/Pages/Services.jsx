import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ClientSection from "@/Components/Sections/Common/ClientSection";
import SolutionSection from "@/Components/Sections/Services/SolutionSection";
import ServiceListSection from "@/Components/Sections/Services/ServiceListSection";
import ServiceCtaSection from "@/Components/Sections/Services/ServiceCtaSection";

export default function Services({ services }) {
    // Helper to resolve image paths
    const getImageUrl = (path) => {
        if (!path) return "/assets/img/service/service_6_1.jpg"; // Default fallback
        if (path.startsWith("http") || path.startsWith("/assets")) return path;
        return `/storage/${path}`;
    };

    return (
        <MainLayout>
            <Head title="Our Services" />

            <Breadcrumb
                title="Services"
                items={[{ label: "Home", link: "/" }, { label: "Services" }]}
            />

            {/* Service Area */}
            <ServiceListSection services={services} getImageUrl={getImageUrl} />

            <ClientSection />

            <SolutionSection />

            {/* CTA Area - Uncomment to enable */}
            {/* <ServiceCtaSection /> */}
        </MainLayout>
    );
}
