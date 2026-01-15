import React from 'react';
import { Link } from '@inertiajs/react';

export default function SolutionSection() {
    return (
        <section className="choose-6-area space-bottom space-bottom">
            <div className="container">
                <div className="row gy-4 align-items-start">
                    <div className="col-xl-5 order-1 order-xl-0">
                        <div className="title-area mt-40 mb-20 pe-xl-5">
                            <span className="sub-title text-anime-style-2">
                                <span className="squre-shape left me-3"></span>🚀 Projects
                                <span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title text-anime-style-3">Our Solution</h2>
                        </div>
                        <style>
                            {`
                                .about-item.style-16 .about-feature ul li::before {
                                    background-image: url("/assets/img/icon/checkmark.svg") !important;
                                }
                            `}
                        </style>
                        <div className="about-item style-16">
                            <div className="about-content">
                                <p className="about-text wow fadeInUp">
                                    At ACTiV, we don't just develop technology; we engineer
                                    powerful digital solutions that drive real business
                                    transformation. Explore our portfolio to see how we leverage
                                    cutting-edge ICT to help our clients achieve greater
                                    productivity, visibility, and growth.
                                </p>
                                <div className="about-featured-box d-sm-flex align-items-start">
                                    <div className="about-feature pe-xl-4">
                                        <ul>
                                            <li className="wow fadeInUp" data-wow-delay=".2s">
                                                Room Solution
                                            </li>
                                            <li className="wow fadeInUp" data-wow-delay=".3s">
                                                Server IP PBX
                                            </li>
                                            <li className="wow fadeInUp" data-wow-delay=".4s">
                                                Storage & Data Solution
                                            </li>
                                        </ul>
                                    </div>
                                    <div className="about-feature">
                                        <ul>
                                            <li className="wow fadeInUp" data-wow-delay=".2s">
                                                Meeting cloud
                                            </li>
                                            <li className="wow fadeInUp" data-wow-delay=".3s">
                                                CCTV & Access Control
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div className="about-btn mt-30 wow fadeInUp" data-wow-delay=".4s">
                                    <Link href="/contact" className="th-btn th-radius style8 th-icon">
                                        Explore Case Studies
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-7 order-0 order-xl-1">
                        <div className="choose-wrapper text-end position-relative mb-80">
                            <div className="choose-item4">
                                <div className="choose-img">
                                    <img src="/assets/img/choose/choose_4_1.jpg" alt="" />
                                </div>
                            </div>
                            <div className="choose-item4">
                                <div className="choose-img">
                                    <img src="/assets/img/choose/choose_4_2.jpg" alt="" />
                                </div>
                            </div>
                            <div className="choose-item4">
                                <div className="choose-img">
                                    <img src="/assets/img/choose/choose_4_3.jpg" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
