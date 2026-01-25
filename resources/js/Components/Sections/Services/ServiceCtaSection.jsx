import React from "react";
import { Link } from "@inertiajs/react";

export default function ServiceCtaSection({
    title,
    subtitle,
    description,
    btn_text,
    btn_url,
    bg_image,
}) {
    const finalTitle = title || "Have any project to work with us";
    const finalSubtitle = subtitle || "Grab up to 35% off";
    const finalDescription =
        description || "Limited time offer, don't miss the opportunity";
    const finalBtnText = btn_text || "Contact With Us";
    const finalBtnUrl = btn_url || "/contact";
    const finalBgImage = bg_image || "/assets/img/normal/cta-img-6.jpg";

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
                                            {finalSubtitle}
                                        </span>
                                        {finalTitle}
                                    </h2>
                                    <p
                                        className="text-white wow fadeInUp"
                                        data-wow-delay=".3s"
                                    >
                                        {finalDescription}
                                    </p>
                                </div>
                                <div
                                    className="btn-group wow fadeInUp"
                                    data-wow-delay=".4s"
                                >
                                    <Link
                                        href={finalBtnUrl}
                                        className="th-btn style5 th-radius th-icon"
                                    >
                                        {finalBtnText}{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="shape-mockup" data-bottom="0%" data-right="0">
                    <img src={finalBgImage} alt="" />
                </div>
            </div>
        </div>
    );
}
