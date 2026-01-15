import React from "react";

export default function ProjectSidebar({ project }) {
    return (
        <aside className="sidebar-area">
            <div className="widget widget_download">
                <h4 className="widget_title">Project Information</h4>
                <div className="sidebar-info">
                    <div className="sidebar-info-item d-flex align-items-center">
                        <span className="sidebar-info-icon">
                            <i className="fa-solid fa-user"></i>
                        </span>
                        <div className="sidebar-info-content">
                            <span className="sidebar-info-meta">Clients:</span>
                            <h4 className="sidebar-info-title">
                                <a href="#">{project.client}</a>
                            </h4>
                        </div>
                    </div>
                    <div className="sidebar-info-item d-flex align-items-center">
                        <span className="sidebar-info-icon">
                            <i className="fa-solid fa-layer-group"></i>
                        </span>
                        <div className="sidebar-info-content">
                            <span className="sidebar-info-meta">Category:</span>
                            <h4 className="sidebar-info-title">
                                <a href="#">{project.category}</a>
                            </h4>
                        </div>
                    </div>
                    <div className="sidebar-info-item d-flex align-items-center">
                        <span className="sidebar-info-icon">
                            <i className="fa-solid fa-calendar-days"></i>
                        </span>
                        <div className="sidebar-info-content">
                            <span className="sidebar-info-meta">Date:</span>
                            <h4 className="sidebar-info-title">
                                <a href="#">{project.date}</a>
                            </h4>
                        </div>
                    </div>
                    <div className="sidebar-info-item d-flex align-items-center">
                        <span className="sidebar-info-icon">
                            <i className="fa-solid fa-location-dot"></i>
                        </span>
                        <div className="sidebar-info-content">
                            <span className="sidebar-info-meta">Address:</span>
                            <h4 className="sidebar-info-title">
                                <a href={project.map_url} target="_blank">
                                    {project.address}
                                </a>
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
            <div className="widget widget_download">
                <h4 className="widget_title">Download Brochure</h4>
                <div className="donwload-media-wrap">
                    <div className="download-media">
                        <div className="download-media_icon">
                            <i className="fa-light fa-file-pdf"></i>
                        </div>
                        <div className="download-media_info">
                            <h5 className="download-media_title">
                                <a href="#">Download PDF</a>
                            </h5>
                        </div>
                    </div>
                    <div className="download-media">
                        <div className="download-media_icon">
                            <i className="fal fa-file-lines"></i>
                        </div>
                        <div className="download-media_info">
                            <h5 className="download-media_title">
                                <a href="#">Download DOC</a>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            <div
                className="widget widget_banner"
                data-bg-src="/assets/img/bg/widget_banner.jpg"
            >
                <div className="widget-banner position-relative text-center">
                    <span className="icon">
                        <i className="fa-solid fa-phone"></i>
                    </span>
                    <span className="text">Need Help? Call Here</span>
                    <a className="phone" href="tel:+25669872564">
                        +256 6987 2564
                    </a>
                    <a href="/contact" className="th-btn style6">
                        Get A Quote{" "}
                        <i className="fa-light fa-arrow-right-long"></i>
                    </a>
                </div>
            </div>
        </aside>
    );
}
