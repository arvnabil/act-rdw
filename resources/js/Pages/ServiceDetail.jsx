import { Head, Link } from "@inertiajs/react";
import React, { useState } from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";

const ServiceDetail = ({ service }) => {
    const getImageUrl = (path) => {
        if (!path) return "";
        if (path.startsWith("http") || path.startsWith("/assets")) {
            return path;
        }
        return `/storage/${path}`;
    };

    return (
        <MainLayout>
            <Head title={service.title} />

            <Breadcrumb
                title={service.title}
                subtitle={
                    service.hero_subtitle ||
                    "Professional Solutions for Your Business"
                }
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    { label: service.title },
                ]}
            />

            <div className="case-area space-bottom space-top">
                <div className="container">
                    <div className="row justify-content-center text-center">
                        <div className="col-xl-12">
                            <span className="sub-title">
                                {service.hero_subtitle}
                            </span>
                            <h2 className="sec-title text-center mb-30">
                                {service.grid_title ||
                                    "Explore a full range of solutions"}
                            </h2>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="case-menu mb-5 text-center">
                                {service.filters.map((filter, index) => (
                                    <button
                                        key={index}
                                        className={`case4-btn ${
                                            index === 0 ? "active" : ""
                                        }`}
                                        data-filter={
                                            filter.value === "*"
                                                ? "*"
                                                : filter.value.startsWith(".")
                                                ? filter.value
                                                : `.${filter.value}`
                                        }
                                    >
                                        {filter.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>
                    <div
                        className="row gy-30 grid"
                        style={{ minHeight: "400px" }}
                    >
                        {service.rooms.length > 0 ? (
                            service.rooms.map((room, index) => (
                                <div
                                    key={room.id}
                                    className={`col-12 grid-item ${room.category.replace(
                                        /\./g,
                                        ""
                                    )}`}
                                >
                                    <div className="row gy-30 align-items-center">
                                        <div
                                            className={`col-lg-6 ${
                                                index % 2 !== 0
                                                    ? "order-lg-1"
                                                    : ""
                                            }`}
                                        >
                                            <div className="about-image ab-img16 global-img">
                                                <img
                                                    src={getImageUrl(
                                                        room.image
                                                    )}
                                                    alt={room.title}
                                                />
                                            </div>
                                        </div>
                                        <div
                                            className={`col-lg-6 ${
                                                index % 2 !== 0
                                                    ? "order-lg-0"
                                                    : ""
                                            }`}
                                        >
                                            <div
                                                className={`about-item style-16 ${
                                                    index % 2 !== 0
                                                        ? "pe-xxl-4 me-xxl-4"
                                                        : "ps-xxl-4 ms-xxl-4"
                                                }`}
                                            >
                                                <div className="about-content">
                                                    <h3 className="box-title text-anime-style-2">
                                                        <Link
                                                            href={`/services/${service.id}/${room.id}`}
                                                        >
                                                            {room.title}
                                                        </Link>
                                                    </h3>
                                                    <p className="about-text wow fadeInUp">
                                                        {room.description}
                                                    </p>
                                                    <div className="about-featured-box d-md-flex align-items-start">
                                                        <div className="about-feature pe-xl-4">
                                                            <ul>
                                                                {room.capacity && (
                                                                    <li className="wow fadeInUp">
                                                                        {
                                                                            room.capacity
                                                                        }{" "}
                                                                        Capacity{" "}
                                                                        <i className="far fa-user"></i>
                                                                    </li>
                                                                )}
                                                                {room.size && (
                                                                    <li className="wow fadeInUp">
                                                                        {
                                                                            room.size
                                                                        }
                                                                    </li>
                                                                )}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div className="about-btn mt-30 wow fadeInUp">
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
                            ))
                        ) : (
                            <div className="col-12 text-center">
                                <p>No solutions found in this category.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </MainLayout>
    );
};

export default ServiceDetail;
