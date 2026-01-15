import React from "react";
import { Link } from "@inertiajs/react";

export default function ProjectPagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <div className="th-pagination mt-60 mb-0 text-center">
            <ul>
                {links.map((link, index) => (
                    <li key={index}>
                        {link.url ? (
                            <Link
                                href={link.url}
                                className={`${link.active ? "active" : ""} ${
                                    link.className || ""
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                                preserveScroll
                            />
                        ) : null}
                    </li>
                ))}
            </ul>
        </div>
    );
}
