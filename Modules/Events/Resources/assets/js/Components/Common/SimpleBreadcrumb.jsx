import React from "react";
import { Link } from "@inertiajs/react";

export default function SimpleBreadcrumb({ title }) {
    return (
        <div className="my-4 d-flex align-items-center small">
            <Link href="/" className="text-muted text-decoration-none">
                Home
            </Link>
            <i
                className="fa-solid fa-chevron-right text-muted mx-2"
                style={{ fontSize: "10px" }}
            ></i>
            <Link
                href={route("events.index")}
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
                {title}
            </span>
        </div>
    );
}
