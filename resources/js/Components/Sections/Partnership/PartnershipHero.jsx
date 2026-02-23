import React from "react";
import { Link, usePage } from "@inertiajs/react";
import { getWhatsAppLink } from "@/Utils/whatsapp";

export default function PartnershipHero({
    title = "Bangun Kemitraan Strategis Bersama Kami",
    subtitle = "Kolaborasi Tanpa Batas",
    description = "Bergabunglah dengan ekosistem teknologi ACTiV untuk solusi ICT yang lebih luas.",
    button_text = "Ajukan Kemitraan",
    button_url = "/about-us#contact",
    use_whatsapp = true,
}) {
    const { settings } = usePage().props;

    const waLink = getWhatsAppLink(settings?.whatsapp_number || "6285162994602", {
        message: `Halo ACTiV, saya tertarik untuk mengajukan kemitraan strategis. Mohon informasi lebih lanjut mengenai prosedurnya.`,
        cta_position: 'partnership_hero',
        cta_label: 'Ajukan Kemitraan',
        entity_type: 'partnership',
        entity_slug: 'kemitraan'
    });

    return (
        <div className="partnership-hero position-relative overflow-hidden bg-white text-dark">
            <style>{`
                .partnership-hero {
                    background: linear-gradient(135deg, #f8fcfd 0%, #ffffff 100%);
                    min-height: 450px;
                    display: flex;
                    align-items: center;
                    padding: 80px 0;
                }
                .hero-pattern {
                    position: absolute;
                    top: 0;
                    right: 0;
                    width: 60%;
                    height: 100%;
                    background-image: radial-gradient(circle at 2px 2px, var(--theme-color, #00B5E2) 1px, transparent 0);
                    background-size: 40px 40px;
                    opacity: 0.07;
                    mask-image: linear-gradient(to left, black, transparent);
                }
                .hero-shape {
                    position: absolute;
                    bottom: -50px;
                    left: -50px;
                    width: 300px;
                    height: 300px;
                    background: var(--theme-color, #00B5E2);
                    filter: blur(120px);
                    opacity: 0.05;
                    border-radius: 50%;
                }
                .hero-title {
                    font-size: 56px;
                    font-weight: 800;
                    line-height: 1.1;
                    margin-bottom: 24px;
                    color: var(--title-color, #00B5E2);
                }
                .hero-desc {
                    font-size: 18px;
                    color: var(--body-color, #666);
                    max-width: 650px;
                    margin-bottom: 40px;
                    line-height: 1.6;
                }
                
                .btn-profile-custom {
                    background-color: var(--theme-color, #4ac15e) !important;
                    color: #fff !important;
                    border: none !important;
                    border-radius: 50px !important;
                }
                .btn-profile-custom:hover {
                    background-color: var(--title-color, #00B5E2) !important;
                    color: #fff !important;
                }

                @media (max-width: 991px) {
                    .hero-title { font-size: 42px; }
                    .partnership-hero { min-height: 400px; padding: 60px 0; }
                }
                @media (max-width: 767px) {
                    .hero-title { font-size: 34px; }
                    .hero-desc { font-size: 16px; }
                    .partnership-hero { text-align: center; }
                    .hero-desc { margin-left: auto; margin-right: auto; }
                    .btn-group { justify-content: center; }
                }
            `}</style>
            <div className="hero-pattern"></div>
            <div className="hero-shape"></div>
            <div className="container position-relative z-index-2">
                <div className="row">
                    <div className="col-lg-10 col-xl-8">
                        <div className="title-area mb-40 wow fadeInUp" data-wow-delay="0.2s">
                            {subtitle && (
                                <span className="sub-title style1 text-anime-style-2 mb-20">
                                    <span className="squre-shape left me-3"></span>
                                    {subtitle}
                                    <span className="squre-shape d-lg-none right ms-3"></span>
                                </span>
                            )}
                            <h1 className="hero-title">{title}</h1>
                            {description && <p className="hero-desc">{description}</p>}
                        </div>
                        
                        <div className="btn-group wow fadeInUp" data-wow-delay="0.4s">
                            <a 
                                href={use_whatsapp ? waLink : button_url} 
                                className="th-btn th-radius"
                                target={use_whatsapp ? "_blank" : "_self"}
                                rel={use_whatsapp ? "noopener noreferrer" : ""}
                            >
                                {button_text} <i className={use_whatsapp ? "fa-brands fa-whatsapp ms-2" : "fa-light fa-arrow-right-long ms-2"}></i>
                            </a>
                            <Link 
                                href="/about-us" 
                                className="th-btn th-radius btn-profile-custom"
                            >
                                Pelajari Profil Kami <i className="fa-light fa-arrow-right-long ms-2"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
