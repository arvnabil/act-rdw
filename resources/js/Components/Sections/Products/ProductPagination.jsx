import React from "react";
import { Link } from "@inertiajs/react";

export default function ProductPagination({ links }) {
    return (
        <div className="th-pagination text-center pt-50">
            <ul>
                {links.map((link, index) => {
                    let label = link.label;
                    let className = link.active ? "active" : "";

                    if (link.label.includes("&laquo; Previous")) {
                        label = '<i class="far fa-arrow-left"></i>';
                    } else if (link.label.includes("Next &raquo;")) {
                        label = '<i class="far fa-arrow-right"></i>';
                        className = "next-page";
                    }

                    return (
                        <li key={index}>
                            {link.url ? (
                                <Link
                                    href={link.url}
                                    className={className}
                                    dangerouslySetInnerHTML={{
                                        __html: label,
                                    }}
                                    preserveScroll
                                />
                            ) : (
                                <a
                                    href="#"
                                    className="disabled"
                                    onClick={(e) => e.preventDefault()}
                                    dangerouslySetInnerHTML={{
                                        __html: label,
                                    }}
                                ></a>
                            )}
                        </li>
                    );
                })}
            </ul>
        </div>
    );
}
