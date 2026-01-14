import React, { useState, useEffect } from "react";
import { Head, Link, router, usePage } from "@inertiajs/react";
import Toast from "@/Components/Common/Toast";
import DashboardLayout from "./Layout";
import { route } from "ziggy-js";
import DashboardTable, {
    DashboardTableRow,
} from "../../../Components/Events/Dashboard/DashboardTable";
import { QRCodeCanvas } from "qrcode.react";

export default function MyEventDetail({
    auth,
    event,
    registration,
    certificate,
    ticket,
}) {
    // Get tab from URL if present
    const getInitialTab = () => {
        if (typeof window !== "undefined") {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get("tab");
            if (
                tab &&
                [
                    "overview",
                    "ticket",
                    "rundown",
                    "speakers",
                    "documentation",
                ].includes(tab)
            ) {
                return tab;
            }
        }
        return "overview";
    };

    const [activeTab, setActiveTab] = useState(getInitialTab());

    // Update URL when tab changes (optional but good for UX)
    useEffect(() => {
        const url = new URL(window.location);
        url.searchParams.set("tab", activeTab);
        window.history.replaceState({}, "", url);
    }, [activeTab]);

    const isConfirmed = [
        "confirmed",
        "Joined",
        "Registered",
        "Certified",
    ].includes(registration.status);

    // Toast State
    const [toast, setToast] = useState(null);
    const [processing, setProcessing] = useState(false);

    const handleClaim = () => {
        setProcessing(true);
        router.post(
            route("events.certificates.claim", event.id),
            {},
            {
                onFinish: () => setProcessing(false),
                onSuccess: () => {
                    setToast({
                        message: "Certificate claimed successfully!",
                        type: "success",
                    });
                },
                onError: (errors) => {
                    console.error("Claim failed:", errors);
                    const msg =
                        Object.values(errors)[0] ||
                        "Failed to claim certificate.";
                    setToast({
                        message: msg,
                        type: "error",
                    });
                },
            }
        );
    };

    return (
        <DashboardLayout pageTitle="My Event Details">
            <Head title={event.title} />
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
                    {isConfirmed ? (
                        <span className="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                            <i className="fa-solid fa-check-circle me-1"></i>{" "}
                            Access Granted
                        </span>
                    ) : (
                        <span className="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">
                            <i className="fa-solid fa-clock me-1"></i>{" "}
                            {registration.status}
                        </span>
                    )}
                </div>
            </div>
            {/* Event Header Card */}
            <div className="card border-0 shadow-sm rounded-20 mb-4 overflow-hidden">
                <div className="position-relative">
                    <img
                        src={
                            event.thumbnail
                                ? `/storage/${event.thumbnail}`
                                : "/assets/img/event/event_thumb_1_1.jpg"
                        }
                        alt={event.title}
                        className="w-100 object-fit-cover"
                        style={{ height: "250px" }}
                    />
                    <div className="position-absolute top-0 start-0 m-4 p-2 z-2">
                        <div className="d-flex justify-content-between align-items-end">
                            <span
                                className={`px-3 py-2 rounded-pill fw-bold small text-uppercase shadow-sm d-inline-flex align-items-center ${
                                    isConfirmed
                                        ? "bg-white text-success"
                                        : "bg-warning text-white"
                                }`}
                            >
                                <i
                                    className={`fa-solid ${
                                        isConfirmed
                                            ? "fa-circle-check"
                                            : "fa-clock"
                                    } me-2`}
                                ></i>
                                {registration.status}
                            </span>
                        </div>
                    </div>
                    <div className="position-absolute bottom-0 start-0 w-100 p-4 bg-gradient-dark text-white">
                        <div className="d-flex justify-content-between align-items-end">
                            <div>
                                <div className="d-flex align-items-center gap-3 small opacity-75">
                                    <span>
                                        <i className="fa-solid fa-calendar me-2"></i>
                                        {new Date(
                                            event.start_date
                                        ).toLocaleDateString()}
                                    </span>
                                    <span>
                                        <i className="fa-solid fa-location-dot me-2"></i>
                                        {event.location || "Online"}
                                    </span>
                                </div>
                            </div>
                            {isConfirmed && (
                                <Link
                                    href={route("events.room", event.slug)}
                                    className="th-btn style8 th-radius shadow-lg"
                                    style={{ padding: "12px" }}
                                >
                                    <i className="fa-solid fa-door-open me-2"></i>
                                    Enter Room
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            <div className="row g-4">
                <div className="col-lg-8">
                    {/* Tabs */}
                    <div className="card border-0 shadow-sm rounded-20 mb-4">
                        <div className="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                            <ul className="nav nav-tabs card-header-tabs border-0 gap-3">
                                <li className="nav-item">
                                    <button
                                        className={`nav-link border-0 bg-transparent fw-medium px-0 pb-3 ${
                                            activeTab === "overview"
                                                ? "active text-theme border-bottom border-2 border-theme"
                                                : "text-muted"
                                        }`}
                                        onClick={() => setActiveTab("overview")}
                                    >
                                        Overview
                                    </button>
                                </li>
                                {isConfirmed && (
                                    <li className="nav-item">
                                        <button
                                            className={`nav-link border-0 bg-transparent fw-medium px-0 pb-3 ${
                                                activeTab === "ticket"
                                                    ? "active text-theme border-bottom border-2 border-theme"
                                                    : "text-muted"
                                            }`}
                                            onClick={() =>
                                                setActiveTab("ticket")
                                            }
                                        >
                                            Your Ticket
                                        </button>
                                    </li>
                                )}
                                <li className="nav-item">
                                    <button
                                        className={`nav-link border-0 bg-transparent fw-medium px-0 pb-3 ${
                                            activeTab === "rundown"
                                                ? "active text-theme border-bottom border-2 border-theme"
                                                : "text-muted"
                                        }`}
                                        onClick={() => setActiveTab("rundown")}
                                    >
                                        Rundown
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link border-0 bg-transparent fw-medium px-0 pb-3 ${
                                            activeTab === "speakers"
                                                ? "active text-theme border-bottom border-2 border-theme"
                                                : "text-muted"
                                        }`}
                                        onClick={() => setActiveTab("speakers")}
                                    >
                                        Speakers
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link border-0 bg-transparent fw-medium px-0 pb-3 ${
                                            activeTab === "documentation"
                                                ? "active text-theme border-bottom border-2 border-theme"
                                                : "text-muted"
                                        }`}
                                        onClick={() =>
                                            setActiveTab("documentation")
                                        }
                                    >
                                        Dokumentasi
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div className="card-body p-4">
                            {activeTab === "overview" && (
                                <div className="animate-fade">
                                    <h5 className="fw-bold mb-3">
                                        About This Event
                                    </h5>
                                    <div
                                        className="text-muted mb-4"
                                        dangerouslySetInnerHTML={{
                                            __html: event.description,
                                        }}
                                    ></div>
                                </div>
                            )}

                            {activeTab === "speakers" && (
                                <div className="animate-fade">
                                    {event.speakers &&
                                    event.speakers.length > 0 ? (
                                        <div className="row g-3">
                                            {event.speakers.map(
                                                (speaker, index) => (
                                                    <div
                                                        className="col-md-6"
                                                        key={index}
                                                    >
                                                        <div className="d-flex align-items-center bg-light p-3 rounded-10">
                                                            <div className="shrink-0 me-3">
                                                                <img
                                                                    src={
                                                                        speaker.image
                                                                            ? `/storage/${speaker.image}`
                                                                            : "/assets/img/icon/user.svg"
                                                                    }
                                                                    alt={
                                                                        speaker.name
                                                                    }
                                                                    className="rounded-circle object-fit-cover"
                                                                    style={{
                                                                        width: "60px",
                                                                        height: "60px",
                                                                    }}
                                                                />
                                                            </div>
                                                            <div>
                                                                <h6 className="mb-1 fw-bold">
                                                                    {
                                                                        speaker.name
                                                                    }
                                                                </h6>
                                                                <p className="mb-0 text-muted small">
                                                                    {
                                                                        speaker.role
                                                                    }
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                )
                                            )}
                                        </div>
                                    ) : (
                                        <div className="text-center py-5">
                                            <div className="mb-3">
                                                <i className="fa-solid fa-users-slash fa-3x text-muted opacity-25"></i>
                                            </div>
                                            <h6 className="fw-bold text-muted">
                                                No Speakers Announced
                                            </h6>
                                            <p className="text-muted small">
                                                Speaker information is not
                                                available for this event yet.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            )}

                            {activeTab === "ticket" && (
                                <div className="animate-fade text-center py-4">
                                    <div className="mb-4 bg-white p-3 d-inline-block rounded-3 shadow-sm border">
                                        <QRCodeCanvas
                                            value={
                                                ticket?.qr_url ||
                                                "INVALID-TICKET"
                                            }
                                            size={200}
                                            level={"H"}
                                            includeMargin={true}
                                        />
                                    </div>
                                    <h5 className="fw-bold mb-1">
                                        {auth.user.name}
                                    </h5>
                                    <div className="mb-4 d-flex justify-content-center">
                                        <p
                                            className="text-muted small text-center"
                                            style={{ maxWidth: "300px" }}
                                        >
                                            <i className="fa-solid fa-circle-info me-1"></i>
                                            Tunjukkan Kode QR ini kepada panitia
                                            acara untuk registrasi ulang saat
                                            event berlangsung.
                                        </p>
                                    </div>
                                    <div className="d-flex justify-content-center gap-3 mt-3">
                                        <div className="d-inline-flex align-items-center bg-white border rounded-pill px-4 py-2 shadow-sm">
                                            <div className="text-muted pe-3 border-end me-3">
                                                <i className="fa-solid fa-hashtag text-theme me-2"></i>
                                                Ticket ID
                                            </div>
                                            <div className="fw-bold text-dark">
                                                {ticket.code}
                                            </div>
                                        </div>

                                        <div className="d-inline-flex align-items-center bg-white border rounded-pill px-4 py-2 shadow-sm">
                                            <div className="text-muted pe-3 border-end me-3">
                                                <i className="fa-solid fa-ticket text-theme me-2"></i>
                                                Type
                                            </div>
                                            <div className="fw-bold text-dark">
                                                {event.price === "Free" ||
                                                event.price == 0
                                                    ? "Free Pass"
                                                    : "Regular Ticket"}
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mt-4 pt-4 border-top">
                                        <a
                                            href={route(
                                                "events.ticket.download",
                                                ticket.code
                                            )}
                                            className="th-btn sm rounded-pill"
                                        >
                                            <i className="fa-solid fa-download me-2"></i>
                                            Download Ticket PDF
                                        </a>
                                    </div>
                                </div>
                            )}

                            {activeTab === "rundown" && (
                                <div className="animate-fade">
                                    {event.schedule &&
                                    event.schedule.length > 0 ? (
                                        <DashboardTable
                                            columns={[
                                                {
                                                    label: (
                                                        <div className="d-flex align-items-center gap-2">
                                                            Time (WIB)
                                                        </div>
                                                    ),
                                                    className:
                                                        "col-4 col-md-3 ps-4 border-end",
                                                },
                                                {
                                                    label: "Agenda",
                                                    className:
                                                        "col-8 col-md-9 ps-4",
                                                },
                                            ]}
                                        >
                                            {event.schedule.map(
                                                (item, index) => (
                                                    <DashboardTableRow
                                                        key={index}
                                                    >
                                                        <div className="col-4 col-md-3 ps-4 border-end">
                                                            <div className="d-flex align-items-center text-nowrap">
                                                                <span
                                                                    className={`fw-bold fs-5 font-heading ${
                                                                        index ===
                                                                        0
                                                                            ? "text-success"
                                                                            : index ===
                                                                              event
                                                                                  .schedule
                                                                                  .length -
                                                                                  1
                                                                            ? "text-danger"
                                                                            : "text-dark"
                                                                    }`}
                                                                >
                                                                    {item.time}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div className="col-8 col-md-9 ps-4">
                                                            <div className="d-flex flex-column h-100 justify-content-center">
                                                                <div className="d-flex align-items-center gap-2 mb-1">
                                                                    <h6 className="fw-bold text-dark mb-0 fs-6">
                                                                        {
                                                                            item.activity
                                                                        }
                                                                    </h6>
                                                                </div>
                                                                {item.description && (
                                                                    <p className="text-muted small mb-0 opacity-75">
                                                                        {
                                                                            item.description
                                                                        }
                                                                    </p>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </DashboardTableRow>
                                                )
                                            )}
                                        </DashboardTable>
                                    ) : (
                                        <div className="text-center py-5">
                                            <div className="mb-3">
                                                <i className="fa-regular fa-calendar-xmark fa-3x text-muted opacity-25"></i>
                                            </div>
                                            <h6 className="fw-bold text-muted">
                                                Schedule Not Published
                                            </h6>
                                            <p className="text-muted small">
                                                The rundown for this event is
                                                not available yet.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            )}

                            {activeTab === "documentation" && (
                                <div className="animate-fade">
                                    {event.documentations &&
                                    event.documentations.length > 0 ? (
                                        <div className="row g-3">
                                            {event.documentations.map((doc) => (
                                                <div
                                                    className="col-md-4 col-sm-6"
                                                    key={doc.id}
                                                >
                                                    <div className="card border-0 shadow-sm h-100 rounded-10 overflow-hidden group-hover-scale">
                                                        <div
                                                            className="position-relative overflow-hidden"
                                                            style={{
                                                                paddingTop:
                                                                    "75%",
                                                            }}
                                                        >
                                                            {doc.file_path &&
                                                            doc.file_path
                                                                .length > 0 ? (
                                                                <img
                                                                    src={`/storage/${doc.file_path[0]}`}
                                                                    alt={
                                                                        doc.caption ||
                                                                        "Event Documentation"
                                                                    }
                                                                    className="position-absolute top-0 start-0 w-100 h-100 object-fit-cover transition-transform"
                                                                />
                                                            ) : (
                                                                <div className="position-absolute top-0 start-0 w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                                                                    <i className="fa-solid fa-image text-muted opacity-25 fa-2x"></i>
                                                                </div>
                                                            )}
                                                        </div>
                                                        {doc.caption && (
                                                            <div className="card-body p-3">
                                                                <p className="card-text small text-muted mb-0 text-truncate">
                                                                    {
                                                                        doc.caption
                                                                    }
                                                                </p>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="text-center py-5">
                                            <div className="mb-3">
                                                <i className="fa-solid fa-camera fa-3x text-muted opacity-25"></i>
                                            </div>
                                            <h6 className="fw-bold text-muted">
                                                No Documentation Yet
                                            </h6>
                                            <p className="text-muted small">
                                                Event documentation will be
                                                uploaded here after the event.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            )}

                            {activeTab === "certificate" &&
                                event.is_certificate_available && (
                                    <div className="animate-fade text-center py-5">
                                        {certificate ? (
                                            <div className="text-center">
                                                <div className="mb-4">
                                                    <i className="fa-solid fa-file-certificate fa-4x text-theme"></i>
                                                </div>
                                                <h4 className="fw-bold mb-3">
                                                    Your Cetrificate is Ready!
                                                </h4>
                                                <p className="text-muted mb-4">
                                                    Congratulations on
                                                    completing the event. You
                                                    can now download your
                                                    certificate.
                                                </p>
                                                <div className="d-flex justify-content-center gap-3">
                                                    <a
                                                        href={`/storage/${certificate.file_path}`}
                                                        target="_blank"
                                                        download
                                                        className="th-btn style3 th-radius"
                                                    >
                                                        <i className="fa-solid fa-download me-2"></i>
                                                        Download PDF
                                                    </a>
                                                </div>
                                            </div>
                                        ) : (
                                            <div className="text-center">
                                                <div className="mb-3">
                                                    <i className="fa-solid fa-award fa-3x text-theme opacity-50"></i>
                                                </div>
                                                <h6 className="fw-bold text-dark">
                                                    Certificate Available
                                                </h6>
                                                <p className="text-muted small mb-4">
                                                    You are eligible for a
                                                    certificate. Click below to
                                                    generate and claim it.
                                                </p>
                                                <button
                                                    onClick={handleClaim}
                                                    disabled={processing}
                                                    className="th-btn th-radius style4"
                                                >
                                                    {processing ? (
                                                        <>
                                                            <i className="fa-solid fa-spinner fa-spin me-2"></i>
                                                            Processing...
                                                        </>
                                                    ) : (
                                                        <>
                                                            <i className="fa-solid fa-file-signature me-2"></i>
                                                            Claim Certificate
                                                        </>
                                                    )}
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                )}
                        </div>
                    </div>
                </div>

                <div className="col-lg-4">
                    {/* Organizer Info */}
                    <div className="card border-0 shadow-sm rounded-20 mb-4">
                        <div className="card-body p-4">
                            <h6 className="fw-bold mb-3">Organizer</h6>
                            <div className="d-flex align-items-center gap-3">
                                <div
                                    className="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                    style={{ width: "50px", height: "50px" }}
                                >
                                    <i className="fa-solid fa-building text-muted"></i>
                                </div>
                                <div>
                                    <h6 className="mb-0 fw-semibold">
                                        {event.organizer_info?.name ||
                                            "ACTiV Organizer"}
                                    </h6>
                                    <small className="text-muted">
                                        Verified Organizer
                                    </small>
                                </div>
                            </div>
                            <hr className="my-3 opacity-10" />
                            <div className="text-center">
                                <a
                                    href={`https://wa.me/${
                                        event.organizer_info?.phone || ""
                                    }?text=${encodeURIComponent(
                                        `Halo Admin, saya ingin bertanya mengenai event "${event.title}"`
                                    )}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="th-btn th-radius style8 w-100"
                                >
                                    <i className="fa-brands fa-whatsapp me-2"></i>
                                    Contact Organizer
                                </a>
                            </div>
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

            <style>{`
                .element-with-gradient {
                    background: linear-gradient(
                        to top,
                        rgba(0, 0, 0, 0.8),
                        transparent
                    );
                }
                .bg-gradient-dark {
                    background: linear-gradient(
                        to top,
                        rgba(0, 0, 0, 0.9) 0%,
                        rgba(0, 0, 0, 0.4) 60%,
                        transparent 100%
                    );
                }
            `}</style>

            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type === "error" ? "danger" : toast?.type}
                onClose={() => setToast(null)}
            />
        </DashboardLayout>
    );
}
