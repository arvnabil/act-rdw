import React, { useState, useEffect } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Modal from '@/Components/Common/Modal';

import Toast from '@/Components/Common/Toast';


export default function ConfiguratorComplete() {
    // Get data passed from the controller/route
    const { selection, userInfo, uuid } = usePage().props;
    const [quantities, setQuantities] = useState({});
    const [showToast, setShowToast] = useState(false);
    const [showShareModal, setShowShareModal] = useState(false);

    // Initialize quantities on mount
    useEffect(() => {
        if (selection && Array.isArray(selection.products)) {
            const initialQty = selection.quantities || {};
            const allItems = [
                ...selection.products,
                ...selection.accessories,
                ...(selection.audio || []),
                ...(selection.controller || []),
                selection.pc ? [selection.pc] : [],
                selection.license ? [selection.license] : [],
                selection.warranty ? [selection.warranty] : []
            ].flat();

            allItems.forEach(item => {
                if (!initialQty[item.id]) initialQty[item.id] = 1;
            });
            setQuantities(initialQty);
        }
    }, [selection]);

    // Safety check if accessed directly without data
    if (!selection || !selection.products) {
        return (
            <MainLayout>
                <div className="container py-5 text-center">
                    <h3>No configuration found.</h3>
                    <Link href="/room-configurator" className="th-btn style4 th-radius mt-3">Start Configuration<i className="fa-solid fa-rotate-right ms-2"></i></Link>

                </div>
            </MainLayout>
        );
    }

    // Handlers
    const handleQuantityChange = (id, value) => {
        const qty = parseInt(value) || 0;
        setQuantities(prev => ({ ...prev, [id]: qty > 0 ? qty : 1 }));
    };

    const calculateTotal = () => {
        const getQty = (id) => quantities[id] || 1;
        const hardwareTotal = selection.products.reduce((sum, item) => sum + (item.price * getQty(item.id)), 0);
        const accessoryTotal = selection.accessories.reduce((sum, item) => sum + (item.price * getQty(item.id)), 0);
        const audioTotal = selection.audio ? selection.audio.reduce((sum, item) => sum + (item.price * getQty(item.id)), 0) : 0;
        const controllerTotal = selection.controller ? selection.controller.reduce((sum, item) => sum + (item.price * getQty(item.id)), 0) : 0;
        return hardwareTotal + accessoryTotal + audioTotal + controllerTotal;
    };

    const handleDownloadExcel = () => {
        const getQty = (id) => quantities[id] || 1;
        const formatCurrency = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(val);

        let htmlTable = `
            <table border="1">
                <thead>
                    <tr>
                        <th colspan="4" style="font-size: 16px; font-weight: bold; text-align: center;">PROJECT SUMMARY - ACTiV</th>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <th>Item Name</th>
                        <th>Quantity</th>
                        <th>Price (MSRP)</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
        `;

        const renderRow = (category, item) => {
            const qty = getQty(item.id);
            const total = item.price * qty;
            return `
                <tr>
                    <td>${category}</td>
                    <td>${item.name}</td>
                    <td style="text-align: center;">${qty}</td>
                    <td>${formatCurrency(item.price)}</td>
                    <td>${formatCurrency(total)}</td>
                </tr>
            `;
        };

        selection.products.forEach(p => htmlTable += renderRow('Camera/Hardware', p));
        if (selection.audio) selection.audio.forEach(a => htmlTable += renderRow('Audio', a));
        if (selection.controller) selection.controller.forEach(c => htmlTable += renderRow('Controller', c));
        selection.accessories.forEach(a => htmlTable += renderRow('Accessory', a));

        htmlTable += `
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold;">TOTAL ESTIMATED COST</td>
                        <td style="font-weight: bold;">${formatCurrency(calculateTotal())}</td>
                    </tr>
                </tbody>
            </table>
        `;

        // Create Blob
        const blob = new Blob([`<html xmlns:x="urn:schemas-microsoft-com:office:excel">${htmlTable}</html>`], { type: 'application/vnd.ms-excel' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Room_Configuration_${uuid || 'Draft'}.xls`;
        a.click();
        URL.revokeObjectURL(url);
    };

    const handleCopyLink = () => {
        // Assuming the link format, using current URL + query or just a placeholder if not persisted
        // The user said "using id user or uuid"
        const shareUrl = `${window.location.origin}/room-configurator/result/${uuid}`; // Hypothetical URL
        navigator.clipboard.writeText(shareUrl);

        setShowToast(true);
    };

    const handleRequestConsultation = () => {
        const message = encodeURIComponent(`Hi, I would like to request a consultation for my room configuration (ID: ${uuid}). Please assist me.`);
        window.open(`https://wa.me/?text=${message}`, '_blank');
    };


    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(price);
    };

    const themeColor = '#4AC15E';

    return (
        <MainLayout>
            <Head title="Configuration Complete" />
            {/* Thank You Section (Replaces Breadcrumb & Intro) */}
            <div className="breadcumb-area style2 bg-smoke4">
                <div
                    className="breadcumb-wrapper"
                    style={{ backgroundImage: 'url("/assets/img/bg/breadcumb-bg.jpg")' }}
                >
                    <div className="container">
                        <div className="breadcumb-content">
                            <span className="sub-title text-white py-2">PROJECT SUMMARY</span>
                            <h1 className="breadcumb-title py-2">
                                Thank you for your interest in ACTiV!
                            </h1>
                            <p
                                className="breadcumb-text text-white mb-30 py-2"
                                style={{ maxWidth: '800px', marginLeft: 'auto', marginRight: 'auto' }}
                            >
                                Explore your finished room(s) below, and when you're ready to talk
                                next steps, simply request a consultation. We'd be happy to
                                discuss your project, develop a formal quote, and facilitate next
                                steps, whether through ACTiV or your preferred partner.
                            </p>
                            <div className="btn-group justify-content-center">
                                <button
                                    type="button"
                                    className="th-btn style3 th-radius th-icon"
                                    onClick={handleCopyLink}
                                >
                                    Copy your custom URL <i className="fa-regular fa-copy"></i>
                                </button>
                                <button className="th-btn th-radius th-icon" onClick={handleRequestConsultation}>
                                    Request Consultation
                                    <i className="fa-regular fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section className="space-top space-bottom bg-white">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-10">

                            {/* New Header Section */}
                            <div className="text-center mb-5">
                                <h2 className="fw-bold mb-3 text-capitalize">
                                    {selection.roomType?.replace('_', ' ')}
                                </h2>
                                <p className="text-muted mx-auto" style={{ maxWidth: '800px', fontSize: '14px' }}>
                                    Configurations are for exploratory purposes only. Room guides and the prices listed are based on local MSRP for the products and are not formal quotes. Prices may vary by location, channel or reseller. Please request a consultation for more information and next steps.
                                </p>

                                <div className="d-flex flex-wrap justify-content-center gap-3 mt-4">
                                    <button onClick={handleDownloadExcel} className="th-btn style4 th-radius th-icon" style={{ minWidth: '200px' }}>
                                        Download Room Guide [Excel] <i className="fa-solid fa-download ms-2"></i>
                                    </button>
                                    <button onClick={handleRequestConsultation} className="th-btn th-radius th-icon" style={{ minWidth: '200px' }}>
                                        Request Consultation <i className="fa-regular fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            {/* Product List */}
                            <div className="mb-4">
                                <h6 className="mb-4 border-bottom pb-3">Room Solution Bundles:</h6>

                                {/* Conferencing Cameras */}
                                {selection.products && selection.products.length > 0 && (
                                    <div className="mb-4">
                                        <div className="d-flex flex-column gap-4">
                                            {selection.products.map((item, idx) => (
                                                <div key={`prod-${item.id}-${idx}`} className="d-flex align-items-center pb-4" style={{ borderBottom: '1px dashed #e5e7eb' }}>
                                                    <div className="flex-shrink-0 me-5" style={{ width: '230px' }}>
                                                        {item.image ? (
                                                            <img src={item.image} alt={item.name} className="img-fluid" style={{ maxHeight: '200px', width: 'auto', objectFit: 'contain' }} />
                                                        ) : (
                                                            <div className="bg-light rounded d-flex align-items-center justify-content-center" style={{ width: '230px', height: '200px' }}>
                                                                <i className="fa-solid fa-cube text-muted fa-3x"></i>
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="flex-grow-1">
                                                        <h5 className="mb-1 text-uppercase fw-bold" style={{ color: themeColor, letterSpacing: '0.5px' }}>{item.name}</h5>
                                                        {item.sku && <small className="text-muted d-block mt-2">Part Number: <span className="text-dark fw-medium">{item.sku}</span></small>}
                                                    </div>
                                                    <div className="flex-shrink-0 ms-4">
                                                        <span className="fs-5 text-muted">x {quantities[item.id] || 1}</span>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* Audio Add-Ons */}
                                {selection.audio && selection.audio.length > 0 && (
                                    <div className="mb-4">
                                        <h6 className="fw-bold mb-3 text-secondary">Audio:</h6>
                                        <div className="d-flex flex-column gap-4">
                                            {selection.audio.map((item, idx) => (
                                                <div key={`audio-${item.id}-${idx}`} className="d-flex align-items-center pb-4" style={{ borderBottom: '1px dashed #e5e7eb' }}>
                                                    <div className="flex-shrink-0 me-5" style={{ width: '230px' }}>
                                                        {item.image ? (
                                                            <img src={item.image} alt={item.name} className="img-fluid" style={{ maxHeight: '200px', width: 'auto', objectFit: 'contain' }} />
                                                        ) : (
                                                            <div className="bg-light rounded d-flex align-items-center justify-content-center" style={{ width: '230px', height: '200px' }}>
                                                                <i className="fa-solid fa-microphone-lines text-muted fa-3x"></i>
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="flex-grow-1">
                                                        <h5 className="mb-1 text-uppercase fw-bold" style={{ color: themeColor, letterSpacing: '0.5px' }}>{item.name}</h5>
                                                        {item.sku && <small className="text-muted d-block mt-2">Part Number: <span className="text-dark fw-medium">{item.sku}</span></small>}
                                                    </div>
                                                    <div className="flex-shrink-0 ms-4">
                                                        <span className="fs-5 text-muted">x {quantities[item.id] || 1}</span>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {/* Additional Add-Ons (PC, Controller, Accessories) */}
                                <div className="mb-4">
                                    <h6 className="fw-bold mb-3 text-secondary">Additional Add-Ons:</h6>
                                    <div className="d-flex flex-column gap-4">
                                        {[
                                            selection.pc,
                                            ...(selection.controller || []),
                                            ...(selection.accessories || [])
                                        ].filter(Boolean).map((item, idx) => (
                                            <div key={`addon-${item.id}-${idx}`} className="d-flex align-items-center pb-4" style={{ borderBottom: '1px dashed #e5e7eb' }}>
                                                <div className="flex-shrink-0 me-5" style={{ width: '230px' }}>
                                                    {item.image ? (
                                                        <img src={item.image} alt={item.name} className="img-fluid" style={{ maxHeight: '200px', width: 'auto', objectFit: 'contain' }} />
                                                    ) : (
                                                        <div className="bg-light rounded d-flex align-items-center justify-content-center" style={{ width: '230px', height: '200px' }}>
                                                            {/* Contextual Icons if no image */}
                                                            {item.id === selection.pc?.id && <i className="fa-solid fa-desktop text-muted fa-3x"></i>}
                                                            {selection.controller?.some(c => c.id === item.id) && <i className="fa-solid fa-tablet-screen-button text-muted fa-3x"></i>}
                                                            {selection.accessories?.some(a => a.id === item.id) && <i className="fa-solid fa-puzzle-piece text-muted fa-3x"></i>}
                                                        </div>
                                                    )}
                                                </div>
                                                <div className="flex-grow-1">
                                                    <h5 className="mb-1 text-uppercase fw-bold" style={{ color: themeColor, letterSpacing: '0.5px' }}>{item.name}</h5>
                                                    <small className="text-muted">{item.desc}</small>
                                                    {item.sku && <small className="text-muted d-block mt-2">Part Number: <span className="text-dark fw-medium">{item.sku}</span></small>}
                                                </div>
                                                <div className="flex-shrink-0 ms-4">
                                                    <span className="fs-5 text-muted">x {quantities[item.id] || 1}</span>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                {/* Room Platform License */}
                                {selection.platformLicense && (
                                    <div className="mb-4">
                                        <h6 className="fw-bold mb-3 text-secondary">Room Platform License:</h6>
                                        <div className="d-flex flex-column gap-4">
                                            <div className="d-flex align-items-center pb-4" style={{ borderBottom: '1px dashed #e5e7eb' }}>
                                                <div className="flex-shrink-0 me-5" style={{ width: '230px' }}>
                                                    <div className="bg-light rounded d-flex align-items-center justify-content-center" style={{ width: '230px', height: '200px' }}>
                                                        <i className="fa-solid fa-file-contract text-muted fa-3x"></i>
                                                    </div>
                                                </div>
                                                <div className="flex-grow-1">
                                                    <h5 className="mb-1 text-uppercase fw-bold" style={{ color: themeColor, letterSpacing: '0.5px' }}>{selection.platformLicense.name}</h5>
                                                    <small className="text-muted">{selection.platformLicense.desc}</small>
                                                </div>
                                                <div className="flex-shrink-0 ms-4">
                                                    <span className="fs-5 text-muted">x {quantities[selection.platformLicense.id] || 1}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Room Management Licenses */}
                                {selection.managementLicense && (
                                    <div className="mb-4">
                                        <h6 className="fw-bold mb-3 text-secondary">Room Management Licenses:</h6>
                                        <div className="d-flex flex-column gap-4">
                                            <div className="d-flex align-items-center pb-4" style={{ borderBottom: '1px dashed #e5e7eb' }}>
                                                <div className="flex-shrink-0 me-5" style={{ width: '230px' }}>
                                                    <div className="bg-light rounded d-flex align-items-center justify-content-center" style={{ width: '230px', height: '200px' }}>
                                                        <i className="fa-solid fa-id-badge text-muted fa-3x"></i>
                                                    </div>
                                                </div>
                                                <div className="flex-grow-1">
                                                    <h5 className="mb-1 text-uppercase fw-bold" style={{ color: themeColor, letterSpacing: '0.5px' }}>{selection.managementLicense.name}</h5>
                                                    <small className="text-muted">{selection.managementLicense.desc}</small>
                                                </div>
                                                <div className="flex-shrink-0 ms-4">
                                                    <span className="fs-5 text-muted">x {quantities[selection.managementLicense.id] || 1}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                {/* Warranty Device */}
                                {selection.warranty && (
                                    <div className="mb-4">
                                        <h6 className="fw-bold mb-3 text-secondary">Warranty Device:</h6>
                                        <div className="d-flex flex-column gap-4">
                                            <div className="d-flex align-items-center pb-4" style={{ borderBottom: '1px dashed #e5e7eb' }}>
                                                <div className="flex-shrink-0 me-5" style={{ width: '230px' }}>
                                                    <div className="bg-light rounded d-flex align-items-center justify-content-center" style={{ width: '230px', height: '200px' }}>
                                                        <i className="fa-solid fa-shield-halved text-muted fa-3x"></i>
                                                    </div>
                                                </div>
                                                <div className="flex-grow-1">
                                                    <h5 className="mb-1 text-uppercase fw-bold" style={{ color: themeColor, letterSpacing: '0.5px' }}>{selection.warranty.name}</h5>
                                                    <small className="text-muted">{selection.warranty.desc}</small>
                                                </div>
                                                <div className="flex-shrink-0 ms-4">
                                                    <span className="fs-5 text-muted">x {quantities[selection.warranty.id] || 1}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Services (Moved out of collapsed info) */}
                            {selection.services && Object.keys(selection.services).length > 0 && (
                                <div className="mb-5 border-top pt-4">
                                    <h6 className="fw-bold mb-4 text-secondary">Services:</h6>
                                    <ul className="list-unstyled">
                                        {Object.entries(selection.services).map(([key, value]) => (
                                            <li key={key} className="mb-3 d-flex align-items-center">
                                                <i className="fa-solid fa-check-circle me-3 fa-lg" style={{ color: themeColor }}></i>
                                                <span className="fs-6 fw-medium">{value}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}

                            {/* User Info & Services (Collapsed/Secondary) */}
                            {userInfo && (
                                <div className="mt-4 pt-4 border-top">
                                    <h6 className="fw-bold mb-3">Configuration Details</h6>
                                    <div className="row g-3 text-muted small">
                                        <div className="col-md-3"><strong>Name:</strong> {userInfo.name}</div>
                                        <div className="col-md-3"><strong>Phone:</strong> {userInfo.phone}</div>
                                        <div className="col-md-3"><strong>Sustainability:</strong> {userInfo.sustainability}</div>
                                        <div className="col-md-3"><strong>Brand:</strong> {selection.brand}</div>
                                    </div>
                                </div>
                            )}

                            {/* Share Button (Bottom) */}
                            <div className="text-center mt-5 pt-4 border-top">
                                <button
                                    onClick={() => setShowShareModal(true)}
                                    className="btn btn-link text-decoration-none fw-bold"
                                    style={{ color: themeColor }}
                                >
                                    <i className="fa-solid fa-share-nodes me-2"></i> Share your project
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Share Modal */}
            <Modal show={showShareModal} onClose={() => setShowShareModal(false)}>
                <div className="text-center pt-2">
                    {/* Icon */}
                    <div
                        className="d-flex align-items-center justify-content-center mx-auto mb-3 rounded-circle text-white shadow-sm"
                        style={{ width: '60px', height: '60px', backgroundColor: themeColor }}
                    >
                        <i className="fa-solid fa-share-nodes fa-xl"></i>
                    </div>

                    <h3 className="fw-bold mb-3">Share Your Project</h3>
                    <p className="text-muted mb-4">
                        Copy your custom link below to share with others.
                    </p>

                    <div className="d-flex align-items-center bg-white border rounded-pill shadow-sm overflow-hidden ps-1">
                        <input
                            type="text"
                            readOnly
                            value={`${window.location.origin}/room-configurator/result/${uuid}`}
                            className="form-control border-0 bg-transparent shadow-none ps-3 text-muted"
                            style={{ fontSize: '14px', height: '46px', paddingRight: '15px' }}
                        />
                        <button
                            onClick={handleCopyLink}
                            className="btn text-white fw-bold flex-shrink-0"
                            style={{ backgroundColor: themeColor, minWidth: '120px', height: '46px', fontSize: '14px', borderRadius: '0' }}
                        >
                            Copy Link <i className="fa-solid fa-link ms-2"></i>
                        </button>
                    </div>
                </div>
            </Modal>

            {/* Toast Notification */}
            <Toast show={showToast} message="Link copied to clipboard!" type="info" onClose={() => setShowToast(false)} />
        </MainLayout >
    );
}
