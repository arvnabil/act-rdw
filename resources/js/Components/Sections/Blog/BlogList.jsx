import React from "react";
import { Link } from "@inertiajs/react";

export default function BlogList({ posts }) {
    return (
        <>
            {posts.data.map((post) => (
                <div
                    className="th-blog blog-single has-post-thumbnail"
                    key={post.id}
                >
                    <div className="blog-img">
                        <Link href={post.link}>
                            <img src={post.image} alt="Blog Image" />
                        </Link>
                    </div>
                    <div className="blog-content">
                        <div className="blog-meta">
                            <Link className="author" href={post.link}>
                                <i className="fa-light fa-user"></i>
                                by {post.author}
                            </Link>
                            <Link href={post.link}>
                                <i className="fa-regular fa-calendar"></i>
                                {post.date}
                            </Link>

                            {/* Primary Category */}
                            {post.category && (
                                <Link href={post.link}>
                                    <i className="fa-light fa-folder"></i>
                                    {post.category}
                                </Link>
                            )}
                        </div>
                        <h2 className="blog-title">
                            <Link href={post.link}>{post.title}</Link>
                        </h2>
                        <p className="blog-text">{post.excerpt}</p>

                        {/* Tags Chip List (if tags exist) */}
                        {post.tags && post.tags.length > 0 && (
                            <div className="blog-tags mt-2 mb-3">
                                {post.tags.map((tag, index) => (
                                    <span
                                        key={index}
                                        className="badge bg-light text-secondary me-1"
                                    >
                                        #{tag.name}
                                    </span>
                                ))}
                            </div>
                        )}

                        <Link
                            href={post.link}
                            className="th-btn style4 th-icon"
                        >
                            Read More{" "}
                            <i className="fa-light fa-arrow-right-long"></i>
                        </Link>
                    </div>
                </div>
            ))}

            {/* Pagination */}
            {posts.links && posts.links.length > 3 && (
                <div className="th-pagination mt-60">
                    <ul>
                        {posts.links.map((link, index) => (
                            <li key={index}>
                                {link.url ? (
                                    <Link
                                        href={link.url}
                                        className={`${
                                            link.active ? "active" : ""
                                        } ${link.className || ""}`}
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                        preserveScroll
                                    />
                                ) : null}
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </>
    );
}
