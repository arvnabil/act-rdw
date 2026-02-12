import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";
import { getImageUrl as globalGetImageUrl } from "@/Utils/image";

export default function ServiceListSection({ services, getImageUrl }) {
    const resolveImage = getImageUrl || globalGetImageUrl;

    return (
        <section
            className="position-relative bg-top-center overflow-hidden space space-bottom"
            id="service-sec"
        >
            <style>{`
                .service-card-modern {
                    background: #fff;
                    border-radius: 16px;
                    overflow: hidden;
                    height: 100%;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.05); /* Softer shadow */
                    border: 1px solid rgba(0,0,0,0.03);
                    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                    display: flex;
                    flex-direction: column;
                    z-index: 1;
                    position: relative;
                }
                
                .service-card-modern:hover {
                    transform: translateY(-8px);
                    box-shadow: 0 15px 30px rgba(0,0,0,0.08); /* Lift effect */
                    border-color: rgba(13, 80, 93, 0.1); 
                }

                .service-card-img-wrapper {
                    position: relative;
                    width: 100%;
                    padding-top: 60%; /* 16:9 Aspect Ratio approx */
                    overflow: hidden;
                    background: #f8f9fa;
                }

                .service-card-img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.6s ease;
                }

                .service-card-modern:hover .service-card-img {
                    transform: scale(1.05); /* Subtle zoom */
                }

                .service-content-wrapper {
                   padding: 25px 30px;
                   display: flex;
                   flex-direction: column;
                   flex-grow: 1;
                }

                .service-card-title {
                    font-size: 20px; /* Slightly smaller for cleaner look */
                    font-weight: 700;
                    margin-bottom: 12px;
                    line-height: 1.4;
                    color: var(--title-color);
                    transition: color 0.3s ease;
                }
                
                .service-card-modern:hover .service-card-title {
                    color: var(--theme-color);
                }

                .service-card-desc {
                    font-size: 15px;
                    line-height: 1.6;
                    color: var(--body-color);
                    margin-bottom: 20px;
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    flex-grow: 1; /* Pushes button down */
                }

                .service-card-footer {
                    margin-top: auto;
                    padding-top: 15px;
                    border-top: 1px solid rgba(0,0,0,0.04);
                }

                .read-more-link {
                    font-size: 14px;
                    font-weight: 600;
                    color: var(--theme-color);
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    transition: gap 0.3s ease;
                }
                
                .read-more-link:hover {
                    gap: 12px;
                }
                
                .read-more-icon {
                    width: 24px;
                    height: 24px;
                    background: rgba(13, 80, 93, 0.08);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    transition: all 0.3s ease;
                }

                .service-card-modern:hover .read-more-icon {
                    background: var(--theme-color);
                    color: #fff;
                }
            `}</style>

            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-lg-8">
                        <SectionTitle
                            subTitle="Layanan Kami"
                            title="Solusi Profesional untuk Bisnis Anda"
                            align="title-area service-title-box text-center"
                        />
                        <div className="text-center">
                            <p className="sec-text mb-50 wow fadeInUp" data-wow-delay=".3s">
                                Solusi IT mencakup berbagai layanan dan teknologi yang dirancang untuk menjawab <br className="d-none d-lg-block" />
                                kebutuhan bisnis spesifik, menyederhanakan operasional, dan mendorong pertumbuhan.
                            </p>
                        </div>
                    </div>
                </div>

                <div className="service-area">
                    <div className="row gy-30 justify-content-center">
                        {services && services.length > 0 ? (
                            services.map((service, index) => (
                                <div className="col-xl-4 col-md-6" key={index}>
                                    <div className="service-card-modern wow fadeInUp" data-wow-delay={`0.${index + 1}s`}>
                                        <div className="service-card-img-wrapper">
                                            <Link href={`/services/${service.slug}`}>
                                                <img
                                                    src={resolveImage(service.thumbnail)}
                                                    alt={service.name}
                                                    className="service-card-img"
                                                />
                                            </Link>
                                        </div>
                                        <div className="service-content-wrapper">
                                            <h3 className="service-card-title">
                                                <Link href={`/services/${service.slug}`}>
                                                    {service.name}
                                                </Link>
                                            </h3>
                                            <p className="service-card-desc">
                                                {service.description
                                                    ? service.description.replace(/<[^>]*>?/gm, '').substring(0, 100) + "..."
                                                    : "Solusi terpercaya untuk kebutuhan bisnis Anda."}
                                            </p>
                                            <div className="service-card-footer">
                                                <Link
                                                    className="read-more-link"
                                                    href={`/services/${service.slug}`}
                                                >
                                                    Baca Selengkapnya
                                                    <span className="read-more-icon">
                                                        <i className="fa-solid fa-arrow-right"></i>
                                                    </span>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="col-12 text-center">
                                <p>Belum ada layanan yang tersedia.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
}
