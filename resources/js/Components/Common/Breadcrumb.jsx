import React from "react";
import { Link } from "@inertiajs/react";
import { getImageUrl } from "@/Utils/image";

export default function Breadcrumb({ title, items, bgImage }) {
    const resolvedBgImage = getImageUrl(bgImage, "/assets/img/bg/default-breadcumb-bg.jpg");

    return (
        <div className="breadcumb-area">
            <div
                className="breadcumb-wrapper"
                data-bg-src={resolvedBgImage}
                style={{
                    backgroundImage: `url(${resolvedBgImage})`,
                    backgroundPosition: "center center",
                    backgroundSize: "cover",
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
            <style jsx="true">{`
                .breadcumb-wrapper {
                    text-align: left !important;
                    padding: 190px 0 !important;
                }
                .breadcumb-wrapper::before {
                    background: rgba(11, 20, 34, 0.55) !important; /* Lightened from 0.8 */
                }
                @media (max-width: 767px) {
                    .breadcumb-title {
                        font-size: 32px !important;
                    }
                    .breadcumb-menu {
                        margin-top: 5px !important;
                    }
                    .breadcumb-menu li {
                        font-size: 13px !important;
                    }
                    .breadcumb-wrapper {
                        padding: 80px 0 !important;
                        background-position: 28% center !important;
                    }
                }
            `}</style>
        </div>
    );
}
