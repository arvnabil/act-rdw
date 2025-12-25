import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Breadcrumb from '@/Components/Common/Breadcrumb';

export default function ProjectDetail({ project }) {
    return (
        <MainLayout>
            <Head title={project.title} />

            <Breadcrumb
                title="Project Details"
                items={[
                    { label: 'Home', link: '/' },
                    { label: 'Projects', link: '/projects' },
                    { label: project.title }
                ]}
            />

            <section className="space">
                <div className="container">
                    <div className="row">
                        <div className="col-12">
                            <div className="project-dsc-img mb-60">
                                <img
                                    className="w-100 rounded-20"
                                    src={project.image}
                                    alt={project.title}
                                    style={{ width: '100%', height: 'auto', maxHeight: '720px', objectFit: 'cover' }}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xxl-8 col-lg-7">
                            <div className="page-single mb-60">
                                <div className="page-content d-block">
                                    <h2 className="box-title">{project.title}</h2>
                                    <p className="box-text mb-30">{project.description}</p>

                                    <h2 className="box-title">The challenge of project</h2>
                                    <p className="box-text mb-30">{project.challenge_text}</p>

                                    <div className="project-gallery-wrapper">
                                        <div className="row gy-4">
                                            {project.gallery && project.gallery.map((img, index) => (
                                                <div className="col-6" key={index}>
                                                    <div className="sv-sm-img">
                                                        <img className="w-100 rounded-10" src={img} alt="Project Gallery" />
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Pagination/Navigation */}
                            <div className="th-pagination">
                                <div className="container">
                                    <div className="pagination-box2 d-flex justify-content-between">
                                        <div className="pagination-prev">
                                            {project.prev_id ? (
                                                <Link href={`/projects/${project.prev_id}`} className="pagination-icon">
                                                    <i className="fa-solid fa-arrow-left"></i>
                                                </Link>
                                            ) : (
                                                <span className="pagination-icon disabled"><i className="fa-solid fa-arrow-left"></i></span>
                                            )}
                                            <p className="pagination-title">Previous Post</p>
                                        </div>
                                        <div className="pagination-next text-end">
                                            <p className="pagination-title">Next Post</p>
                                            <Link href={`/projects/${project.next_id}`} className="pagination-icon">
                                                <i className="fa-solid fa-arrow-right"></i>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Sidebar */}
                        <div className="col-xxl-4 col-lg-5">
                            <aside className="sidebar-area">
                                <div className="widget widget_download">
                                    <h4 className="widget_title">Project Information</h4>
                                    <div className="sidebar-info">
                                        <div className="sidebar-info-item d-flex align-items-center">
                                            <span className="sidebar-info-icon"><i className="fa-solid fa-user"></i></span>
                                            <div className="sidebar-info-content">
                                                <span className="sidebar-info-meta">Clients:</span>
                                                <h4 className="sidebar-info-title">
                                                    <a href="#">{project.client}</a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div className="sidebar-info-item d-flex align-items-center">
                                            <span className="sidebar-info-icon"><i className="fa-solid fa-layer-group"></i></span>
                                            <div className="sidebar-info-content">
                                                <span className="sidebar-info-meta">Category:</span>
                                                <h4 className="sidebar-info-title">
                                                    <a href="#">{project.category}</a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div className="sidebar-info-item d-flex align-items-center">
                                            <span className="sidebar-info-icon"><i className="fa-solid fa-calendar-days"></i></span>
                                            <div className="sidebar-info-content">
                                                <span className="sidebar-info-meta">Date:</span>
                                                <h4 className="sidebar-info-title">
                                                    <a href="#">{project.date}</a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div className="sidebar-info-item d-flex align-items-center">
                                            <span className="sidebar-info-icon"><i className="fa-solid fa-location-dot"></i></span>
                                            <div className="sidebar-info-content">
                                                <span className="sidebar-info-meta">Address:</span>
                                                <h4 className="sidebar-info-title">
                                                    <a href={project.map_url} target="_blank">{project.address}</a>
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
                                <div className="widget widget_banner" data-bg-src="/assets/img/bg/widget_banner.jpg">
                                    <div className="widget-banner position-relative text-center">
                                        <span className="icon"><i className="fa-solid fa-phone"></i></span>
                                        <span className="text">Need Help? Call Here</span>
                                        <a className="phone" href="tel:+25669872564">+256 6987 2564</a>
                                        <a href="/contact" className="th-btn style6">Get A Quote <i className="fa-light fa-arrow-right-long"></i></a>
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
