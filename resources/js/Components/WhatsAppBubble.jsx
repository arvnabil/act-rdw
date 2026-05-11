import React, { useState, useEffect } from 'react';
import { usePage } from '@inertiajs/react';

const WhatsAppBubble = () => {
    const { url, props } = usePage();
    const settings = props.whatsapp_bubble;
    
    if (!settings || !settings.is_enabled) return null;

    // Visibility Logic
    const activePages = settings.active_pages || ['all'];
    const isVisibleOnThisPage = () => {
        if (activePages.includes('all')) return true;
        if (activePages.includes('homepage') && url === '/') return true;
        if (activePages.includes('products') && url.includes('/products')) return true;
        return false;
    };

    if (!isVisibleOnThisPage()) return null;

    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        // Entrance animation delay
        const timer = setTimeout(() => setIsVisible(true), 1000);
        return () => clearTimeout(timer);
    }, []);

    const {
        phone,
        message,
        tooltip,
        position,
        show_online_badge,
        show_pulse_animation,
        open_in_new_tab,
        offset_bottom,
        offset_side,
        icon,
        button_color
    } = settings;

    const pageTitle = typeof document !== 'undefined' ? document.title.split(' - ')[0] : '';
    const entityId = props.product?.id || props.service?.id || null;
    const entityType = props.product ? 'product' : (props.service ? 'service' : null);
    const entitySlug = props.product?.slug || props.service?.slug || null;
    
    const waUrl = new URL('https://activ.co.id/wa');
    waUrl.searchParams.set('phone', phone);
    waUrl.searchParams.set('text', message || '');
    waUrl.searchParams.set('message', message || ''); // Include both for compatibility
    waUrl.searchParams.set('cta_label', `Bubble Chat: ${pageTitle}`);
    waUrl.searchParams.set('cta_position', 'bubble_chat');
    waUrl.searchParams.set('page_route', window.location.href);
    
    if (entityId) waUrl.searchParams.set('entity_id', entityId);
    if (entityType) waUrl.searchParams.set('entity_type', entityType);
    if (entitySlug) waUrl.searchParams.set('entity_slug', entitySlug);
    
    const waUrlString = waUrl.toString();

    const isBottomRight = position === 'bottom-right';

    const containerStyle = {
        position: 'fixed',
        bottom: offset_bottom || '110px',
        [isBottomRight ? 'right' : 'left']: offset_side || '24px',
        zIndex: 9998, // Just below the chatbot if overlapping, but usually separate
        display: 'flex',
        flexDirection: 'column',
        alignItems: isBottomRight ? 'flex-end' : 'flex-start',
        gap: '8px',
        transition: 'all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)',
        transform: isVisible ? 'translateY(0) scale(1)' : 'translateY(20px) scale(0)',
        opacity: isVisible ? 1 : 0,
    };

    return (
        <div style={containerStyle} className="wa-bubble-container">
            {/* Tooltip */}
            {tooltip && (
                <div style={{
                    background: '#fff',
                    color: '#333',
                    padding: '8px 12px',
                    borderRadius: '10px',
                    fontSize: '13px',
                    fontWeight: '500',
                    boxShadow: '0 4px 15px rgba(0,0,0,0.1)',
                    marginBottom: '4px',
                    whiteSpace: 'nowrap',
                    pointerEvents: 'none',
                    animation: 'fadeIn 0.3s ease'
                }}>
                    {tooltip}
                </div>
            )}

            {/* Bubble Button */}
            <a 
                href={waUrlString}
                target={open_in_new_tab ? "_blank" : "_self"}
                rel="noopener noreferrer"
                aria-label="Contact us on WhatsApp"
                style={{
                    width: '60px',
                    height: '60px',
                    background: button_color || '#25D366',
                    borderRadius: '50%',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    boxShadow: `0 8px 25px ${button_color || '#25D366'}66`, // 66 is approx 40% opacity in hex
                    cursor: 'pointer',
                    textDecoration: 'none',
                    position: 'relative',
                    marginBottom: '70px',
                    transition: 'transform 0.3s'
                }}
                onMouseEnter={e => e.currentTarget.style.transform = 'scale(1.1)'}
                onMouseLeave={e => e.currentTarget.style.transform = 'scale(1)'}
            >
                {/* Pulse Animation */}
                {show_pulse_animation && (
                    <div style={{
                        position: 'absolute',
                        top: 0, left: 0, right: 0, bottom: 0,
                        borderRadius: '50%',
                        background: button_color || '#25D366',
                        opacity: 0.6,
                        zIndex: -1,
                        animation: 'wa-pulse 2s infinite'
                    }} />
                )}

                {/* WhatsApp Icon */}
                {icon ? (
                    <img src={`/storage/${icon}`} alt="WA" style={{ width: '32px', height: '32px', objectFit: 'contain' }} />
                ) : (
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="#fff">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.438 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.149-.174.198-.298.298-.497.099-.198.05-.372-.025-.521-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413z" />
                    </svg>
                )}

                {/* Online Badge */}
                {show_online_badge && (
                    <div style={{
                        position: 'absolute',
                        top: '2px',
                        right: '2px',
                        width: '14px',
                        height: '14px',
                        background: '#2ecc71',
                        border: '2px solid #fff',
                        borderRadius: '50%',
                        zIndex: 2
                    }} />
                )}
            </a>

            <style>{`
                @keyframes wa-pulse {
                    0% { transform: scale(1); opacity: 0.6; }
                    100% { transform: scale(1.6); opacity: 0; }
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `}</style>
        </div>
    );
};

export default WhatsAppBubble;
