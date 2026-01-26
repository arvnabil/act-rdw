import React from "react";
import HomeBrandSection from "@/Components/Sections/Home/BrandSection";

export default function BrandSection(props) {
    return (
        <HomeBrandSection
            {...props}
            brands={props.brands || []}
            isBuilder={props.isBuilder || false}
        />
    );
}
