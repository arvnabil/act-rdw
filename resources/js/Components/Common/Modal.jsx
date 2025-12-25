import React, { useEffect } from 'react';

/**
 * Reusable Modal Component
 * 
 * Features:
 * - Backdrop blur effect
 * - Centered content
 * - Fade-in animation
 * - Escape key to close
 * - Click outside to close (optional)
 * 
 * @param {boolean} show - Whether the modal is visible
 * @param {function} onClose - Function to call when closing
 * @param {ReactNode} children - Modal content
 * @param {string} maxWidth - Max width of the modal container (default: '550px')
 * @param {string} zIndex - Z-index of the modal (default: '1055')
 */
const Modal = ({ show, onClose, children, maxWidth = '550px', zIndex = '1055' }) => {

    // Close on Escape key
    useEffect(() => {
        const handleEsc = (e) => {
            if (e.key === 'Escape') onClose();
        };
        if (show) window.addEventListener('keydown', handleEsc);
        return () => window.removeEventListener('keydown', handleEsc);
    }, [show, onClose]);

    if (!show) return null;

    return (
        <div
            className="modal-overlay"
            style={{
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                backgroundColor: 'rgba(0,0,0,0.6)',
                zIndex: zIndex,
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                backdropFilter: 'blur(8px)',
                WebkitBackdropFilter: 'blur(8px)', // Safari support
                transition: 'all 0.3s ease-in-out',
                padding: '20px'
            }}
            onClick={(e) => {
                // Close if clicking the overlay (background)
                if (e.target === e.currentTarget) onClose();
            }}
        >
            <div
                className="modal-content position-relative"
                style={{
                    backgroundColor: '#fff',
                    maxWidth: maxWidth,
                    width: '100%',
                    boxShadow: '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                    animation: 'fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1)',
                    padding: '40px',
                    borderRadius: '20px',
                    position: 'relative'
                }}
            >
                {/* Close Button */}
                <button
                    onClick={onClose}
                    className="position-absolute btn-close-custom"
                    style={{
                        top: '20px',
                        right: '20px',
                        background: '#f4f7fa',
                        border: 'none',
                        width: '32px',
                        height: '32px',
                        borderRadius: '50%',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        fontSize: '16px',
                        color: '#666',
                        cursor: 'pointer',
                        transition: '0.2s',
                        zIndex: 10
                    }}
                    onMouseEnter={(e) => { e.currentTarget.style.background = '#e2e8f0'; e.currentTarget.style.color = '#333'; }}
                    onMouseLeave={(e) => { e.currentTarget.style.background = '#f4f7fa'; e.currentTarget.style.color = '#666'; }}
                >
                    <i className="fa-solid fa-xmark"></i>
                </button>

                {/* Content */}
                {children}
            </div>

            {/* Animation Style */}
            <style jsx global>{`
                @keyframes fadeInUp {
                    from { opacity: 0; transform: translate3d(0, 40px, 0); }
                    to { opacity: 1; transform: translate3d(0, 0, 0); }
                }
            `}</style>
        </div>
    );
};

export default Modal;
