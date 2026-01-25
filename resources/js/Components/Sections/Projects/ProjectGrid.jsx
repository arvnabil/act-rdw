import React from "react";
import { Link } from "@inertiajs/react";

export default function ProjectGrid({ projects }) {
    if (!projects || !projects.data || projects.data.length === 0) {
        return (
            <div className="text-center py-5">
                <div className="mb-4">
                    <i className="fa-regular fa-briefcase fa-3x text-muted"></i>
                </div>
                <h3>No Projects Found</h3>
                <p className="text-muted">
                    We haven't published any projects yet. Check back soon!
                </p>
            </div>
        );
    }

    return (
        <div className="row gy-4">
            {projects.data.map((project) => (
                <div className="col-xl-4 col-md-6" key={project.id}>
                    <div className="case-box style2 position-relative">
                        <div className="case-img global-img">
                            <img src={project.image} alt={project.title} />
                            <Link href={project.link} className="icon-btn">
                                <i className="fa-light fa-arrow-right-long"></i>
                            </Link>
                        </div>
                        <div className="case-content">
                            <div className="media-left">
                                <h4 className="box-title">
                                    <Link href={project.link}>
                                        {project.title}
                                    </Link>
                                </h4>
                                <span className="case-subtitle">
                                    {project.subtitle}
                                </span>
                            </div>
                        </div>
                        <div className="case-action">
                            <Link href={project.link} className="case-btn">
                                <i className="fa-light fa-arrow-right-long"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}
