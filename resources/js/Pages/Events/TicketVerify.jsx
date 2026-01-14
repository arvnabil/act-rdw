import React from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import { route } from "ziggy-js";

export default function TicketVerify({
    status,
    message,
    event,
    registration,
    user,
}) {
    const isValid = status === "success";

    // Atek Theme Colors (extracted from style.css)
    const theme = {
        primary: "#0e5d6f", // Dark Teal
        secondary: "#1da2c0", // Lighter Teal
        accent: "#4ac15e", // Green
        bg: "#f3f4f6", // Light Gray
        text: "#6e7070", // Body Gray
        title: "#0b1422", // Dark Title
    };

    return (
        <MainLayout>
            <Head
                title={`Verify Ticket - ${
                    registration?.ticket_code || "Invalid"
                }`}
            />

            <div
                className="d-flex flex-column align-items-center justify-content-center p-4"
                style={{
                    backgroundColor: theme.bg,
                    fontFamily: "'Inter', sans-serif",
                    minHeight: "80vh",
                    paddingTop: "120px",
                }}
            >
                <div
                    className="card border-0 shadow-lg overflow-hidden position-relative rounded-4 my-5"
                    style={{
                        maxWidth: "600px",
                        width: "100%",
                        background: "#fff",
                    }}
                >
                    {/* Decorative Top Line */}
                    <div
                        style={{
                            height: "6px",
                            background: `linear-gradient(90deg, ${theme.secondary} 0%, ${theme.accent} 100%)`,
                        }}
                    ></div>

                    <div className="card-body p-5">
                        {/* Header Section */}
                        <div className="text-center mb-5">
                            <div
                                className="d-inline-flex align-items-center justify-content-center rounded-circle mb-4"
                                style={{
                                    width: "80px",
                                    height: "80px",
                                    backgroundColor:
                                        status === "warning"
                                            ? "rgba(255, 193, 7, 0.1)"
                                            : isValid
                                            ? "rgba(40, 167, 69, 0.1)"
                                            : "rgba(220, 53, 69, 0.1)",
                                    color:
                                        status === "warning"
                                            ? "#ffc107"
                                            : isValid
                                            ? "#28a745"
                                            : "#dc3545",
                                }}
                            >
                                <i
                                    className={`fas ${
                                        status === "warning"
                                            ? "fa-exclamation"
                                            : isValid
                                            ? "fa-check"
                                            : "fa-times"
                                    } fa-3x`}
                                ></i>
                            </div>

                            <h2
                                className="fw-bold mb-2"
                                style={{
                                    color: theme.title,
                                    fontFamily:
                                        "'Plus Jakarta Sans', sans-serif",
                                }}
                            >
                                {status === "warning"
                                    ? "Details Required"
                                    : isValid
                                    ? "Check-in Verified"
                                    : "Check-in Failed"}
                            </h2>
                            <p
                                className="mb-0 fs-5"
                                style={{ color: theme.text }}
                            >
                                {status === "warning"
                                    ? "Please complete payment."
                                    : isValid
                                    ? "Registrasi ulang berhasil. Peserta terdaftar."
                                    : `We could not find a ticket with the provided code.`}
                            </p>
                        </div>

                        {status === "success" && (
                            <>
                                {/* Ticket Details */}
                                <div
                                    className="rounded-3 p-4 mb-4"
                                    style={{
                                        backgroundColor: "#f8f9fa",
                                        border: "1px dashed #e9ecef",
                                    }}
                                >
                                    <div className="row g-4">
                                        <div className="col-12">
                                            <small
                                                className="text-uppercase fw-bold d-block mb-1"
                                                style={{
                                                    fontSize: "11px",
                                                    letterSpacing: "1px",
                                                    color: "#999",
                                                }}
                                            >
                                                Attendee
                                            </small>
                                            <div
                                                className="h5 fw-bold mb-0"
                                                style={{ color: theme.primary }}
                                            >
                                                {user?.name ||
                                                    registration.name ||
                                                    "Unknown Attendee"}
                                            </div>
                                            <small className="text-muted">
                                                {user?.email ||
                                                    registration.email}
                                            </small>
                                        </div>
                                        <div className="col-12">
                                            <small
                                                className="text-uppercase fw-bold d-block mb-1"
                                                style={{
                                                    fontSize: "11px",
                                                    letterSpacing: "1px",
                                                    color: "#999",
                                                }}
                                            >
                                                Event
                                            </small>
                                            <div className="fs-6 fw-medium text-dark">
                                                {event?.title ||
                                                    "Unknown Event"}
                                            </div>
                                        </div>
                                        <div className="col-sm-4">
                                            <small
                                                className="text-uppercase fw-bold d-block mb-1"
                                                style={{
                                                    fontSize: "11px",
                                                    letterSpacing: "1px",
                                                    color: "#999",
                                                }}
                                            >
                                                Status
                                            </small>
                                            <div
                                                className={`text-center fw-bold small bg-white px-3 py-1 rounded-pill shadow-sm border ${
                                                    [
                                                        "Joined",
                                                        "Registered",
                                                        "Certified",
                                                        "confirmed",
                                                        "paid",
                                                        "approved",
                                                    ].includes(
                                                        registration.status
                                                    )
                                                        ? "text-success border-success-subtle"
                                                        : registration.status ===
                                                              "Pending" ||
                                                          registration.status ===
                                                              "pending"
                                                        ? "text-warning border-warning-subtle"
                                                        : "text-danger border-danger-subtle"
                                                }`}
                                            >
                                                <i
                                                    className={`fa-solid ${
                                                        [
                                                            "Joined",
                                                            "Registered",
                                                            "Certified",
                                                            "confirmed",
                                                            "paid",
                                                            "approved",
                                                        ].includes(
                                                            registration.status
                                                        )
                                                            ? "fa-circle-check"
                                                            : registration.status ===
                                                                  "Pending" ||
                                                              registration.status ===
                                                                  "pending"
                                                            ? "fa-clock"
                                                            : "fa-circle-xmark"
                                                    } me-2`}
                                                ></i>
                                                {registration.status}
                                            </div>
                                        </div>
                                        <div className="col-sm-8">
                                            <small
                                                className="text-uppercase fw-bold d-block mb-1"
                                                style={{
                                                    fontSize: "11px",
                                                    letterSpacing: "1px",
                                                    color: "#999",
                                                }}
                                            >
                                                Ticket Code
                                            </small>
                                            <div
                                                className="fw-medium"
                                                style={{
                                                    fontFamily: "monospace",
                                                    color: theme.title,
                                                }}
                                            >
                                                {registration.ticket_code}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Action Button */}
                                <Link
                                    href={route("events.my-event-detail", {
                                        slug: event.slug,
                                        tab: "ticket",
                                    })}
                                    className="th-btn style8 btn-lg w-100 text-white border-0 fw-bold d-flex align-items-center justify-content-center gap-2 text-decoration-none"
                                    style={{
                                        padding: "14px",
                                        borderRadius: "8px",
                                        fontFamily:
                                            "'Plus Jakarta Sans', sans-serif",
                                    }}
                                >
                                    <i className="fas fa-ticket"></i>
                                    View Your Ticket
                                </Link>
                            </>
                        )}

                        {(status === "error" || !status) && (
                            <div
                                className="alert alert-danger border-0 d-flex gap-3 align-items-start rounded-3"
                                role="alert"
                            >
                                <i className="fas fa-exclamation-triangle mt-1"></i>
                                <div>
                                    <strong>Verification Error</strong> <br />
                                    <span className="small">{message}</span>
                                </div>
                            </div>
                        )}

                        {status === "warning" && (
                            <div
                                className="alert alert-warning border-0 d-flex gap-3 align-items-start rounded-3"
                                role="alert"
                            >
                                <i className="fas fa-exclamation-circle mt-1"></i>
                                <div>
                                    <strong>Payment Required</strong> <br />
                                    <span className="small">{message}</span>
                                </div>
                            </div>
                        )}

                        {/* Back Link */}
                        <div className="text-center mt-4">
                            <Link
                                href="/"
                                className="text-decoration-none fw-medium d-inline-flex align-items-center gap-2"
                                style={{ color: theme.text, fontSize: "14px" }}
                            >
                                <i className="fas fa-long-arrow-left"></i>
                                Back to Home
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
