import React from "react";

export default function ProjectMainContent({ project }) {
    return (
        <div className="page-single mb-60">
            <div className="page-content d-block">
                <h2 className="box-title">{project.title}</h2>

                {/* Dynamic Content Rendering */}
                <div
                    className="box-text mb-30 has-typography"
                    dangerouslySetInnerHTML={{ __html: project.content }}
                />
            </div>
        </div>
    );
}
