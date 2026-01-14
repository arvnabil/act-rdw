import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

export default function BlogDetail({ post, recentPosts, categories }) {
    return (
        <MainLayout>
            <Head title={post.title} />

            <Breadcrumb
                title="News Detail"
                items={[
                    { label: "Home", link: "/" },
                    { label: "News", link: "/news" },
                    { label: "News Detail" },
                ]}
            />

            <section className="th-blog-wrapper blog-details space-top space-extra-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-xxl-8 col-lg-7">
                            <div className="th-blog blog-single">
                                <div className="blog-img">
                                    <img src={post.image} alt="Blog Image" />
                                </div>
                                <div className="blog-content">
                                    <div className="blog-meta">
                                        <Link className="author" href="/news">
                                            <i className="fa-light fa-user"></i>
                                            by {post.author}
                                        </Link>
                                        <Link href="/news">
                                            <i className="fa-regular fa-calendar"></i>
                                            {post.date}
                                        </Link>
                                        <Link href="/news">
                                            <img
                                                src="/assets/img/icon/map.svg"
                                                alt=""
                                            />
                                            {post.location}
                                        </Link>
                                    </div>
                                    <h2 className="blog-title">{post.title}</h2>
                                    <p className="blog-text mb-30">
                                        {post.content_intro}
                                    </p>

                                    <p className="blog-text mb-30">
                                        {post.content_main}
                                    </p>

                                    <blockquote>
                                        <p>{post.quote.text}</p>
                                        <cite>{post.quote.author}</cite>
                                    </blockquote>

                                    <p className="blog-text mt-5 mb-4">
                                        These attacks can have devastating
                                        consequences for businesses, ranging
                                        from financial losses and reputational
                                        damage to legal liabilities. By
                                        investing in cybersecurity measures such
                                        as firewalls, intrusion detection
                                        systems, and security awareness
                                        training, businesses can reduce their
                                        susceptibility.
                                    </p>

                                    <p className="blog-text mt-5 mb-4">
                                        Living sustainably at Realar Residence
                                        is more than a choice; it's an immersive
                                        experience that shapes every moment of
                                        your day. From the moment you wake up in
                                        your solar-powered home to the evening
                                        gatherings with like-minded neighbors.
                                    </p>

                                    <h3 className="mt-4">
                                        The sustainable traveller These 6 hotels
                                        epitomise ethical luxury
                                    </h3>

                                    <p className="">
                                        Whether you work from home or commute to
                                        a nearby office, the energy-efficient
                                        features of your home contribute to a
                                        productive and eco-conscious workday.
                                        Smart home systems allow you to monitor
                                        and control energy usage, ensuring that
                                        your environmental impact remains
                                        minimal.
                                    </p>

                                    <div className="row gy-4">
                                        {post.gallery_images &&
                                            post.gallery_images.map(
                                                (img, index) => (
                                                    <div
                                                        className="col-6"
                                                        key={index}
                                                    >
                                                        <div className="blog-img">
                                                            <img
                                                                className="w-100"
                                                                src={img}
                                                                alt="Blog Image"
                                                            />
                                                        </div>
                                                    </div>
                                                )
                                            )}
                                    </div>

                                    <p className="blog-text mb-4">
                                        Quickly build covalent data after
                                        turnkey content. Distinctively
                                        reconceptualize customized growth
                                        strategies via prospective
                                        potentialities. Professionally pursue
                                        cutting-edge web-readiness vis-a-vis
                                        just in time infrastructures.
                                        Conveniently target client-based systems
                                        with turnkey sources.
                                    </p>
                                    <p className="mb-0">
                                        Collaboratively syndicate focused
                                        opportunities for interactive
                                        deliverables. Assertively initiate
                                        client-based infomediaries through
                                        collaborative mindshare create
                                        bleeding-edge meta-services
                                    </p>

                                    <div className="share-links clearfix ">
                                        <div className="row justify-content-between">
                                            <div className="col-md-auto">
                                                <span className="share-links-title">
                                                    Tags:
                                                </span>
                                                <div className="tagcloud">
                                                    {post.tags &&
                                                        post.tags.map(
                                                            (tag, index) => (
                                                                <Link
                                                                    key={index}
                                                                    href="/blog"
                                                                >
                                                                    {tag}
                                                                </Link>
                                                            )
                                                        )}
                                                </div>
                                            </div>
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
                                                            <i className="fab fa-instagram"></i>
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

                            <div className="th-comments-wrap ">
                                <h2 className="blog-inner-title h4">
                                    {" "}
                                    Comments (
                                    {post.comments.length < 10
                                        ? "0" + post.comments.length
                                        : post.comments.length}
                                    )
                                </h2>
                                <ul className="comment-list">
                                    {post.comments.map((comment) => (
                                        <li
                                            className="th-comment-item"
                                            key={comment.id}
                                        >
                                            <div className="th-post-comment">
                                                <div className="comment-avater">
                                                    <img
                                                        src={comment.avatar}
                                                        alt="Comment Author"
                                                    />
                                                </div>
                                                <div className="comment-content">
                                                    <h3 className="name">
                                                        {comment.author}
                                                    </h3>
                                                    <span className="commented-on">
                                                        {comment.date}
                                                    </span>
                                                    <p className="text">
                                                        {comment.text}
                                                    </p>
                                                    <div className="reply_and_edit">
                                                        <Link
                                                            href="#"
                                                            className="reply-btn"
                                                        >
                                                            <i className="fas fa-reply"></i>
                                                            Reply
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                            {comment.replies &&
                                                comment.replies.length > 0 && (
                                                    <ul className="children">
                                                        {comment.replies.map(
                                                            (reply) => (
                                                                <li
                                                                    className="th-comment-item"
                                                                    key={
                                                                        reply.id
                                                                    }
                                                                >
                                                                    <div className="th-post-comment">
                                                                        <div className="comment-avater">
                                                                            <img
                                                                                src={
                                                                                    reply.avatar
                                                                                }
                                                                                alt="Comment Author"
                                                                            />
                                                                        </div>
                                                                        <div className="comment-content">
                                                                            <div className="">
                                                                                <h3 className="name">
                                                                                    {
                                                                                        reply.author
                                                                                    }
                                                                                </h3>
                                                                                <span className="commented-on">
                                                                                    {
                                                                                        reply.date
                                                                                    }
                                                                                </span>
                                                                            </div>
                                                                            <p className="text">
                                                                                {
                                                                                    reply.text
                                                                                }
                                                                            </p>
                                                                            <div className="reply_and_edit">
                                                                                <Link
                                                                                    href="#"
                                                                                    className="reply-btn"
                                                                                >
                                                                                    <i className="fas fa-reply"></i>
                                                                                    Reply
                                                                                </Link>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            )
                                                        )}
                                                    </ul>
                                                )}
                                        </li>
                                    ))}
                                </ul>
                            </div>

                            <div className="th-comment-form ">
                                <div className="row">
                                    <h3 className="blog-inner-title h4 mb-2">
                                        Leave a Reply
                                    </h3>
                                    <p className="mb-25">
                                        Your email address will not be
                                        published. Required fields are marked
                                    </p>
                                    <div className="col-md-6 form-group">
                                        <input
                                            type="text"
                                            placeholder="Full Name*"
                                            className="form-control"
                                            required
                                        />
                                        <i className="far fa-user"></i>
                                    </div>
                                    <div className="col-md-6 form-group">
                                        <input
                                            type="text"
                                            placeholder="Your Email*"
                                            className="form-control"
                                            required
                                        />
                                        <i className="far fa-envelope"></i>
                                    </div>
                                    <div className="col-12 form-group">
                                        <input
                                            type="text"
                                            placeholder="Website"
                                            className="form-control"
                                            required
                                        />
                                        <i className="far fa-globe"></i>
                                    </div>
                                    <div className="col-12 form-group">
                                        <textarea
                                            placeholder="Comment*"
                                            className="form-control"
                                        ></textarea>
                                        <i className="far fa-pencil"></i>
                                    </div>
                                    <div className="col-12 form-group">
                                        <input type="checkbox" id="html" />
                                        <label htmlFor="html">
                                            Save my name, email, and website in
                                            this browser for the next time I
                                            comment.
                                        </label>
                                    </div>
                                    <div className="col-12 form-group mb-0">
                                        <button className="th-btn">
                                            Send Message
                                            <img
                                                src="/assets/img/icon/plane2.svg"
                                                alt=""
                                            />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="col-xxl-4 col-lg-5">
                            <aside className="sidebar-area">
                                <div className="widget widget_search">
                                    <form className="search-form">
                                        <input
                                            type="text"
                                            placeholder="Search"
                                        />
                                        <button type="submit">
                                            <i className="far fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                                <div className="widget widget_categories">
                                    <h3 className="widget_title">Categories</h3>
                                    <ul>
                                        {categories.map((cat, index) => (
                                            <li key={index}>
                                                <Link href="/news">{cat}</Link>
                                                <span>
                                                    <i className="fa-regular fa-arrow-up-right"></i>
                                                </span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                                <div className="widget">
                                    <h3 className="widget_title">
                                        Recent Posts
                                    </h3>
                                    <div className="recent-post-wrap">
                                        {recentPosts.map((rPost) => (
                                            <div
                                                className="recent-post"
                                                key={rPost.id}
                                            >
                                                <div className="media-img">
                                                    <Link href={rPost.link}>
                                                        <img
                                                            src={rPost.image}
                                                            alt="Blog Image"
                                                        />
                                                    </Link>
                                                </div>
                                                <div className="media-body">
                                                    <div className="recent-post-meta">
                                                        <Link href={rPost.link}>
                                                            <i className="fa-solid fa-calendar-days"></i>
                                                            {rPost.date}
                                                        </Link>
                                                    </div>
                                                    <h4 className="post-title">
                                                        <Link
                                                            className="text-inherit"
                                                            href={rPost.link}
                                                        >
                                                            {rPost.title}
                                                        </Link>
                                                    </h4>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                                <div className="widget widget_tag_cloud">
                                    <h3 className="widget_title">
                                        Popular Tags
                                    </h3>
                                    <div className="tagcloud">
                                        <Link href="/news">Advice</Link>
                                        <Link href="/news">Technology</Link>
                                        <Link href="/news">Atek</Link>
                                        <Link href="/news">Ux/Ui</Link>
                                        <Link href="/news">Consulting</Link>
                                        <Link href="/news">Solution</Link>
                                        <Link href="/news">Health</Link>
                                        <Link href="/news">IT Solution</Link>
                                        <Link href="/news">Cloud</Link>
                                    </div>
                                </div>
                                <div
                                    className="widget widget_banner"
                                    data-bg-src="/assets/img/bg/widget_banner.jpg"
                                    style={{
                                        backgroundImage:
                                            "url(/assets/img/bg/widget_banner.jpg)",
                                    }}
                                >
                                    <div className="widget-banner position-relative text-center">
                                        <span className="icon">
                                            <i className="fa-solid fa-phone"></i>
                                        </span>
                                        <span className="text">
                                            Need Help? Call Here
                                        </span>
                                        <a
                                            className="phone"
                                            href="tel:+25669872564"
                                        >
                                            +256 6987 2564
                                        </a>
                                        <Link
                                            href="/contact"
                                            className="th-btn style6"
                                        >
                                            Get A Quote{" "}
                                            <i className="fa-light fa-arrow-right-long"></i>
                                        </Link>
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
