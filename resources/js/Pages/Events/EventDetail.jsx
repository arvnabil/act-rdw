import React, { useState, useEffect } from "react";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

import Modal from "@/Components/Common/Modal";
import Toast from "@/Components/Common/Toast";

import { route } from "ziggy-js";

export default function EventDetail({ event }) {
    const { auth, flash } = usePage().props;
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [activeFaqIndex, setActiveFaqIndex] = useState(0);

    // Auth Mode: 'login' or 'register' (only used if !auth.user)
    const [authMode, setAuthMode] = useState("login");
    const [toast, setToast] = useState(null);

    // Login Form
    const loginForm = useForm({
        email: "",
        password: "",
    });

    // Register Form (also used for booking if !auth.user)
    const registerForm = useForm({
        name: "",
        email: "",
        phone: "",
        password: "",
        tickets: 1,
    });

    // Booking Form (for authenticated user confirmation)
    const bookingForm = useForm({
        tickets: 1,
    });

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

    const handleLogin = (e) => {
        e.preventDefault();
        loginForm.post(route("events.login"), {
            onSuccess: () => {
                // Inertia reloads, auth.user becomes available.
                // Modal stays open? Usually yes if state preserved, but Inertia reload might reset state.
                // If it resets, user clicks "Book Now" again and sees "Confirm".
                // We can assume user is happy.
                setToast({ message: "Login Berhasil!", type: "success" });
            },
            onError: () => {
                setToast({ message: "Login Failed", type: "danger" });
            },
        });
    };

    const handleRegister = (e) => {
        e.preventDefault();
        registerForm.post(route("events.join", event.slug), {
            onSuccess: () => {
                setIsModalOpen(false);
                registerForm.reset();
                setToast({ message: "You are registered!", type: "success" });
            },
        });
    };

    const handleBookingConfirm = (e) => {
        e.preventDefault();
        bookingForm.post(route("events.join", event.slug), {
            onSuccess: () => {
                setIsModalOpen(false);
                setToast({ message: "Booking Confirmed!", type: "success" });
            },
        });
    };

    return (
        <MainLayout>
            <Head title={event.title} />
            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type}
                onClose={() => setToast(null)}
            />

            {/* Event Hero Section */}
            <section className="space-top">
                <div className="container">
                    <div className="row">
                        <div className="col-12">
                            <div className="mb-3 d-flex align-items-center small">
                                <Link
                                    href="/"
                                    className="text-muted text-decoration-none"
                                >
                                    Home
                                </Link>
                                <i
                                    className="fa-solid fa-chevron-right text-muted mx-2"
                                    style={{ fontSize: "10px" }}
                                ></i>
                                <Link
                                    href="/events"
                                    className="text-muted text-decoration-none"
                                >
                                    Events
                                </Link>
                                <i
                                    className="fa-solid fa-chevron-right text-muted mx-2"
                                    style={{ fontSize: "10px" }}
                                ></i>
                                <span
                                    className="text-theme fw-medium text-truncate"
                                    style={{ maxWidth: "300px" }}
                                >
                                    {event.title}
                                </span>
                            </div>
                            <div className="event-hero-img position-relative rounded-20 overflow-hidden mb-60">
                                <img
                                    src={event.image}
                                    alt={event.title}
                                    className="w-100"
                                    style={{
                                        maxHeight: "600px",
                                        objectFit: "cover",
                                    }}
                                />
                                <div
                                    className="event-date-box"
                                    style={{
                                        position: "absolute",
                                        bottom: "30px",
                                        left: "30px",
                                        background: "#fff",
                                        padding: "15px 25px",
                                        borderRadius: "12px",
                                        boxShadow:
                                            "0 10px 30px rgba(0,0,0,0.15)",
                                        textAlign: "center",
                                    }}
                                >
                                    <span
                                        className="d-block text-theme fw-bold"
                                        style={{
                                            fontSize: "32px",
                                            lineHeight: "1",
                                        }}
                                    >
                                        {event.date.day}
                                    </span>
                                    <span
                                        className="d-block text-uppercase fw-medium"
                                        style={{
                                            fontSize: "14px",
                                            letterSpacing: "1px",
                                        }}
                                    >
                                        {event.date.month}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="space-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-8">
                            <div className="event-details-content">
                                <div className="meta-tags mb-30">
                                    <span className="th-btn style4 sm-btn me-2">
                                        {event.category}
                                    </span>
                                </div>
                                <h2 className="entry-title mb-30">
                                    {event.title}
                                </h2>
                                <div
                                    className="mb-40 text-lg description-content"
                                    dangerouslySetInnerHTML={{
                                        __html: event.description,
                                    }}
                                />

                                <h3 className="h4 mb-20">Event Schedule</h3>
                                <div
                                    className="event-schedule mb-40"
                                    style={{
                                        border: "1px solid #eee",
                                        borderRadius: "15px",
                                        overflow: "hidden",
                                    }}
                                >
                                    {event.schedule.map((item, index) => (
                                        <div
                                            className="schedule-item d-flex align-items-center"
                                            key={index}
                                            style={{
                                                padding: "20px",
                                                borderBottom:
                                                    index !==
                                                    event.schedule.length - 1
                                                        ? "1px solid #eee"
                                                        : "none",
                                                backgroundColor:
                                                    index % 2 === 0
                                                        ? "#f9f9f9"
                                                        : "#fff",
                                            }}
                                        >
                                            <div
                                                className="time text-theme fw-bold"
                                                style={{ minWidth: "120px" }}
                                            >
                                                {item.time} WIB
                                            </div>
                                            <div className="activity fw-medium">
                                                {item.activity}
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <h3 className="h4 mb-4">Featured Speakers</h3>
                                <div className="row gy-4 mb-40">
                                    {event.speakers.map((speaker, index) => (
                                        <div
                                            className="col-md-4 col-sm-6"
                                            key={index}
                                        >
                                            <div
                                                className="speaker-card text-center bg-white rounded-20 h-100 position-relative"
                                                style={{
                                                    padding: "30px 20px",
                                                    boxShadow:
                                                        "0 10px 30px rgba(0,0,0,0.03)",
                                                    transition: "all 0.3s ease",
                                                    border: "1px solid #f0f0f0",
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.currentTarget.style.transform =
                                                        "translateY(-10px)";
                                                    e.currentTarget.style.boxShadow =
                                                        "0 20px 40px rgba(74, 193, 94, 0.15)";
                                                    e.currentTarget.style.borderColor =
                                                        "var(--theme-color)";
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.currentTarget.style.transform =
                                                        "translateY(0)";
                                                    e.currentTarget.style.boxShadow =
                                                        "0 10px 30px rgba(0,0,0,0.03)";
                                                    e.currentTarget.style.borderColor =
                                                        "#f0f0f0";
                                                }}
                                            >
                                                <div
                                                    className="img-wrap rounded-circle overflow-hidden mx-auto mb-4 position-relative d-flex align-items-center justify-content-center bg-light"
                                                    style={{
                                                        width: "100px",
                                                        height: "100px",
                                                        border: "3px solid #fff",
                                                        boxShadow:
                                                            "0 5px 15px rgba(0,0,0,0.1)",
                                                    }}
                                                >
                                                    {speaker.image ? (
                                                        <img
                                                            src={speaker.image}
                                                            alt={speaker.name}
                                                            className="w-100 h-100 object-fit-cover"
                                                        />
                                                    ) : (
                                                        <i className="fa-solid fa-user fs-1 text-secondary opacity-50"></i>
                                                    )}
                                                </div>
                                                <h5 className="h6 mb-2 fw-bold text-dark">
                                                    {speaker.name}
                                                </h5>
                                                <p
                                                    className="text-sm text-theme mb-3 fw-medium text-uppercase"
                                                    style={{
                                                        fontSize: "12px",
                                                        letterSpacing: "1px",
                                                    }}
                                                >
                                                    {speaker.role}
                                                </p>

                                                <div className="social-links d-flex justify-content-center gap-2 opacity-0-hover show-on-hover">
                                                    {speaker.linkedin_link && (
                                                        <a
                                                            href={
                                                                speaker.linkedin_link
                                                            }
                                                            target="_blank"
                                                            rel="noreferrer"
                                                            className="rounded-circle d-flex align-items-center justify-content-center text-muted hover-theme"
                                                            style={{
                                                                width: "32px",
                                                                height: "32px",
                                                                background:
                                                                    "#f5f5f5",
                                                                transition:
                                                                    "0.2s",
                                                            }}
                                                        >
                                                            <i className="fa-brands fa-linkedin-in"></i>
                                                        </a>
                                                    )}
                                                    {speaker.instagram_link && (
                                                        <a
                                                            href={
                                                                speaker.instagram_link
                                                            }
                                                            target="_blank"
                                                            rel="noreferrer"
                                                            className="rounded-circle d-flex align-items-center justify-content-center text-muted hover-theme"
                                                            style={{
                                                                width: "32px",
                                                                height: "32px",
                                                                background:
                                                                    "#f5f5f5",
                                                                transition:
                                                                    "0.2s",
                                                            }}
                                                        >
                                                            <i className="fa-brands fa-instagram"></i>
                                                        </a>
                                                    )}
                                                    {speaker.tiktok_link && (
                                                        <a
                                                            href={
                                                                speaker.tiktok_link
                                                            }
                                                            target="_blank"
                                                            rel="noreferrer"
                                                            className="rounded-circle d-flex align-items-center justify-content-center text-muted hover-theme"
                                                            style={{
                                                                width: "32px",
                                                                height: "32px",
                                                                background:
                                                                    "#f5f5f5",
                                                                transition:
                                                                    "0.2s",
                                                            }}
                                                        >
                                                            <i className="fa-brands fa-tiktok"></i>
                                                        </a>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <h3 className="h4 mb-4">Dokumentasi Event</h3>
                                <div className="documentation-section mb-60">
                                    {(() => {
                                        const photos = [];
                                        const materials = [];

                                        if (event.documentations) {
                                            event.documentations.forEach(
                                                (doc) => {
                                                    if (doc.type === "image") {
                                                        doc.file_paths.forEach(
                                                            (path) => {
                                                                photos.push({
                                                                    path,
                                                                    caption:
                                                                        doc.caption,
                                                                });
                                                            }
                                                        );
                                                    } else {
                                                        doc.file_paths.forEach(
                                                            (path) => {
                                                                materials.push({
                                                                    type: doc.type,
                                                                    path,
                                                                    caption:
                                                                        doc.caption,
                                                                });
                                                            }
                                                        );
                                                    }
                                                }
                                            );
                                        }

                                        const hasContent =
                                            photos.length > 0 ||
                                            materials.length > 0;

                                        if (!hasContent) {
                                            return (
                                                <div
                                                    className="alert alert-info d-flex align-items-center"
                                                    role="alert"
                                                    style={{
                                                        backgroundColor:
                                                            "#e3f2fd",
                                                        color: "#0d47a1",
                                                        border: "none",
                                                        borderRadius: "12px",
                                                    }}
                                                >
                                                    <i className="fa-solid fa-circle-info me-2 fs-5"></i>
                                                    <div>
                                                        Belum ada foto yang
                                                        diunggah oleh pengelola
                                                        Event.
                                                    </div>
                                                </div>
                                            );
                                        }

                                        return (
                                            <>
                                                {/* Photos Section */}
                                                {photos.length > 0 && (
                                                    <div className="mb-5">
                                                        <h5 className="mb-4 fw-bold">
                                                            Foto Kegiatan
                                                        </h5>
                                                        <div className="row g-4">
                                                            {photos.map(
                                                                (
                                                                    photo,
                                                                    index
                                                                ) => (
                                                                    <div
                                                                        key={`photo-${index}`}
                                                                        className="col-md-6"
                                                                    >
                                                                        <div className="documentation-item position-relative rounded-20 overflow-hidden group-image-scale transition-all duration-500">
                                                                            <a
                                                                                href={
                                                                                    photo.path
                                                                                }
                                                                                target="_blank"
                                                                                rel="noreferrer"
                                                                                className="d-block w-100"
                                                                            >
                                                                                <img
                                                                                    src={
                                                                                        photo.path
                                                                                    }
                                                                                    alt={
                                                                                        photo.caption ||
                                                                                        "Event Documentation"
                                                                                    }
                                                                                    className="w-100 object-fit-cover transition-transform duration-500"
                                                                                    style={{
                                                                                        height: "250px",
                                                                                        objectFit:
                                                                                            "cover",
                                                                                    }}
                                                                                />
                                                                            </a>
                                                                            {photo.caption && (
                                                                                <div className="position-absolute bottom-0 start-0 w-100 p-2 bg-dark bg-opacity-75 text-white text-center small pointer-events-none">
                                                                                    {
                                                                                        photo.caption
                                                                                    }
                                                                                </div>
                                                                            )}
                                                                        </div>
                                                                    </div>
                                                                )
                                                            )}
                                                        </div>
                                                    </div>
                                                )}

                                                {/* Materials Section */}
                                                {materials.length > 0 && (
                                                    <div className="mb-4">
                                                        <h5 className="mb-4 fw-bold">
                                                            Materi & Presentasi
                                                        </h5>
                                                        <div className="row g-4">
                                                            {materials.map(
                                                                (
                                                                    item,
                                                                    index
                                                                ) => (
                                                                    <div
                                                                        key={`mat-${index}`}
                                                                        className="col-md-6"
                                                                    >
                                                                        <div className="documentation-item position-relative rounded-20 overflow-hidden group-image-scale transition-all duration-500">
                                                                            {item.type ===
                                                                            "video_link" ? (
                                                                                <div className="ratio ratio-16x9">
                                                                                    <iframe
                                                                                        src={item.path.replace(
                                                                                            "watch?v=",
                                                                                            "embed/"
                                                                                        )}
                                                                                        title={
                                                                                            item.caption
                                                                                        }
                                                                                        allowFullScreen
                                                                                        className="rounded-20"
                                                                                    ></iframe>
                                                                                </div>
                                                                            ) : (
                                                                                <div className="p-4 border rounded-20 text-center h-100 d-flex flex-column justify-content-center align-items-center bg-light">
                                                                                    <i className="fa-solid fa-file-pdf fs-1 text-danger mb-3"></i>
                                                                                    <h6 className="mb-2 text-truncate w-100">
                                                                                        {item.caption ||
                                                                                            "Document"}
                                                                                    </h6>
                                                                                    <a
                                                                                        href={
                                                                                            item.path
                                                                                        }
                                                                                        target="_blank"
                                                                                        rel="noreferrer"
                                                                                        className="th-btn style4 sm-btn mt-2"
                                                                                    >
                                                                                        Download
                                                                                    </a>
                                                                                </div>
                                                                            )}
                                                                        </div>
                                                                    </div>
                                                                )
                                                            )}
                                                        </div>
                                                    </div>
                                                )}
                                            </>
                                        );
                                    })()}
                                </div>

                                <h3 className="h4 mb-4">
                                    FAQ - Sering Ditanyakan
                                </h3>
                                {/* FAQ Section with React State for Collapse */}
                                <div className="accordion" id="faqAccordion">
                                    {(event.faq && event.faq.length > 0
                                        ? event.faq
                                        : [
                                              {
                                                  question:
                                                      "Bagaimana cara mendaftar event ini?",
                                                  answer: "Anda dapat mendaftar dengan mengklik tombol 'Booking Now' di bagian kanan halaman ini, lalu isi formulir pendaftaran secara lengkap.",
                                              },
                                              {
                                                  question:
                                                      "Apakah event ini berbayar?",
                                                  answer: `Event ini ${
                                                      event.price === "Free"
                                                          ? "Gratis"
                                                          : "Berbayar sebesar " +
                                                            event.price
                                                  }. Silakan cek detail booking untuk informasi lebih lanjut.`,
                                              },
                                          ]
                                    ).map((item, i) => {
                                        const isActive = activeFaqIndex === i;
                                        return (
                                            <div
                                                className={`accordion-card style4 ${
                                                    isActive ? "active" : ""
                                                }`}
                                                key={i}
                                            >
                                                <div
                                                    className="accordion-header"
                                                    id={`collapse-item-${i}`}
                                                >
                                                    <button
                                                        className={`accordion-button ${
                                                            isActive
                                                                ? ""
                                                                : "collapsed"
                                                        }`}
                                                        type="button"
                                                        onClick={() =>
                                                            setActiveFaqIndex(
                                                                isActive
                                                                    ? null
                                                                    : i
                                                            )
                                                        }
                                                        aria-expanded={isActive}
                                                        aria-controls={`collapse-${i}`}
                                                    >
                                                        {item.question}
                                                    </button>
                                                </div>
                                                <div
                                                    id={`collapse-${i}`}
                                                    className={`accordion-collapse collapse ${
                                                        isActive ? "show" : ""
                                                    }`}
                                                    aria-labelledby={`collapse-item-${i}`}
                                                    data-bs-parent="#faqAccordion"
                                                >
                                                    <div className="accordion-body">
                                                        <p className="faq-text">
                                                            {item.answer}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-4">
                            <aside
                                className="sidebar-area pt-3"
                                style={{ position: "sticky", top: "100px" }}
                            >
                                {/* Participation Widget */}
                                <div
                                    className="widget widget-participation p-4 rounded-20 mb-30"
                                    style={{
                                        backgroundColor: "#fff",
                                        border: "1px solid #eee",
                                    }}
                                >
                                    <h3 className="widget_title">
                                        Pendaftaran
                                    </h3>
                                    {event.is_finished ? (
                                        <div className="alert alert-danger rounded-10 mb-0 text-center fw-bold">
                                            Event Selesai
                                        </div>
                                    ) : (
                                        <>
                                            <div className="d-flex mb-3 align-items-center">
                                                <div
                                                    className="icon-box me-3 text-theme"
                                                    style={{ fontSize: "20px" }}
                                                >
                                                    <i className="fa-regular fa-ticket"></i>
                                                </div>
                                                <div>
                                                    <small
                                                        className="text-muted d-block text-uppercase"
                                                        style={{
                                                            fontSize: "11px",
                                                            letterSpacing:
                                                                "1px",
                                                            fontWeight: "600",
                                                        }}
                                                    >
                                                        Pricing
                                                    </small>
                                                    <span className="d-block fw-bold text-dark fs-5">
                                                        {event.price ===
                                                            "Free" ||
                                                        event.price === 0 ||
                                                        event.price === "0"
                                                            ? "Free"
                                                            : event.price}
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="d-flex mb-3 align-items-center">
                                                <div
                                                    className="icon-box me-3 text-theme"
                                                    style={{ fontSize: "20px" }}
                                                >
                                                    <i className="fa-regular fa-users"></i>
                                                </div>
                                                <div>
                                                    <small
                                                        className="text-muted d-block text-uppercase"
                                                        style={{
                                                            fontSize: "11px",
                                                            letterSpacing:
                                                                "1px",
                                                            fontWeight: "600",
                                                        }}
                                                    >
                                                        Sisa Kuota:
                                                    </small>
                                                    <span className="d-block fw-bold text-dark fs-5">
                                                        {event.available_seats >
                                                        0
                                                            ? `${event.available_seats} Seats`
                                                            : "Full"}
                                                    </span>
                                                </div>
                                            </div>
                                            {event.available_seats === 0 ? (
                                                <button
                                                    disabled
                                                    className="th-btn th-radius th-icon w-100 bg-secondary border-secondary"
                                                    style={{
                                                        cursor: "not-allowed",
                                                        opacity: 0.8,
                                                    }}
                                                >
                                                    Pendaftaran Penuh{" "}
                                                    <i className="fa-solid fa-ban"></i>
                                                </button>
                                            ) : (
                                                <button
                                                    className="th-btn th-radius th-icon w-100"
                                                    onClick={() =>
                                                        setIsModalOpen(true)
                                                    }
                                                >
                                                    Daftar Sekarang{" "}
                                                    <i className="fa-solid fa-ticket"></i>
                                                </button>
                                            )}
                                        </>
                                    )}
                                </div>

                                {/* Schedule Widget */}
                                <div
                                    className="widget widget-schedule p-4 rounded-20 mb-30"
                                    style={{
                                        backgroundColor: "#fff",
                                        border: "1px solid #eee",
                                    }}
                                >
                                    <h3 className="widget_title">
                                        Jadwal Pelaksanaan
                                    </h3>
                                    <div className="schedule-list">
                                        <div className="d-flex mb-3">
                                            <div
                                                className="icon-box me-3 text-theme"
                                                style={{ fontSize: "20px" }}
                                            >
                                                <i className="fa-regular fa-calendar-check"></i>
                                            </div>
                                            <div>
                                                <small
                                                    className="d-block text-muted text-uppercase mb-1"
                                                    style={{
                                                        fontSize: "11px",
                                                        letterSpacing: "1px",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    Mulai
                                                </small>
                                                <span className="d-block fw-bold text-dark">
                                                    {event.start_time_full}
                                                </span>
                                            </div>
                                        </div>
                                        <div className="d-flex">
                                            <div
                                                className="icon-box me-3 text-theme"
                                                style={{ fontSize: "20px" }}
                                            >
                                                <i className="fa-regular fa-calendar-xmark"></i>
                                            </div>
                                            <div>
                                                <small
                                                    className="d-block text-muted text-uppercase mb-1"
                                                    style={{
                                                        fontSize: "11px",
                                                        letterSpacing: "1px",
                                                        fontWeight: "600",
                                                    }}
                                                >
                                                    Selesai
                                                </small>
                                                <span className="d-block fw-bold text-dark">
                                                    {event.end_time_full}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Location Widget */}
                                <div
                                    className="widget widget-location p-4 rounded-20 mb-30"
                                    style={{
                                        backgroundColor: "#fff",
                                        border: "1px solid #eee",
                                    }}
                                >
                                    <h3 className="widget_title">Lokasi</h3>
                                    <div className="d-flex mb-3">
                                        <div
                                            className="icon-box me-3 text-theme"
                                            style={{ fontSize: "20px" }}
                                        >
                                            <i className="fa-regular fa-location-dot"></i>
                                        </div>
                                        <div>
                                            <span className="d-block fw-bold text-dark">
                                                {event.location}
                                            </span>
                                            {event.location === "Online" && (
                                                <small className="text-muted">
                                                    LIVE at{" "}
                                                    {event.meeting_link ? (
                                                        "Zoom Meeting" // Do not show link
                                                    ) : event.youtube_link ? (
                                                        <a
                                                            href={
                                                                event.youtube_link
                                                            }
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            className="text-theme fw-bold"
                                                        >
                                                            YouTube ACTiV
                                                            Teknologi
                                                        </a>
                                                    ) : (
                                                        "Online Platform"
                                                    )}
                                                </small>
                                            )}
                                        </div>
                                    </div>
                                    {/* Embed Map moved here */}
                                    {event.location !== "Online" &&
                                        event.map_url && (
                                            <div className="event-map rounded-10 overflow-hidden mt-3">
                                                <iframe
                                                    src={event.map_url}
                                                    width="100%"
                                                    loading="lazy"
                                                ></iframe>
                                            </div>
                                        )}
                                </div>

                                <div className="share-links clearfix box-shadow p-30 mb-60 rounded-20">
                                    <h3 className="widget_title">Organizer</h3>
                                    <div className="organizer-info d-flex align-items-center mb-20">
                                        <div
                                            className="org-img me-3 rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center"
                                            style={{
                                                width: "50px",
                                                height: "50px",
                                            }}
                                        >
                                            {event.organizer_logo ? (
                                                <img
                                                    src={event.organizer_logo}
                                                    alt="Organizer"
                                                    className="w-100 h-100 object-fit-cover"
                                                />
                                            ) : (
                                                <i className="fa-solid fa-users text-secondary"></i>
                                            )}
                                        </div>
                                        <div>
                                            <h4 className="h6 mb-0">
                                                {event.organizer}
                                            </h4>
                                            <a
                                                href={`mailto:${event.organizer_email}`}
                                                className="text-xs"
                                            >
                                                {event.organizer_email}
                                            </a>
                                        </div>
                                    </div>
                                    <a
                                        href={`tel:${event.organizer_phone}`}
                                        className="th-btn style4 w-100"
                                    >
                                        <i className="fa-brands fa-whatsapp me-2"></i>
                                        Contact via Whatsapp
                                    </a>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>

            {/* Booking Modal */}
            <Modal show={isModalOpen} onClose={() => setIsModalOpen(false)}>
                <div className="text-center mb-4">
                    <span className="sub-title text-theme mb-2 d-block">
                        {event.title}
                    </span>
                    <h3 className="box-title mb-0">Reserve Your Spot</h3>
                    <div
                        className="divider mx-auto mt-3"
                        style={{
                            height: "2px",
                            width: "50px",
                            background: "var(--theme-color)",
                        }}
                    ></div>
                </div>

                {auth?.user ? (
                    <div className="authenticted-booking text-center">
                        <div className="alert alert-success d-inline-block px-4 py-2 rounded-pill mb-4">
                            <i className="fa-solid fa-user-check me-2"></i>
                            Logged in as <strong>{auth.user.name}</strong>
                        </div>
                        <p className="text-muted mb-4">
                            Click below to confirm your registration for{" "}
                            <strong>{event.title}</strong>.
                        </p>
                        <form onSubmit={handleBookingConfirm}>
                            <button
                                type="submit"
                                disabled={bookingForm.processing}
                                className="th-btn w-100"
                            >
                                {bookingForm.processing ? (
                                    <>
                                        <span
                                            className="spinner-border spinner-border-sm me-2"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>
                                        Processing...
                                    </>
                                ) : (
                                    <>
                                        Confirm Booking{" "}
                                        <i className="fa-solid fa-check-circle ms-2"></i>
                                    </>
                                )}
                            </button>
                        </form>
                    </div>
                ) : (
                    <div className="guest-booking">
                        {/* Auth Toggle */}
                        <div
                            className="nav nav-pills nav-fill mb-4 p-1 bg-light rounded-pill"
                            role="tablist"
                        >
                            <button
                                className={`nav-link rounded-pill border-0 ${
                                    authMode === "login"
                                        ? "active bg-theme text-white shadow"
                                        : "text-muted"
                                }`}
                                onClick={() => setAuthMode("login")}
                                style={{ transition: "all 0.3s ease" }}
                            >
                                Login
                            </button>
                            <button
                                className={`nav-link rounded-pill border-0 ${
                                    authMode === "register"
                                        ? "active bg-theme text-white shadow"
                                        : "text-muted"
                                }`}
                                onClick={() => setAuthMode("register")}
                                style={{ transition: "all 0.3s ease" }}
                            >
                                Register
                            </button>
                        </div>

                        {authMode === "login" ? (
                            <form onSubmit={handleLogin} className="login-form">
                                <div className="mb-3 text-start">
                                    <label className="form-label fw-medium small text-uppercase">
                                        Email Address
                                    </label>
                                    <div className="input-group">
                                        <span className="input-group-text bg-light border-end-0">
                                            <i className="fa-regular fa-envelope text-muted"></i>
                                        </span>
                                        <input
                                            type="email"
                                            className="form-control border-start-0 ps-0 bg-light"
                                            placeholder="name@example.com"
                                            value={loginForm.data.email}
                                            onChange={(e) =>
                                                loginForm.setData(
                                                    "email",
                                                    e.target.value
                                                )
                                            }
                                            required
                                        />
                                    </div>
                                    {loginForm.errors.email && (
                                        <small className="text-danger">
                                            {loginForm.errors.email}
                                        </small>
                                    )}
                                </div>
                                <div className="mb-4 text-start">
                                    <label className="form-label fw-medium small text-uppercase">
                                        Password
                                    </label>
                                    <div className="input-group">
                                        <span className="input-group-text bg-light border-end-0">
                                            <i className="fa-regular fa-lock text-muted"></i>
                                        </span>
                                        <input
                                            type="password"
                                            className="form-control border-start-0 ps-0 bg-light"
                                            placeholder="Enter your password"
                                            value={loginForm.data.password}
                                            onChange={(e) =>
                                                loginForm.setData(
                                                    "password",
                                                    e.target.value
                                                )
                                            }
                                            required
                                        />
                                    </div>
                                    {loginForm.errors.password && (
                                        <small className="text-danger">
                                            {loginForm.errors.password}
                                        </small>
                                    )}
                                </div>
                                <button
                                    type="submit"
                                    disabled={loginForm.processing}
                                    className="th-btn w-100"
                                >
                                    {loginForm.processing
                                        ? "Logging in..."
                                        : "Login"}
                                </button>
                            </form>
                        ) : (
                            <form
                                onSubmit={handleRegister}
                                className="register-form"
                            >
                                <div className="mb-3 text-start">
                                    <label className="form-label fw-medium small text-uppercase">
                                        Full Name
                                    </label>
                                    <input
                                        type="text"
                                        className="form-control bg-light"
                                        placeholder="John Doe"
                                        value={registerForm.data.name}
                                        onChange={(e) =>
                                            registerForm.setData(
                                                "name",
                                                e.target.value
                                            )
                                        }
                                        required
                                    />
                                    {registerForm.errors.name && (
                                        <small className="text-danger">
                                            {registerForm.errors.name}
                                        </small>
                                    )}
                                </div>
                                <div className="mb-3 text-start">
                                    <label className="form-label fw-medium small text-uppercase">
                                        Email Address
                                    </label>
                                    <input
                                        type="email"
                                        className="form-control bg-light"
                                        placeholder="name@example.com"
                                        value={registerForm.data.email}
                                        onChange={(e) =>
                                            registerForm.setData(
                                                "email",
                                                e.target.value
                                            )
                                        }
                                        required
                                    />
                                    {registerForm.errors.email && (
                                        <small className="text-danger">
                                            {registerForm.errors.email}
                                        </small>
                                    )}
                                </div>
                                <div className="mb-3 text-start">
                                    <label className="form-label fw-medium small text-uppercase">
                                        Phone Number (WhatsApp)
                                    </label>
                                    <input
                                        type="tel"
                                        className="form-control bg-light"
                                        placeholder="+62..."
                                        value={registerForm.data.phone}
                                        onChange={(e) =>
                                            registerForm.setData(
                                                "phone",
                                                e.target.value
                                            )
                                        }
                                    />
                                    {registerForm.errors.phone && (
                                        <small className="text-danger">
                                            {registerForm.errors.phone}
                                        </small>
                                    )}
                                </div>
                                <div className="mb-4 text-start">
                                    <label className="form-label fw-medium small text-uppercase">
                                        Create Password
                                    </label>
                                    <input
                                        type="password"
                                        className="form-control bg-light"
                                        placeholder="Min 8 characters"
                                        value={registerForm.data.password}
                                        onChange={(e) =>
                                            registerForm.setData(
                                                "password",
                                                e.target.value
                                            )
                                        }
                                        required
                                    />
                                    <div className="form-text text-xs">
                                        Password for your Dashboard login.
                                    </div>
                                    {registerForm.errors.password && (
                                        <small className="text-danger">
                                            {registerForm.errors.password}
                                        </small>
                                    )}
                                </div>
                                <button
                                    type="submit"
                                    disabled={registerForm.processing}
                                    className="th-btn w-100"
                                >
                                    {registerForm.processing
                                        ? "Registering..."
                                        : "Confirm & Register"}
                                </button>
                            </form>
                        )}
                        <div className="mt-4 text-center">
                            <small className="text-muted">
                                {authMode === "login"
                                    ? "Don't have an account?"
                                    : "Already have an account?"}
                                <button
                                    className="btn btn-link p-0 ms-1 fw-bold text-decoration-none text-theme align-baseline"
                                    onClick={() =>
                                        setAuthMode(
                                            authMode === "login"
                                                ? "register"
                                                : "login"
                                        )
                                    }
                                >
                                    {authMode === "login"
                                        ? "Register"
                                        : "Login"}
                                </button>
                            </small>
                        </div>
                    </div>
                )}
            </Modal>
        </MainLayout>
    );
}
