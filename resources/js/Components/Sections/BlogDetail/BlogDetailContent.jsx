import React from "react";
import { Link } from "@inertiajs/react";

export default function BlogDetailContent({ post }) {
    // Format date if available
    const formatDate = (dateString) => {
        if (!dateString) return "";
        const options = { year: "numeric", month: "long", day: "numeric" };
        return new Date(dateString).toLocaleDateString("en-US", options);
    };

    return (
        <div className="th-blog blog-single">
            <div className="blog-img">
                <img src={post.thumbnail || post.image} alt="Blog Image" />
            </div>
            <div className="blog-content">
                <div className="blog-meta">
                    <Link className="author" href="/news">
                        <i className="fa-light fa-user"></i>
                        by ACTiV Team
                    </Link>
                    <Link href="/news">
                        <i className="fa-regular fa-calendar"></i>
                        {formatDate(post.published_at)}
                    </Link>
                    <Link href="/news">
                        <i className="fa-regular fa-folder"></i>
                        News
                    </Link>
                </div>
                <h2 className="blog-title">{post.title}</h2>

                {/* Dynamic Content Rendering */}
                <div
                    className="blog-text mb-30 has-typography"
                    dangerouslySetInnerHTML={{ __html: post.content }}
                />

                <div className="share-links clearfix ">
                    <div className="row justify-content-between">
                        <div className="col-md-auto text-xl-end">
                            <div className="share-links_wrapp">
                                <span className="share-links-title">
                                    Share:
                                </span>
                                <div className="social-links">
                                    <a href="#">
                                        <i className="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="#">
                                        <i className="fab fa-twitter"></i>
                                    </a>
                                    <a href="#">
                                        <i className="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
