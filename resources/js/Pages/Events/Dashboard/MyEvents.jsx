import React from "react";
import { Head, Link } from "@inertiajs/react";
import DashboardLayout from "./Layout";
import EmptyState from "@/Components/Common/EmptyState";

export default function MyEvents({ registrations }) {
    return (
        <DashboardLayout pageTitle="My Events">
            <Head title="My Events" />

            <div className="mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h2 className="fw-bold fs-3 mb-1">My Events</h2>
                    <p className="text-muted">
                        Manage the events you have registered for.
                    </p>
                </div>
                <Link
                    href={route("events.index")}
                    className="btn th-btn style8 th-radius"
                >
                    <i className="fa-solid fa-plus me-2"></i> Join New Event
                </Link>
            </div>

            {registrations.data.length > 0 ? (
                <div className="row g-4">
                    {registrations.data.map((event) => (
                        <div key={event.id} className="col-xl-4 col-md-6">
                            <div className="card h-100 border-0 shadow-sm rounded-20 overflow-hidden hover-up transition-all">
                                <div className="position-relative">
                                    <img
                                        src={
                                            event.thumbnail ||
                                            "/assets/img/event/event_thumb_1_1.jpg"
                                        }
                                        alt={event.title}
                                        className="card-img-top object-fit-cover"
                                        style={{ height: "200px" }}
                                    />
                                    <span
                                        className={`position-absolute px-3 py-2 top-0 end-0 m-3 p-2 badge rounded-pill fw-bold small shadow-sm  ${
                                            event.status === "confirmed"
                                                ? "bg-white text-success fw-bold"
                                                : "bg-warning text-white fw-bold"
                                        }`}
                                    >
                                        {event.status.toUpperCase()}
                                    </span>
                                </div>
                                <div className="card-body p-4 d-flex flex-column">
                                    <div className="mb-2 text-muted small d-flex align-items-center gap-2">
                                        <i className="fa-solid fa-calendar-day text-theme"></i>
                                        {new Date(
                                            event.start_date
                                        ).toLocaleDateString()}
                                    </div>
                                    <h5 className="card-title fw-bold mb-3 flex-grow-1">
                                        <Link
                                            href={route(
                                                "events.my-event-detail",
                                                event.slug
                                            )}
                                            className="text-dark text-decoration-none hover-theme"
                                        >
                                            {event.title}
                                        </Link>
                                    </h5>

                                    <div className="d-grid gap-2">
                                        {event.status === "confirmed" ? (
                                            <Link
                                                href={route(
                                                    "events.room",
                                                    event.slug
                                                )}
                                                className="th-btn th-radius style8"
                                                style={{ padding: "10px" }}
                                            >
                                                <i className="fa-solid fa-door-open me-2"></i>{" "}
                                                Enter Event Room
                                            </Link>
                                        ) : (
                                            <button
                                                className="th-btn th-radius style8 disabled opacity-75"
                                                disabled
                                                style={{
                                                    padding: "10px",
                                                    cursor: "not-allowed",
                                                }}
                                            >
                                                <i className="fa-solid fa-hourglass-start me-2"></i>
                                                {event.status === "pending"
                                                    ? "Waiting Approval"
                                                    : "Access Denied"}
                                            </button>
                                        )}
                                        <Link
                                            href={route(
                                                "events.my-event-detail",
                                                event.slug
                                            )}
                                            style={{ padding: "10px" }}
                                            className="th-btn th-radius style4"
                                        >
                                            Details
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            ) : (
                <EmptyState
                    title="No Events Found"
                    message="You haven't registered for any events yet. Explore our
                        upcoming events and join one today!"
                    link={route("events.index")}
                    linkText="Explore Events"
                />
            )}

            {/* Pagination if needed */}
        </DashboardLayout>
    );
}
