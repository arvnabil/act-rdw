import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

import BlogDetailContent from "@/Components/Sections/BlogDetail/BlogDetailContent";
import BlogComments from "@/Components/Sections/BlogDetail/BlogComments";
import BlogSidebar from "@/Components/Sections/Blog/BlogSidebar";

export default function BlogDetail({ post, recentPosts, categories, tags }) {
    return (
        <MainLayout>
            <Head title={post.title} />

            <Breadcrumb
                title={post.title}
                items={[
                    { label: "Home", link: "/" },
                    { label: "News", link: "/news" },
                    { label: post.title },
                ]}
            />

            <section className="th-blog-wrapper blog-details space-top space-extra-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-8 col-lg-7">
                            <BlogDetailContent post={post} />
                            {/* Comments Section - Hidden for now as DB doesn't support it yet */}
                            {post.comments && (
                                <BlogComments comments={post.comments} />
                            )}
                        </div>

                        <div className="col-xxl-4 col-lg-5">
                            <BlogSidebar
                                categories={categories}
                                recentPosts={recentPosts}
                                tags={tags}
                            />
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
