import React from "react";

export default function ConfiguratorHero({ handleCopyUrl }) {
    return (
        <div className="breadcumb-area style2 bg-smoke4">
            <div
                className="breadcumb-wrapper background-image"
                style={{
                    backgroundImage: `url('/assets/img/bg/breadcumb-bg.jpg')`,
                }}
            >
                <div className="container">
                    <div className="breadcumb-content">
                        <span className="sub-title text-white py-2">
                            PROJECT SUMMARY
                        </span>
                        <h1 className="breadcumb-title py-2">
                            Thank you for your interest in ACTiV!
                        </h1>
                        <p
                            className="breadcumb-text text-white mb-30 py-2"
                            style={{
                                maxWidth: "800px",
                                marginLeft: "auto",
                                marginRight: "auto",
                            }}
                        >
                            Explore your finished room(s) below, and when you're
                            ready to talk next steps, simply request a
                            consultation. We'd be happy to discuss your project,
                            develop a formal quote, and facilitate next steps,
                            whether through ACTiV or your preferred partner.
                        </p>
                        <div className="btn-group justify-content-center">
                            <button
                                type="button"
                                className="th-btn style3 th-radius th-icon"
                                onClick={handleCopyUrl}
                            >
                                Copy your custom URL{" "}
                                <i className="fa-regular fa-copy"></i>
                            </button>
                            <a
                                href="/contact"
                                className="th-btn th-radius th-icon"
                            >
                                Request Consultation
                                <i className="fa-regular fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
