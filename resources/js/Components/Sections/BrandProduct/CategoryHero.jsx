import React from "react";

export default function CategoryHero({ heroContent }) {
    return (
        <div
            className="category-hero th-radius mb-50 position-relative overflow-hidden d-flex align-items-center"
            style={{
                background: "linear-gradient(135deg, #4ac15e 0%, #22c55e 40%, #0ea5e9 100%)",
                minHeight: "320px",
                padding: "60px 50px",
                borderRadius: "32px",
                border: "1px solid rgba(255,255,255,0.2)",
                boxShadow: "0 25px 50px -12px rgba(14, 165, 233, 0.4)",
            }}
        >
            {/* Decorative Background Elements */}
            <div
                className="position-absolute"
                style={{
                    width: "300px",
                    height: "300px",
                    background: "radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%)",
                    top: "-100px",
                    right: "-50px",
                    filter: "blur(40px)",
                }}
            />
            <div
                className="position-absolute"
                style={{
                    width: "200px",
                    height: "200px",
                    background: "radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%)",
                    bottom: "-50px",
                    left: "20%",
                    filter: "blur(30px)",
                }}
            />

            <div className="row align-items-center w-100 m-0 position-relative z-index-common">
                <div className="col-lg-7">
                    <h2
                        className="text-white mb-3 text-capitalize fw-bold"
                        style={{ fontSize: "2.8rem", letterSpacing: "-1px" }}
                    >
                        {heroContent.title}
                    </h2>
                    <p
                        className="text-white mb-0 opacity-75"
                        style={{ fontSize: "1.2rem", maxWidth: "500px", lineHeight: "1.6" }}
                    >
                        {heroContent.desc}
                    </p>
                </div>
                <div className="col-lg-5 text-center mt-4 mt-lg-0">
                    {heroContent.image ? (
                        <div className="hero-img-box p-4" style={{
                            background: "#ffffff",
                            borderRadius: "28px",
                            border: "1px solid rgba(255, 255, 255, 0.2)",
                            display: "inline-block",
                            boxShadow: "0 15px 35px rgba(0,0,0,0.1)"
                        }}>
                            <img
                                src={heroContent.image}
                                alt={heroContent.title}
                                className="img-fluid"
                                style={{
                                    maxHeight: "180px",
                                    objectFit: "contain",
                                    filter: "drop-shadow(0 10px 20px rgba(0,0,0,0.3))"
                                }}
                                onError={(e) => {
                                    e.target.closest('.hero-img-box').style.display = "none";
                                }}
                            />
                        </div>
                    ) : null}
                </div>
            </div>

            <style jsx="true">{`
                @media (max-width: 991px) {
                    .category-hero {
                        padding: 40px 30px !important;
                        min-height: auto !important;
                    }
                    .category-hero h2 {
                        font-size: 2rem !important;
                    }
                    .category-hero p {
                        font-size: 1rem !important;
                    }
                }
            `}</style>
        </div>
    );
}
