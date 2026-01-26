import React from "react";
import HomeProjectSection from "@/Components/Sections/Home/ProjectSection";

export default function ProjectSection(props) {
    // Backend resolver will provide 'projects' in props based on query config
    return (
        <HomeProjectSection
            {...props}
            projects={props.projects || props.items || []}
            isBuilder={props.isBuilder || false}
        />
    );
}
