import React from "react";
import { usePage } from "@inertiajs/react";

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
                                <a href="#">
                                    {new Date(
                                        project.project_date ||
                                            project.published_at,
                                    ).toLocaleDateString("id-ID", {
                                        day: "2-digit",
                                        month: "long",
                                        year: "numeric",
                                    })}
                                </a>
                            </h4>
                        </div>
                    </div>
                    <div className="sidebar-info-item d-flex align-items-center">
                        <span className="sidebar-info-icon flex-shrink-0">
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
            {project.download_brochures &&
                project.download_brochures.length > 0 && (
                    <div className="widget widget_download">
                        <h4 className="widget_title">Download Brochure</h4>
                        <div className="donwload-media-wrap">
                            {project.download_brochures.map((file, index) => {
                                const isPdf = file
                                    .toLowerCase()
                                    .endsWith(".pdf");
                                // Extract extension for label (e.g. PDF, JPG)
                                const extension = file
                                    .split(".")
                                    .pop()
                                    .toUpperCase();
                                return (
                                    <div className="download-media" key={index}>
                                        <div className="download-media_icon">
                                            <i
                                                className={
                                                    isPdf
                                                        ? "fa-light fa-file-pdf"
                                                        : "fal fa-file-lines"
                                                }
                                            ></i>
                                        </div>
                                        <div className="download-media_info">
                                            <h5 className="download-media_title">
                                                <a
                                                    href={`/storage/${file}`}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
                                                    Download {extension}
                                                </a>
                                            </h5>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                )}
            <div
                className="widget widget_banner"
                data-bg-src="/assets/img/bg/widget_banner.jpg"
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

                            const defaultMessage = `Halo, saya tertarik dengan project: ${project.title}`;
                            const message =
                                project.whatsapp_note || defaultMessage;

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
