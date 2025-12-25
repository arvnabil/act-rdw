import React from 'react';
import { Link } from '@inertiajs/react';

export default function NewsSection() {
    return (
        <section className="overflow-hidden mb-4">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>News<span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span className="scroll-text-ani">ACTiV News Articles</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="row gx-24 gy-30">
                    <div className="col-xl-8">
                        <div className="blog-grid2 style2 th-ani">
                            <div className="blog-img global-img">
                                <img src="/assets/img/blog/blog_5_1.jpg" alt="blog image" />
                            </div>
                            <div className="blog-grid2_content">
                                <div className="blog-meta">
                                    <Link className="author" href="/blog">Sep 09, 2025</Link>
                                    <Link href="/blog">6 min read</Link>
                                </div>
                                <h3 className="box-title">
                                    <Link href="/blog">The Security Risks of Changing Package Owners</Link>
                                </h3>
                                <Link href="/blog" className="th-btn style4 th-radius th-icon">
                                    Read More <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                        <div className="blog-grid2 th-ani style2 mt-24">
                            <div className="blog-img global-img">
                                <img src="/assets/img/blog/blog_5_2.jpg" alt="blog image" />
                            </div>
                            <div className="blog-grid2_content">
                                <div className="blog-meta">
                                    <Link className="author" href="/blog">Sep 10, 2025</Link>
                                    <Link href="/blog">8 min read</Link>
                                </div>
                                <h3 className="box-title">
                                    <Link href="/blog">The Security Risks of Changing Package Owners</Link>
                                </h3>
                                <Link href="/blog" className="th-btn style4 th-radius th-icon">
                                    Read More <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-4">
                        <div className="blog-grid2 th-ani">
                            <div className="blog-img global-img">
                                <img src="/assets/img/blog/blog_5_3.jpg" alt="blog image" />
                            </div>
                            <div className="blog-grid2_content">
                                <div className="blog-meta">
                                    <Link className="author" href="/blog">Sep 05, 2025</Link>
                                    <Link href="/blog">6 min read</Link>
                                </div>
                                <h3 className="box-title">
                                    <Link href="/blog">The Security Risks of Changing Package Owners</Link>
                                </h3>
                                <Link href="/blog" className="th-btn style4 th-radius th-icon">
                                    Read More <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
