import React, { useState, useEffect } from "react";
import { Head, Link, usePage, router } from "@inertiajs/react";
import DashboardLayout from "./Layout";

import Toast from "@/Components/Common/Toast";

export default function EventRoom({
    auth,
    event,
    registration,
    user_certificate,
}) {
    const { flash } = usePage().props;
    const [claiming, setClaiming] = useState(false);
    const [toast, setToast] = useState(null);

    useEffect(() => {
        if (flash?.success) {
            setToast({ message: flash.success, type: "success" });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
        if (flash?.error) {
            setToast({ message: flash.error, type: "danger" });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const handleClaimCertificate = () => {
        setClaiming(true);
        router.post(
            route("events.certificates.claim", event.id),
            {},
            {
                preserveScroll: true,
                onFinish: () => setClaiming(false),
            }
        );
    };

    return (
        <DashboardLayout>
            <Head title={`Room: ${event.title}`} />
            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type}
                onClose={() => setToast(null)}
            />

            <div className="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <Link
                        href={route("events.my-events")}
                        className="text-muted text-decoration-none mb-2 d-inline-block small"
                    >
                        <i className="fa-solid fa-arrow-left me-1"></i> Back to
                        My Events
                    </Link>
                    <h2 className="fw-bold fs-3 mb-1">{event.title}</h2>
                    <span className="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                        <i className="fa-solid fa-check-circle me-1"></i> Access
                        Granted
                    </span>
                </div>
            </div>

            <div className="row g-4">
                {/* Meeting Link Section */}
                <div className="col-lg-8">
                    <div className="card border-0 shadow-sm rounded-20 mb-4 overflow-hidden">
                        <div className="card-header bg-theme text-white border-0 p-4">
                            <h5 className="fw-bold mb-0 text-white">
                                <i className="fa-solid fa-video me-2"></i> Live
                                Session
                            </h5>
                        </div>
                        <div className="card-body p-5 text-center">
                            {event.meeting_link ? (
                                <>
                                    <div className="mb-4">
                                        <div
                                            className="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3"
                                            style={{
                                                width: "100px",
                                                height: "100px",
                                            }}
                                        >
                                            <i className="fa-solid fa-video fs-1 text-theme"></i>
                                        </div>
                                        <h4 className="fw-bold">
                                            The Event is Live!
                                        </h4>
                                        <p className="text-muted">
                                            Click the button below to join the
                                            meeting room.
                                        </p>
                                    </div>
                                    <a
                                        href={event.meeting_link}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="btn th-btn th-radius px-5"
                                    >
                                        <i className="fa-solid fa-external-link-alt me-2"></i>{" "}
                                        Join Meeting Now
                                    </a>
                                    <p className="mt-3 small text-muted">
                                        Please ensure you have Zoom/Meet
                                        installed.
                                    </p>
                                </>
                            ) : (
                                <div className="py-4">
                                    <div
                                        className="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3"
                                        style={{
                                            width: "100px",
                                            height: "100px",
                                        }}
                                    >
                                        <i className="fa-solid fa-hourglass-start fs-1 text-muted opacity-50"></i>
                                    </div>
                                    <h5 className="fw-bold text-muted">
                                        Meeting Link Not Available Yet
                                    </h5>
                                    <p className="text-muted mb-0">
                                        The link will be available here once the
                                        event starts or is configured by the
                                        organizer.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Description or Schedule could go here */}
                </div>

                {/* Sidebar Widgets */}
                <div className="col-lg-4">
                    {/* Certificate Widget */}
                    <div className="card border-0 shadow-sm rounded-20 mb-4">
                        <div className="card-body p-4">
                            <div className="d-flex align-items-center gap-3 mb-3">
                                <div
                                    className="icon-box bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center"
                                    style={{ width: "40px", height: "40px" }}
                                >
                                    <i className="fa-solid fa-award text-white"></i>
                                </div>
                                <h6 className="fw-bold mb-0">Certificate</h6>
                            </div>

                            {user_certificate ? (
                                <div>
                                    <p className="text-muted small mb-3">
                                        Your certificate is ready for download.
                                    </p>
                                    <a
                                        href={`/storage/${user_certificate.file_path}`}
                                        target="_blank"
                                        className="th-btn style4 w-100 rounded-pill fw-bold dashed-border text-center text-decoration-none"
                                    >
                                        <i className="fa-solid fa-download me-2"></i>{" "}
                                        Open Certificate
                                    </a>
                                </div>
                            ) : event.is_certificate_available &&
                              event.has_certificate_template ? (
                                <div>
                                    <p className="text-muted small mb-3">
                                        You are eligible to claim your
                                        certificate.
                                    </p>
                                    <button
                                        onClick={handleClaimCertificate}
                                        disabled={claiming}
                                        className="th-btn w-100 rounded-pill fw-bold"
                                    >
                                        {claiming ? (
                                            <>
                                                <span
                                                    className="spinner-border spinner-border-sm me-2"
                                                    role="status"
                                                    aria-hidden="true"
                                                ></span>
                                                Claiming...
                                            </>
                                        ) : (
                                            <>
                                                <i className="fa-solid fa-award me-2"></i>{" "}
                                                Claim Certificate
                                            </>
                                        )}
                                    </button>
                                </div>
                            ) : (
                                <div>
                                    <p className="text-muted small mb-0">
                                        Certificate is not yet available for
                                        this event.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Support */}
                    <div
                        className="widget widget_banner"
                        style={{
                            backgroundImage:
                                "url('/assets/img/bg/widget_banner.jpg')",
                        }}
                    >
                        <div className="widget-banner position-relative text-center">
                            <span className="icon">
                                <i className="fa-solid fa-headset"></i>
                            </span>
                            <span className="text">Need Help?</span>
                            <div className=" mb-3 text-white">
                                <small>
                                    If you have any issues accessing the event
                                    or questions about your ticket, please
                                    contact our support team.
                                </small>
                            </div>
                            <Link
                                href="/contact"
                                className="th-btn style6 th-radius"
                            >
                                Get Support{" "}
                                <i className="fa-solid fa-arrow-right-long"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </DashboardLayout>
    );
}
