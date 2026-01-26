import React from "react";
import HomeAboutSection from "@/Components/Sections/Home/AboutSection";

export default function AboutSection(props) {
    return <HomeAboutSection {...props} isBuilder={props.isBuilder || false} />;
}
