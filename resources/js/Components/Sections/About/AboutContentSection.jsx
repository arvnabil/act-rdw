import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function AboutContentSection() {
    return (
        <div
            className="about-area position-relative overflow-hidden space"
            id="about-sec"
        >
            <div className="container">
                <div className="row">
                    <div className="col-xl-6 col-xxl-7">
                        <div className="img-box2">
                            <div className="img1 reveal">
                                <img
                                    src="/assets/img/normal/about_4_1.jpg"
                                    alt="About"
                                />
                            </div>
                            <div className="img2 wow fadeInUp">
                                <img
                                    src="/assets/img/normal/about_4_2.jpg"
                                    alt="About"
                                />
                                <a
                                    href="https://www.youtube.com/watch?v=hIIQbkkKnno"
                                    className="play-btn popup-video"
                                >
                                    <i className="fa-sharp fa-solid fa-play"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6 col-xxl-5">
                        <div>
                            <SectionTitle
                                subTitle="About Us"
                                title="Bridging Technology and Education for a Better Future."
                                align="title-area about4-titlebox mb-20"
                                mb="mb-20"
                            />
                            <p
                                className="sec-text mb-30 wow fadeInUp"
                                data-wow-delay=".2s"
                            >
                                ACTiV (PT Alfa Cipta Teknologi Virtual) is a
                                dynamic company specializing in the sales and
                                rental of software, hardware, and supporting
                                accessories, with a primary focus on Information
                                Communication Technology (ICT) and Education
                                solutions. Backed by a team with over 6 years of
                                experience and official partnerships with
                                multinational ICT brands, we are dedicated to
                                delivering the best comprehensive technology
                                solutions to our clients.
                            </p>
                            <div className="about-item-wrap">
                                <div
                                    className="about-item wow fadeInUp"
                                    data-wow-delay=".2s"
                                >
                                    <div className="about-item_img">
                                        <img
                                            src="/assets/img/icon/guide.svg"
                                            alt=""
                                        />
                                    </div>
                                    <div className="about-item_centent">
                                        <h5 className="box-title">
                                            Expert Team
                                        </h5>
                                        <p className="about-item_text">
                                            There are many variations of
                                            passages of available but the
                                            majority.
                                        </p>
                                    </div>
                                </div>
                                <div
                                    className="about-item wow fadeInUp"
                                    data-wow-delay=".3s"
                                >
                                    <div className="about-item_img">
                                        <img
                                            src="/assets/img/icon/shield.svg"
                                            alt=""
                                        />
                                    </div>
                                    <div className="about-item_centent">
                                        <h5 className="box-title">
                                            Certified Brand Partners
                                        </h5>
                                        <p className="about-item_text">
                                            Official partnerships ensuring
                                            product authenticity and certified
                                            technical support.
                                        </p>
                                    </div>
                                </div>
                                <div
                                    className="about-item wow fadeInUp"
                                    data-wow-delay=".4s"
                                >
                                    <div className="about-item_img">
                                        <img
                                            src="/assets/img/icon/headphone.svg"
                                            alt=""
                                        />
                                    </div>
                                    <div className="about-item_centent">
                                        <h5 className="box-title">
                                            24/7 Customer Support
                                        </h5>
                                        <p className="about-item_text">
                                            A support system operating non-stop
                                            through various channels (phone,
                                            chat, email, etc.) to provide
                                            instant or timely responses and
                                            resolutions.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div
                                className="mt-45 wow fadeInUp"
                                data-wow-delay=".5s"
                            >
                                <Link
                                    href="/contact"
                                    className="th-btn style12 btn-2 th-radius th-icon"
                                >
                                    Contact Us{" "}
                                    <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
