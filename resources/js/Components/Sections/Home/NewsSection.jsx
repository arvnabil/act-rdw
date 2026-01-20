import React from "react";
import { Link } from "@inertiajs/react";

export default function NewsSection({
    posts,
    title,
    subtitle,
    cta_text,
    cta_url,
}) {
    const t = title || "ACTiV News Articles";
    const st = subtitle || "News";
    const btnText = cta_text || "Read More";

    // Fallback static posts if no data provided
    const defaultPosts = [
        {
            title: "The Security Risks of Changing Package Owners",
            published_at: "Sep 09, 2025",
            read_time: "6 min read",
            image: "/assets/img/blog/blog_5_1.jpg",
            link: "/news",
        },
        {
            title: "The Security Risks of Changing Package Owners",
            published_at: "Sep 10, 2025",
            read_time: "8 min read",
            image: "/assets/img/blog/blog_5_2.jpg",
            link: "/blog",
        },
        {
            title: "The Security Risks of Changing Package Owners",
            published_at: "Sep 05, 2025",
            read_time: "6 min read",
            image: "/assets/img/blog/blog_5_3.jpg",
            link: "/blog",
        },
    ];

    const list = posts && posts.length > 0 ? posts : defaultPosts;

    return (
        <section className="overflow-hidden mb-4">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>
                                {st}
                                <span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span className="scroll-text-ani">{t}</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="row gx-24 gy-30">
                    <div className="col-xl-8">
                        {list[0] && (
                            <div className="blog-grid2 style2 th-ani">
                                <div className="blog-img global-img">
                                    <img
                                        src={list[0].thumbnail || list[0].image}
                                        alt="blog image"
                                    />
                                </div>
                                <div className="blog-grid2_content">
                                    <div className="blog-meta">
                                        <Link
                                            className="author"
                                            href={
                                                list[0].link ||
                                                `/news/${list[0].slug}`
                                            }
                                        >
                                            {list[0].published_at_formatted ||
                                                list[0].published_at}
                                        </Link>
                                        <Link
                                            href={
                                                list[0].link ||
                                                `/news/${list[0].slug}`
                                            }
                                        >
                                            {list[0].read_time || "5 min read"}
                                        </Link>
                                    </div>
                                    <h3 className="box-title">
                                        <Link
                                            href={
                                                list[0].link ||
                                                `/news/${list[0].slug}`
                                            }
                                        >
                                            {list[0].title}
                                        </Link>
                                    </h3>
                                    <Link
                                        href={
                                            list[0].link ||
                                            `/news/${list[0].slug}`
                                        }
                                        className="th-btn style4 th-radius th-icon"
                                    >
                                        {cta_text || "Read More"}{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        )}
                        {list[1] && (
                            <div className="blog-grid2 th-ani style2 mt-24">
                                <div className="blog-img global-img">
                                    <img
                                        src={list[1].thumbnail || list[1].image}
                                        alt="blog image"
                                    />
                                </div>
                                <div className="blog-grid2_content">
                                    <div className="blog-meta">
                                        <Link
                                            className="author"
                                            href={
                                                list[1].link ||
                                                `/news/${list[1].slug}`
                                            }
                                        >
                                            {list[1].published_at_formatted ||
                                                list[1].published_at}
                                        </Link>
                                        <Link
                                            href={
                                                list[1].link ||
                                                `/news/${list[1].slug}`
                                            }
                                        >
                                            {list[1].read_time || "5 min read"}
                                        </Link>
                                    </div>
                                    <h3 className="box-title">
                                        <Link
                                            href={
                                                list[1].link ||
                                                `/news/${list[1].slug}`
                                            }
                                        >
                                            {list[1].title}
                                        </Link>
                                    </h3>
                                    <Link
                                        href={
                                            list[1].link ||
                                            `/news/${list[1].slug}`
                                        }
                                        className="th-btn style4 th-radius th-icon"
                                    >
                                        {cta_text || "Read More"}{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                    <div className="col-xl-4">
                        {list[2] && (
                            <div className="blog-grid2 th-ani">
                                <div className="blog-img global-img">
                                    <img
                                        src={list[2].thumbnail || list[2].image}
                                        alt="blog image"
                                    />
                                </div>
                                <div className="blog-grid2_content">
                                    <div className="blog-meta">
                                        <Link
                                            className="author"
                                            href={
                                                list[2].link ||
                                                `/news/${list[2].slug}`
                                            }
                                        >
                                            {list[2].published_at_formatted ||
                                                list[2].published_at}
                                        </Link>
                                        <Link
                                            href={
                                                list[2].link ||
                                                `/news/${list[2].slug}`
                                            }
                                        >
                                            {list[2].read_time || "5 min read"}
                                        </Link>
                                    </div>
                                    <h3 className="box-title">
                                        <Link
                                            href={
                                                list[2].link ||
                                                `/news/${list[2].slug}`
                                            }
                                        >
                                            {list[2].title}
                                        </Link>
                                    </h3>
                                    <Link
                                        href={
                                            list[2].link ||
                                            `/news/${list[2].slug}`
                                        }
                                        className="th-btn style4 th-radius th-icon"
                                    >
                                        {cta_text || "Read More"}{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
