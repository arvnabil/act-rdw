import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Breadcrumb from '@/Components/Common/Breadcrumb';

export default function Blog({ posts }) {
    return (
        <MainLayout>
            <Head title="Blog" />

            <Breadcrumb
                title="Blog Standard"
                items={[
                    { label: 'Home', link: '/' },
                    { label: 'Blog Standard' }
                ]}
            />

            <section className="th-blog-wrapper space-top space-extra-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-8 col-lg-7">
                            {posts.data.map((post) => (
                                <div className="th-blog blog-single has-post-thumbnail" key={post.id}>
                                    <div className="blog-img">
                                        <Link href={post.link}>
                                            <img src={post.image} alt="Blog Image" />
                                        </Link>
                                    </div>
                                    <div className="blog-content">
                                        <div className="blog-meta">
                                            <Link className="author" href={post.link}>
                                                <i className="fa-light fa-user"></i>by {post.author}
                                            </Link>
                                            <Link href={post.link}>
                                                <i className="fa-regular fa-calendar"></i>{post.date}
                                            </Link>
                                            <Link href={post.link}>
                                                <img src="/assets/img/icon/map.svg" alt="" />Sea Beach
                                            </Link>
                                        </div>
                                        <h2 className="blog-title">
                                            <Link href={post.link}>{post.title}</Link>
                                        </h2>
                                        <p className="blog-text">
                                            {post.excerpt}
                                        </p>
                                        <Link href={post.link} className="th-btn style4 th-icon">
                                            Read More <i className="fa-light fa-arrow-right-long"></i>
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
                                                        className={`${link.active ? 'active' : ''} ${link.className || ''}`}
                                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                                        preserveScroll
                                                    />
                                                ) : (
                                                    null
                                                )}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>

                        {/* Sidebar */}
                        <div className="col-xxl-4 col-lg-5">
                            <aside className="sidebar-area">
                                <div className="widget widget_search">
                                    <form className="search-form">
                                        <input type="text" placeholder="Search" />
                                        <button type="submit"><i className="far fa-search"></i></button>
                                    </form>
                                </div>
                                <div className="widget widget_categories">
                                    <h3 className="widget_title">Categories</h3>
                                    <ul>
                                        {['IT Strategy & Planning', 'Web Developments', 'Cloud Consulting', 'Machine Learning', 'Database Security', 'IT Management'].map((cat, index) => (
                                            <li key={index}>
                                                <Link href="/blog">{cat}</Link>
                                                <span><i className="fa-regular fa-arrow-up-right"></i></span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <div className="widget">
                                    <h3 className="widget_title">Recent Posts</h3>
                                    <div className="recent-post-wrap">
                                        {[1, 2, 3].map((num) => (
                                            <div className="recent-post" key={num}>
                                                <div className="media-img">
                                                    <Link href={`/blog/${num}`}>
                                                        <img src={`/assets/img/blog/recent-post-1-${num}.jpg`} alt="Blog Image" />
                                                    </Link>
                                                </div>
                                                <div className="media-body">
                                                    <div className="recent-post-meta">
                                                        <Link href={`/blog/${num}`}><i className="fa-solid fa-calendar-days"></i>22 Sep, 2025</Link>
                                                    </div>
                                                    <h4 className="post-title">
                                                        <Link className="text-inherit" href={`/blog/${num}`}>
                                                            {['5 Common IT Issues and How to Solve Them', 'Hybrid Cloud Solutions: The Best of Both Worlds', 'Top 10 IT Solutions Every Business'][num - 1]}
                                                        </Link>
                                                    </h4>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                                <div className="widget widget_tag_cloud">
                                    <h3 className="widget_title">Popular Tags</h3>
                                    <div className="tagcloud">
                                        {['Advice', 'Technology', 'Atek', 'Ux/Ui', 'Consulting', 'Solution', 'Health', 'IT Solution', 'Cloud'].map((tag, index) => (
                                            <Link href="/blog" key={index}>{tag}</Link>
                                        ))}
                                    </div>
                                </div>
                                <div className="widget widget_banner" data-bg-src="/assets/img/bg/widget_banner.jpg">
                                    <div className="widget-banner position-relative text-center">
                                        <span className="icon"><i className="fa-solid fa-phone"></i></span>
                                        <span className="text">Need Help? Call Here</span>
                                        <a className="phone" href="tel:+25669872564">+256 6987 2564</a>
                                        <Link href="/contact" className="th-btn style6">Get A Quote <i className="fa-light fa-arrow-right-long"></i></Link>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
