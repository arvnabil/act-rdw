import { Head } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import SliderSection from "@/Components/Sections/Home/SliderSection";
import AboutSection from "@/Components/Sections/Home/AboutSection";
import ServiceSection from "@/Components/Sections/Home/ServiceSection";
import BrandSection from "@/Components/Sections/Home/BrandSection";
import WhyChooseUsSection from "@/Components/Sections/Home/WhyChooseUsSection";
import ProjectSection from "@/Components/Sections/Home/ProjectSection";
import ClientSection from "@/Components/Sections/Common/ClientSection";
import NewsSection from "@/Components/Sections/Home/NewsSection";

export default function Home({ services }) {
    return (
        <MainLayout>
            <Head title="Home" />

            <SliderSection />

            <AboutSection />

            <ServiceSection services={services} />

            <BrandSection />

            <WhyChooseUsSection />

            <ProjectSection />

            <ClientSection />

            <NewsSection />
        </MainLayout>
    );
}
