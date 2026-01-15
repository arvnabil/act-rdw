import React from "react";
import { Link } from "@inertiajs/react";

export default function BlogDetailContent({ post }) {
    return (
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
                        <img src="/assets/img/icon/map.svg" alt="" />
                        {post.location}
                    </Link>
                </div>
                <h2 className="blog-title">{post.title}</h2>
                <p className="blog-text mb-30">{post.content_intro}</p>

                <p className="blog-text mb-30">{post.content_main}</p>

                <blockquote>
                    <p>{post.quote.text}</p>
                    <cite>{post.quote.author}</cite>
                </blockquote>

                <p className="blog-text mt-5 mb-4">
                    These attacks can have devastating consequences for
                    businesses, ranging from financial losses and reputational
                    damage to legal liabilities. By investing in cybersecurity
                    measures such as firewalls, intrusion detection systems, and
                    security awareness training, businesses can reduce their
                    susceptibility.
                </p>

                <p className="blog-text mt-5 mb-4">
                    Living sustainably at Realar Residence is more than a
                    choice; it's an immersive experience that shapes every
                    moment of your day. From the moment you wake up in your
                    solar-powered home to the evening gatherings with
                    like-minded neighbors.
                </p>

                <h3 className="mt-4">
                    The sustainable traveller These 6 hotels epitomise ethical
                    luxury
                </h3>

                <p className="">
                    Whether you work from home or commute to a nearby office,
                    the energy-efficient features of your home contribute to a
                    productive and eco-conscious workday. Smart home systems
                    allow you to monitor and control energy usage, ensuring that
                    your environmental impact remains minimal.
                </p>

                <div className="row gy-4">
                    {post.gallery_images &&
                        post.gallery_images.map((img, index) => (
                            <div className="col-6" key={index}>
                                <div className="blog-img">
                                    <img
                                        className="w-100"
                                        src={img}
                                        alt="Blog Image"
                                    />
                                </div>
                            </div>
                        ))}
                </div>

                <p className="blog-text mb-4">
                    Quickly build covalent data after turnkey content.
                    Distinctively reconceptualize customized growth strategies
                    via prospective potentialities. Professionally pursue
                    cutting-edge web-readiness vis-a-vis just in time
                    infrastructures. Conveniently target client-based systems
                    with turnkey sources.
                </p>
                <p className="mb-0">
                    Collaboratively syndicate focused opportunities for
                    interactive deliverables. Assertively initiate client-based
                    infomediaries through collaborative mindshare create
                    bleeding-edge meta-services
                </p>

                <div className="share-links clearfix ">
                    <div className="row justify-content-between">
                        <div className="col-md-auto">
                            <span className="share-links-title">Tags:</span>
                            <div className="tagcloud">
                                {post.tags &&
                                    post.tags.map((tag, index) => (
                                        <Link key={index} href="/news">
                                            {tag}
                                        </Link>
                                    ))}
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
    );
}
