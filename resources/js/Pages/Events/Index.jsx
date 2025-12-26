import React, { useState } from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

export default function Events({ upcomingEvents, pastEvents }) {
    return (
        <MainLayout>
            <Head title="Events" />

            {/* ... keeping Hero Section ... */}
            <section
                className="space position-relative overflow-hidden"
                style={{
                    background: "linear-gradient(to right, #f8f9fa, #fff)",
                }}
            >
                <div className="container">
                    <div className="row align-items-center">
                        <div className="col-lg-6 mb-5 mb-lg-0">
                            <h1 className="display-4 fw-bold mb-4 text-dark">
                                Expand Your{" "}
                                <span className="text-theme">Network</span>,
                                <br />
                                Master New{" "}
                                <span className="text-theme">Skills</span>.
                            </h1>
                            <p
                                className="fs-5 text-muted mb-5"
                                style={{ maxWidth: "500px" }}
                            >
                                Join our community of developers and innovators.
                                Attend exclusive workshops, webinars, and
                                conferences designed to level up your career.
                            </p>
                            <div className="d-flex gap-3">
                                <Link
                                    href="/events/list"
                                    className="th-btn style4"
                                >
                                    Browse All Events{" "}
                                    <i className="fa-regular fa-arrow-right ms-2"></i>
                                </Link>
                            </div>
                        </div>
                        <div className="col-lg-6">
                            <div className="position-relative">
                                <div
                                    className="rounded-20 overflow-hidden shadow-lg position-relative"
                                    style={{
                                        transform: "rotate(-2deg)",
                                        border: "5px solid #fff",
                                    }}
                                >
                                    <img
                                        src="/assets/img/blog/blog-s-1-1.jpg"
                                        alt="Event Hero"
                                        className="w-100"
                                    />
                                </div>
                                {/* Floating Badge */}
                                <div
                                    className="position-absolute bg-white shadow p-3 rounded-20 bounce-slide"
                                    style={{
                                        bottom: "-30px",
                                        left: "-30px",
                                        maxWidth: "200px",
                                    }}
                                >
                                    <div className="d-flex align-items-center gap-3">
                                        <div
                                            className="icon-box bg-theme text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style={{
                                                width: "50px",
                                                height: "50px",
                                            }}
                                        >
                                            <i className="fa-regular fa-calendar-check fa-lg"></i>
                                        </div>
                                        <div>
                                            <h6 className="mb-0 fw-bold">
                                                100+
                                            </h6>
                                            <p className="mb-0 small text-muted">
                                                Events Yearly
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Benefits Section (kept as is) */}
            <section className="space-bottom space">
                <div className="container">
                    <div className="row justify-content-center text-center mb-5">
                        <div className="col-lg-8">
                            <h2 className="fw-bold mb-3">
                                Why Join Our Events?
                            </h2>
                            <p className="text-muted">
                                Designed for growth, connection, and
                                inspiration.
                            </p>
                        </div>
                    </div>
                    <div className="row gy-4">
                        {[
                            {
                                icon: "fa-chalkboard-user",
                                title: "Expert-Led Workshops",
                                desc: "Learn directly from industry leaders and experienced practitioners.",
                            },
                            {
                                icon: "fa-handshake",
                                title: "Networking Opportunities",
                                desc: "Connect with like-minded professionals and expand your circle.",
                            },
                            {
                                icon: "fa-certificate",
                                title: "Certified Learning",
                                desc: "Earn certificates to validate your new skills and knowledge.",
                            },
                        ].map((benefit, index) => (
                            <div className="col-md-4" key={index}>
                                <div
                                    className="text-center p-4 h-100 rounded-20 hover-card-shadow"
                                    style={{
                                        backgroundColor: "#fff",
                                        border: "1px solid #eee",
                                        transition: "0.3s",
                                    }}
                                >
                                    <div
                                        className="d-inline-flex align-items-center justify-content-center bg-light text-theme rounded-circle mb-4"
                                        style={{
                                            width: "80px",
                                            height: "80px",
                                            fontSize: "32px",
                                        }}
                                    >
                                        <i
                                            className={`fa-regular ${benefit.icon}`}
                                        ></i>
                                    </div>
                                    <h4 className="h5 fw-bold mb-3">
                                        {benefit.title}
                                    </h4>
                                    <p className="text-muted mb-0">
                                        {benefit.desc}
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Upcoming Events Preview */}
            <section className="space bg-light">
                <div className="container">
                    <div className="text-center mb-5">
                        <h2 className="fw-bold mb-2">Upcoming Events</h2>
                        <p className="text-muted mb-0">
                            Don't miss out on what's happening next.
                        </p>
                    </div>

                    <div className="row gy-30">
                        {upcomingEvents.length > 0 ? (
                            upcomingEvents.map((event) => {
                                const isFull = event.available_seats <= 0;
                                const daysLeft = event.days_remaining;
                                const organizer = event.organizer;

                                return (
                                    <div
                                        className="col-xl-4 col-md-6"
                                        key={event.id}
                                    >
                                        <div
                                            className="event-card bg-white h-100 d-flex flex-column"
                                            style={{
                                                borderRadius: "16px",
                                                overflow: "hidden",
                                                transition: "all 0.3s ease",
                                                boxShadow:
                                                    "0 10px 30px rgba(0,0,0,0.05)",
                                                border: "1px solid #f0f0f0",
                                            }}
                                        >
                                            {/* Clickable Image Area */}
                                            <Link
                                                href={event.link}
                                                className="event-img position-relative d-block overflow-hidden"
                                            >
                                                <img
                                                    src={event.image}
                                                    alt={event.title}
                                                    className="w-100"
                                                    style={{
                                                        height: "220px",
                                                        objectFit: "cover",
                                                        transition:
                                                            "transform 0.5s ease",
                                                    }}
                                                    onMouseOver={(e) =>
                                                        (e.target.style.transform =
                                                            "scale(1.05)")
                                                    }
                                                    onMouseOut={(e) =>
                                                        (e.target.style.transform =
                                                            "scale(1)")
                                                    }
                                                />
                                            </Link>

                                            <div className="event-content d-flex flex-column flex-grow-1 p-4">
                                                {/* Category Badge */}
                                                <div className="mb-3">
                                                    <span
                                                        className="d-inline-block px-3 py-1 fw-medium text-dark"
                                                        style={{
                                                            border: "1px solid #e0e0e0",
                                                            borderRadius: "8px",
                                                            fontSize: "12px",
                                                            backgroundColor:
                                                                "#fff",
                                                        }}
                                                    >
                                                        {event.category ||
                                                            "Seminar"}
                                                    </span>
                                                </div>

                                                {/* Title */}
                                                <h3
                                                    className="event-title h5 mb-2 fw-bold"
                                                    style={{
                                                        lineHeight: "1.4",
                                                    }}
                                                >
                                                    <Link
                                                        href={event.link}
                                                        className="text-decoration-none"
                                                        style={{
                                                            color: "var(--theme-color)",
                                                            transition: "0.2s",
                                                        }}
                                                    >
                                                        {event.title}
                                                    </Link>
                                                </h3>

                                                {/* Organizer */}
                                                <p
                                                    className="text-muted small mb-3"
                                                    style={{ fontSize: "13px" }}
                                                >
                                                    oleh:{" "}
                                                    <span className="fw-medium text-dark">
                                                        {organizer}
                                                    </span>
                                                </p>

                                                {/* Description (Truncated) */}
                                                <p
                                                    className="event-desc text-muted mb-4 flex-grow-1"
                                                    style={{
                                                        fontSize: "14px",
                                                        lineHeight: "1.6",
                                                        overflow: "hidden",
                                                        display: "-webkit-box",
                                                        WebkitLineClamp: "3",
                                                        WebkitBoxOrient:
                                                            "vertical",
                                                    }}
                                                >
                                                    {event.description}
                                                </p>

                                                {/* Divider */}
                                                <div
                                                    className="w-100 bg-light mb-3"
                                                    style={{ height: "1px" }}
                                                ></div>

                                                {/* Footer - Entire row clickable */}
                                                <Link
                                                    href={event.link}
                                                    className="d-flex justify-content-between align-items-center text-decoration-none w-100"
                                                >
                                                    <span className="text-muted small hover-theme">
                                                        Sisa Kuota:{" "}
                                                        <span
                                                            className={`fw-bold ${
                                                                isFull
                                                                    ? "text-danger"
                                                                    : "text-dark"
                                                            }`}
                                                        >
                                                            {isFull
                                                                ? "Full"
                                                                : event.available_seats}
                                                        </span>
                                                    </span>

                                                    <span className="text-muted small">
                                                        {daysLeft} Hari Lagi
                                                    </span>
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })
                        ) : (
                            // Empty State
                            <div className="col-12 text-center py-5">
                                <div
                                    className="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mb-3"
                                    style={{ width: "80px", height: "80px" }}
                                >
                                    <i className="fa-regular fa-calendar-xmark fs-2 text-muted opacity-50"></i>
                                </div>
                                <h5 className="fw-bold text-muted">
                                    No Upcoming Events
                                </h5>
                                <p className="text-muted mb-0">
                                    Check back later for new events.
                                </p>
                            </div>
                        )}
                    </div>
                    <div className="text-center mt-5">
                        <Link
                            href="/events/list"
                            className="th-btn style4 shadow-sm"
                        >
                            Lihat event lainnya
                        </Link>
                    </div>
                </div>
            </section>

            {/* Successful Events Section */}
            <section className="space bg-white">
                <div className="container">
                    <div className="row justify-content-center text-center mb-5">
                        <div className="col-lg-8">
                            <h2 className="fw-bold mb-3">Successful Events</h2>
                            <p className="text-muted">
                                Simak Event mana saja yang sukses
                                diselenggarakan. Acara-acara ini punya reputasi
                                networking yang luas, follow-up action yang
                                positif, dan tentunya sesak dihadiri lebih dari
                                100 persen target peserta. Penasaran?
                            </p>
                        </div>
                    </div>

                    <div className="row gy-30">
                        {pastEvents.map((event) => {
                            const organizer = event.organizer;

                            return (
                                <div
                                    className="col-xl-4 col-md-6"
                                    key={`past-${event.id}`}
                                >
                                    <div
                                        className="event-card bg-white h-100 d-flex flex-column"
                                        style={{
                                            borderRadius: "16px",
                                            overflow: "hidden",
                                            transition: "all 0.3s ease",
                                            boxShadow:
                                                "0 10px 30px rgba(0,0,0,0.05)",
                                            border: "1px solid #f0f0f0",
                                        }}
                                    >
                                        {/* Clickable Image Area */}
                                        <Link
                                            href={event.link}
                                            className="event-img position-relative d-block overflow-hidden"
                                        >
                                            <img
                                                src={event.image}
                                                alt={event.title}
                                                className="w-100"
                                                style={{
                                                    height: "220px",
                                                    objectFit: "cover",
                                                    transition:
                                                        "transform 0.5s ease",
                                                    filter: "grayscale(100%)",
                                                }}
                                                onMouseOver={(e) => {
                                                    e.target.style.transform =
                                                        "scale(1.05)";
                                                    e.target.style.filter =
                                                        "grayscale(0%)";
                                                }}
                                                onMouseOut={(e) => {
                                                    e.target.style.transform =
                                                        "scale(1)";
                                                    e.target.style.filter =
                                                        "grayscale(100%)";
                                                }}
                                            />
                                        </Link>

                                        <div className="event-content d-flex flex-column flex-grow-1 p-4">
                                            {/* Category Badge */}
                                            <div className="mb-3">
                                                <span
                                                    className="d-inline-block px-3 py-1 fw-medium text-dark"
                                                    style={{
                                                        border: "1px solid #e0e0e0",
                                                        borderRadius: "8px",
                                                        fontSize: "12px",
                                                        backgroundColor: "#fff",
                                                    }}
                                                >
                                                    {event.category ||
                                                        "Seminar"}
                                                </span>
                                            </div>

                                            {/* Title */}
                                            <h3
                                                className="event-title h5 mb-2 fw-bold"
                                                style={{ lineHeight: "1.4" }}
                                            >
                                                <Link
                                                    href={event.link}
                                                    className="text-decoration-none"
                                                    style={{
                                                        color: "var(--theme-color)",
                                                        transition: "0.2s",
                                                    }}
                                                >
                                                    {event.title}
                                                </Link>
                                            </h3>

                                            {/* Organizer */}
                                            <p
                                                className="text-muted small mb-3"
                                                style={{ fontSize: "13px" }}
                                            >
                                                oleh:{" "}
                                                <span className="fw-medium text-dark">
                                                    {organizer}
                                                </span>
                                            </p>

                                            {/* Description (Truncated) */}
                                            <p
                                                className="event-desc text-muted mb-4 flex-grow-1"
                                                style={{
                                                    fontSize: "14px",
                                                    lineHeight: "1.6",
                                                    overflow: "hidden",
                                                    display: "-webkit-box",
                                                    WebkitLineClamp: "3",
                                                    WebkitBoxOrient: "vertical",
                                                }}
                                            >
                                                {event.description}
                                            </p>

                                            {/* Divider */}
                                            <div
                                                className="w-100 bg-light mb-3"
                                                style={{ height: "1px" }}
                                            ></div>

                                            {/* Footer - "Completed" Status */}
                                            <Link
                                                href={event.link}
                                                className="d-flex justify-content-between align-items-center text-decoration-none w-100"
                                            >
                                                <span className="text-muted small"></span>
                                                <span className="text-success fw-bold small d-flex align-items-center gap-1">
                                                    <i className="fa-solid fa-circle-check"></i>{" "}
                                                    Completed
                                                </span>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                    <div className="text-center mt-5">
                        <Link
                            href="/events/list"
                            className="th-btn style4 shadow-sm"
                        >
                            Lihat event lainnya
                        </Link>
                    </div>
                </div>
            </section>

            {/* Partners Section */}
            <section className="space-bottom">
                <div className="container">
                    <div
                        className="rounded-20 p-4 p-md-5"
                        style={{ backgroundColor: "var(--theme-color)" }}
                    >
                        <div className="row align-items-center">
                            <div className="col-lg-5 mb-4 mb-lg-0">
                                <h2 className="fw-bold mb-3 text-white">
                                    Partner
                                </h2>
                                <p className="mb-4 text-white opacity-75">
                                    Berikut adalah beberapa partner yang telah
                                    menggunakan jasa kami. Pelajari cara membuat
                                    event di ACTiV Event.
                                </p>
                                <Link
                                    href="#"
                                    className="th-btn style5 th-radius shadow-sm border-0"
                                >
                                    {" "}
                                    {/* style3 usually works well on colored bg or just standard btn */}
                                    Lihat Selengkapnya
                                </Link>
                            </div>
                            <div className="col-lg-7">
                                <div className="row g-3">
                                    {[
                                        { name: "Logitech", img: "logi.jpg" },
                                        { name: "Zoom", img: "zoom.jpg" },
                                        {
                                            name: "Microsoft Teams",
                                            img: "microsoft-teams.jpg",
                                        },
                                        { name: "Yealink", img: "yealink.jpg" },
                                    ].map((partner, index) => (
                                        <div className="col-md-6" key={index}>
                                            <div
                                                className="bg-white rounded-3 p-3 d-flex align-items-center justify-content-center h-100 shadow-sm"
                                                style={{ minHeight: "100px" }}
                                            >
                                                <img
                                                    src={`/assets/img/partners/${partner.img}`}
                                                    alt={partner.name}
                                                    className="img-fluid"
                                                    style={{
                                                        maxHeight: "40px",
                                                        objectFit: "contain",
                                                    }}
                                                />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
