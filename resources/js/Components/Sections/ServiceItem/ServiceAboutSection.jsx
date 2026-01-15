import React from "react";

export default function ServiceAboutSection({ item, getImageUrl, waLink }) {
    return (
        <div
            className="about-area smoke-bg p-5 position-relative overflow-hidden space-top space-bottom"
            id="about-sec"
        >
            <div className="container">
                <div className="row">
                    <div className="col-xl-6">
                        <div className="img-box6">
                            <div className="img1 reveal">
                                <img
                                    src={getImageUrl(item.images[0])}
                                    alt={item.title}
                                />
                            </div>
                            {item.images[1] && (
                                <div className="img2 reveal">
                                    <img
                                        src={getImageUrl(item.images[1])}
                                        alt={item.title}
                                    />
                                </div>
                            )}
                        </div>
                    </div>
                    <div className="col-xl-6">
                        <div className="ps-xl-3 ms-xl-3 ps-xl-5 ms-xxl-5">
                            <div className="title-area about-7-titlebox mb-20">
                                <span className="sub-title style1 text-anime-style-2">
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
                                        className="th-btn black-btn th-radius th-icon"
                                    >
                                        Consultation Now{" "}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </a>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
