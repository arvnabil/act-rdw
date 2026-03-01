import React from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, FreeMode } from "swiper/modules";
import "swiper/css";
import "swiper/css/free-mode";

export default function PartnerVerificationSection({
    brand,
    getImageUrl,
    setLightboxImage,
}) {
    const config = brand.landing_config?.partner || {};
    const awards = brand.landing_config?.awards || [];

    if (!config.enabled && awards.length === 0) return null;

    const title = config.modal_title || `Authorized ${brand.name} Partner`;
    const subtitle = config.modal_subtitle || `ACTiV is an official ${brand.name} Partner in Indonesia`;
    const description = config.modal_description || `ACTiV is an official ${brand.name} Partner in Indonesia, dedicated to providing premium solutions and expert consultation for your business needs.`;

    const displayAwards = awards.map((a, i) => ({
        id: i,
        title: a.title,
        src: a.image ? (a.image.startsWith('http') || a.image.startsWith('/assets') ? a.image : `/storage/${a.image}`) : null
    })).filter(a => a.src);

    return (
        <section className="partner-verification-section space-top space-bottom bg-light overflow-hidden">
            <div className="container">
                <div className="row align-items-center">
                    {/* Left Column: Content */}
                    <div className="col-lg-6">
                        <div className="title-area text-start mb-40">
                            <span className="sub-title style1 text-anime-style-2">
                                <span className="squre-shape left me-3"></span>
                                Authorized Partner
                            </span>
                            <h2 className="sec-title text-theme mb-3">
                                {title}
                            </h2>
                            <div className="partner-desc text-secondary" style={{ fontSize: '1.05rem', lineHeight: '1.7' }}>
                                <p className="fw-bold text-dark mb-3">{subtitle}</p>
                                <div dangerouslySetInnerHTML={{ __html: description }} />
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Awards Scroller */}
                    <div className="col-lg-6">
                        <div className="awards-container ps-lg-4">
                            <h6 className="awards-label text-muted text-uppercase fw-bold mb-4" style={{ fontSize: '0.75rem', letterSpacing: '1px' }}>
                                Official Accreditations
                            </h6>
                            <Swiper
                                modules={[Autoplay, FreeMode]}
                                spaceBetween={20}
                                slidesPerView={1.5}
                                freeMode={true}
                                autoplay={{
                                    delay: 3000,
                                    disableOnInteraction: false,
                                }}
                                breakpoints={{
                                    480: { slidesPerView: 2 },
                                    768: { slidesPerView: 3 },
                                    992: { slidesPerView: 3 },
                                }}
                                className="awards-swiper"
                            >
                                {displayAwards.map((award) => (
                                    <SwiperSlide key={award.id}>
                                        <div 
                                            className="award-item cursor-pointer shadow-sm rounded-4 bg-white p-3 d-flex align-items-center justify-content-center"
                                            style={{ height: '140px', transition: 'all 0.3s ease', border: '1px solid rgba(0,0,0,0.05)' }}
                                            onClick={() => setLightboxImage(award.src)}
                                        >
                                            <img
                                                src={award.src}
                                                alt={award.title}
                                                className="img-fluid award-img"
                                                style={{ maxHeight: '100%', maxWidth: '100%', objectFit: 'contain' }}
                                                onError={(e) => {
                                                    e.target.src = "https://placehold.co/200x200?text=Award";
                                                }}
                                            />
                                        </div>
                                    </SwiperSlide>
                                ))}
                                {displayAwards.length === 0 && (
                                     <div className="text-muted small">No accreditations found.</div>
                                )}
                            </Swiper>
                        </div>
                    </div>
                </div>
            </div>

            <style jsx="true">{`
                .partner-verification-section {
                    background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
                    border-top: 1px solid #f3f4f6;
                    border-bottom: 1px solid #f3f4f6;
                }
                .award-item:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
                    border-color: var(--theme-color, #20B2AA);
                }
                .award-img {
                    transition: transform 0.4s ease;
                }
                .award-item:hover .award-img {
                    transform: scale(1.05);
                }
                .awards-swiper {
                    padding: 10px 5px 30px 5px;
                }
                @media (max-width: 991px) {
                    .awards-container {
                        margin-top: 2rem;
                    }
                }
            `}</style>
        </section>
    );
}
