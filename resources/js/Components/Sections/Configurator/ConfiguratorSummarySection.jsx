import React, { useState } from "react";
import jsPDF from "jspdf";
import html2canvas from "html2canvas";

export default function ConfiguratorSummarySection({
    selection,
    userInfo,
    uuid,
    summaryItems,
    configType,
    setToastMessage,
    setShowToast,
}) {
    const handleDownloadPDF = async () => {
        const input = document.getElementById("configuration-summary");
        if (!input) {
            setToastMessage("Summary not found to generate PDF.");
            setShowToast(true);
            return;
        }

        try {
            const canvas = await html2canvas(input, {
                scale: 2, // Improve quality
                useCORS: true, // Handle cross-origin images if any
            });

            const imgData = canvas.toDataURL("image/png");
            const pdf = new jsPDF("p", "mm", "a4");
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = pdf.internal.pageSize.getHeight();
            const imgWidth = canvas.width;
            const imgHeight = canvas.height;
            const ratio = Math.min(pdfWidth / imgWidth, pdfHeight / imgHeight);
            const imgX = (pdfWidth - imgWidth * ratio) / 2;
            const imgY = 30; // Top margin

            pdf.text("Configuration Summary", 10, 20);
            pdf.addImage(
                imgData,
                "PNG",
                imgX,
                imgY,
                imgWidth * ratio,
                imgHeight * ratio
            );
            pdf.save(`configuration-${uuid || "summary"}.pdf`);

            setToastMessage("PDF Downloaded!");
            setShowToast(true);
        } catch (error) {
            console.error("PDF generation failed:", error);
            setToastMessage("Failed to generate PDF.");
            setShowToast(true);
        }
    };

    // Helper functions to render summary based on type
    const renderRoomSummary = () => {
        // Fallback helper: Find a summary item by question label part
        const getSummaryValue = (key, legacyKey) => {
            if (selection[key]) return selection[key].name || selection[key];
            if (selection[legacyKey])
                return selection[legacyKey].name || selection[legacyKey];

            // Try to find in summaryItems by context
            if (summaryItems) {
                const match = summaryItems.find(
                    (item) =>
                        item.question_label &&
                        item.question_label
                            .toLowerCase()
                            .includes(key.replace(/_/g, " "))
                );
                if (match) return match.name;
            }
            return "Not Selected";
        };

        return (
            <div className="row g-4 mb-5">
                <div className="col-md-4">
                    <div className="p-4 bg-light rounded text-center h-100 border">
                        <div className="mb-3 text-theme">
                            <i className="fa-regular fa-expand fa-3x"></i>
                        </div>
                        <h5 className="fw-bold mb-2">Room Size</h5>
                        <p className="fs-5 text-dark mb-0">
                            {getSummaryValue("meeting_room_size", "roomSize")}
                        </p>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="p-4 bg-light rounded text-center h-100 border">
                        <div className="mb-3 text-theme">
                            <i className="fa-regular fa-video fa-3x"></i>
                        </div>
                        <h5 className="fw-bold mb-2">Platform</h5>
                        <p className="fs-5 text-dark mb-0">
                            {getSummaryValue("platform", "platform")}
                        </p>
                    </div>
                </div>
                <div className="col-md-4">
                    <div className="p-4 bg-light rounded text-center h-100 border">
                        <div className="mb-3 text-theme">
                            <i className="fa-regular fa-tag fa-3x"></i>
                        </div>
                        <h5 className="fw-bold mb-2">Brand</h5>
                        <p className="fs-5 text-dark mb-0">
                            {getSummaryValue("preferred_brand", "brand")}
                        </p>
                    </div>
                </div>
            </div>
        );
    };

    const renderServerSummary = () => (
        <div className="row g-4 mb-5">
            <div className="col-md-6">
                <div className="p-4 bg-light rounded text-center h-100 border">
                    <div className="mb-3 text-theme">
                        <i className="fa-regular fa-server fa-3x"></i>
                    </div>
                    <h5 className="fw-bold mb-2">Server Type</h5>
                    <p className="fs-5 text-dark mb-0">
                        {selection.serverType?.name ||
                            selection.serverType ||
                            "Not Selected"}
                    </p>
                </div>
            </div>
            <div className="col-md-6">
                <div className="p-4 bg-light rounded text-center h-100 border">
                    <div className="mb-3 text-theme">
                        <i className="fa-regular fa-database fa-3x"></i>
                    </div>
                    <h5 className="fw-bold mb-2">Capacity</h5>
                    <p className="fs-5 text-dark mb-0">
                        {selection.capacity?.name ||
                            selection.capacity ||
                            "Not Selected"}
                    </p>
                </div>
            </div>
        </div>
    );

    const renderSurveillanceSummary = () => (
        <div className="row g-4 mb-5">
            <div className="col-md-6">
                <div className="p-4 bg-light rounded text-center h-100 border">
                    <div className="mb-3 text-theme">
                        <i className="fa-regular fa-camera-cctv fa-3x"></i>
                    </div>
                    <h5 className="fw-bold mb-2">System Type</h5>
                    <p className="fs-5 text-dark mb-0">
                        {selection.systemType?.name ||
                            selection.systemType ||
                            "Not Selected"}
                    </p>
                </div>
            </div>
            <div className="col-md-6">
                <div className="p-4 bg-light rounded text-center h-100 border">
                    <div className="mb-3 text-theme">
                        <i className="fa-regular fa-eye fa-3x"></i>
                    </div>
                    <h5 className="fw-bold mb-2">Cameras</h5>
                    <p className="fs-5 text-dark mb-0">
                        {selection.cameraCount || "Not Selected"}
                    </p>
                </div>
            </div>
        </div>
    );

    return (
        <section className="space">
            <div className="container">
                <div className="title-area text-center">
                    <h2 className="sec-title my-2">
                        Explore Your Finished Rooms
                    </h2>
                    <p
                        className="sec-text ms-auto me-auto my-2"
                        style={{ maxWidth: "900px" }}
                    >
                        Configurations are for exploratory purposes only. Room
                        guides and the prices listed are based on local MSRP for
                        the products and are not formal quotes. Prices may vary
                        by location, channel or reseller. Please request a
                        consultation for more information and next steps.
                    </p>
                </div>
                {/* Main Download Button */}
                <div className="d-flex justify-content-center gap-3 mb-5">
                    <button
                        type="button"
                        className="th-btn th-radius th-icon"
                        onClick={handleDownloadPDF}
                    >
                        Download Room Guide (All){" "}
                        <i className="fa-regular fa-download"></i>
                    </button>
                </div>

                <div className="row justify-content-center">
                    <div className="col-lg-10">
                        {/* Summary Card */}
                        <div
                            id="configuration-summary"
                            className="bg-white rounded-20 shadow-sm p-4 p-lg-5 border"
                        >
                            <div className="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                <h4 className="fw-bold m-0">
                                    Configuration Summary
                                </h4>
                                {/* Secondary Download Button for when printed/captured */}
                                <div className="d-print-none">
                                    <small className="text-muted">
                                        #{uuid || "REF-12345"}
                                    </small>
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

                            {/* Dynamic Summary Data Table */}
                            {summaryItems && summaryItems.length > 0 && (
                                <div className="mt-5 pt-4 border-top">
                                    <h6 className="fw-bold mb-3">
                                        Selected Items
                                    </h6>
                                    <div className="table-responsive">
                                        <table className="table table-bordered align-middle">
                                            <thead className="table-light">
                                                <tr>
                                                    <th
                                                        scope="col"
                                                        style={{
                                                            width: "100px",
                                                        }}
                                                    >
                                                        Image
                                                    </th>
                                                    <th scope="col">
                                                        Selection Details
                                                    </th>
                                                    <th
                                                        scope="col"
                                                        className="text-center"
                                                        style={{
                                                            width: "100px",
                                                        }}
                                                    >
                                                        Qty
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {summaryItems.map((item) => (
                                                    <tr key={item.id}>
                                                        <td>
                                                            {item.image ? (
                                                                <img
                                                                    src={
                                                                        item.image
                                                                    }
                                                                    alt={
                                                                        item.name
                                                                    }
                                                                    className="img-fluid rounded"
                                                                    style={{
                                                                        maxHeight:
                                                                            "60px",
                                                                        objectFit:
                                                                            "contain",
                                                                    }}
                                                                />
                                                            ) : (
                                                                <div
                                                                    className="bg-light rounded d-flex align-items-center justify-content-center"
                                                                    style={{
                                                                        width: "60px",
                                                                        height: "60px",
                                                                    }}
                                                                >
                                                                    <i className="fa-solid fa-list text-muted"></i>
                                                                </div>
                                                            )}
                                                        </td>
                                                        <td>
                                                            {item.step_label &&
                                                                item.question_label && (
                                                                    <div className="text-muted small mb-1">
                                                                        {
                                                                            item.step_label
                                                                        }{" "}
                                                                        &rsaquo;{" "}
                                                                        {
                                                                            item.question_label
                                                                        }
                                                                    </div>
                                                                )}
                                                            <div className="fw-bold text-dark">
                                                                {item.name}
                                                            </div>
                                                            {item.sku &&
                                                                item.sku !==
                                                                    "-" && (
                                                                    <small className="text-muted">
                                                                        SKU:{" "}
                                                                        {
                                                                            item.sku
                                                                        }
                                                                    </small>
                                                                )}
                                                        </td>
                                                        <td className="text-center fw-bold text-dark">
                                                            {item.type ===
                                                            "product"
                                                                ? `x${item.quantity}`
                                                                : "-"}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
