import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import SectionTitle from "@/Components/Common/SectionTitle";
import TestimonialSection from "@/Components/Sections/Common/TestimonialSection";
import ContactSection from "@/Components/Sections/Common/ContactSection";
import AboutContentSection from "@/Components/Sections/About/AboutContentSection";
import VisionMissionSection from "@/Components/Sections/About/VisionMissionSection";
import Seo from "@/Components/Common/Seo";

export default function About({ seo, breadcrumb_image, show_breadcrumb = true }) {
    return (
        <MainLayout>
            <Seo seo={seo} />

            {show_breadcrumb && (
                <Breadcrumb
                    title="Tentang Kami"
                    items={[
                        { label: "Home", link: "/" },
                        { label: "About" },
                    ]}
                    bgImage={breadcrumb_image}
                />
            )}

            {/* About Area */}
            <AboutContentSection />

            {/* History Area (Vision, Mission, Goal) */}
            <VisionMissionSection />

            {/* Testimonial Area */}
            <TestimonialSection />

            {/* Contact & Map Area */}
            <ContactSection />
        </MainLayout>
    );
}
