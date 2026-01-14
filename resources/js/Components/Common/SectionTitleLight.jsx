import React from "react";

export default function SectionTitle({
    subTitle,
    title,
    align = "text-center text-lg-start",
    mb = "mb-20",
}) {
    return (
        <div className={`title-area ${align} ${mb}`}>
            {subTitle && (
                <span className="sub-title style1 text-anime-style-2">
                    <span className="squre-shape left me-3"></span>
                    {subTitle}
                    <span className="squre-shape d-lg-none right ms-3"></span>
                </span>
            )}
            <h2 className="sec-title text-white">{title}</h2>
        </div>
    );
}
