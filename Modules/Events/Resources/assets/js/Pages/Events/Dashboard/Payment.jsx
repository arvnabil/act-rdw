import React from "react";
import { Head, Link } from "@inertiajs/react";
import DashboardLayout from "./Layout";
import EmptyState from "@/Components/Common/EmptyState";
import DashboardTable, {
    DashboardTableRow,
} from "../../../Components/Events/Dashboard/DashboardTable";
import { route } from "ziggy-js";

export default function Payment({ auth, payments }) {
    return (
        <DashboardLayout pageTitle="Payment History">
            <Head title="Payment History" />

            <div className="card border-0 shadow-sm rounded-20 overflow-hidden">
                <div className="card-header bg-white border-bottom-0 p-4 pb-0">
                    <h5 className="fw-bold mb-0">Payment Transactions</h5>
                </div>
                <div className="card-body p-4">
                    {payments.data.length > 0 ? (
                        <DashboardTable
                            columns={[
                                {
                                    label: "Invoice ID",
                                    className: "col-2 ps-3",
                                },
                                { label: "Event", className: "col-3" },
                                { label: "Amount", className: "col-2" },
                                {
                                    label: "Status",
                                    className: "col-2 text-center",
                                },
                                {
                                    label: "Date",
                                    className: "col-1 text-end pe-3",
                                },
                                {
                                    label: "Action",
                                    className: "col-2 text-center",
                                },
                            ]}
                        >
                            {payments.data.map((payment, index) => (
                                <DashboardTableRow
                                    key={payment.id}
                                    style={{
                                        backgroundColor:
                                            index % 2 === 0 ? "#fff" : "#fff", // Keep consistent white or alternating if desired
                                        borderBottom:
                                            index === payments.data.length - 1
                                                ? "none"
                                                : "1px solid #f0f0f0",
                                    }}
                                >
                                    {/* Invoice */}
                                    <div className="col-2 ps-3 mb-0">
                                        <span className="fw-medium text-theme font-monospace">
                                            {payment.invoice_id}
                                        </span>
                                    </div>

                                    {/* Event */}
                                    <div className="col-3 mb-0">
                                        <div>
                                            <span className="d-block fw-bold text-dark text-truncate pe-3">
                                                {payment.event_title}
                                            </span>
                                            <small className="text-muted">
                                                <i className="fa-regular fa-calendar me-1"></i>
                                                {payment.event_date}
                                            </small>
                                        </div>
                                    </div>

                                    {/* Amount */}
                                    <div className="col-2 mb-0">
                                        <span className="fw-bold text-dark fs-6">
                                            {Number(payment.amount) > 0
                                                ? `Rp ${parseInt(
                                                      payment.amount
                                                  ).toLocaleString("id-ID")}`
                                                : "Free"}
                                        </span>
                                    </div>

                                    {/* Status */}
                                    <div className="col-2 text-center mb-0">
                                        <span
                                            className={`px-3 py-2 rounded-pill fw-bold small text-uppercase d-inline-flex align-items-center ${
                                                payment.status === "Joined" ||
                                                payment.status ===
                                                    "Registered" ||
                                                payment.status === "Certified"
                                                    ? "bg-theme text-white shadow-sm"
                                                    : payment.status ===
                                                      "Pending"
                                                    ? "bg-warning bg-opacity-25 text-white"
                                                    : "bg-danger bg-opacity-10 text-white"
                                            }`}
                                        >
                                            <i
                                                className={`fa-solid ${
                                                    payment.status ===
                                                        "Joined" ||
                                                    payment.status ===
                                                        "Registered" ||
                                                    payment.status ===
                                                        "Certified"
                                                        ? "fa-circle-check"
                                                        : payment.status ===
                                                          "Pending"
                                                        ? "fa-clock"
                                                        : "fa-ban"
                                                } me-2`}
                                            ></i>
                                            {payment.status}
                                        </span>
                                    </div>

                                    {/* Date */}
                                    <div className="col-1 text-end pe-3">
                                        <span className="text-muted small">
                                            {payment.date}
                                        </span>
                                    </div>

                                    {/* Action */}
                                    <div className="col-2 text-center mb-0">
                                        {Number(payment.amount) > 0 &&
                                        payment.status === "Pending" ? (
                                            <a
                                                href={`https://wa.me/${
                                                    payment.event_organizer_phone
                                                }?text=${encodeURIComponent(
                                                    `Hello, I would like to confirm payment for Invoice ${
                                                        payment.invoice_id
                                                    } for Event: ${
                                                        payment.event_title
                                                    }. Amount: Rp ${parseInt(
                                                        payment.amount
                                                    ).toLocaleString("id-ID")}`
                                                )}`}
                                                target="_blank"
                                                rel="noreferrer"
                                                className="th-btn btn-sm btn-primary text-white rounded-pill px-3 d-inline-flex align-items-center gap-1"
                                                title="Confirm via WhatsApp"
                                                style={{
                                                    padding: "8px 15px",
                                                    fontSize: "12px",
                                                }}
                                            >
                                                <i className="fa-brands fa-whatsapp"></i>
                                                Confirm
                                            </a>
                                        ) : (
                                            <span className="text-muted">
                                                -
                                            </span>
                                        )}
                                    </div>
                                </DashboardTableRow>
                            ))}
                        </DashboardTable>
                    ) : (
                        <EmptyState
                            message="No payment history found."
                            icon="fa-receipt"
                        />
                    )}

                    {/* Pagination if needed */}
                    {payments.links && payments.links.length > 3 && (
                        <div className="d-flex justify-content-center mt-4">
                            {/* Pagination Component would go here */}
                        </div>
                    )}
                </div>
            </div>
        </DashboardLayout>
    );
}
