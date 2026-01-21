import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function AboutContentSection({
    subtitle = "About Us",
    title = "Bridging Technology and Education for a Better Future.",
    description = "ACTiV (PT Alfa Cipta Teknologi Virtual) is a dynamic company specializing in the sales and rental of software, hardware, and supporting accessories, with a primary focus on Information Communication Technology (ICT) and Education solutions. Backed by a team with over 6 years of experience and official partnerships with multinational ICT brands, we are dedicated to delivering the best comprehensive technology solutions to our clients.",
    images = [],
    video_url = "https://www.youtube.com/watch?v=hIIQbkkKnno",
    features = [
        {
            title: "Expert Team",
            description:
                "There are many variations of passages of available but the majority.",
            icon: "/assets/img/icon/guide.svg",
        },
        {
            title: "Certified Brand Partners",
            description:
                "Official partnerships ensuring product authenticity and certified technical support.",
            icon: "/assets/img/icon/shield.svg",
        },
        {
            title: "24/7 Customer Support",
            description:
                "A support system operating non-stop through various channels (phone, chat, email, etc.) to provide instant or timely responses and resolutions.",
            icon: "/assets/img/icon/headphone.svg",
        },
    ],
}) {
    // Helper to get image URL safely
    const getImage = (index, fallback) => {
        if (images && images[index]) {
            const img = images[index];
            if (typeof img === "string") return img;
            return img?.image || fallback;
        }
        return fallback;
    };

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
                                    src={getImage(
                                        0,
                                        "/assets/img/normal/about_4_1.jpg",
                                    )}
                                    alt="About 1"
                                />
                            </div>
                            <div className="img2 wow fadeInUp">
                                <img
                                    src={getImage(
                                        1,
                                        "/assets/img/normal/about_4_2.jpg",
                                    )}
                                    alt="About 2"
                                />
                                <a
                                    href={video_url}
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
                                subTitle={subtitle}
                                title={title}
                                align="title-area about4-titlebox mb-20"
                                mb="mb-20"
                            />
                            <p
                                className="sec-text mb-30 wow fadeInUp"
                                data-wow-delay=".2s"
                            >
                                {description}
                            </p>
                            <div className="about-item-wrap">
                                {features.map((feature, index) => (
                                    <div
                                        key={index}
                                        className="about-item wow fadeInUp"
                                        data-wow-delay={`.${index + 2}s`}
                                    >
                                        <div className="about-item_img flex items-center justify-center">
                                            <img
                                                src={feature.icon}
                                                alt={feature.title}
                                            />
                                        </div>
                                        <div className="about-item_centent">
                                            <h5 className="box-title">
                                                {feature.title}
                                            </h5>
                                            <p className="about-item_text">
                                                {feature.description ||
                                                    feature.text}
                                            </p>
                                        </div>
                                    </div>
                                ))}
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
