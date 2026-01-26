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

            <Breadcrumb
                title={pageTitle}
                items={[
                    { label: "Home", link: "/" },
                    { label: "News", link: "/news" },
                    ...(activeCategory ? [{ label: activeCategory.name }] : []),
                    ...(activeTag ? [{ label: activeTag.name }] : []),
                ]}
            />

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
