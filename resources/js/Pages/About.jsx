import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import SectionTitle from "@/Components/Common/SectionTitle";
import TestimonialSection from "@/Components/Sections/Common/TestimonialSection";
import ContactSection from "@/Components/Sections/Common/ContactSection";
import AboutContentSection from "@/Components/Sections/About/AboutContentSection";
import VisionMissionSection from "@/Components/Sections/About/VisionMissionSection";
import Seo from "@/Components/Common/Seo";

export default function About({ seo }) {
    return (
        <MainLayout>
            <Seo seo={seo} />

            <Breadcrumb
                title="About Us"
                items={[{ label: "Home", link: "/" }, { label: "About Us" }]}
            />

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
