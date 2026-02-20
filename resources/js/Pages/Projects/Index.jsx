import React, { useState, useEffect } from "react";
import { Head, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ProjectGrid from "@/Components/Sections/Projects/ProjectGrid";
import ProjectPagination from "@/Components/Sections/Projects/ProjectPagination";
import ProjectSkeleton from "@/Components/Sections/Projects/ProjectSkeleton";
import ProjectToolbar from "@/Components/Sections/Projects/ProjectToolbar";
import ProjectFilterSidebar from "@/Components/Sections/Projects/ProjectFilterSidebar";

export default function Projects({ projects, filters, stats, queryParams }) {
    const [search, setSearch] = useState(queryParams.search || "");
    const [industry, setIndustry] = useState(queryParams.industry || "");
    const [brand, setBrand] = useState(queryParams.brand || "");
    const [solution, setSolution] = useState(queryParams.solution || "");
    const [orderby, setOrderby] = useState(queryParams.orderby || "latest");
    const [loading, setLoading] = useState(false);
    const [showFilters, setShowFilters] = useState(false);

    // Unified Filter Change Handler
    const handleFilterChange = (key, value) => {
        const params = {
            search,
            industry,
            brand,
            solution,
            orderby,
            [key]: value
        };

        // Local state updates
        if(key === 'industry') setIndustry(value);
        if(key === 'brand') setBrand(value);
        if(key === 'solution') setSolution(value);

        router.get(
            "/projects",
            params,
            { 
                preserveState: true, 
                replace: true,
                onStart: () => setLoading(true),
                onFinish: () => setLoading(false),
            }
        );
    };

    const handleSearch = (e) => {
        if(e) e.preventDefault();
        router.get(
            "/projects",
            { search, industry, brand, solution, orderby },
            { 
                preserveState: true,
                onStart: () => setLoading(true),
                onFinish: () => setLoading(false),
            }
        );
    };

    const handleSortChange = (e) => {
        const value = e.target.value;
        setOrderby(value);
        router.get(
            "/projects",
            { search, industry, brand, solution, orderby: value },
            { preserveState: true }
        );
    };

    const handleRemoveFilter = (key) => {
        if (key === "search") {
            setSearch("");
            handleFilterChange("search", "");
        } else {
            handleFilterChange(key, "");
        }
    };

    const handleResetFilters = () => {
        setSearch("");
        setIndustry("");
        setBrand("");
        setSolution("");
        router.get("/projects", { orderby }, { replace: true });
    };

    return (
        <MainLayout>
            <Head title="Our Projects - ACTiV System Integrator" />

            <Breadcrumb
                title="Our Projects Portfolios"
                items={[{ label: "Home", link: "/" }, { label: "Projects" }]}
            />

            {/* Content Area matching Product Style */}
            <section className="space-extra-bottom space">
                <div className="container">
                    
                    <ProjectToolbar
                        search={search}
                        setSearch={setSearch}
                        handleSearch={handleSearch}
                        orderby={orderby}
                        handleSortChange={handleSortChange}
                        showFilters={showFilters}
                        setShowFilters={setShowFilters}
                        // Data
                        brands={filters.brands}
                        solutions={filters.solutions}
                        industries={filters.industries}
                        // Selected
                        selectedBrand={brand}
                        selectedSolution={solution}
                        selectedIndustry={industry}
                        // Handlers
                        handleRemoveFilter={handleRemoveFilter}
                        handleResetFilters={handleResetFilters}
                    />

                    <div className="row mt-4">
                        <ProjectFilterSidebar
                            showFilters={showFilters}
                            filters={{
                                brand,
                                industry,
                                solution,
                            }}
                            handleFilterChange={handleFilterChange}
                            industries={filters.industries}
                            brands={filters.brands}
                            solutions={filters.solutions}
                        />

                        <div className={showFilters ? "col-lg-9 shadow-none" : "col-lg-12 shadow-none"}>
                            {/* Explicit Heading Title Area */}
                            <div className="mb-5 pb-3 border-bottom">
                                <h2 className="text-3xl font-bold text-slate-800 mb-1">
                                    {search ? `Search results for "${search}"` : "Our Projects Portfolio"}
                                </h2>
                                <p className="text-slate-500 mb-0">
                                    Showing <span className="text-emerald-600 font-bold">{projects.total || 0}</span> success stories across our core industries.
                                </p>
                            </div>

                            {loading ? (
                                <ProjectSkeleton />
                            ) : (
                                <>
                                    {projects.data && projects.data.length > 0 ? (
                                        <div className="w-full">
                                            <ProjectGrid projects={projects} />
                                        </div>
                                    ) : (
                                        <div className="py-20 text-center bg-white rounded-3xl border-2 border-dashed border-slate-200">
                                            <div className="inline-flex items-center justify-center w-20 h-20 mb-6 bg-slate-50 rounded-full text-slate-300">
                                                <i className="fa-light fa-magnifying-glass text-3xl"></i>
                                            </div>
                                            <h3 className="text-2xl font-bold text-slate-800 mb-2">No projects found</h3>
                                            <p className="text-slate-500 max-w-sm mx-auto">Try adjusting your filters or search keywords to see our portfolio.</p>
                                        </div>
                                    )}
                                    
                                    {projects.links && projects.links.length > 3 && (
                                        <div className="mt-12 pt-5 border-top">
                                            <ProjectPagination links={projects.links} />
                                        </div>
                                    ) }
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
