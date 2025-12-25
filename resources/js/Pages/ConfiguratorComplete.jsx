import React, { useState, useEffect } from "react";
import { Head, Link, usePage } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Modal from "@/Components/Common/Modal";
import Toast from "@/Components/Common/Toast";

export default function ConfiguratorComplete() {
    // Get data passed from the controller/route
    const { selection, userInfo, uuid } = usePage().props;
    const [quantities, setQuantities] = useState({});
    const [showToast, setShowToast] = useState(false);
    const [showShareModal, setShowShareModal] = useState(false);
    const [configType, setConfigType] = useState(
        selection?.products
            ? "room"
            : selection?.scenario
            ? "server"
            : selection?.premise
            ? "surveillance"
            : null
    );

    // Detect Configurator Type & Initialize
    useEffect(() => {
        if (!selection) return;

        if (selection.products) {
            setConfigType("room");
            // Room: Init quantities
            const initialQty = selection.quantities || {};
            const allItems = [
                ...selection.products,
                ...selection.accessories,
                ...(selection.audio || []),
                ...(selection.controller || []),
                selection.pc ? [selection.pc] : [],
                selection.license ? [selection.license] : [],
                selection.warranty ? [selection.warranty] : [],
            ].flat();

            allItems.forEach((item) => {
                if (!initialQty[item.id]) initialQty[item.id] = 1;
            });
            setQuantities(initialQty);
        } else if (selection.scenario) {
            setConfigType("server");
        } else if (selection.premise) {
            setConfigType("surveillance");
        }
    }, [selection]);

    // Safety check
    if (!selection) {
        return (
            <MainLayout>
                <div className="container py-5 text-center">
                    <h3>No configuration found.</h3>
                    <Link
                        href="/services"
                        className="th-btn style4 th-radius mt-3"
                    >
                        Back to Services
                    </Link>
                </div>
            </MainLayout>
        );
    }

    // --- Helpers ---
    const handleQuantityChange = (id, value) => {
        const qty = parseInt(value) || 0;
        setQuantities((prev) => ({ ...prev, [id]: qty > 0 ? qty : 1 }));
    };

    const calculateTotal = () => {
        if (configType !== "room" || !selection?.products) return 0;
        const getQty = (id) => quantities[id] || 1;
        const hardwareTotal = selection.products.reduce(
            (sum, item) => sum + item.price * getQty(item.id),
            0
        );
        const accessoryTotal = (selection.accessories || []).reduce(
            (sum, item) => sum + item.price * getQty(item.id),
            0
        );
        const audioTotal = selection.audio
            ? selection.audio.reduce(
                  (sum, item) => sum + item.price * getQty(item.id),
                  0
              )
            : 0;
        const controllerTotal = selection.controller
            ? selection.controller.reduce(
                  (sum, item) => sum + item.price * getQty(item.id),
                  0
              )
            : 0;
        return hardwareTotal + accessoryTotal + audioTotal + controllerTotal;
    };

    const handleDownloadExcel = () => {
        if (configType !== "room") {
            alert(
                "Download available for Room Configurations only at this time."
            );
            return;
        }
        // ... (Existing Excel Logic for Room)
        const getQty = (id) => quantities[id] || 1;
        const formatCurrency = (val) =>
            new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
            }).format(val);

        let htmlTable = `
            <table border="1">
                <thead>
                    <tr><th colspan="4" style="font-size: 16px; font-weight: bold; text-align: center;">PROJECT SUMMARY - ACTiV</th></tr>
                    <tr><th>Category</th><th>Item Name</th><th>Quantity</th><th>Price (MSRP)</th><th>Total</th></tr>
                </thead>
                <tbody>
        `;
        const renderRow = (category, item) => {
            const qty = getQty(item.id);
            const total = item.price * qty;
            return `<tr><td>${category}</td><td>${
                item.name
            }</td><td style="text-align: center;">${qty}</td><td>${formatCurrency(
                item.price
            )}</td><td>${formatCurrency(total)}</td></tr>`;
        };
        selection.products.forEach(
            (p) => (htmlTable += renderRow("Camera/Hardware", p))
        );
        if (selection.audio)
            selection.audio.forEach(
                (a) => (htmlTable += renderRow("Audio", a))
            );
        if (selection.controller)
            selection.controller.forEach(
                (c) => (htmlTable += renderRow("Controller", c))
            );
        selection.accessories.forEach(
            (a) => (htmlTable += renderRow("Accessory", a))
        );
        htmlTable += `<tr><td colspan="4" style="text-align: right; font-weight: bold;">TOTAL</td><td style="font-weight: bold;">${formatCurrency(
            calculateTotal()
        )}</td></tr></tbody></table>`;

        const blob = new Blob(
            [
                `<html xmlns:x="urn:schemas-microsoft-com:office:excel">${htmlTable}</html>`,
            ],
            { type: "application/vnd.ms-excel" }
        );
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `Room_Configuration_${uuid || "Draft"}.xls`;
        a.click();
        URL.revokeObjectURL(url);
    };

    const handleCopyLink = () => {
        const shareUrl = `${window.location.origin}/configurator/result/${uuid}`; // Generic URL
        navigator.clipboard.writeText(shareUrl);
        setShowToast(true);
    };

    const handleRequestConsultation = () => {
        const message = encodeURIComponent(
            `Hi, I would like to request a consultation for my configuration (ID: ${uuid}, Type: ${configType}). Please assist me.`
        );
        window.open(`https://wa.me/?text=${message}`, "_blank");
    };

    const themeColor = "#4AC15E";

    // --- Render Sections ---

    const renderRoomSummary = () => (
        <div className="mb-4">
            <h6 className="mb-4 border-bottom pb-3">Room Solution Bundles:</h6>

            {/* Conferencing Cameras */}
            {selection.products && selection.products.length > 0 && (
                <div className="mb-4">
                    <div className="d-flex flex-column gap-4">
                        {selection.products.map((item, idx) => (
                            <div
                                key={`prod-${item.id}-${idx}`}
                                className="d-flex align-items-center pb-4"
                                style={{ borderBottom: "1px dashed #e5e7eb" }}
                            >
                                <div
                                    className="flex-shrink-0 me-5"
                                    style={{ width: "230px" }}
                                >
                                    {item.image ? (
                                        <img
                                            src={item.image}
                                            alt={item.name}
                                            className="img-fluid"
                                            style={{
                                                maxHeight: "200px",
                                                width: "auto",
                                                objectFit: "contain",
                                            }}
                                        />
                                    ) : (
                                        <div
                                            className="bg-light rounded d-flex align-items-center justify-content-center"
                                            style={{
                                                width: "230px",
                                                height: "200px",
                                            }}
                                        >
                                            <i className="fa-solid fa-cube text-muted fa-3x"></i>
                                        </div>
                                    )}
                                </div>
                                <div className="flex-grow-1">
                                    <h5
                                        className="mb-1 text-uppercase fw-bold"
                                        style={{
                                            color: themeColor,
                                            letterSpacing: "0.5px",
                                        }}
                                    >
                                        {item.name}
                                    </h5>
                                    {item.sku && (
                                        <small className="text-muted d-block mt-2">
                                            Part Number:{" "}
                                            <span className="text-dark fw-medium">
                                                {item.sku}
                                            </span>
                                        </small>
                                    )}
                                </div>
                                <div className="flex-shrink-0 ms-4">
                                    <span className="fs-5 text-muted">
                                        x {quantities[item.id] || 1}
                                    </span>
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
                            <div
                                key={`audio-${item.id}-${idx}`}
                                className="d-flex align-items-center pb-4"
                                style={{ borderBottom: "1px dashed #e5e7eb" }}
                            >
                                <div
                                    className="flex-shrink-0 me-5"
                                    style={{ width: "230px" }}
                                >
                                    {item.image ? (
                                        <img
                                            src={item.image}
                                            alt={item.name}
                                            className="img-fluid"
                                            style={{
                                                maxHeight: "200px",
                                                width: "auto",
                                                objectFit: "contain",
                                            }}
                                        />
                                    ) : (
                                        <div
                                            className="bg-light rounded d-flex align-items-center justify-content-center"
                                            style={{
                                                width: "230px",
                                                height: "200px",
                                            }}
                                        >
                                            <i className="fa-solid fa-microphone-lines text-muted fa-3x"></i>
                                        </div>
                                    )}
                                </div>
                                <div className="flex-grow-1">
                                    <h5
                                        className="mb-1 text-uppercase fw-bold"
                                        style={{
                                            color: themeColor,
                                            letterSpacing: "0.5px",
                                        }}
                                    >
                                        {item.name}
                                    </h5>
                                    {item.sku && (
                                        <small className="text-muted d-block mt-2">
                                            Part Number:{" "}
                                            <span className="text-dark fw-medium">
                                                {item.sku}
                                            </span>
                                        </small>
                                    )}
                                </div>
                                <div className="flex-shrink-0 ms-4">
                                    <span className="fs-5 text-muted">
                                        x {quantities[item.id] || 1}
                                    </span>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {/* Additional Add-Ons (PC, Controller, Accessories) */}
            <div className="mb-4">
                <h6 className="fw-bold mb-3 text-secondary">
                    Additional Add-Ons:
                </h6>
                <div className="d-flex flex-column gap-4">
                    {[
                        selection.pc,
                        ...(selection.controller || []),
                        ...(selection.accessories || []),
                    ]
                        .filter(Boolean)
                        .map((item, idx) => (
                            <div
                                key={`addon-${item.id}-${idx}`}
                                className="d-flex align-items-center pb-4"
                                style={{ borderBottom: "1px dashed #e5e7eb" }}
                            >
                                <div
                                    className="flex-shrink-0 me-5"
                                    style={{ width: "230px" }}
                                >
                                    {item.image ? (
                                        <img
                                            src={item.image}
                                            alt={item.name}
                                            className="img-fluid"
                                            style={{
                                                maxHeight: "200px",
                                                width: "auto",
                                                objectFit: "contain",
                                            }}
                                        />
                                    ) : (
                                        <div
                                            className="bg-light rounded d-flex align-items-center justify-content-center"
                                            style={{
                                                width: "230px",
                                                height: "200px",
                                            }}
                                        >
                                            {/* Contextual Icons if no image */}
                                            {item.id === selection.pc?.id && (
                                                <i className="fa-solid fa-desktop text-muted fa-3x"></i>
                                            )}
                                            {selection.controller?.some(
                                                (c) => c.id === item.id
                                            ) && (
                                                <i className="fa-solid fa-tablet-screen-button text-muted fa-3x"></i>
                                            )}
                                            {selection.accessories?.some(
                                                (a) => a.id === item.id
                                            ) && (
                                                <i className="fa-solid fa-puzzle-piece text-muted fa-3x"></i>
                                            )}
                                        </div>
                                    )}
                                </div>
                                <div className="flex-grow-1">
                                    <h5
                                        className="mb-1 text-uppercase fw-bold"
                                        style={{
                                            color: themeColor,
                                            letterSpacing: "0.5px",
                                        }}
                                    >
                                        {item.name}
                                    </h5>
                                    <small className="text-muted">
                                        {item.desc}
                                    </small>
                                    {item.sku && (
                                        <small className="text-muted d-block mt-2">
                                            Part Number:{" "}
                                            <span className="text-dark fw-medium">
                                                {item.sku}
                                            </span>
                                        </small>
                                    )}
                                </div>
                                <div className="flex-shrink-0 ms-4">
                                    <span className="fs-5 text-muted">
                                        x {quantities[item.id] || 1}
                                    </span>
                                </div>
                            </div>
                        ))}
                </div>
            </div>
        </div>
    );

    const renderServerSummary = () => (
        <div className="mb-4">
            <h6 className="mb-4 border-bottom pb-3">
                Server Infrastructure Selection:
            </h6>
            <div className="row g-4">
                <div className="col-md-6">
                    <div className="p-3 border rounded bg-light h-100">
                        <small className="text-uppercase text-muted fw-bold">
                            Usage Scenario
                        </small>
                        <h5 className="mt-2 text-success">
                            <i className="fa-solid fa-server me-2"></i>
                            {selection?.scenario?.name || selection.scenario}
                        </h5>
                        <p className="small mb-0 text-muted">
                            {selection?.scenario?.desc}
                        </p>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="p-3 border rounded bg-light h-100">
                        <small className="text-uppercase text-muted fw-bold">
                            Form Factor
                        </small>
                        <h5 className="mt-2 text-dark">
                            <i className="fa-solid fa-box me-2"></i>
                            {selection?.formFactor?.name ||
                                selection.formFactor}
                        </h5>
                    </div>
                </div>
                <div className="col-md-12">
                    <div className="p-3 border rounded bg-light">
                        <small className="text-uppercase text-muted fw-bold">
                            Specs & Storage
                        </small>
                        <div className="d-flex justify-content-between mt-2 flex-wrap gap-3">
                            <div>
                                <strong>Performance:</strong>{" "}
                                {selection?.performance?.name}{" "}
                                <span className="text-muted">
                                    ({selection?.performance?.specs})
                                </span>
                            </div>
                            <div>
                                <strong>Storage:</strong>{" "}
                                {selection?.storage?.name}
                            </div>
                        </div>
                    </div>
                </div>
                <div className="col-md-12">
                    <div className="p-3 border rounded bg-light">
                        <div className="d-flex align-items-center justify-content-between">
                            <div>
                                <small className="text-uppercase text-muted fw-bold">
                                    Brand Preference
                                </small>
                                <h4 className="mt-1 mb-0">
                                    {selection?.brand?.name || selection.brand}
                                </h4>
                            </div>
                            {selection.brand?.logo && (
                                <img
                                    src={selection.brand.logo}
                                    alt="Brand"
                                    style={{ height: "40px" }}
                                />
                            )}
                        </div>
                    </div>
                </div>
                {selection.software && selection.software.length > 0 && (
                    <div className="col-md-12">
                        <div className="p-3 border rounded bg-light">
                            <small className="text-uppercase text-muted fw-bold">
                                Software & Licensing
                            </small>
                            <ul className="mt-2 mb-0 list-unstyled row">
                                {selection.software.map((sw) => (
                                    <li key={sw.id} className="col-sm-6">
                                        <i className="fa-solid fa-check text-success me-2"></i>
                                        {sw.name}
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );

    const renderSurveillanceSummary = () => (
        <div className="mb-4">
            <h6 className="mb-4 border-bottom pb-3">
                Surveillance System Selection:
            </h6>
            <div className="row g-4">
                <div className="col-md-6">
                    <div className="p-3 border rounded bg-light h-100">
                        <small className="text-uppercase text-muted fw-bold">
                            Environment
                        </small>
                        <h5 className="mt-2 text-success">
                            <i className="fa-solid fa-building-shield me-2"></i>
                            {selection?.premise?.name}
                        </h5>
                        <p className="small mb-0 text-muted">
                            {selection?.premise?.desc}
                        </p>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="p-3 border rounded bg-light h-100">
                        <small className="text-uppercase text-muted fw-bold">
                            Coverage Area
                        </small>
                        <h5 className="mt-2 text-dark">
                            <i className="fa-solid fa-ruler-combined me-2"></i>
                            {selection?.areaSize?.name}
                        </h5>
                        <p className="small mb-0 text-muted">
                            {selection?.areaSize?.desc}
                        </p>
                    </div>
                </div>

                <div className="col-md-12">
                    <div className="p-3 border rounded bg-light">
                        <small className="text-uppercase text-muted fw-bold">
                            Hardware Requirements
                        </small>
                        <div className="mt-2">
                            <strong>Cameras:</strong>{" "}
                            {selection?.cameraType?.length
                                ? selection.cameraType
                                      .map((c) => c.name)
                                      .join(", ")
                                : "None selected"}
                        </div>
                        <div className="mt-2">
                            <strong>Smart Features:</strong>{" "}
                            {selection?.features?.length
                                ? selection.features
                                      .map((f) => f.name)
                                      .join(", ")
                                : "None selected"}
                        </div>
                    </div>
                </div>

                <div className="col-md-12">
                    <div className="p-3 border rounded bg-light">
                        <div className="d-flex align-items-center justify-content-between">
                            <div>
                                <small className="text-uppercase text-muted fw-bold">
                                    Preferred Ecosystem
                                </small>
                                <h4 className="mt-1 mb-0">
                                    {selection?.brand?.name}
                                </h4>
                            </div>
                            {selection.brand?.logo && (
                                <img
                                    src={selection.brand.logo}
                                    alt="Brand"
                                    style={{ height: "40px" }}
                                />
                            )}
                        </div>
                    </div>
                </div>
                <div className="col-md-12">
                    <div className="p-3 border rounded bg-light d-flex align-items-center">
                        <i className="fa-solid fa-clock-rotate-left fa-2x text-muted me-3"></i>
                        <div>
                            <small className="text-uppercase text-muted fw-bold">
                                Retention Period
                            </small>
                            <h5 className="mb-0">
                                {selection?.retention?.name}{" "}
                                <span className="text-muted fw-normal fs-6">
                                    ({selection?.retention?.desc})
                                </span>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );

    return (
        <MainLayout>
            <Head title="Configuration Complete" />
            {/* Thank You Section (Replaces Breadcrumb & Intro) */}
            <div className="breadcumb-area style2 bg-smoke4">
                <div
                    className="breadcumb-wrapper"
                    style={{
                        backgroundImage:
                            'url("/assets/img/bg/breadcumb-bg.jpg")',
                    }}
                >
                    <div className="container">
                        <div className="breadcumb-content">
                            <span className="sub-title text-white py-2">
                                PROJECT SUMMARY
                            </span>
                            <h1 className="breadcumb-title py-2">
                                Thank you for your interest in ACTiV!
                            </h1>
                            <p
                                className="breadcumb-text text-white mb-30 py-2"
                                style={{ maxWidth: "800px", margin: "0 auto" }}
                            >
                                Explore your finished room(s) below, and when
                                you're ready to talk next steps, simply request
                                a consultation. We'd be happy to discuss your
                                project, develop a formal quote, and facilitate
                                next steps, whether through ACTiV or your
                                preferred partner.
                            </p>
                            <div className="btn-group justify-content-center">
                                <button
                                    type="button"
                                    className="th-btn style3 th-radius th-icon"
                                    onClick={handleCopyLink}
                                >
                                    Copy your custom URL{" "}
                                    <i className="fa-regular fa-copy"></i>
                                </button>
                                <button
                                    className="th-btn th-radius th-icon"
                                    onClick={handleRequestConsultation}
                                >
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
                            <div className="text-center mb-5">
                                <h2 className="fw-bold mb-3 text-capitalize">
                                    {configType === "server"
                                        ? "Server Configuration"
                                        : configType === "surveillance"
                                        ? "Surveillance System"
                                        : selection.roomType?.replace(
                                              "_",
                                              " "
                                          ) || "Room Configuration"}
                                </h2>
                                <p
                                    className="text-muted mx-auto"
                                    style={{
                                        maxWidth: "800px",
                                        fontSize: "14px",
                                    }}
                                >
                                    Configurations are for exploratory purposes
                                    only. Room guides and the prices listed are
                                    based on local MSRP for the products and are
                                    not formal quotes. Prices may vary by
                                    location, channel or reseller. Please
                                    request a consultation for more information
                                    and next steps.
                                </p>

                                <div className="d-flex flex-wrap justify-content-center gap-3 mt-4">
                                    {configType === "room" && (
                                        <button
                                            onClick={handleDownloadExcel}
                                            className="th-btn style4 th-radius th-icon"
                                            style={{ minWidth: "200px" }}
                                        >
                                            Download Room Guide [Excel]{" "}
                                            <i className="fa-solid fa-download ms-2"></i>
                                        </button>
                                    )}
                                    <button
                                        onClick={handleRequestConsultation}
                                        className="th-btn th-radius th-icon"
                                        style={{ minWidth: "200px" }}
                                    >
                                        Request Consultation{" "}
                                        <i className="fa-regular fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            {configType === "room" && renderRoomSummary()}
                            {configType === "server" && renderServerSummary()}
                            {configType === "surveillance" &&
                                renderSurveillanceSummary()}

                            {/* User Info & Services (Collapsed/Secondary) */}
                            {userInfo && (
                                <div className="mt-4 pt-4 border-top">
                                    <h6 className="fw-bold mb-3">
                                        Configuration Details
                                    </h6>
                                    <div className="row g-3 text-muted small">
                                        <div className="col-md-3">
                                            <strong>Name:</strong>{" "}
                                            {userInfo.name}
                                        </div>
                                        <div className="col-md-3">
                                            <strong>Phone:</strong>{" "}
                                            {userInfo.phone}
                                        </div>
                                        <div className="col-md-3">
                                            <strong>Sustainability:</strong>{" "}
                                            {userInfo.sustainability}
                                        </div>
                                        <div className="col-md-3">
                                            <strong>Brand:</strong>{" "}
                                            {typeof selection.brand === "object"
                                                ? selection.brand?.name
                                                : selection.brand}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Share Button */}
                            <div className="text-center mt-5 pt-4 border-top">
                                <button
                                    onClick={() => setShowShareModal(true)}
                                    className="btn btn-link text-decoration-none fw-bold"
                                    style={{ color: themeColor }}
                                >
                                    <i className="fa-solid fa-share-nodes me-2"></i>{" "}
                                    Share this configuration
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <Modal
                show={showShareModal}
                onClose={() => setShowShareModal(false)}
            >
                <div className="text-center pt-2">
                    <h3 className="fw-bold mb-3">Share Project</h3>
                    <div className="d-flex align-items-center bg-white border rounded-pill shadow-sm overflow-hidden ps-1">
                        <input
                            type="text"
                            readOnly
                            value={`${window.location.origin}/room-configurator/result/${uuid}`}
                            className="form-control border-0 bg-transparent shadow-none ps-3 text-muted"
                            style={{
                                fontSize: "14px",
                                height: "46px",
                                paddingRight: "15px",
                            }}
                        />
                        <button
                            onClick={handleCopyLink}
                            className="btn text-white fw-bold flex-shrink-0"
                            style={{
                                backgroundColor: themeColor,
                                minWidth: "120px",
                                height: "46px",
                                fontSize: "14px",
                                borderRadius: "0",
                            }}
                        >
                            Copy Link <i className="fa-solid fa-link ms-2"></i>
                        </button>
                    </div>
                </div>
            </Modal>
            <Toast
                show={showToast}
                message="Link copied!"
                type="info"
                onClose={() => setShowToast(false)}
            />
        </MainLayout>
    );
}
