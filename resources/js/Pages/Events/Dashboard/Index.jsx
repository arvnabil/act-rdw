import React from "react";
import { Head, Link } from "@inertiajs/react";
import DashboardLayout from "./Layout";
import DashboardTable, {
    DashboardTableRow,
} from "@/Components/Events/Dashboard/DashboardTable";
import SimpleBreadcrumb from "@/Components/Common/SimpleBreadcrumb";
import EmptyState from "@/Components/Common/EmptyState";
import { route } from "ziggy-js";

export default function Index({ auth, stats, recent_events }) {
    return (
        <DashboardLayout pageTitle="Dashboard">
            <Head title="Dashboard" />

            <div className="mb-4">
                <h2 className="fw-bold fs-3 mb-1">Dashboard</h2>
                <p className="text-muted">Welcome back, {auth.user.name}!</p>
            </div>

            <div className="row g-4 mb-5">
                {/* Stats Widgets */}
                <div className="col-xl-3 col-md-6">
                    <div className="card border-0 shadow-sm rounded-20 h-100 overflow-hidden">
                        <div className="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p className="text-muted mb-1 fs-sm fw-medium uppercase">
                                    Total Events
                                </p>
                                <h3 className="fw-bold mb-0 text-theme">
                                    {stats.total_events}
                                </h3>
                            </div>
                            <div
                                className="icon_box bg-theme bg-opacity-10 text-theme rounded-circle d-flex align-items-center justify-content-center"
                                style={{ width: "50px", height: "50px" }}
                            >
                                <i className="fa-solid fa-calendar-alt fs-4 text-white"></i>
                            </div>
                        </div>
                        <div
                            className="progress rounded-0"
                            style={{ height: "4px" }}
                        >
                            <div
                                className="progress-bar bg-theme"
                                role="progressbar"
                                style={{ width: "100%" }}
                            ></div>
                        </div>
                    </div>
                </div>
                <div className="col-xl-3 col-md-6">
                    <div className="card border-0 shadow-sm rounded-20 h-100 overflow-hidden">
                        <div className="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p className="text-muted mb-1 fs-sm fw-medium uppercase">
                                    Active Events
                                </p>
                                <h3 className="fw-bold mb-0 text-success">
                                    {stats.active_events}
                                </h3>
                            </div>
                            <div
                                className="icon_box bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center"
                                style={{ width: "50px", height: "50px" }}
                            >
                                <i className="fa-solid fa-clock fs-4 text-white"></i>
                            </div>
                        </div>
                        <div
                            className="progress rounded-0"
                            style={{ height: "4px" }}
                        >
                            <div
                                className="progress-bar bg-success"
                                role="progressbar"
                                style={{ width: "100%" }}
                            ></div>
                        </div>
                    </div>
                </div>
                <div className="col-xl-3 col-md-6">
                    <div className="card border-0 shadow-sm rounded-20 h-100 overflow-hidden">
                        <div className="card-body p-4 d-flex align-items-center justify-content-between">
                            <div>
                                <p className="text-muted mb-1 fs-sm fw-medium uppercase">
                                    Certificates
                                </p>
                                <h3 className="fw-bold mb-0 text-info">
                                    {stats?.certificates || 0}
                                </h3>
                            </div>
                            <div
                                className="icon_box bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center"
                                style={{ width: "50px", height: "50px" }}
                            >
                                <i className="fa-solid fa-award fs-4 text-white"></i>
                            </div>
                        </div>
                        <div
                            className="progress rounded-0"
                            style={{ height: "4px" }}
                        >
                            <div
                                className="progress-bar bg-info"
                                role="progressbar"
                                style={{ width: "100%" }}
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Recent Events Section */}
            <div className="card border-0 shadow-sm rounded-20 overflow-hidden">
                <div className="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 className="fw-bold mb-0">Recent Activity</h5>
                    <Link
                        href={route("events.my-events")}
                        className="th-btn style8 fw-semibold fs-sm"
                        style={{ padding: "12px 20px" }}
                    >
                        View All
                    </Link>
                </div>
                <div className="card-body p-4">
                    {recent_events.length > 0 ? (
                        <DashboardTable
                            columns={[
                                { label: "Event", className: "col-md-5 ps-3" },
                                { label: "Date", className: "col-md-3" },
                                { label: "Status", className: "col-md-2" },
                                {
                                    label: "Action",
                                    className: "col-md-2 text-end pe-3",
                                },
                            ]}
                        >
                            {recent_events.map((event, index) => (
                                <DashboardTableRow
                                    key={event.id}
                                    style={{
                                        backgroundColor:
                                            index % 2 === 0 ? "#fff" : "#fff",
                                        borderBottom:
                                            index !== recent_events.length - 1
                                                ? "1px solid #eee"
                                                : "none",
                                    }}
                                >
                                    {/* Event */}
                                    <div className="col-md-5 ps-3 mb-2 mb-md-0">
                                        <div className="d-flex d-md-none fw-bold small text-muted text-uppercase mb-1">
                                            Event
                                        </div>
                                        <div className="d-flex align-items-center gap-3">
                                            <img
                                                src={event.thumbnail}
                                                alt={event.title}
                                                className="rounded-3 object-fit-cover shadow-sm"
                                                style={{
                                                    width: "48px",
                                                    height: "48px",
                                                }}
                                            />
                                            <span className="fw-semibold text-dark text-truncate">
                                                {event.title}
                                            </span>
                                        </div>
                                    </div>

                                    {/* Date */}
                                    <div className="col-md-3 mb-2 mb-md-0">
                                        <div className="d-flex d-md-none fw-bold small text-muted text-uppercase mb-1">
                                            Date
                                        </div>
                                        <span className="text-muted small">
                                            <i className="fa-regular fa-calendar me-1"></i>
                                            {new Date(
                                                event.start_date,
                                            ).toLocaleDateString()}
                                        </span>
                                    </div>

                                    {/* Status */}
                                    <div className="col-md-2 mb-2 mb-md-0">
                                        <div className="d-flex d-md-none fw-bold small text-muted text-uppercase mb-1">
                                            Status
                                        </div>
                                        <span
                                            className={`px-3 py-2 rounded-pill fw-bold small text-uppercase d-inline-flex align-items-center ${
                                                event.status === "Joined" ||
                                                event.status === "Registered" ||
                                                event.status === "Certified"
                                                    ? "bg-theme text-white shadow-sm"
                                                    : event.status === "Pending"
                                                      ? "bg-warning bg-opacity-25 text-white"
                                                      : "bg-warning bg-opacity-10 text-white"
                                            }`}
                                        >
                                            <i
                                                className={`fa-solid ${
                                                    event.status === "Joined" ||
                                                    event.status ===
                                                        "Registered" ||
                                                    event.status === "Certified"
                                                        ? "fa-circle-check"
                                                        : "fa-clock"
                                                } me-2`}
                                            ></i>
                                            {event.status
                                                .charAt(0)
                                                .toUpperCase() +
                                                event.status.slice(1)}
                                        </span>
                                    </div>

                                    {/* Action */}
                                    <div className="col-md-2 text-md-end pe-3">
                                        <div className="d-flex d-md-none fw-bold small text-muted text-uppercase mb-1">
                                            Action
                                        </div>
                                        <Link
                                            href={route(
                                                "events.my-event-detail",
                                                event.slug,
                                            )}
                                            className="btn btn-sm btn-light rounded-pill px-3 fw-medium"
                                        >
                                            View Details
                                        </Link>
                                    </div>
                                </DashboardTableRow>
                            ))}
                        </DashboardTable>
                    ) : (
                        <EmptyState
                            message="You haven't joined any events yet."
                            link={route("events.index")}
                            linkText="Browse Events"
                        />
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
