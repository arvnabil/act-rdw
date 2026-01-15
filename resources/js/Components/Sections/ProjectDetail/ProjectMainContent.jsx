import React from "react";

export default function ProjectMainContent({ project }) {
    return (
        <div className="page-single mb-60">
            <div className="page-content d-block">
                <h2 className="box-title">{project.title}</h2>
                <p className="box-text mb-30">{project.description}</p>

                <h2 className="box-title">The challenge of project</h2>
                <p className="box-text mb-30">{project.challenge_text}</p>

                <div className="project-gallery-wrapper">
                    <div className="row gy-4">
                        {project.gallery &&
                            project.gallery.map((img, index) => (
                                <div className="col-6" key={index}>
                                    <div className="sv-sm-img">
                                        <img
                                            className="w-100 rounded-10"
                                            src={img}
                                            alt="Project Gallery"
                                        />
                                    </div>
                                </div>
                            ))}
                    </div>
                </div>
            </div>
        </div>
    );
}
