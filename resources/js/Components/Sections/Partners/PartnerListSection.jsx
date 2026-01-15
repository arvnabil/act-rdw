import React from "react";
import { Link } from "@inertiajs/react";

const categories = [
    { id: "*", label: "All", filter: "*" },
    { id: "Video Conference", label: "Video Conference", filter: ".case4" },
    { id: "Audio Visual", label: "Audio Visual", filter: ".case1" },
    { id: "Network & Security", label: "Network & Security", filter: ".case2" },
    { id: "Server & Storage", label: "Server & Storage", filter: ".case3" },
    { id: "Cloud Meeting", label: "Cloud Meeting", filter: ".case4" },
    {
        id: "Surveillance & Security System",
        label: "Surveillance & Security System",
        filter: ".case4",
    },
    {
        id: "IP PBX, IPPhone, Server Cloud",
        label: "IP PBX, IPPhone, Server Cloud",
        filter: ".case4",
    },
    {
        id: "Rack, Power & Cooling",
        label: "Rack, Power & Cooling",
        filter: ".case4",
    },
    {
        id: "Printing, Peripheral & Tools",
        label: "Printing, Peripheral & Tools",
        filter: ".case4",
    },
    { id: "Data Protection", label: "Data Protection", filter: ".case4" },
    { id: "PC and Computing", label: "PC and Computing", filter: ".case4" },
    {
        id: "SaaS (Software as a Service)",
        label: "SaaS (Software as a Service)",
        filter: ".case4",
    },
];

const partners = [
    {
        id: 1,
        name: "Maxhub",
        image: "/assets/img/partners/maxhub.jpg",
        categories: ["Audio Visual", "Server & Storage"],
        displayCategory: "Maxhub",
    },
    {
        id: 2,
        name: "Logitech",
        image: "/assets/img/partners/logi.jpg",
        categories: ["Network & Security", "Cloud Meeting"],
        displayCategory: "Logi",
    },
    {
        id: 3,
        name: "Zoom",
        image: "/assets/img/partners/zoom.jpg",
        categories: ["Audio Visual", "Cloud Meeting"],
        displayCategory: "Zoom",
    },
    {
        id: 4,
        name: "Jabra",
        image: "/assets/img/partners/jabra.jpg",
        categories: ["Network & Security", "Cloud Meeting"],
        displayCategory: "Jabra",
    },
    {
        id: 5,
        name: "Microsoft Teams",
        image: "/assets/img/partners/microsoft-teams.jpg",
        categories: ["Audio Visual", "Cloud Meeting", "Network & Security"],
        displayCategory: "Microsoft",
    },
    {
        id: 6,
        name: "Yealink",
        image: "/assets/img/partners/yealink.jpg",
        categories: ["Audio Visual", "Cloud Meeting", "Network & Security"],
        displayCategory: "Yealink",
    },
];

const getPartnerClasses = (cats) => {
    let classes = [];
    cats.forEach((c) => {
        if (c === "Audio Visual") classes.push("case1");
        else if (c === "Network & Security") classes.push("case2");
        else if (c === "Server & Storage") classes.push("case3");
        else classes.push("case4");
    });
    return [...new Set(classes)].join(" ");
};

export default function PartnerListSection() {
    return (
        <div className="case-area space-bottom space">
            <div className="container">
                <div className="row justify-content-center text-center">
                    <div className="col-xl-12">
                        <span className="sub-title">Our Partners</span>
                        <h2 className="sec-title text-center mb-30">
                            Let’s Collaborate with us!
                        </h2>
                    </div>
                </div>

                <div className="row">
                    <div className="col-xl-12">
                        <div className="case-menu mb-5 text-center">
                            {categories.map((cat, index) => (
                                <button
                                    key={index}
                                    className={`case4-btn ${
                                        cat.id === "*" ? "active" : ""
                                    }`}
                                    data-filter={cat.filter}
                                >
                                    {cat.label}
                                </button>
                            ))}
                        </div>
                    </div>
                </div>

                <div className="row gy-30 grid">
                    {partners.map((partner) => (
                        <div
                            key={partner.id}
                            className={`col-xl-4 col-lg-6 col-md-6 grid-item ${getPartnerClasses(
                                partner.categories
                            )}`}
                        >
                            <div className="case-box style2 inner-style1 position-relative">
                                <div className="case-img global-img">
                                    <img src={partner.image} alt="case image" />
                                    <Link
                                        href={
                                            "/" +
                                            partner.name
                                                .toLowerCase()
                                                .replace(/\s+/g, "-")
                                        }
                                        className="icon-btn"
                                    >
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                                <div className="case-content">
                                    <div className="media-left">
                                        <h4 className="box-title">
                                            <Link
                                                href={
                                                    "/" +
                                                    partner.name
                                                        .toLowerCase()
                                                        .replace(/\s+/g, "-")
                                                }
                                            >
                                                {partner.name}
                                            </Link>
                                        </h4>
                                        <span className="case-subtitle">
                                            {partner.displayCategory}
                                        </span>
                                    </div>
                                </div>
                                <div className="case-action">
                                    <Link
                                        href={
                                            "/" +
                                            partner.name
                                                .toLowerCase()
                                                .replace(/\s+/g, "-")
                                        }
                                        className="case-btn"
                                    >
                                        <i className="fa-light fa-arrow-right-long"></i>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
