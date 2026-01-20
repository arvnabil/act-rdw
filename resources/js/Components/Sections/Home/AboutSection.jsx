import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function AboutSection({
    title,
    subtitle,
    description,
    features,
    images,
    button_text,
    button_url,
}) {
    // Defaults
    const t = title || "Bridging Technology and Education for a Better Future.";
    const st = subtitle || "Who We Are";
    const desc =
        description ||
        "ACTiV (PT Alfa Cipta Teknologi Virtual) is a dynamic company specializing in the sales and rental of software, hardware, and supporting accessories, with a primary focus on Information Communication Technology (ICT) and Education solutions. Backed by a team with over 6 years of experience and official partnerships with multinational ICT brands, we are dedicated to delivering the best comprehensive technology solutions to our clients.";
    const btnText = button_text || "Learn More";
    const btnUrl = button_url || "/about";

    const imgs =
        images && images.length > 0
            ? images.map((i) => i.url || i)
            : [
                  "/assets/img/normal/about_7_1.jpg",
                  "/assets/img/normal/about_7_2.jpg",
                  "/assets/img/normal/about_7_3.jpg",
              ];

    const feats =
        features && features.length > 0
            ? features
            : [
                  {
                      title: "ICT & Education Product Supply",
                      text: "Official provider of hardware and software tailored for education and ICT infrastructure.",
                      icon: "/assets/img/icon/shield.svg",
                  },
                  {
                      title: "Solution Services & Custom Development",
                      text: "Expert technical solutions and custom software development tailored to your specific needs.",
                      icon: "/assets/img/icon/shield.svg",
                  },
              ];

    return (
        <div
            className="about-area position-relative overflow-hidden space"
            id="about-sec"
        >
            <div className="container">
                <div className="row gy-40">
                    <div className="col-xl-6 col-lg-6">
                        <SectionTitle
                            subTitle={st}
                            title={t}
                            align="title-area mb-20"
                            mb="mb-20"
                        />
                        <p
                            className="sec-text mb-60 wow fadeInUp"
                            data-wow-delay=".2s"
                        >
                            {desc}
                        </p>
                        <div className="img-box8">
                            <div className="row gy-4">
                                <div className="col-xl-6 col-md-6 col-12">
                                    <div className="img1 reveal mb-0">
                                        <img src={imgs[0]} alt="About" />
                                    </div>
                                </div>
                                <div className="col-xl-6 col-md-6 col-12">
                                    <div className="img2 reveal mb-0">
                                        <img src={imgs[1]} alt="About" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-6 col-lg-6">
                        <div className="img-box8 ms-xl-5">
                            <div className="img3 reveal">
                                <img src={imgs[2]} alt="About" />
                            </div>
                            <div className="about-item-wrap">
                                {feats.map((feat, i) => (
                                    <div
                                        className="about-item wow fadeInUp"
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
                                className="mt-35 wow fadeInUp"
                                data-wow-delay=".5s"
                            >
                                <Link
                                    href={btnUrl}
                                    className="th-btn th-radius th-icon"
                                >
                                    {btnText}{" "}
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
