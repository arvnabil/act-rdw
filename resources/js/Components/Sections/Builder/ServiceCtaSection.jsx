import React from "react";
import ServiceCtaSection from "@/Components/Sections/Services/ServiceCtaSection";

export default function BuilderServiceCtaSection(props) {
    // Same logic as SolutionSection.
    // ServiceCtaSection (Step 32) does not accept props.
    // passing props for now, will likely update ServiceCtaSection to accept them.
    return <ServiceCtaSection {...props} />;
}
