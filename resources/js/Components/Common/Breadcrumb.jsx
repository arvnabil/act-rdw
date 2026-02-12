import React from "react";
import { Link } from "@inertiajs/react";
import { getImageUrl } from "@/Utils/image";

export default function Breadcrumb({ title, items, bgImage }) {
    const resolvedBgImage = getImageUrl(bgImage) || "/assets/img/foto-dok-logitech-1.png";

    return (
        <div className="breadcumb-area">
            <div
                className="breadcumb-wrapper"
                data-bg-src={resolvedBgImage}
                style={{
                    backgroundImage: `url(${resolvedBgImage})`,
                }}
            >
                <div className="container">
                    <div className="breadcumb-content">
                        <h1 className="breadcumb-title">{title}</h1>
                        <ul className="breadcumb-menu">
                            {items.map((item, index) => (
                                <li key={index}>
                                    {item.link ? (
                                        <Link href={item.link}>
                                            {item.label}
                                        </Link>
                                    ) : (
                                        item.label
                                    )}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
}
