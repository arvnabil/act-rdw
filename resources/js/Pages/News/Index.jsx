import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import BlogList from "@/Components/Sections/Blog/BlogList";
import BlogSidebar from "@/Components/Sections/Blog/BlogSidebar";
export default function Blog({
    posts,
    categories,
    tags,
    recentPosts,
    filters,
    activeCategory,
    activeTag,
    breadcrumb_image,
    show_breadcrumb = true,
    page_title,
}) {
    const pageTitle = activeCategory
        ? `Category: ${activeCategory.name}`
        : activeTag
            ? `Tag: ${activeTag.name}`
            : filters?.search
                ? `Search: ${filters.search}`
                : "News";

    return (
        <MainLayout>
            <Head title={pageTitle} />

            {show_breadcrumb && (
                <Breadcrumb
                    title={page_title || "Latest News"}
                    items={[
                        { label: "Home", link: "/" },
                        { label: page_title || "News" },
                    ]}
                    bgImage={breadcrumb_image}
                />
            )}

            <section className="th-blog-wrapper space-top space-extra-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-8 col-lg-7">
                            <BlogList posts={posts} />
                        </div>

                        {/* Sidebar */}
                        <div className="col-xxl-4 col-lg-5">
                            <BlogSidebar
                                categories={categories}
                                tags={tags}
                                recentPosts={recentPosts}
                                filters={filters}
                            />
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
