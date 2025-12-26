import React from "react";
import Lottie from "lottie-react";
import { Link } from "@inertiajs/react";
import noDataAnimation from "../Animations/No-Data.json";

export default function EmptyState({
    title,
    message = "No Data Found",
    link,
    linkText,
    onClick,
    buttonText,
}) {
    return (
        <div className="text-center py-5">
            <div className="mb-3 d-flex justify-content-center">
                <div style={{ width: "200px", height: "200px" }}>
                    <Lottie animationData={noDataAnimation} loop={true} />
                </div>
            </div>
            {title && <h3 className="h5 fw-bold text-dark mb-2">{title}</h3>}
            <p className="text-muted mb-3">{message}</p>

            {link && linkText && (
                <Link
                    href={link}
                    className="btn th-btn style8 th-radius btn-sm"
                >
                    {linkText}
                </Link>
            )}

            {onClick && buttonText && (
                <button onClick={onClick} className="btn th-btn style4 sm-btn">
                    {buttonText}
                </button>
            )}
        </div>
    );
}
