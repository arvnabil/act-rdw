import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import HeroSection from '@/Components/Sections/HeroSection';
import AboutSection from '@/Components/Sections/AboutSection';
import ServiceSection from '@/Components/Sections/ServiceSection';
import BrandSection from '@/Components/Sections/BrandSection';
import WhyChooseUsSection from '@/Components/Sections/WhyChooseUsSection';
import ProjectSection from '@/Components/Sections/ProjectSection';
import ClientSection from '@/Components/Sections/ClientSection';
import NewsSection from '@/Components/Sections/NewsSection';

export default function Home() {
    return (
        <MainLayout>
            <Head title="Home" />

            <HeroSection />

            <AboutSection />

            <ServiceSection />

            <BrandSection />

            <WhyChooseUsSection />

            <ProjectSection />

            <ClientSection />

            <NewsSection />

        </MainLayout>
    );
}
