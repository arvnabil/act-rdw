import React from "react";
import HomeSliderSection from "@/Components/Sections/Home/SliderSection";
import HomeBrandSection from "@/Components/Sections/Home/BrandSection";

export default function SliderSection(props) {
    const { variant } = props;

    // Map 'brand' variant to Home/BrandSection
    if (variant === "brand") {
        return (
            <HomeBrandSection
                {...props}
                slides={props.slides || []}
                builderMode={props.isBuilder || false}
            />
        );
    }

    // Default to Hero Slider
    return (
        <HomeSliderSection {...props} elementId={`hero-slider-${props.id}`} />
    );
}
