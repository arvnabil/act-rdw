import React from "react";
import { Link, usePage } from "@inertiajs/react";

export default function BlogSidebar({
    categories = [],
    recentPosts = [],
    tags = [],
}) {
    // Default data if not provided (handling the hardcoded ones from Blog.jsx)
    const defaultCategories = [
        "IT Strategy & Planning",
        "Web Developments",
        "Cloud Consulting",
        "Machine Learning",
        "Database Security",
        "IT Management",
    ];

    const displayCategories =
        categories.length > 0 ? categories : defaultCategories;

    const defaultTags = [
        "Advice",
        "Technology",
        "Atek",
        "Ux/Ui",
        "Consulting",
        "Solution",
        "Health",
        "IT Solution",
        "Cloud",
    ];
    const displayTags = tags.length > 0 ? tags : defaultTags;

    // Helper for recent posts if they are numbers (dummies) or objects
    const isDummyPost = (post) => typeof post === "number";

    return (
        <aside className="sidebar-area">
            <div className="widget widget_search">
                <form className="search-form">
                    <input type="text" placeholder="Search" />
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

            {/* Recent Posts Widget - Keeping dummy logic for now as 'recentPosts' prop wasn't fully refactored yet, or maybe it was passed?
                Actually NewsController didn't pass 'recentPosts'.
                I should probably leave it as is or ask user?
                The plan only mentioned Categories/Tags.
                I will skip Recent Posts changes for now to stay focused.
            */}
            <div className="widget">
                <h3 className="widget_title">Recent Posts</h3>
                <div className="recent-post-wrap">
                    {/* ... keeping existing recent post logic or assuming it defaults to empty/dummy ... */}
                    {recentPosts.length > 0
                        ? recentPosts.map((rPost, index) => (
                              <div
                                  className="recent-post"
                                  key={rPost.id || index}
                              >
                                  <div className="media-img">
                                      <Link
                                          href={rPost.link || `/news/${rPost}`}
                                      >
                                          <img
                                              src={
                                                  rPost.image ||
                                                  `/assets/img/blog/recent-post-1-${
                                                      isDummyPost(rPost)
                                                          ? rPost
                                                          : index + 1
                                                  }.jpg`
                                              }
                                              alt="Blog Image"
                                          />
                                      </Link>
                                  </div>
                                  <div className="media-body">
                                      <div className="recent-post-meta">
                                          <Link
                                              href={
                                                  rPost.link || `/news/${rPost}`
                                              }
                                          >
                                              <i className="fa-solid fa-calendar-days"></i>
                                              {rPost.date || "22 Sep, 2025"}
                                          </Link>
                                      </div>
                                      <h4 className="post-title">
                                          <Link
                                              className="text-inherit"
                                              href={
                                                  rPost.link || `/news/${rPost}`
                                              }
                                          >
                                              {rPost.title ||
                                                  "Recent Post Title"}
                                          </Link>
                                      </h4>
                                  </div>
                              </div>
                          ))
                        : [1, 2, 3].map((num) => (
                              <div className="recent-post" key={num}>
                                  <div className="media-img">
                                      <Link href={`/news/${num}`}>
                                          <img
                                              src={`/assets/img/blog/recent-post-1-${num}.jpg`}
                                              alt="Blog Image"
                                          />
                                      </Link>
                                  </div>
                                  <div className="media-body">
                                      <div className="recent-post-meta">
                                          <Link href={`/news/${num}`}>
                                              <i className="fa-solid fa-calendar-days"></i>
                                              22 Sep, 2025
                                          </Link>
                                      </div>
                                      <h4 className="post-title">
                                          <Link
                                              className="text-inherit"
                                              href={`/news/${num}`}
                                          >
                                              Recent Post {num}
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
