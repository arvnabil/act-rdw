import React from 'react';
import { Link } from '@inertiajs/react';
import SectionTitle from '@/Components/Common/SectionTitle';

export default function AboutSection() {
    return (
        <div className="about-area position-relative overflow-hidden space" id="about-sec">
            <div className="container">
                <div className="row gy-40">
                    <div className="col-xl-6 col-lg-6">
                        <SectionTitle
                            subTitle="Who We Are"
                            title="Bridging Technology and Education for a Better Future."
                            align="title-area mb-20"
                            mb="mb-20"
                        />
                        <p className="sec-text mb-60 wow fadeInUp" data-wow-delay=".2s">
                            ACTiV (PT Alfa Cipta Teknologi Virtual) is a dynamic company specializing in the sales and rental of software, hardware, and supporting accessories, with a primary focus on Information Communication Technology (ICT) and Education solutions. Backed by a team with over 6 years of experience and official partnerships with multinational ICT brands, we are dedicated to delivering the best comprehensive technology solutions to our clients.
                        </p>
                        <div className="img-box8">
                            <div className="row gy-4">
                                <div className="col-xl-6 col-md-6 col-12">
                                    <div className="img1 reveal mb-0">
                                        <img src="/assets/img/normal/about_7_1.jpg" alt="About" />
                                    </div>
                                </div>
                                <div className="col-xl-6 col-md-6 col-12">
                                    <div className="img2 reveal mb-0">
                                        <img src="/assets/img/normal/about_7_2.jpg" alt="About" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6 col-lg-6">
                        <div className="img-box8 ms-xl-5">
                            <div className="img3 reveal">
                                <img src="/assets/img/normal/about_7_3.jpg" alt="About" />
                            </div>
                            <div className="about-item-wrap">
                                <div className="about-item wow fadeInUp" data-wow-delay=".3s">
                                    <div className="about-item_img">
                                        <img src="/assets/img/icon/shield.svg" alt="" />
                                    </div>
                                    <div className="about-item_centent">
                                        <h5 className="box-title">ICT & Education Product Supply</h5>
                                        <p className="about-item_text">
                                            Official provider of hardware and software tailored for education and ICT infrastructure.
                                        </p>
                                    </div>
                                </div>
                                <div className="about-item wow fadeInUp" data-wow-delay=".4s">
                                    <div className="about-item_img">
                                        <img src="/assets/img/icon/shield.svg" alt="" />
                                    </div>
                                    <div className="about-item_centent">
                                        <h5 className="box-title">Solution Services & Custom Development</h5>
                                        <p className="about-item_text">
                                            Expert technical solutions and custom software development tailored to your specific needs.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div className="mt-35 wow fadeInUp" data-wow-delay=".5s">
                                <Link href="/about" className="th-btn th-radius th-icon">
                                    Learn More <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
