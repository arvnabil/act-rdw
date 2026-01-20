import React from "react";
import HomeServiceSection from "@/Components/Sections/Home/ServiceSection";

export default function ServiceSection(props) {
    return (
        <HomeServiceSection
            {...props}
            services={props.services || props.allServices}
        />
    );
}
