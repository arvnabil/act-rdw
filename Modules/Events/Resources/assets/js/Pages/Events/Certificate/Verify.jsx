import React from "react";
import { Head, Link } from "@inertiajs/react";

import MainLayout from "@/Layouts/MainLayout";

export default function Verify({ status, certificate, code }) {
    const isValid = status === "valid";

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
            <Head title={`Verify Certificate - ${code}`} />

            <div
                className="d-flex flex-column align-items-center justify-content-center p-4"
                style={{
                    backgroundColor: theme.bg,
                    fontFamily: "'Inter', sans-serif",
                    minHeight: "80vh",
                    paddingTop: "120px", // Add space for fixed header if needed, or just visual balance
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
                                    backgroundColor: isValid
                                        ? "rgba(40, 167, 69, 0.1)"
                                        : "rgba(220, 53, 69, 0.1)",
                                    color: isValid ? "#28a745" : "#dc3545",
                                }}
                            >
                                <i
                                    className={`fas ${
                                        isValid ? "fa-check" : "fa-times"
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
                                {isValid
                                    ? "Certificate Verified"
                                    : "Verification Failed"}
                            </h2>
                            <p
                                className="mb-0 fs-5"
                                style={{ color: theme.text }}
                            >
                                {isValid
                                    ? "This certificate is authentic and valid."
                                    : `We could not find a certificate with the code below.`}
                            </p>
                        </div>

                        {isValid ? (
                            <>
                                {/* Certificate Details */}
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
                                                Participant
                                            </small>
                                            <div
                                                className="h5 fw-bold mb-0"
                                                style={{ color: theme.primary }}
                                            >
                                                {certificate.participant}
                                            </div>
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
                                                {certificate.event_title}
                                            </div>
                                        </div>
                                        <div className="col-sm-6">
                                            <small
                                                className="text-uppercase fw-bold d-block mb-1"
                                                style={{
                                                    fontSize: "11px",
                                                    letterSpacing: "1px",
                                                    color: "#999",
                                                }}
                                            >
                                                Issued On
                                            </small>
                                            <div className="fw-medium text-dark">
                                                {certificate.issued_at}
                                            </div>
                                        </div>
                                        <div className="col-sm-6">
                                            <small
                                                className="text-uppercase fw-bold d-block mb-1"
                                                style={{
                                                    fontSize: "11px",
                                                    letterSpacing: "1px",
                                                    color: "#999",
                                                }}
                                            >
                                                Certificate Code
                                            </small>
                                            <div
                                                className="fw-medium"
                                                style={{
                                                    fontFamily: "monospace",
                                                    color: theme.title,
                                                }}
                                            >
                                                {code}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Action Button */}
                                <button
                                    onClick={() =>
                                        (window.location.href = route(
                                            "events.certificates.download",
                                            code
                                        ))
                                    }
                                    className="th-btn style8 btn-lg w-100 text-white border-0 fw-bold d-flex align-items-center justify-content-center gap-2"
                                    style={{
                                        padding: "14px",
                                        borderRadius: "8px",
                                        fontFamily:
                                            "'Plus Jakarta Sans', sans-serif",
                                        cursor: "pointer",
                                    }}
                                >
                                    <i className="fas fa-download"></i>
                                    Download Certificate
                                </button>
                            </>
                        ) : (
                            <div
                                className="alert alert-danger border-0 d-flex gap-3 align-items-start rounded-3"
                                role="alert"
                            >
                                <i className="fas fa-exclamation-triangle mt-1"></i>
                                <div>
                                    <strong>Invalid Code:</strong> {code} <br />
                                    <span className="small">
                                        Please check your code and try again. If
                                        you believe this is an error, contact
                                        support.
                                    </span>
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
