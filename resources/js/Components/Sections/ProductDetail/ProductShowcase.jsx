import React, { useState, useLayoutEffect, useRef } from "react";
import { Link, usePage } from "@inertiajs/react";
import { getImageUrl } from "@/Utils/image";
import { getWhatsAppLink } from "@/Utils/whatsapp";
import ElitePartnerModal from "./ElitePartnerModal";
import gsap from "gsap";
import "@/../css/premium-product.css";

export default function ProductShowcase({ product }) {
    const [showEliteModal, setShowEliteModal] = useState(false);
    const { settings } = usePage().props;
    const containerRef = useRef(null);

    useLayoutEffect(() => {
        const ctx = gsap.context(() => {
            gsap.from(".premium-image-wrapper", {
                x: -50,
                opacity: 0,
                duration: 1,
                ease: "power3.out"
            });
            gsap.from(".premium-info-panel > *", {
                y: 30,
                opacity: 0,
                duration: 0.8,
                stagger: 0.1,
                ease: "power3.out"
            });
        }, containerRef);
        return () => ctx.revert();
    }, []);

    const [selectedPreviewImage, setSelectedPreviewImage] = useState(null);

    // --- Dynamic Spec Resolution ---
    const allSpecs = product.specification || {};
    const specEntries = Object.entries(allSpecs);
    
    // Helper to ensure values are renderable (strings/numbers)
    const safeValue = (val) => {
        if (typeof val === 'object' && val !== null) {
            // Priority: direct 'value' key if it exists
            if (val.value !== undefined) return safeValue(val.value);
            
            if (Array.isArray(val)) return val.map(v => safeValue(v)).join(', ');

            const values = Object.values(val);
            if (values.length === 1) return safeValue(values[0]);
            
            return Object.entries(val)
                .map(([k, v]) => isNaN(k) ? `${k}: ${safeValue(v)}` : safeValue(v))
                .join(' | ');
        }
        return val;
    };

    // Fixed Top Items
    const topGridItems = [
        { label: "SKU", value: product.sku, icon: "fa-barcode" },
        { label: "Category", value: product.category, icon: "fa-layer-group" },
        { label: "Brand", value: product.brand?.name || "Logitech", icon: "fa-copyright" },
    ];

    // Add Product Sheet as a conditional high-priority card
    topGridItems.push({ 
        label: "Product Sheet", 
        value: product.datasheet_url ? null : "Tidak tersedia", 
        icon: "fa-file-pdf", 
        link: product.datasheet_url || null,
        rel: product.datasheet_rel || "noopener noreferrer",
        isDatasheet: true
    });

    // Features Highlights (2 shortest features)
    // Schema uses 'name' and 'value'
    const features = Array.isArray(product.features) ? product.features : [];
    const shortestFeatures = [...features]
        .sort((a, b) => {
            const textA = (a.name || "") + (a.value || "");
            const textB = (b.name || "") + (b.value || "");
            return textA.length - textB.length;
        })
        .slice(0, 2);

    const featureGridItems = shortestFeatures.map((f, i) => ({
        label: f.name || "Feature",
        value: f.value || "-",
        icon: "fa-star"
    }));

    const finalGridItems = [...topGridItems, ...featureGridItems];
    
    // Remaining specs for the table
    const consumedKeys = ["sku", "brand", "category", "datasheet", "product sheet"];
    const usedInGridKeys = [...consumedKeys];
    const additionalSpecs = specEntries
        .filter(([key]) => !usedInGridKeys.some(uk => key.toLowerCase().includes(uk)) && isNaN(key))
        .map(([label, value]) => ({ label, value: safeValue(value) }));

    const whatsappMessage = (
        product.whatsapp_note ||
        `Halo, saya tertarik dengan produk ${product.name}. Mohon info harga terbaik.`
    ).replace("{Product Name}", product.name);

    const whatsappLink = getWhatsAppLink(settings?.whatsapp_number, {
        message: whatsappMessage,
        cta_position: 'product_detail',
        cta_label: `CTA Produk: ${product.name}`,
        entity_type: 'product',
        entity_id: product.id,
        entity_slug: product.slug
    }) || "#";

    return (
        <div className="premium-showcase-container mb-5" ref={containerRef}>
            <div className="row g-5 align-items-center">
                {/* LEFT SIDE – PRODUCT VISUAL */}
                <div className="col-lg-5">
                    <div 
                        className="premium-image-wrapper p-4 cursor-pointer"
                        style={{ position: 'relative' }}
                        onClick={() => setSelectedPreviewImage({
                            src: getImageUrl(product.image),
                            title: product.name,
                            badge: product.tags?.find(tag => 
                                ["certified", "teams", "zoom", "google", "meet"].some(kw => tag.toLowerCase().includes(kw))
                            )
                        })}
                    >
                        {/* Floating Badge (Certification/Highlight) */}
                        {(() => {
                            const certKeywords = ["certified", "teams", "zoom", "google", "meet"];
                            const highlightTag = product.tags?.find(tag => 
                                certKeywords.some(kw => tag.toLowerCase().includes(kw))
                            );
                            
                            if (!highlightTag) return null;
                            
                            return (
                                <div className="premium-floating-badge" style={{ zIndex: 10 }}>
                                    <i className="fa-solid fa-check-circle"></i>
                                    <span>{highlightTag}</span>
                                </div>
                            );
                        })()}
                        
                        <div className="premium-image-inner" style={{ position: 'relative', textAlign: 'center' }}>
                            <img
                                src={getImageUrl(product.image)}
                                alt={product.name}
                                className="img-fluid"
                                style={{ 
                                    maxHeight: '450px', 
                                    width: 'auto',
                                    objectFit: 'contain', 
                                    filter: 'drop-shadow(0 20px 30px rgba(0,0,0,0.1))',
                                    transform: 'perspective(1000px) rotateY(-5deg) scale(1.05)',
                                    transition: 'all 0.5s ease'
                                }}
                                onMouseEnter={e => e.target.style.transform = 'perspective(1000px) rotateY(0deg) scale(1.1)'}
                                onMouseLeave={e => e.target.style.transform = 'perspective(1000px) rotateY(-5deg) scale(1.05)'}
                                onError={(e) => {
                                    e.currentTarget.src = "/assets/default.png";
                                    e.currentTarget.onerror = null;
                                }}
                            />
                            {/* Hover Indicator */}
                            <div className="premium-preview-hint">
                                <i className="fa-solid fa-expand"></i>
                                <span>Preview Gambar</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* RIGHT SIDE – PRODUCT INFORMATION PANEL */}
                <div className="col-lg-7">
                    <div className="premium-info-panel">
                        {/* Header */}
                        <header className="mb-4">
                            <h1 className="premium-info-title">{product.name}</h1>
                            <p className="premium-info-subtitle">
                                {product.subtitle || product.name}
                            </p>
                        </header>

                        {/* Quick Info Grid (2x3 Modern Cards) */}
                        <div className="premium-grid">
                            {finalGridItems.map((item, idx) => (
                                <div 
                                    key={idx} 
                                    className={`premium-grid-card ${item.link ? 'cursor-pointer' : ''}`}
                                    onClick={() => {
                                        if (item.link) {
                                            const win = window.open(item.link, '_blank', 'noopener,noreferrer');
                                            if (win) win.opener = null;
                                        }
                                    }}
                                >
                                    <span className="premium-card-label">
                                        <i className={`fa-solid ${item.icon} text-success`}></i>
                                        {item.label}
                                    </span>
                                    <span className="premium-card-value">
                                        {item.value !== null ? (item.value || "-") : null}
                                        {item.isDatasheet && item.link && (
                                            <div className="mt-2">
                                                <span className="premium-btn-tech shadow-sm">
                                                    <i className="fa-solid fa-download"></i>
                                                    Download PDF
                                                </span>
                                            </div>
                                        )}
                                    </span>
                                    {item.label === "Brand" && (product.brand?.config?.partner?.enabled !== false) && (
                                        <div 
                                            className="premium-trust-badge"
                                            onClick={(e) => { e.stopPropagation(); setShowEliteModal(true); }}
                                        >
                                            <i className="fa-solid fa-circle-check"></i>
                                            {product.brand?.config?.partner?.modal_title || `Authorized ${product.brand?.name} Partner`}
                                            <i className="fa-solid fa-circle-info ms-1" style={{ fontSize: '0.7em' }}></i>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>



                        {/* TAGS SECTION */}
                        <div className="mb-4 d-flex flex-wrap gap-2">
                            {product.tags && product.tags.length > 0 ? (
                                product.tags.map((tag, i) => (
                                    <span key={i} className="premium-tag-pill">
                                        #{tag}
                                    </span>
                                ))
                            ) : (
                                <span className="text-muted small">No tags</span>
                            )}
                        </div>

                        {/* CTA AREA */}
                        <div className="d-flex flex-wrap gap-3">
                            <a href={whatsappLink} target="_blank" rel="noopener noreferrer" className="premium-btn-primary d-flex align-items-center gap-2">
                                Permintaan Harga Terbaik
                                <i className="fa-solid fa-paper-plane ms-1"></i>
                            </a>
                            {product.link_accommerce && (
                                <a href={product.link_accommerce} target="_blank" rel={product.link_accommerce_rel || "noopener noreferrer"} className="premium-btn-online d-flex align-items-center gap-2">
                                    Pembelian Online Store
                                    <i className="fa-solid fa-cart-shopping ms-1"></i>
                                </a>
                            )}
                        </div>


                    </div>
                </div>
            </div>

            {/* Modal */}
            <ElitePartnerModal 
                show={showEliteModal} 
                onClose={() => setShowEliteModal(false)} 
                config={product.brand?.config?.partner}
                awards={product.brand?.config?.awards}
                brandName={product.brand?.name}
            />

            {/* Lightbox / Popup */}
            {selectedPreviewImage && (
                <div 
                    className="lightbox-overlay" 
                    onClick={() => setSelectedPreviewImage(null)}
                    style={{
                        position: 'fixed',
                        top: 0, left: 0, width: '100%', height: '100%',
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        zIndex: 3000,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        backdropFilter: 'blur(10px)',
                        animation: 'fadeIn 0.3s ease'
                    }}
                >
                    <div className="position-relative" style={{ maxWidth: '90%', maxHeight: '90%' }} onClick={e => e.stopPropagation()}>
                        <button 
                            className="btn-close btn-close-white position-absolute" 
                            style={{ top: '-50px', right: '0', padding: '10px' }}
                            onClick={() => setSelectedPreviewImage(null)}
                        ></button>
                        
                        <div className="bg-white p-3 rounded-[2.5rem] shadow-2xl">
                            <img 
                                src={selectedPreviewImage.src} 
                                alt={selectedPreviewImage.title} 
                                style={{ width: 'auto', height: 'auto', maxWidth: '100%', maxHeight: '75vh', borderRadius: '1.5rem', objectFit: 'contain' }}
                            />
                            <div className="pt-4 pb-2 px-4 text-center">
                                {selectedPreviewImage.badge && (
                                    <span className="text-[10px] font-black tracking-widest text-success uppercase mb-1 d-block">
                                        {selectedPreviewImage.badge}
                                    </span>
                                )}
                                <h4 className="text-xl font-bold text-slate-900 m-0">
                                    {selectedPreviewImage.title}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <style>{`
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .premium-preview-hint {
                    position: absolute;
                    bottom: 1rem;
                    left: 50%;
                    transform: translateX(-50%) translateY(10px);
                    background: rgba(255, 255, 255, 0.85);
                    backdrop-filter: blur(8px);
                    padding: 8px 16px;
                    border-radius: 50px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 0.7rem;
                    font-weight: 800;
                    color: #0f172a;
                    opacity: 0;
                    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
                    pointer-events: none;
                    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
                    z-index: 5;
                    text-transform: uppercase;
                    white-space: nowrap;
                }
                .premium-image-wrapper:hover .premium-preview-hint {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }
                .premium-image-inner {
                    position: relative;
                }
            `}</style>
        </div>
    );
}
