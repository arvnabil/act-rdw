import React, { useState } from "react";
import { Head, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

export default function ServiceDetail({ service }) {
    const [filter, setFilter] = useState("*");

    const categories = service.filters || [
        { label: "Meetings Rooms", value: ".meetings-rooms" },
        { label: "Individual Space", value: ".individual-space" },
        {
            label: "Training & Education Space",
            value: ".training-education-space",
        },
        { label: "Divisible Space", value: ".divisible-space" },
    ];

    const filteredRooms =
        filter === "*"
            ? service.rooms
            : service.rooms.filter((room) =>
                  room.category.includes(filter.replace(".", ""))
              );

    return (
        <MainLayout>
            <Head title={service.title} />
            <Breadcrumb
                title={service.title}
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    { label: service.title },
                ]}
            />

            <div className="case-area space-bottom space">
                <div className="container">
                    <div className="row justify-content-center text-center">
                        <div className="col-xl-12">
                            <span className="sub-title">
                                {service.hero_subtitle ||
                                    "Reimagine your workspaces."}
                            </span>
                            <h2 className="sec-title text-center mb-30">
                                {service.grid_title ||
                                    "Explore a full range of workspaces"}
                            </h2>
                        </div>
                    </div>
                    {/* Filter Buttons */}
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="case-menu mb-5 text-center">
                                <button
                                    className={`case4-btn ${
                                        filter === "*" ? "active" : ""
                                    }`}
                                    onClick={() => setFilter("*")}
                                >
                                    All Categories
                                </button>
                                {categories.map((cat, index) => (
                                    <button
                                        key={index}
                                        className={`case4-btn ${
                                            filter === cat.value ? "active" : ""
                                        }`}
                                        onClick={() => setFilter(cat.value)}
                                    >
                                        {cat.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Rooms Grid */}
                    <div className="row gy-30 border-0">
                        {filteredRooms.map((room, index) => (
                            <div className="col-12" key={room.id}>
                                <div className="row gy-30 align-items-center">
                                    <div
                                        className={`col-lg-6 ${
                                            index % 2 !== 0 ? "order-lg-1" : ""
                                        }`}
                                    >
                                        <div className="about-image ab-img16 global-img">
                                            <img
                                                src={room.image}
                                                alt={room.title}
                                                className="w-100 rounded-20"
                                            />
                                        </div>
                                    </div>
                                    <div
                                        className={`col-lg-6 ${
                                            index % 2 !== 0 ? "order-lg-0" : ""
                                        }`}
                                    >
                                        <div className="about-item style-16 ps-xxl-4 ms-xxl-4">
                                            <div className="about-content">
                                                <h3 className="box-title text-anime-style-2">
                                                    <Link
                                                        href={`/services/${service.id}/${room.id}`}
                                                    >
                                                        {room.title}
                                                    </Link>
                                                </h3>
                                                <p
                                                    className="about-text wow fadeInUp"
                                                    data-wow-delay=".3s"
                                                >
                                                    {room.description}
                                                </p>
                                                <div className="about-featured-box d-md-flex align-items-start">
                                                    <div className="about-feature pe-xl-4">
                                                        <ul>
                                                            <li
                                                                className="wow fadeInUp"
                                                                data-wow-delay=".1s"
                                                            >
                                                                {room.capacity}{" "}
                                                                <i className="far fa-user text-theme ms-1"></i>
                                                            </li>
                                                            <li
                                                                className="wow fadeInUp"
                                                                data-wow-delay=".2s"
                                                            >
                                                                {room.size}
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div
                                                    className="about-btn mt-30 wow fadeInUp"
                                                    data-wow-delay=".6s"
                                                >
                                                    <Link
                                                        href={`/services/${service.id}/${room.id}`}
                                                        className="th-btn btn-2 th-icon"
                                                    >
                                                        Get Started Now
                                                        <i className="fa-light fa-arrow-right-long"></i>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
