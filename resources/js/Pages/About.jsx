import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import SectionTitle from "@/Components/Common/SectionTitle";
import TestimonialSection from "@/Components/Sections/Common/TestimonialSection";
import ContactSection from "@/Components/Sections/Common/ContactSection";
import AboutContentSection from "@/Components/Sections/About/AboutContentSection";
import VisionMissionSection from "@/Components/Sections/About/VisionMissionSection";

export default function About() {
    return (
        <MainLayout>
            <Head title="About Us" />

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
