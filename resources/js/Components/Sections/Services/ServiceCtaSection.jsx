import React from "react";
import { Link } from "@inertiajs/react";

export default function ServiceCtaSection() {
    return (
        <div className="position-relative overflow-hidden space-bottom">
            <div className="cta-sec6 theme-bg position-relative overflow-hidden">
                <div className="container">
                    <div className="row">
                        <div className="col-md-6 col-lg-6">
                            <div className="cta-area6 text-center text-md-start space position-relative">
                                <div className="title-area mb-40">
                                    <h2 className="sec-title text-white pe-xl-5 me-xl-4 mt-n3 text-anime-style-2">
                                        <span className="discount-text">
                                            Grab up to 35% off
                                        </span>
                                        Have any project to work with us
                                    </h2>
                                    <p
                                        className="text-white wow fadeInUp"
                                        data-wow-delay=".3s"
                                    >
                                        Limited time offer, don't miss the
                                        opportunity
                                    </p>
                                </div>
                                <div
                                    className="btn-group wow fadeInUp"
                                    data-wow-delay=".4s"
                                >
                                    <Link
                                        href="/contact"
                                        className="th-btn style5 th-radius th-icon"
                                    >
                                        Contact With Us{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="shape-mockup" data-bottom="0%" data-right="0">
                    <img src="/assets/img/normal/cta-img-6.jpg" alt="" />
                </div>
            </div>
        </div>
    );
}
