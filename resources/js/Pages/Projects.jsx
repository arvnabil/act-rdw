import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProjectGrid from "@/Components/Sections/Projects/ProjectGrid";
import ProjectPagination from "@/Components/Sections/Projects/ProjectPagination";

export default function Projects({ projects }) {
    return (
        <MainLayout>
            <Head title="Projects" />

            <Breadcrumb
                title="Projects Grid"
                items={[{ label: "Home", link: "/" }, { label: "Projects" }]}
            />

            {/* Gallery Area */}
            <div className="overflow-hidden space" id="case-study-sec">
                <div className="container">
                    {/* Grid */}
                    <ProjectGrid projects={projects} />

                    {/* Pagination */}
                    <ProjectPagination links={projects.links} />
                </div>
            </div>
        </MainLayout>
    );
}
