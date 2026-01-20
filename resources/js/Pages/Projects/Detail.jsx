import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProjectMainContent from "@/Components/Sections/ProjectDetail/ProjectMainContent";
import ProjectSidebar from "@/Components/Sections/ProjectDetail/ProjectSidebar";
import ProjectPagination from "@/Components/Sections/ProjectDetail/ProjectPagination";

export default function ProjectDetail({ project }) {
    return (
        <MainLayout>
            <Head title={project.title} />

            <Breadcrumb
                title="Project Details"
                items={[
                    { label: "Home", link: "/" },
                    { label: "Projects", link: "/projects" },
                    { label: project.title },
                ]}
            />

            <section className="space">
                <div className="container">
                    <div className="row">
                        <div className="col-12">
                            <div className="project-dsc-img mb-60">
                                <img
                                    className="w-100 rounded-20"
                                    src={project.thumbnail || project.image}
                                    alt={project.title}
                                    style={{
                                        width: "100%",
                                        height: "auto",
                                        maxHeight: "720px",
                                        objectFit: "cover",
                                    }}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xxl-8 col-lg-7">
                            <ProjectMainContent project={project} />

                            {/* Pagination/Navigation */}
                            <ProjectPagination project={project} />
                        </div>

                        {/* Sidebar */}
                        <div className="col-xxl-4 col-lg-5">
                            <ProjectSidebar project={project} />
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
