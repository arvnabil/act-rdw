import React from "react";

export default function CategoryHero({ heroContent }) {
    return (
        <div
            className="category-hero th-radius mb-50 position-relative overflow-hidden d-flex align-items-center"
            style={{
                backgroundColor: "#333", // Dark background like Logitech
                minHeight: "300px",
                padding: "40px",
            }}
        >
            <div className="row align-items-center w-100 m-0">
                <div className="col-md-6 z-index-common">
                    <h2 className="text-white mb-3 text-capitalize">
                        {heroContent.title}
                    </h2>
                    <p className="text-white-50 mb-0 fs-5">
                        {heroContent.desc}
                    </p>
                </div>
                <div className="col-md-6 position-relative text-center">
                    {/* Abstract circle or geometric shape background if possible */}

                    {heroContent.image ? (
                        <img
                            src={heroContent.image}
                            alt={heroContent.title}
                            className="position-relative z-index-common"
                            style={{
                                maxHeight: "200px",
                                objectFit: "contain",
                            }}
                            onError={(e) => {
                                e.target.style.display = "none"; // Hide if broken
                            }}
                        />
                    ) : null}
                </div>
            </div>
        </div>
    );
}
