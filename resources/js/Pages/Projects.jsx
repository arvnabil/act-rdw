import React from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Breadcrumb from '@/Components/Common/Breadcrumb';

export default function Projects({ projects }) {
    return (
        <MainLayout>
            <Head title="Projects" />

            <Breadcrumb
                title="Projects Grid"
                items={[
                    { label: 'Home', link: '/' },
                    { label: 'Projects' }
                ]}
            />

            {/* Gallery Area */}
            <div className="overflow-hidden space" id="case-study-sec">
                <div className="container">
                    <div className="row gy-4">
                        {projects.data.map((project) => (
                            <div className="col-xl-4 col-md-6" key={project.id}>
                                <div className="case-box style2 position-relative">
                                    <div className="case-img global-img">
                                        <img src={project.image} alt={project.title} />
                                        <Link href={project.link} className="icon-btn">
                                            <i className="fa-light fa-arrow-right-long"></i>
                                        </Link>
                                    </div>
                                    <div className="case-content">
                                        <div className="media-left">
                                            <h4 className="box-title">
                                                <Link href={project.link}>{project.title}</Link>
                                            </h4>
                                            <span className="case-subtitle">{project.subtitle}</span>
                                        </div>
                                    </div>
                                    <div className="case-action">
                                        <Link href={project.link} className="case-btn">
                                            <i className="fa-light fa-arrow-right-long"></i>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination */}
                    {projects.links && projects.links.length > 3 && (
                        <div className="th-pagination mt-60 mt-60 mb-0 text-center">
                            <ul>
                                {projects.links.map((link, index) => (
                                    <li key={index}>
                                        {link.url ? (
                                            <Link
                                                href={link.url}
                                                className={`${link.active ? 'active' : ''} ${link.className || ''}`}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                                preserveScroll
                                            />
                                        ) : (
                                            null // Don't render span for disabled arrows to match style or handle disabled state if needed
                                        )}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            </div>
        </MainLayout>
    );
}
