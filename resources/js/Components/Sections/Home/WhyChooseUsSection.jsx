import React from 'react';
import { Link } from '@inertiajs/react';
import SectionTitle from '@/Components/Common/SectionTitle';

export default function WhyChooseUsSection() {
    return (
        <div className="bg-smoke position-relative overflow-hidden space" id="why-sec">
            <div className="container">
                <div className="row">
                    <div className="col-xl-6">
                        <div className="ab5-wrapp mt-40">
                            <SectionTitle
                                align="title-area"
                                subTitle="Why Choose ACTiV"
                                title="Empowering Your Future with Proven Technology."
                                mb=""
                            />
                            <p className="sec-text mb-30 wow fadeInUp" data-wow-delay=".2s">
                                We are dedicated to providing comprehensive ICT and Education solutions that are not only advanced but also reliable. By combining technical expertise with global support, we ensure every technology investment you make delivers real, sustainable value.
                            </p>
                            <div className="about-item-wrap">
                                <div className="about-item ab5-item wow fadeInUp" data-wow-delay=".3s">
                                    <div className="about-item_img">
                                        <img src="/assets/img/icon/shield.svg" alt="" />
                                    </div>
                                    <div className="about-item_centent">
                                        <h5 className="box-title">Expert Team</h5>
                                        <p className="about-item_text">
                                            Backed by professionals with over 6 years of experience in ICT infrastructure
                                        </p>
                                    </div>
                                </div>
                                <div className="about-item ab5-item wow fadeInUp" data-wow-delay=".4s">
                                    <div className="about-item_img">
                                        <img src="/assets/img/icon/shield.svg" alt="" />
                                    </div>
                                    <div className="about-item_centent">
                                        <h5 className="box-title">Certified Brand Partners</h5>
                                        <p className="about-item_text">
                                            Official partnerships ensuring product authenticity and certified technical support.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div className="mt-35 wow fadeInUp mb-2" data-wow-delay=".5s">
                                <Link href="/about" className="th-btn th-radius th-icon">
                                    Learn More <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6">
                        <div className="img-box4 ab5-imgbox space-bottom text-end">
                            <div className="img1 reveal">
                                <img src="/assets/img/normal/about_4_1.jpg" alt="About" />
                            </div>
                            <div className="img2">
                                <img src="/assets/img/normal/about_4_2.jpg" alt="About" />
                                <a href="https://www.youtube.com/watch?v=hIIQbkkKnno" className="play-btn popup-video">
                                    <i className="fa-sharp fa-solid fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
