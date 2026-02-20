import React, { useState } from "react";
import Modal from "@/Components/Common/Modal";

const ElitePartnerModal = ({ show, onClose, config = {}, awards = [], brandName = "Logitech" }) => {
    const [selectedImage, setSelectedImage] = useState(null);

    const title = config.modal_title || `Authorized ${brandName} Partner`;
    const subtitle = config.modal_subtitle || `ACTiV is an official ${brandName} Elite Partner in Indonesia`;
    const description = config.modal_description || `ACTiV is an official ${brandName} Elite Partner in Indonesia and recipient of the ${brandName} Partner Connect Program 2024 awards, including:`;

    const defaultAwards = [
        { id: 1, title: "Elite Partner Award 2024", src: "/assets/img/awards/elite-partner.png" },
        { id: 2, title: "Partner of the Year 2024", src: "/assets/img/awards/partner-year.png" },
        { id: 3, title: "Marketing Excellence 2024", src: "/assets/img/awards/marketing-excel.png" }
    ];

    const displayAwards = awards && awards.length > 0 
        ? awards.map((a, i) => ({ id: i, title: a.title, src: a.image ? `/storage/${a.image}` : null }))
        : defaultAwards;

    const defaultGuarantees = [
        "Original products with official warranty",
        "Professional technical support",
        "Competitive corporate pricing",
        "Certified deployment & consultation"
    ];

    const displayGuarantees = config.guarantees && config.guarantees.length > 0
        ? config.guarantees
        : defaultGuarantees;

    return (
        <>
            <Modal show={show} onClose={onClose} maxWidth="700px">
                <div className="elite-partner-modal-content">
                    <div className="text-center mb-4">
                        <div className="d-inline-flex align-items-center justify-content-center bg-success-subtle text-success rounded-circle mb-3" style={{ width: '64px', height: '64px' }}>
                            <i className="fa-solid fa-certificate fa-2xl"></i>
                        </div>
                        <h2 className="fw-bold text-dark mb-2">{title}</h2>
                        <p className="text-muted mb-0">{subtitle}</p>
                    </div>

                    <div className="mb-4 text-secondary" style={{ fontSize: '0.95rem', lineHeight: '1.6' }}>
                        {config.modal_description ? (
                            <div dangerouslySetInnerHTML={{ __html: description }} />
                        ) : (
                            <>
                                <p>{description}</p>
                                <ul className="list-unstyled">
                                    <li className="d-flex align-items-center gap-2 mb-1">
                                        <span className="text-success">•</span> <strong>Elite Partner Level</strong>
                                    </li>
                                    <li className="d-flex align-items-center gap-2 mb-1">
                                        <span className="text-success">•</span> <strong>Partner of the Year</strong>
                                    </li>
                                    <li className="d-flex align-items-center gap-2 mb-1">
                                        <span className="text-success">•</span> <strong>Marketing Excellence Award</strong>
                                    </li>
                                </ul>
                            </>
                        )}
                    </div>

                    <div className="bg-light p-4 rounded-4 mb-4">
                        <h5 className="fw-bold text-dark mb-3">As an official partner, ACTiV guarantees:</h5>
                        <div className="row g-3">
                            {displayGuarantees.map((item, i) => (
                                <div key={i} className="col-6 d-flex align-items-center gap-2">
                                    <i className="fa-solid fa-check text-success"></i>
                                    <span style={{ fontSize: '0.8125rem', lineHeight: '1.2' }}>{item}</span>
                                </div>
                            ))}
                        </div>
                    </div>

                    <div className="awards-section">
                        <h6 className="text-muted text-uppercase fw-bold mb-3" style={{ fontSize: '0.75rem', letterSpacing: '0.05em' }}>Recent Awards & Recognition</h6>
                        <div className="premium-scroll-x d-md-flex gap-3 justify-content-center flex-wrap">
                            {displayAwards.map((award) => (
                                <img
                                    key={award.id}
                                    src={award.src}
                                    alt={award.title}
                                    className="award-thumb"
                                    onClick={() => setSelectedImage(award)}
                                    title={award.title}
                                    style={{ background: '#f8fafc' }}
                                    onError={(e) => {
                                        e.target.src = "https://placehold.co/100x100?text=Award";
                                    }}
                                />
                            ))}
                        </div>
                    </div>
                </div>
            </Modal>


            {/* Lightbox / Popup */}
            {selectedImage && (
                <div 
                    className="lightbox-overlay" 
                    onClick={() => setSelectedImage(null)}
                    style={{
                        position: 'fixed',
                        top: 0, left: 0, width: '100%', height: '100%',
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        zIndex: 2000,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        backdropFilter: 'blur(10px)',
                        animation: 'fadeIn 0.3s ease'
                    }}
                >
                    <div className="position-relative" style={{ maxWidth: '90%', maxHeight: '90%' }}>
                        <button 
                            className="btn-close btn-close-white position-absolute" 
                            style={{ top: '-40px', right: 0 }}
                            onClick={() => setSelectedImage(null)}
                        ></button>
                        <img 
                            src={selectedImage.src} 
                            alt={selectedImage.title} 
                            style={{ width: 'auto', height: 'auto', maxWidth: '100%', maxHeight: '80vh', borderRadius: '12px' }}
                            onError={(e) => {
                                e.target.src = "https://placehold.co/600x400?text=Award+Preview";
                            }}
                        />
                        <div className="text-center text-white mt-3 fw-medium">
                            {selectedImage.title}
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
            `}</style>
        </>
    );
};

export default ElitePartnerModal;
