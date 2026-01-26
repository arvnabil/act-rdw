import React, { useState } from "react";
import { Link, usePage, router } from "@inertiajs/react";

export default function BlogSidebar({
    categories = [],
    recentPosts = [],
    tags = [],
    filters = {},
}) {
    const [searchTerm, setSearchTerm] = useState(filters.search || "");

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(
            route("news.index"),
            { search: searchTerm },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    return (
        <aside className="sidebar-area">
            <div className="widget widget_search">
                <form className="search-form" onSubmit={handleSearch}>
                    <input
                        type="text"
                        placeholder="Search"
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                    <button type="submit">
                        <i className="far fa-search"></i>
                    </button>
                </form>
            </div>
            <div className="widget widget_categories">
                <h3 className="widget_title">Categories</h3>
                <ul>
                    {categories.length > 0 ? (
                        categories.map((cat, index) => (
                            <li key={index}>
                                <Link
                                    href={route("news.category", cat.slug)}
                                    className="text-inherit"
                                >
                                    {cat.name}
                                </Link>
                                <span>({cat.posts_count})</span>
                                <span className="float-end">
                                    <i className="fa-regular fa-arrow-up-right"></i>
                                </span>
                            </li>
                        ))
                    ) : (
                        <li>No categories found.</li>
                    )}
                </ul>
            </div>

            <div className="widget">
                <h3 className="widget_title">Recent Posts</h3>
                <div className="recent-post-wrap">
                    {recentPosts.length > 0 ? (
                        recentPosts.map((rPost, index) => (
                            <div
                                className="recent-post"
                                key={rPost.id || index}
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
                        ))
                    ) : (
                        <div>No recent posts.</div>
                    )}
                </div>
            </div>

            <div className="widget widget_tag_cloud">
                <h3 className="widget_title">Popular Tags</h3>
                <div className="tagcloud">
                    {tags.length > 0 ? (
                        tags.map((tag, index) => (
                            <Link
                                href={route("news.tag", tag.slug)}
                                key={index}
                            >
                                {tag.name}
                            </Link>
                        ))
                    ) : (
                        <span>No tags found.</span>
                    )}
                </div>
            </div>
            <div
                className="widget widget_banner"
                data-bg-src="/assets/img/bg/widget_banner.jpg"
                style={{
                    backgroundImage: "url(/assets/img/bg/widget_banner.jpg)",
                }}
            >
                <div className="widget-banner position-relative text-center">
                    <span className="icon">
                        <i className="fa-solid fa-phone"></i>
                    </span>
                    <span className="text">Butuh bantuan? Hubungi kami</span>

                    {(() => {
                        const { settings } = usePage().props;
                        const whatsappNumber = settings?.whatsapp_number;

                        let link = "/contact";
                        let target = "_self";

                        if (whatsappNumber) {
                            // Clean non-digits
                            let number = whatsappNumber.replace(/\D/g, "");
                            // Replace leading 0 with 62 (Indonesian specific convenience)
                            if (number.startsWith("0")) {
                                number = "62" + number.substring(1);
                            }

                            const message =
                                "Halo, saya butuh bantuan terkait layanan Anda.";

                            link = `https://wa.me/${number}?text=${encodeURIComponent(message)}`;
                            target = "_blank";
                        }

                        return (
                            <a
                                href={link}
                                className="th-btn style6"
                                target={target}
                                rel={
                                    target === "_blank"
                                        ? "noopener noreferrer"
                                        : ""
                                }
                            >
                                Hubungi kami{" "}
                                <i className="fa-light fa-arrow-right-long"></i>
                            </a>
                        );
                    })()}
                </div>
            </div>
        </aside>
    );
}
