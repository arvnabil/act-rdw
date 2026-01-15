import React from "react";
import { Link } from "@inertiajs/react";

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
                    {displayCategories.map((cat, index) => (
                        <li key={index}>
                            <Link href="/news" className="text-inherit">
                                {typeof cat === "string" ? cat : cat.name}
                            </Link>
                            {typeof cat !== "string" && cat.count && (
                                <span>({cat.count})</span>
                            )}
                            <span className="float-end">
                                <i className="fa-regular fa-arrow-up-right"></i>
                            </span>
                        </li>
                    ))}
                </ul>
            </div>
            <div className="widget">
                <h3 className="widget_title">Recent Posts</h3>
                <div className="recent-post-wrap">
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
                                                  [
                                                      "5 Common IT Issues and How to Solve Them",
                                                      "Hybrid Cloud Solutions: The Best of Both Worlds",
                                                      "Top 10 IT Solutions Every Business",
                                                  ][
                                                      isDummyPost(rPost)
                                                          ? rPost - 1
                                                          : index
                                                  ] ||
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
                                              {
                                                  [
                                                      "5 Common IT Issues and How to Solve Them",
                                                      "Hybrid Cloud Solutions: The Best of Both Worlds",
                                                      "Top 10 IT Solutions Every Business",
                                                  ][num - 1]
                                              }
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
                    {displayTags.map((tag, index) => (
                        <Link href="/news" key={index}>
                            {tag}
                        </Link>
                    ))}
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
                    <span className="text">Need Help? Call Here</span>
                    <a className="phone" href="tel:+25669872564">
                        +256 6987 2564
                    </a>
                    <Link href="/contact" className="th-btn style6">
                        Get A Quote{" "}
                        <i className="fa-light fa-arrow-right-long"></i>
                    </Link>
                </div>
            </div>
        </aside>
    );
}
