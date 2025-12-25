import React from 'react';
import { Link } from '@inertiajs/react';

export default function ServiceSection() {
    return (
        <div className="service-area space-bottom" id="service-sec">
            <div className="container">
                <div className="row justify-content-lg-between justify-content-center align-items-center">
                    <div className="col-lg-6 mb-n2 mb-lg-0">
                        <div className="title-area text-center text-lg-start">
                            <span className="sub-title">
                                <span className="squre-shape left me-3"></span>What we Offer<span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title">
                                <span className="scroll-text-ani">Explore Our Services</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div className="row">
                    <div className="service-list-area">
                        <div className="service-list-wrap sv-list2 service7-list active">
                            <div className="service-list" data-bg-src="/assets/img/service/service_6_1.jpg">
                                <span className="service-icon">
                                    <img src="/assets/img/icon/sv-6-1.svg" alt="" />
                                </span>
                                <div className="service-content">
                                    <h4 className="box-title">
                                        <Link href="/services/video-conference">Video Conference</Link>
                                    </h4>
                                    <span className="service-subtitle">High-quality AV solutions for seamless collaboration in meetings.</span>
                                </div>
                                <Link href="/services/video-conference" className="th-btn th-radius style2">
                                    View Details <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                        <div className="service-list-wrap sv-list2 service7-list">
                            <div className="service-list" data-bg-src="/assets/img/service/service_2_2.jpg">
                                <span className="service-icon">
                                    <img src="/assets/img/icon/sv-6-2.svg" alt="" />
                                </span>
                                <div className="service-content">
                                    <h4 className="box-title">
                                        <Link href="/services/server-storage">Server & Storage</Link>
                                    </h4>
                                    <span className="service-subtitle">Robust and secure infrastructure for high-performance data management.</span>
                                </div>
                                <Link href="/services/server-storage" className="th-btn th-radius style2">
                                    View Details <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                        <div className="service-list-wrap sv-list2 service7-list">
                            <div className="service-list" data-bg-src="/assets/img/service/service_2_3.jpg">
                                <span className="service-icon">
                                    <img src="/assets/img/icon/sv-6-3.svg" alt="" />
                                </span>
                                <div className="service-content">
                                    <h4 className="box-title">
                                        <Link href="/services/smart-surveillance">Smart Surveillance</Link>
                                    </h4>
                                    <span className="service-subtitle">Advanced 24/7 security monitoring and CCTV systems.</span>
                                </div>
                                <Link href="/services/smart-surveillance" className="th-btn th-radius style2">
                                    View Details <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
