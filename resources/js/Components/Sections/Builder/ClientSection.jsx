import React from "react";
import CommonClientSection from "@/Components/Sections/Common/ClientSection";

export default function ClientSection(props) {
    // Backend resolver will provide 'clients' in props based on query config
    return (
        <CommonClientSection {...props} isBuilder={props.isBuilder || false} />
    );
}
