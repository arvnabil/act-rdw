import React from "react";
import HomeNewsSection from "@/Components/Sections/Home/NewsSection";

export default function NewsSection(props) {
    // Backend resolver will provide 'posts' in props based on query config
    return (
        <HomeNewsSection
            {...props}
            posts={props.posts || []}
            isBuilder={props.isBuilder || false}
        />
    );
}
