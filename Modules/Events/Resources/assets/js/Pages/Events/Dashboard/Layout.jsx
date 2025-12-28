import React from "react";
import { Link, usePage } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import { route } from "ziggy-js";

import SimpleBreadcrumb from "@/Components/Common/SimpleBreadcrumb";

export default function DashboardLayout({ children, pageTitle }) {
    const { auth } = usePage().props;

    return (
        <MainLayout>
            <section className="space-extra-bottom bg-light-2 mt-4">
                <div className="container">
                    <div className="row">
                        {/* Sidebar */}
                        {pageTitle && <SimpleBreadcrumb title={pageTitle} />}
                        <div className="col-lg-3 mb-4 mb-lg-0">
                            <aside
                                className="dashboard-sidebar bg-white shadow-sm rounded-20 p-4 sticky-top"
                                style={{ top: "120px", zIndex: 1 }}
                            >
                                <div className="user-profile text-center mb-4 pb-4 border-bottom">
                                    <div
                                        className="avatar mb-3 mx-auto"
                                        style={{
                                            width: "80px",
                                            height: "80px",
                                        }}
                                    >
                                        <img
                                            src={
                                                auth.user.avatar ||
                                                "https://ui-avatars.com/api/?name=" +
                                                    auth.user.name +
                                                    "&background=0D5EF4&color=fff"
                                            }
                                            alt={auth.user.name}
                                            className="rounded-circle w-100 h-100 object-fit-cover"
                                        />
                                    </div>
                                    <h6 className="fw-bold mb-1">
                                        {auth.user.name}
                                    </h6>
                                    <p className="small text-muted mb-0">
                                        {auth.user.email}
                                    </p>
                                </div>

                                <nav className="nav flex-column gap-2">
                                    <NavLink
                                        href={route("events.dashboard")}
                                        active={route().current(
                                            "events.dashboard"
                                        )}
                                        icon="fa-solid fa-home"
                                    >
                                        Dashboard
                                    </NavLink>
                                    <NavLink
                                        href={route("events.my-events")}
                                        active={
                                            route().current(
                                                "events.my-events"
                                            ) || route().current("events.room")
                                        }
                                        icon="fa-solid fa-calendar-check"
                                    >
                                        My Events
                                    </NavLink>
                                    <NavLink
                                        href={route("events.payments")}
                                        active={route().current(
                                            "events.payments"
                                        )}
                                        icon="fa-solid fa-receipt"
                                    >
                                        Payments
                                    </NavLink>
                                    <NavLink
                                        href={route("events.certificates")}
                                        active={route().current(
                                            "events.certificates"
                                        )}
                                        icon="fa-solid fa-award"
                                    >
                                        Certificates
                                    </NavLink>
                                    <NavLink
                                        href={route("events.profile")}
                                        active={route().current(
                                            "events.profile"
                                        )}
                                        icon="fa-solid fa-user"
                                    >
                                        Profile
                                    </NavLink>

                                    <div className="pt-3 mt-2 border-top">
                                        <Link
                                            href={route("events.logout")}
                                            method="post"
                                            as="button"
                                            className="th-btn th-icon th-radius w-100"
                                            style={{
                                                backgroundColor: "#dc3545",
                                            }}
                                        >
                                            <i
                                                className="fa-solid fa-sign-out-alt"
                                                style={{ width: "20px" }}
                                            ></i>
                                            <span>Logout</span>
                                        </Link>
                                    </div>
                                </nav>
                            </aside>
                        </div>

                        {/* Main Content */}
                        <div className="col-lg-9">{children}</div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}

function NavLink({ href, active, icon, children }) {
    return (
        <Link
            href={href}
            className={`nav-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 transition-all ${
                active
                    ? "bg-theme text-white shadow-sm fw-medium"
                    : "text-dark hover:bg-light"
            }`}
        >
            <i
                className={`${icon} ${active ? "text-white" : "text-theme"}`}
                style={{ width: "20px" }}
            ></i>
            <span>{children}</span>
        </Link>
    );
}
