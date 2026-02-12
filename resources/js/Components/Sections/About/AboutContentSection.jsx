import React from "react";
import { Link } from "@inertiajs/react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function AboutContentSection({
    subtitle = "About ACTiV",
    title = "Bridging Technology and Education for a Better Future.",
    description = "ACTiV (PT Alfa Cipta Teknologi Virtual) is a premier provider of ICT and Educational solutions, specializing in hardware, software, and accessory rentals and sales. With over six years of industry experience and strategic partnerships with multinational brands, we deliver comprehensive, tailored technology solutions to empower our clients' success.",
    images = [],
    video_url = "https://www.youtube.com/watch?v=hIIQbkkKnno",
    features = [
        {
            title: "Expert Team",
            description: "Our team consists of seasoned professionals with deep expertise in ICT and education sectors.",
            icon: "/assets/img/icon/guide.svg",
        },
        {
            title: "Certified Partnerships",
            description: "We hold official partnerships with leading global brands, ensuring authentic products and certified technical support.",
            icon: "/assets/img/icon/policy.svg",
        },
        {
            title: "Dedicated Support",
            description: "Our responsive support system operates across multiple channels to provide timely resolutions and ensure business continuity.",
            icon: "/assets/img/icon/support.svg",
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

    // Helper to render icon (Image or Font Class)
    const renderIcon = (icon) => {
        if (!icon) return <i className="fa-solid fa-check"></i>;

        if (icon.includes("/") || icon.includes(".")) {
            return <img src={icon} alt="icon" />;
        }

        return <i className={icon}></i>;
    };

    return (
        <section className="about-area position-relative overflow-hidden space" id="about-sec">
            <style>{`
                .about-wrapper {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 50px;
                    align-items: center;
                }
                @media (min-width: 992px) {
                    .about-wrapper {
                        grid-template-columns: 0.9fr 1.1fr;
                        gap: 70px;
                    }
                }
                
                /* Image Content */
                .about-images-box {
                    position: relative;
                    padding-right: 20px;
                    padding-bottom: 20px;
                }
                .about-img-main {
                    border-radius: 20px;
                    overflow: hidden;
                    position: relative;
                    z-index: 2;
                }
                .about-img-main img {
                    width: 100%;
                    height: auto;
                    display: block;
                    border-radius: 20px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
                }
                
                .about-img-sub {
                    position: absolute;
                    bottom: 0;
                    right: 0;
                    width: 50%;
                    border: 8px solid #ffffff;
                    border-radius: 20px;
                    z-index: 3;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
                }
                .about-img-sub img {
                    width: 100%;
                    height: auto;
                    display: block;
                    border-radius: 12px;
                }
                
                .about-shape-1 {
                    position: absolute;
                    top: -30px;
                    left: -30px;
                    width: 100px;
                    height: 100px;
                    background-image: radial-gradient(var(--theme-color) 2px, transparent 2px);
                    background-size: 10px 10px;
                    opacity: 0.3;
                    z-index: 1;
                }

                /* Typography Override */
                .about-content-box h2 {
                    font-family: var(--title-font, "Plus Jakarta Sans", sans-serif);
                }
                .about-content-box p, 
                .about-content-box span, 
                .about-content-box li {
                    font-family: var(--body-font, "Inter", sans-serif);
                }

                .custom-sub-title {
                    font-size: 14px;
                    font-weight: 600;
                    color: var(--theme-color);
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    margin-bottom: 15px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                .custom-sub-title::before {
                    content: '';
                    display: block;
                    width: 20px;
                    height: 2px;
                    background: var(--theme-color);
                }

                .custom-title {
                    font-size: 40px;
                    line-height: 1.25;
                    margin-bottom: 25px;
                    font-weight: 700;
                    color: var(--title-color);
                }
                @media (max-width: 768px) {
                    .custom-title {
                        font-size: 32px;
                    }
                }

                .custom-desc {
                    color: var(--body-color);
                    font-size: 16px;
                    line-height: 1.7;
                    margin-bottom: 35px;
                }

                /* Minimal Feature List */
                .clean-feature-list {
                    display: flex;
                    flex-direction: column;
                    gap: 24px;
                }
                .clean-feature-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 20px;
                    transition: all 0.3s ease;
                }
                .clean-feature-item:hover {
                    transform: translateX(5px);
                }
                
                .clean-feature-icon {
                    width: 65px;
                    height: 65px;
                    background: #EFF4F5; /* Matches provided image style */
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                    transition: all 0.3s ease;
                }
                
                .clean-feature-item:hover .clean-feature-icon {
                    background: var(--theme-color);
                }

                .clean-feature-icon img, 
                .clean-feature-icon i {
                    color: var(--theme-color);
                    font-size: 26px;
                    transition: all 0.3s ease;
                }
                .clean-feature-icon img {
                    width: 32px;
                    height: auto;
                    filter: brightness(0) saturate(100%) invert(26%) sepia(16%) saturate(1968%) hue-rotate(152deg) brightness(96%) contrast(91%);
                }
                
                .clean-feature-item:hover .clean-feature-icon i {
                    color: #fff;
                }
                .clean-feature-item:hover .clean-feature-icon img {
                    filter: brightness(0) invert(1);
                }

                .clean-feature-content h5 {
                    font-family: var(--title-font);
                    font-size: 18px;
                    font-weight: 700;
                    color: var(--title-color);
                    margin: 0 0 8px 0;
                }
                .clean-feature-content p {
                    font-size: 15px;
                    color: var(--body-color);
                    margin: 0;
                    line-height: 1.6;
                }

                .play-btn-overlay {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    width: 60px;
                    height: 60px;
                    background: #fff;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--theme-color);
                    font-size: 20px;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                    transition: 0.3s;
                    cursor: pointer;
                }
                .play-btn-overlay:hover {
                    background: var(--theme-color);
                    color: #fff;
                    transform: translate(-50%, -50%) scale(1.1);
                }
            `}</style>
            <div className="container">
                <div className="about-wrapper">
                    {/* Left: Images */}
                    <div className="about-images-box wow fadeInLeft">
                        <div className="about-shape-1"></div>
                        <div className="about-img-main">
                            <img src={getImage(0, "/assets/img/normal/about_4_1.jpg")} alt="About Main" />
                        </div>
                        <div className="about-img-sub">
                            <img src={getImage(1, "/assets/img/normal/about_4_2.jpg")} alt="About Secondary" />
                            {video_url && (
                                <a href={video_url} className="play-btn-overlay popup-video">
                                    <i className="fa-solid fa-play"></i>
                                </a>
                            )}
                        </div>
                    </div>

                    {/* Right: Content */}
                    <div className="about-content-box wow fadeInRight">
                        <div className="custom-sub-title">{subtitle}</div>
                        <h2 className="custom-title">{title}</h2>
                        <p className="custom-desc">{description}</p>

                        <div className="clean-feature-list">
                            {features.map((feature, index) => (
                                <div key={index} className="clean-feature-item">
                                    <div className="clean-feature-icon">
                                        {renderIcon(feature.icon)}
                                    </div>
                                    <div className="clean-feature-content">
                                        <h5>{feature.title}</h5>
                                        <p>{feature.description || feature.text}</p>
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div className="mt-40">
                            <a
                                href="https://wa.me/6285162994602"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="th-btn style3 th-radius th-icon"
                            >
                                Kontak Kami <i className="fa-brands fa-whatsapp ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
