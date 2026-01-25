import React, { Suspense } from "react";

// Import all sections
// We use Static Imports to ensure they are bundled.
import SliderSection from "./Sections/Builder/SliderSection";
import AboutSection from "./Sections/Builder/AboutSection";
import ServiceSection from "./Sections/Builder/ServiceSection";
import ProjectSection from "./Sections/Builder/ProjectSection";
import BrandSection from "./Sections/Builder/BrandSection";
import NewsSection from "./Sections/Builder/NewsSection";
import WhyChooseUsSection from "./Sections/Builder/WhyChooseUsSection";
import ClientSection from "./Sections/Builder/ClientSection";
import BuilderServiceListSection from "./Sections/Builder/ServiceListSection";
import ServiceSolutionSection from "./Sections/Builder/ServiceSolutionSection";
import BuilderServiceCtaSection from "./Sections/Builder/ServiceCtaSection";

// Legacy Components for Migration
import AboutContentSection from "./Sections/About/AboutContentSection";
import VisionMissionSection from "./Sections/About/VisionMissionSection";
import TestimonialSection from "./Sections/Common/TestimonialSection";
import ContactSection from "./Sections/Common/ContactSection";

// Map keys to components
/*
 * CORE SYSTEM FILE - DO NOT EDIT WITHOUT ARCHITECTURE REVIEW
 * This map defines the contract between Backend Keys and Frontend Components.
 */
const SECTIONS = {
    hero: SliderSection, // Keep for legacy/fallback
    slider: SliderSection, // New generic key
    about: AboutSection,
    services: ServiceSection,
    projects: ProjectSection,
    brands: BrandSection,
    brand_partners: BrandSection, // Fix: Match database key
    news: NewsSection,
    why_choose_us: WhyChooseUsSection,
    cta: ClientSection,

    // Services Page Sections
    service_list: BuilderServiceListSection,
    service_clients: ClientSection,
    service_solution: ServiceSolutionSection,
    service_cta: BuilderServiceCtaSection,

    // Legacy Mappings (Static Sections)
    about_content: AboutContentSection,
    vision_mission: VisionMissionSection,
    testimonial: TestimonialSection,
    contact: ContactSection,
};

export default function SectionRenderer({ sectionKey, props }) {
    const Component = SECTIONS[sectionKey];

    if (!Component) {
        if (process.env.NODE_ENV === "development") {
            console.warn(
                `Section type "${sectionKey}" not found in SectionRenderer.`,
            );
        }
        return null;
    }

    return <Component {...props} />;
}
