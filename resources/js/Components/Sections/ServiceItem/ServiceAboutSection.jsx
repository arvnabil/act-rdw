import React from "react";

export default function ServiceAboutSection({ item, getImageUrl, waLink }) {
    return (
        <div
            className="about-area smoke-bg p-xl-5 p-3 position-relative overflow-hidden space-top space-bottom"
            id="about-sec"
        >
            <div className="container">
                <div className="row">
                    <div className="col-xl-6">
                        <div className="about-img-single wow fadeInUp">
                            <img
                                src={getImageUrl(item.images[0])}
                                alt={item.title}
                                className="w-100 h-auto rounded-20"
                                style={{ borderRadius: "24px" }}
                            />
                        </div>
                    </div>
                    <div className="col-xl-6">
                        <div className="ps-xl-3 ms-xl-3 ps-xl-5 ms-xxl-5">
                            <div className="title-area about-7-titlebox mb-20 mt-30 mt-xl-0">
                                <span className="sub-title style1 text- anime-style-2">
                                    {item.title}
                                </span>
                                <h2 className="sec-title mb-20 text-anime-style-3">
                                    {item.subtitle || item.title}
                                </h2>
                                <p className="sec-text mb-30 wow fadeInUp">
                                    {item.description}
                                </p>
                            </div>
                            <div className="about-item-wrap">
                                {item.features.map((feature, index) => (
                                    <div
                                        key={index}
                                        className="about-item wow fadeInUp"
                                    >
                                        <div className="about-item_img">
                                            <img
                                                src={getImageUrl(feature.icon)}
                                                alt="icon"
                                            />
                                        </div>
                                        <div className="about-item_centent">
                                            <h5 className="box-title">
                                                {feature.title}
                                            </h5>
                                            <p className="about-item_text">
                                                {feature.text}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            {item.wa_message && (
                                <div className="mt-35 wow fadeInUp">
                                    <a
                                        href={waLink}
                                        target="_blank"
                                        className="th-btn black-btn th-radius th-icon w-100 w-sm-auto"
                                    >
                                        Konsultasi Sekarang{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
            <style jsx="true">{`
                @media (max-width: 767px) {
                    .about-area {
                        padding-top: 40px !important;
                        padding-bottom: 40px !important;
                        padding-left: 15px !important;
                        padding-right: 15px !important;
                    }
                    .img-box6 {
                        margin-bottom: 20px !important;
                    }
                    .sec-title {
                        font-size: 26px !important;
                    }
                    .about-img-single img {
                        border-radius: 16px !important;
                    }
                }
            `}</style>
        </div>
    );
}
