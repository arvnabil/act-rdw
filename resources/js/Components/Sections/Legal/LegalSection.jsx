import React, { useState, useEffect } from "react";
import { Link, usePage } from "@inertiajs/react";
import { getWhatsAppLink } from "@/Utils/whatsapp";

export default function LegalSection({
    title = "Legal Information",
    subtitle = "Information",
    last_updated = null,
    sections = [],
    contact_email = "sales@activ.co.id",
}) {
    const { settings } = usePage().props;
    const [activeSection, setActiveSection] = useState("");

    useEffect(() => {
        const handleScroll = () => {
            const scrollPosition = window.scrollY + 150;
            const items = sections.map(s => document.getElementById(s.slug));
            
            for (let i = items.length - 1; i >= 0; i--) {
                const item = items[i];
                if (item && item.offsetTop <= scrollPosition) {
                    setActiveSection(sections[i].slug);
                    break;
                }
            }
        };

        window.addEventListener("scroll", handleScroll);
        return () => window.removeEventListener("scroll", handleScroll);
    }, [sections]);

    const scrollTo = (id) => {
        const element = document.getElementById(id);
        if (element) {
            window.scrollTo({
                top: element.offsetTop - 120,
                behavior: "smooth"
            });
        }
    };

    return (
        <section className="legal-section space py-80">
            <style>{`
                .legal-container {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 40px;
                }
                @media (min-width: 992px) {
                    .legal-container {
                        grid-template-columns: 300px 1fr;
                    }
                }
                
                .legal-sidebar {
                    position: sticky;
                    top: 100px;
                    height: fit-content;
                }
                
                .toc-list {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                    border-left: 2px solid #eee;
                }
                
                .toc-item {
                    padding: 8px 20px;
                    cursor: pointer;
                    font-size: 15px;
                    color: #666;
                    transition: all 0.3s ease;
                    border-left: 2px solid transparent;
                    margin-left: -2px;
                }
                
                .toc-item.active {
                    color: var(--theme-color, #00B5E2);
                    border-left-color: var(--theme-color, #00B5E2);
                    font-weight: 600;
                    background: #f8fcfd;
                }
                
                .legal-content h3 {
                    font-size: 24px;
                    margin-bottom: 20px;
                    font-weight: 700;
                }
                
                .legal-card {
                    background: #fff;
                    border-radius: 16px;
                    padding: 30px;
                    margin-bottom: 30px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                    border: 1px solid #f0f0f0;
                }
                
                .callout-box {
                    background: #f0faff;
                    border-left: 4px solid var(--theme-color, #00B5E2);
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                }
                
                .accordion-item {
                    border: none;
                    background: #f9f9f9;
                    margin-bottom: 15px;
                    border-radius: 12px;
                    overflow: hidden;
                }
                
                .accordion-header {
                    padding: 15px 20px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    cursor: pointer;
                    font-weight: 600;
                }
                
                .legal-meta {
                    color: #999;
                    font-size: 14px;
                    margin-bottom: 40px;
                }
                
                .footer-cta .th-btn {
                    border-radius: 100px !important;
                    min-width: 180px;
                }
                
                .btn-email-legal {
                    background-color: transparent !important;
                    border: 1px solid #fff !important;
                    color: #fff !important;
                }
                
                .btn-email-legal:hover {
                    background-color: var(--theme-color, #00B5E2) !important;
                    border-color: var(--theme-color, #00B5E2) !important;
                    color: #fff !important;
                }

                .btn-whatsapp-legal {
                    background-color: #46c05d !important;
                    border: none !important;
                    color: #fff !important;
                }

                .btn-whatsapp-legal:hover {
                    background-color: #3ba64f !important;
                    color: #fff !important;
                }

                @media (max-width: 767px) {
                    .legal-section { padding-top: 60px; padding-bottom: 60px; }
                    .legal-card { padding: 20px; }
                    .legal-content h3 { font-size: 20px; }
                    .legal-meta { margin-bottom: 25px; text-align: center; }
                    .footer-cta { padding: 40px 20px !important; }
                    .footer-cta .d-flex { flex-direction: column; width: 100%; }
                    .footer-cta .th-btn { width: 100%; }
                }
            `}</style>
            
            <div className="container">
                <div className="legal-meta">
                    {last_updated && (
                        <span>Terakhir diperbarui pada: {last_updated}</span>
                    )}
                </div>
                
                <div className="legal-container">
                    {/* Sidebar / TOC */}
                    <div className="legal-sidebar d-none d-lg-block">
                        <h5 className="mb-4">Daftar Isi</h5>
                        <ul className="toc-list">
                            {sections.map((s, idx) => (
                                <li 
                                    key={idx} 
                                    className={`toc-item ${activeSection === s.slug ? 'active' : ''}`}
                                    onClick={() => scrollTo(s.slug)}
                                >
                                    {s.title}
                                </li>
                            ))}
                        </ul>
                        
                        <div className="mt-5 p-4 smoke-bg rounded-3">
                            <h6>Butuh bantuan hukum?</h6>
                            <p className="small text-muted">Hubungi tim kepatuhan kami:</p>
                            <a href={`mailto:${contact_email}`} className="text-primary font-weight-bold">{contact_email}</a>
                        </div>
                    </div>
                    
                    {/* Content Area */}
                    <div className="legal-content">
                        {sections.map((s, idx) => (
                            <div key={idx} id={s.slug} className="legal-card scroll-mt-20">
                                <div className="d-flex align-items-baseline gap-3 mb-3">
                                    {s.icon && (
                                        <div className="text-primary" style={{fontSize: '20px'}}>
                                            <i className={s.icon}></i>
                                        </div>
                                    )}
                                    <h3 className="mb-0">{s.title}</h3>
                                </div>
                                <div className="text-muted" dangerouslySetInnerHTML={{ __html: s.content }}></div>
                                
                                {s.callout && (
                                    <div className="callout-box">
                                        <strong>Catatan Penting:</strong>
                                        <p className="mb-0 mt-1 small">{s.callout}</p>
                                    </div>
                                )}
                                
                                {s.items && s.items.length > 0 && (
                                    <div className="accordion mt-4">
                                        {s.items.map((item, i) => (
                                            <div key={i} className="accordion-item shadow-none border">
                                                <div className="accordion-header bg-white">
                                                    <span>{item.title}</span>
                                                    <i className="fa-regular fa-chevron-down small"></i>
                                                </div>
                                                <div className="p-4 pt-0 text-muted small">
                                                    {item.content}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        ))}
                        
                        <div className="footer-cta mt-5 text-center p-5 rounded-4 bg-dark text-white shadow-lg">
                            <h4 className="text-white">Ada Pertanyaan Mengenai Legal?</h4>
                            <p className="mb-4 opacity-75">Tim kami siap membantu menjelaskan syarat dan kebijakan kami.</p>
                            <div className="d-flex justify-content-center gap-3">
                                <a 
                                    href={getWhatsAppLink(settings?.whatsapp_number, {
                                        cta_position: 'legal_footer',
                                        cta_label: 'Hubungi Kami Legal'
                                    }) || "#"} 
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="th-btn btn-whatsapp-legal th-radius"
                                >
                                    Hubungi Kami <i className="fa-brands fa-whatsapp ms-2"></i>
                                </a>
                                <a href={`mailto:${contact_email}`} className="th-btn btn-email-legal th-radius">Email Legal</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
