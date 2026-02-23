import React from "react";
import SectionTitle from "@/Components/Common/SectionTitle";

export default function PartnershipSection({
    title = "Kemitraan Digital & E-Commerce",
    subtitle = "Ekosistem Terintegrasi",
    description = "",
    partner_logo = "/assets/img/partners/accommerceid.webp",
    partner_name = "Accommerce.id",
    partner_url = "https://accommerce.id",
    partner_description = "Platform e-commerce resmi milik ACTiV...",
    features = [],
    steps = [],
    feature_title = "🚀 Keunggulan Platform",
}) {
    return (
        <section className="partnership-section space pb-100" id="partnership-details">
            <style>{`
                .partner-card {
                    background: #fff;
                    border-radius: 24px;
                    padding: 40px;
                    border: 1px solid #f0f0f0;
                    box-shadow: 0 15px 45px rgba(0,0,0,0.03);
                    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                    height: 100%;
                }
                .partner-card:hover {
                    box-shadow: 0 25px 55px rgba(0,181,226,0.1);
                    transform: translateY(-8px);
                    border-color: var(--theme-color, #00B5E2);
                }
                .partner-logo-box {
                    width: 220px;
                    height: 80px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #fdfdfd;
                    border-radius: 16px;
                    margin-bottom: 28px;
                    padding: 15px;
                    border: 1px solid #f5f5f5;
                }
                .partner-logo-box img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                }
                .badge-official {
                    background: #e6f7ff;
                    color: #00B5E2;
                    padding: 6px 18px;
                    border-radius: 100px;
                    font-size: 11px;
                    font-weight: 800;
                    text-transform: uppercase;
                    display: inline-block;
                    margin-bottom: 16px;
                    letter-spacing: 0.5px;
                }
                
                /* Steps Styling - Aligned with About patterns */
                .steps-list {
                    display: flex;
                    flex-direction: column;
                    gap: 30px;
                }
                .step-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 20px;
                    transition: all 0.3s ease;
                }
                .step-icon-box {
                    width: 60px;
                    height: 60px;
                    background: #F2F5FA;
                    border-radius: 16px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-shrink: 0;
                    color: var(--theme-color, #00B5E2);
                    font-size: 22px;
                    font-weight: 800;
                    transition: 0.3s;
                }
                .step-item:hover .step-icon-box {
                    background: var(--theme-color, #00B5E2);
                    color: #fff;
                }
                .step-content h5 {
                    font-size: 18px;
                    font-weight: 700;
                    color: var(--title-color);
                    margin: 0 0 6px 0;
                }
                .step-content p {
                    font-size: 15px;
                    line-height: 1.6;
                    margin: 0;
                }

                .partner-card .th-btn {
                    border-radius: 100px !important;
                    transition: all 0.3s ease !important;
                }
                .btn-accommerce-custom {
                    background-color: transparent !important;
                    color: var(--theme-color, #00B5E2) !important;
                    border: 1px solid var(--theme-color, #00B5E2) !important;
                }
                .btn-accommerce-custom:hover {
                    background-color: var(--theme-color, #00B5E2) !important;
                    color: #fff !important;
                }

                @media (max-width: 991px) {
                    .partnership-section .row { flex-direction: column-reverse; }
                    .partner-card { margin-top: 50px; }
                }
                @media (max-width: 575px) {
                    .partner-card { padding: 30px 20px; }
                    .partner-logo-box { width: 100%; }
                }
            `}</style>

            <div className="container">
                <div className="row">
                    {/* Left side: Accommerce Card */}
                    <div className="col-lg-6 mb-40 mb-lg-0 wow fadeInLeft">
                        <div className="partner-card">
                            <div className="badge-official">Official Online Sales Platform</div>
                            <div className="partner-logo-box">
                                <img src={partner_logo} alt={partner_name} />
                            </div>
                            <h3 className="h4 mb-3">{partner_name}</h3>
                            <p className="text-muted mb-4" style={{fontSize: '15px', lineHeight: '1.7'}} dangerouslySetInnerHTML={{ __html: partner_description }}></p>
                            
                            <h6 className="mt-4 mb-3">{feature_title}</h6>
                            <div className="feature-grid row g-3">
                                {features.map((f, idx) => {
                                    const isObject = typeof f === 'object' && f !== null;
                                    const fTitle = isObject ? f.title : f;
                                    const fText = isObject ? f.text : null;
                                    const fIcon = isObject ? f.icon : 'fa-solid fa-circle-check';

                                    return (
                                        <div key={idx} className="col-sm-12 col-md-6">
                                            <div className="d-flex align-items-start gap-2">
                                                <div className="text-primary mt-1" style={{fontSize: '14px'}}>
                                                    {fIcon.includes('/') ? <img src={fIcon} alt="" style={{width: '20px'}} /> : <i className={fIcon}></i>}
                                                </div>
                                                <div>
                                                    <span className="small text-dark font-weight-bold d-block">{fTitle}</span>
                                                    {fText && <p className="small text-muted mb-0" style={{lineHeight: '1.4'}}>{fText}</p>}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>

                            <a href={partner_url} target="_blank" rel="noopener noreferrer" className="th-btn btn-accommerce-custom w-100 mt-40">
                                Kunjungi {partner_name} <i className="fa-solid fa-arrow-up-right-from-square ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    {/* Right side: Section Title & Steps */}
                    <div className="col-lg-6 ps-lg-5 wow fadeInRight">
                        <SectionTitle 
                            subTitle={subtitle}
                            title={title}
                            align="title-area mb-35"
                            mb="mb-35"
                        />
                        <p className="opacity-75 mb-40" dangerouslySetInnerHTML={{ __html: description }}></p>
                        
                        <div className="steps-wrap">
                            <h5 className="mb-4 d-flex align-items-center gap-2">
                                <i className="fa-solid fa-code-merge text-primary"></i> Alur Kemitraan
                            </h5>
                            <div className="steps-list">
                                {steps.map((step, idx) => (
                                    <div key={idx} className="step-item">
                                        <div className="step-icon-box">
                                            {idx + 1}
                                        </div>
                                        <div className="step-content">
                                            <h5>{step.title}</h5>
                                            <p className="text-muted">{step.description}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
