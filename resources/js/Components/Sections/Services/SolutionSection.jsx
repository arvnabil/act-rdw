import React from "react";
import { Link } from "@inertiajs/react";

export default function SolutionSection({
    title,
    subtitle,
    description,
    btn_text,
    btn_url,
    features,
    images,
}) {
    // Defaults matching original content
    const finalTitle = title || "Our Solution";
    const finalSubtitle = subtitle || "Projects";
    const finalDesc =
        description ||
        "At ACTiV, we don't just develop technology; we engineer powerful digital solutions that drive real business transformation. Explore our portfolio to see how we leverage cutting-edge ICT to help our clients achieve greater productivity, visibility, and growth.";
    const finalBtnText = btn_text || "Explore Case Studies";
    const finalBtnUrl = btn_url || "/contact";

    // Default features
    const defaultFeatures = [
        { text: "Room Solution" },
        { text: "Server IP PBX" },
        { text: "Storage & Data Solution" },
        { text: "Meeting cloud" },
        { text: "CCTV & Access Control" },
    ];
    const finalFeatures =
        features && features.length > 0 ? features : defaultFeatures;

    // Default images
    const defaultImages = [
        "/assets/img/choose/choose_4_1.jpg",
        "/assets/img/choose/choose_4_2.jpg",
        "/assets/img/choose/choose_4_3.jpg",
    ];
    // Helper to handle image array or objects
    const resolveImage = (img) =>
        typeof img === "string" ? img : img?.url || img?.image || "";
    const img1 =
        images && images[0] ? resolveImage(images[0]) : defaultImages[0];
    const img2 =
        images && images[1] ? resolveImage(images[1]) : defaultImages[1];
    const img3 =
        images && images[2] ? resolveImage(images[2]) : defaultImages[2];

    // Split features into two columns if possible (legacy had 2 columns: 3 items, 2 items)
    // We'll just split roughly in half or hardcode logic if needed.
    // Legacy: Col 1 has 3 items, Col 2 has 2 items.
    const splitIndex = Math.ceil(finalFeatures.length / 2);
    // Adjust slightly to match legacy 3/2 split if length is 5
    const computedSplitIndex = finalFeatures.length === 5 ? 3 : splitIndex;

    const featuresCol1 = finalFeatures.slice(0, computedSplitIndex);
    const featuresCol2 = finalFeatures.slice(computedSplitIndex);

    return (
        <section
            className="choose-6-area space"
            style={{ paddingBottom: "120px" }}
        >
            <div className="container">
                <div className="row gy-4 align-items-start">
                    <div className="col-xl-5 order-1 order-xl-0">
                        <div className="title-area mt-40 mb-20 pe-xl-5">
                            <span className="sub-title text-anime-style-2">
                                <span className="squre-shape left me-3"></span>
                                🚀 {finalSubtitle}
                                <span className="squre-shape d-lg-none right ms-3"></span>
                            </span>
                            <h2 className="sec-title text-anime-style-3">
                                {finalTitle}
                            </h2>
                        </div>
                        <style>
                            {`
                                .about-item.style-16 .about-feature ul li::before {
                                    background-image: url("/assets/img/icon/checkmark.svg") !important;
                                }
                            `}
                        </style>
                        <div className="about-item style-16">
                            <div className="about-content">
                                <p className="about-text wow fadeInUp">
                                    {finalDesc}
                                </p>
                                <div className="about-featured-box d-sm-flex align-items-start">
                                    <div className="about-feature pe-xl-4">
                                        <ul>
                                            {featuresCol1.map((f, i) => (
                                                <li
                                                    key={i}
                                                    className="wow fadeInUp"
                                                    data-wow-delay=".2s"
                                                >
                                                    {f.text ||
                                                        (typeof f === "string"
                                                            ? f
                                                            : "")}
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                    <div className="about-feature">
                                        <ul>
                                            {featuresCol2.map((f, i) => (
                                                <li
                                                    key={i}
                                                    className="wow fadeInUp"
                                                    data-wow-delay=".2s"
                                                >
                                                    {f.text ||
                                                        (typeof f === "string"
                                                            ? f
                                                            : "")}
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                </div>
                                <div
                                    className="about-btn mt-30 wow fadeInUp"
                                    data-wow-delay=".4s"
                                >
                                    <Link
                                        href={finalBtnUrl}
                                        className="th-btn th-radius style8 th-icon"
                                    >
                                        {finalBtnText}
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-7 order-0 order-xl-1">
                        <div className="choose-wrapper text-end position-relative mb-80">
                            <div className="choose-item4">
                                <div className="choose-img">
                                    <img src={img1} alt="" />
                                </div>
                            </div>
                            <div className="choose-item4">
                                <div className="choose-img">
                                    <img src={img2} alt="" />
                                </div>
                            </div>
                            <div className="choose-item4">
                                <div className="choose-img">
                                    <img src={img3} alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
