import React, { useState } from "react";
import { Head, Link, router } from "@inertiajs/react";
import DashboardLayout from "./Layout";
import EmptyState from "@/Components/Common/EmptyState";
import Toast from "@/Components/Common/Toast";
import { route } from "ziggy-js";

export default function Certificate({ auth, certificates }) {
    const [toast, setToast] = useState(null);
    const [processingId, setProcessingId] = useState(null);

    const handleClaim = (eventId) => {
        setProcessingId(eventId);
        router.post(
            route("events.certificates.claim", eventId),
            {},
            {
                preserveScroll: true,
                onFinish: () => setProcessingId(null),
                onSuccess: () => {
                    setToast({
                        message: "Certificate Claimed successfully!",
                        type: "success",
                    });
                },
                onError: (errors) => {
                    const msg =
                        Object.values(errors)[0] ||
                        "Failed to claim certificate.";
                    setToast({ message: msg, type: "danger" });
                },
            }
        );
    };

    return (
        <DashboardLayout pageTitle="My Certificates">
            <Head title="My Certificates" />

            {/* Toast Notification */}
            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type}
                onClose={() => setToast(null)}
            />

            <div className="d-flex flex-column gap-3">
                {certificates.length > 0 ? (
                    certificates.map((cert) => (
                        <div
                            className="card border shadow-sm rounded-20 overflow-hidden hover-lift transition-all position-relative"
                            key={cert.id}
                        >
                            {/* Status Badge - Top Right Absolute */}
                            <div className="position-absolute top-0 end-0 m-3 z-1">
                                {cert.status === "Claimed" ? (
                                    <div className="d-flex align-items-center text-success fw-bold small bg-white px-3 py-1 rounded-pill shadow-sm border border-success-subtle">
                                        <i className="fa-solid fa-circle-check me-2"></i>
                                        VERIFIED & CLAIMED
                                    </div>
                                ) : (
                                    <div className="d-flex align-items-center text-warning fw-bold small bg-white px-3 py-1 rounded-pill shadow-sm border border-warning-subtle">
                                        <i className="fa-solid fa-circle-exclamation me-2"></i>
                                        ACTION REQUIRED
                                    </div>
                                )}
                            </div>

                            <div className="card-body p-0">
                                <div className="d-lg-flex align-items-center p-3">
                                    {/* Thumbnail Section */}
                                    <div className="pe-lg-3 mb-3 mb-lg-0">
                                        <div
                                            className="rounded-4 overflow-hidden position-relative bg-light"
                                            style={{
                                                width: "120px",
                                            }}
                                        >
                                            <img
                                                src={cert.event_thumbnail}
                                                alt={cert.event_title}
                                                className="w-100 h-100 object-fit-cover"
                                                style={{ aspectRatio: "4/3" }}
                                                onError={(e) => {
                                                    e.target.style.display =
                                                        "none";
                                                    e.target.parentElement.classList.add(
                                                        "d-flex",
                                                        "align-items-center",
                                                        "justify-content-center"
                                                    );
                                                    e.target.parentElement.innerHTML =
                                                        '<i class="fa-solid fa-award text-muted fs-3"></i>';
                                                }}
                                            />
                                        </div>
                                    </div>

                                    {/* Content Section */}
                                    <div className="grow pe-lg-4 mb-3 mb-lg-0">
                                        <div className="d-flex flex-column justify-content-center h-100">
                                            <div className="mb-1 text-muted small">
                                                <i className="fa-regular fa-calendar me-2"></i>
                                                {cert.event_date}
                                            </div>
                                            <h6
                                                className="fw-bold text-dark mb-1 text-truncate-2"
                                                style={{
                                                    lineHeight: "1.4",
                                                    maxWidth: "90%",
                                                }}
                                            >
                                                {cert.event_title}
                                            </h6>
                                            <p className="text-muted small mb-0 mt-1">
                                                {cert.status === "Claimed"
                                                    ? `Issued: ${cert.issue_date}`
                                                    : "Certificate ready to claim"}
                                            </p>
                                        </div>
                                    </div>

                                    {/* Action Section */}
                                    <div
                                        className="ps-lg-3 border-start-lg"
                                        style={{ minWidth: "220px" }}
                                    >
                                        <div className="d-flex flex-column gap-2 justify-content-end h-100 align-items-lg-end text-lg-end pt-5 pb-2">
                                            {/* Action Buttons */}
                                            <div className="w-100">
                                                {cert.status === "Claimed" ? (
                                                    <a
                                                        href={cert.file_path}
                                                        target="_blank"
                                                        download
                                                        className="th-btn w-100 rounded-pill fw-bold border-2 d-flex align-items-center justify-content-center"
                                                        style={{
                                                            padding: "8px 20px",
                                                        }}
                                                    >
                                                        <i className="fa-solid fa-download me-2"></i>
                                                        Download PDF
                                                    </a>
                                                ) : (
                                                    <button
                                                        onClick={() =>
                                                            handleClaim(
                                                                cert.event_id
                                                            )
                                                        }
                                                        disabled={
                                                            processingId ===
                                                            cert.event_id
                                                        }
                                                        className="th-btn w-100 rounded-pill shadow-sm d-flex align-items-center justify-content-center"
                                                        style={{
                                                            padding:
                                                                "10px 20px",
                                                        }}
                                                    >
                                                        {processingId ===
                                                        cert.event_id ? (
                                                            <>
                                                                <span className="spinner-border spinner-border-sm me-2"></span>
                                                                Processing...
                                                            </>
                                                        ) : (
                                                            <>
                                                                <i className="fa-solid fa-award ms-2"></i>
                                                                Claim
                                                                Certificate
                                                            </>
                                                        )}
                                                    </button>
                                                )}
                                            </div>

                                            {/* Helper Text */}
                                            {cert.status !== "Claimed" && (
                                                <div className="text-lg-end w-100">
                                                    <span
                                                        className="text-muted small fst-italic"
                                                        style={{
                                                            fontSize: "11px",
                                                        }}
                                                    >
                                                        Available for download
                                                        after claiming
                                                    </span>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))
                ) : (
                    <div className="col-12">
                        <div className="card border-0 shadow-sm rounded-20 p-5 text-center">
                            <EmptyState
                                message="You haven't earned any certificates yet."
                                icon="fa-award"
                                link="/events"
                                linkText="Browse Events"
                            />
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
