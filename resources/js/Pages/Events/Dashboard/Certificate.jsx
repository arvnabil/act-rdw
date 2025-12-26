import React from "react";
import { Head, Link } from "@inertiajs/react";
import DashboardLayout from "./Layout";
import EmptyState from "@/Components/Common/EmptyState";

export default function Certificate({ auth, certificates }) {
    return (
        <DashboardLayout pageTitle="My Certificates">
            <Head title="My Certificates" />

            <div className="row g-4">
                {certificates.length > 0 ? (
                    certificates.map((cert) => (
                        <div className="col-md-6" key={cert.id}>
                            <div className="card border-0 shadow-sm rounded-20 overflow-hidden h-100 group-hover-scale">
                                <div className="d-flex p-3">
                                    <div
                                        className="shrink-0 me-3 rounded-3 overflow-hidden"
                                        style={{
                                            width: "100px",
                                            height: "80px",
                                        }}
                                    >
                                        <img
                                            src={cert.event_thumbnail}
                                            alt={cert.event_title}
                                            className="w-100 h-100 object-fit-cover"
                                        />
                                    </div>
                                    <div className="grow">
                                        <h6 className="fw-bold mb-1 line-clamp-2">
                                            {cert.event_title}
                                        </h6>
                                        <p className="text-muted small mb-2">
                                            Completed on {cert.event_date}
                                        </p>
                                        <span className="badge bg-theme bg-opacity-10 text-theme rounded-pill">
                                            <i className="fa-solid fa-award me-1"></i>{" "}
                                            Certified
                                        </span>
                                    </div>
                                </div>
                                <div className="card-footer bg-light border-0 p-3 d-flex justify-content-between align-items-center">
                                    <small className="text-muted">
                                        Issued: {cert.issue_date}
                                    </small>
                                    <a
                                        href={cert.file_path}
                                        target="_blank"
                                        download
                                        className="btn btn-sm btn-outline-theme rounded-pill fw-medium"
                                    >
                                        <i className="fa-solid fa-download me-2"></i>
                                        Download PDF
                                    </a>
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
