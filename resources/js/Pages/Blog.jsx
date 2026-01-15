import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import BlogList from "@/Components/Sections/Blog/BlogList";
import BlogSidebar from "@/Components/Sections/Blog/BlogSidebar";
export default function Blog({ posts }) {
    return (
        <MainLayout>
            <Head title="News" />

            <Breadcrumb
                title="News"
                items={[{ label: "Home", link: "/" }, { label: "News" }]}
            />

            <section className="th-blog-wrapper space-top space-extra-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-8 col-lg-7">
                            <BlogList posts={posts} />
                        </div>

                        {/* Sidebar */}
                        <div className="col-xxl-4 col-lg-5">
                            <BlogSidebar />
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
