import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Seo from "@/Components/Common/Seo";
import SliderSection from "@/Components/Sections/Home/SliderSection";
import AboutSection from "@/Components/Sections/Home/AboutSection";
import ServiceSection from "@/Components/Sections/Home/ServiceSection";
import BrandSection from "@/Components/Sections/Home/BrandSection";
import WhyChooseUsSection from "@/Components/Sections/Home/WhyChooseUsSection";
import ProjectSection from "@/Components/Sections/Home/ProjectSection";
import ClientSection from "@/Components/Sections/Common/ClientSection";
import NewsSection from "@/Components/Sections/Home/NewsSection";

export default function Home({ services, projects, seo }) {
    return (
        <MainLayout>
            <Seo seo={seo} />

            <SliderSection />

            <AboutSection />

            <ServiceSection services={services} />

            <BrandSection />

            <WhyChooseUsSection />

            <ProjectSection projects={projects} />

            <ClientSection />

            <NewsSection />
        </MainLayout>
    );
}
