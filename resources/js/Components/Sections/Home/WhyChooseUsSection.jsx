import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function WhyChooseUsSection({
    title,
    subtitle,
    description,
    features,
    images,
    video_url,
    builderMode,
}) {
    // Defaults
    const t = title || "Empowering Your Future with Proven Technology.";
    // Disable wow animations in builder mode to ensure visibility
    const wow = (cls) => (builderMode ? "" : cls);
    const st = subtitle || "Why Choose ACTiV";
    const desc =
        description ||
        "We are dedicated to providing comprehensive ICT and Education solutions that are not only advanced but also reliable. By combining technical expertise with global support, we ensure every technology investment you make delivers real, sustainable value.";
    const vidUrl = video_url || "https://www.youtube.com/watch?v=hIIQbkkKnno";

    const imgs =
        images && images.length > 0
            ? images.map((i) => i.url || i)
            : [
                  "/assets/img/normal/about_4_1.jpg",
                  "/assets/img/normal/about_4_2.jpg",
              ];

    const feats =
        features && features.length > 0
            ? features
            : [
                  {
                      title: "Expert Team",
                      text: "Backed by professionals with over 6 years of experience in ICT infrastructure",
                      icon: "/assets/img/icon/shield.svg",
                  },
                  {
                      title: "Certified Brand Partners",
                      text: "Official partnerships ensuring product authenticity and certified technical support.",
                      icon: "/assets/img/icon/shield.svg",
                  },
              ];

    return (
        <div
            className="bg-smoke position-relative overflow-hidden space"
            id="why-sec"
        >
            <div className="container">
                <div className="row">
                    <div className="col-xl-6">
                        <div className="ab5-wrapp mt-40">
                            <SectionTitle
                                align="title-area"
                                subTitle={st}
                                title={t}
                                mb=""
                            />
                            <p
                                className={`sec-text mb-30 ${wow("wow fadeInUp")}`}
                                data-wow-delay=".2s"
                            >
                                {desc}
                            </p>
                            <div className="about-item-wrap">
                                {feats.map((feat, i) => (
                                    <div
                                        className={`about-item ab5-item ${wow("wow fadeInUp")}`}
                                        data-wow-delay={`.${3 + i}s`}
                                        key={i}
                                    >
                                        <div className="about-item_img d-flex justify-content-center align-items-center">
                                            <img src={feat.icon} alt="" />
                                        </div>
                                        <div className="about-item_centent">
                                            <h5 className="box-title">
                                                {feat.title}
                                            </h5>
                                            <p className="about-item_text">
                                                {feat.text}
                                            </p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            <div
                                className={`mt-35 mb-2 ${wow("wow fadeInUp")}`}
                                data-wow-delay=".5s"
                            >
                                <Link
                                    href="/about"
                                    className="th-btn th-radius th-icon"
                                >
                                    Learn More{" "}
                                    <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6">
                        <div className="img-box4 ab5-imgbox space-bottom text-end">
                            <div className="img1 reveal">
                                <img src={imgs[0]} alt="About" />
                            </div>
                            <div className="img2">
                                <img src={imgs[1]} alt="About" />
                                <a
                                    href={vidUrl}
                                    className="play-btn popup-video"
                                >
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
