import React, { useEffect, useState } from "react";
import { Link, usePage } from "@inertiajs/react";
import { useTemplateInit } from "@/hooks/useTemplateInit";
import Toast from "@/Components/Common/Toast";

export default function MainLayout({ children }) {
    useTemplateInit();
    const { auth, flash, menus } = usePage().props;
    const [toast, setToast] = useState(null);

    useEffect(() => {
        if (flash?.success) {
            setToast({ message: flash.success, type: "success" });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
        if (flash?.error) {
            setToast({ message: flash.error, type: "danger" });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    // Helper to render menu items recursively
    const renderMenuItems = (items) => {
        return items.map((item, index) => {
            const hasChildren = item.children && item.children.length > 0;
            const liClass = hasChildren ? "menu-item-has-children" : "";

            return (
                <li key={index} className={liClass}>
                    {item.type === "custom" ? (
                        <a href={item.url} target={item.target}>
                            {item.title}
                        </a>
                    ) : (
                        <Link href={item.url}>{item.title}</Link>
                    )}

                    {hasChildren && (
                        <ul className="sub-menu">
                            {renderMenuItems(item.children)}
                        </ul>
                    )}
                </li>
            );
        });
    };

    // Helper for footer links (simple list)
    const renderFooterLinks = (items) => {
        return items.map((item, index) => (
            <li key={index}>
                {item.type === "custom" ? (
                    <a
                        href={item.url}
                        style={{ color: "#fff" }}
                        target={item.target}
                    >
                        {item.title}
                    </a>
                ) : (
                    <Link href={item.url} style={{ color: "#fff" }}>
                        {item.title}
                    </Link>
                )}
            </li>
        ));
    };

    return (
        <>
            <div className="slider-drag-cursor d-flex align-items-center justify-content-between">
                <span className="drag-icon-left">
                    <img src="/assets/img/icon/drag-arrow-left.svg" alt="" />
                </span>
                DRAG
                <span className="drag-icon-right">
                    <img src="/assets/img/icon/drag-arrow-right.svg" alt="" />
                </span>
            </div>

            <div className="preloader">
                <button className="th-btn preloaderCls">
                    Cancel Preloader
                </button>
                <div className="preloader-inner">
                    <img src="/assets/img/logo-icon3.svg" alt="img" />
                </div>
            </div>

            <div className="sidemenu-wrapper sidemenu-info">
                <div className="sidemenu-content">
                    <button className="closeButton sideMenuCls">
                        <i className="far fa-times"></i>
                    </button>
                    <div className="widget">
                        <div className="th-widget-about">
                            <div className="about-logo">
                                <Link href="/">
                                    <img
                                        src="/assets/img/logo2.svg"
                                        alt="ACTiV"
                                    />
                                </Link>
                            </div>
                            <p className="about-text">
                                Quick access to essential system features,
                                including the dashboard for an overview of
                                operations, network settings for managing
                                connectivity, system logs for tracking
                                activities.
                            </p>
                            <div className="th-social">
                                <a href="https://www.facebook.com/">
                                    <i className="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://www.twitter.com/">
                                    <i className="fab fa-twitter"></i>
                                </a>
                                <a href="https://www.linkedin.com/">
                                    <i className="fab fa-linkedin-in"></i>
                                </a>
                                <a href="https://www.whatsapp.com/">
                                    <i className="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://instagram.com/">
                                    <i className="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div className="widget">
                        <h3 className="widget_title">Recent Posts</h3>

                        <div className="recent-post-wrap">
                            <div className="recent-post d-flex align-items-center">
                                <div className="media-img">
                                    <Link href="/news">
                                        <img
                                            src="/assets/img/blog/recent-post-1-1.jpg"
                                            alt="Blog Image"
                                        />
                                    </Link>
                                </div>
                                <div className="media-body">
                                    <div className="recent-post-meta">
                                        <Link href="/news">
                                            <i className="far fa-calendar"></i>
                                            24 Jun , 2025
                                        </Link>
                                    </div>
                                    <h4 className="post-title">
                                        <Link
                                            className="text-inherit"
                                            href="/blog"
                                        >
                                            Where Vision Meets Concrete Reality
                                        </Link>
                                    </h4>
                                </div>
                            </div>
                            <div className="recent-post d-flex align-items-center">
                                <div className="media-img">
                                    <Link href="/news">
                                        <img
                                            src="/assets/img/blog/recent-post-1-2.jpg"
                                            alt="Blog Image"
                                        />
                                    </Link>
                                </div>
                                <div className="media-body">
                                    <div className="recent-post-meta">
                                        <Link href="/news">
                                            <i className="far fa-calendar"></i>
                                            22 Jun , 2025
                                        </Link>
                                    </div>
                                    <h4 className="post-title">
                                        <Link
                                            className="text-inherit"
                                            href="/blog"
                                        >
                                            Raising the Bar in Construction.
                                        </Link>
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="widget">
                        <h3 className="widget_title">Get In Touch</h3>
                        <div className="th-widget-contact">
                            <div className="info-box_text">
                                <div className="icon">
                                    <img
                                        src="/assets/img/icon/phone.svg"
                                        alt="img"
                                    />
                                </div>
                                <div className="details">
                                    <p>
                                        <a
                                            href="tel:+01234567890"
                                            className="info-box_link"
                                        >
                                            +01 234 567 890
                                        </a>
                                    </p>
                                    <p>
                                        <a
                                            href="tel:+09876543210"
                                            className="info-box_link"
                                        >
                                            +09 876 543 210
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div className="info-box_text">
                                <div className="icon">
                                    <img
                                        src="/assets/img/icon/envelope.svg"
                                        alt="img"
                                    />
                                </div>
                                <div className="details">
                                    <p>
                                        <a
                                            href="mailto:mailinfo00@activ.com"
                                            className="info-box_link"
                                        >
                                            mailinfo00@activ.com
                                        </a>
                                    </p>
                                    <p>
                                        <a
                                            href="mailto:support24@activ.com"
                                            className="info-box_link"
                                        >
                                            support24@activ.com
                                        </a>
                                    </p>
                                </div>
                            </div>
                            <div className="info-box_text">
                                <div className="icon">
                                    <img
                                        src="/assets/img/icon/location-dot.svg"
                                        alt="img"
                                    />
                                </div>
                                <div className="details">
                                    <p>
                                        789 Inner Lane, Holy park, California,
                                        USA
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="popup-search-box">
                <button className="searchClose">
                    <i className="fal fa-times"></i>
                </button>
                <form action="#">
                    <input
                        type="text"
                        placeholder="What are you looking for?"
                    />
                    <button type="submit">
                        <i className="fal fa-search"></i>
                    </button>
                </form>
            </div>

            <div className="th-menu-wrapper onepage-nav">
                <div className="th-menu-area text-center">
                    <button className="th-menu-toggle">
                        <i className="fal fa-times"></i>
                    </button>
                    <div className="mobile-logo">
                        <Link href="/">
                            <img src="/assets/img/logo2.svg" alt="ACTiV" />
                        </Link>
                    </div>
                    <div className="th-mobile-menu allow-natural-scroll">
                        <div
                            className="mobile-search-box position-relative mb-4"
                            style={{ padding: "0 20px" }}
                        >
                            <form action="#">
                                <input
                                    type="text"
                                    placeholder="Search..."
                                    className="form-control"
                                    style={{
                                        height: "50px",
                                        paddingRight: "50px",
                                        borderRadius: "5px",
                                    }}
                                />
                                <button
                                    type="submit"
                                    style={{
                                        position: "absolute",
                                        right: "35px",
                                        top: "50%",
                                        transform: "translateY(-50%)",
                                        border: "none",
                                        background: "none",
                                        color: "var(--theme-color)",
                                    }}
                                >
                                    <i className="fal fa-search"></i>
                                </button>
                            </form>
                        </div>
                        <ul>
                            {menus?.primary ? (
                                renderMenuItems(menus.primary)
                            ) : (
                                // Fallback static menu if dynamic fails (or empty)
                                <>
                                    <li>
                                        <Link href="/">Home</Link>
                                    </li>
                                </>
                            )}
                        </ul>
                        {/* Mobile Header Info */}
                        <div className="mobile-header-info mt-30 text-center">
                            <div
                                className="currency-menu mb-3 d-inline-flex align-items-center"
                                style={{
                                    border: "1px solid var(--light-color)",
                                    borderRadius: "100px",
                                    padding: "8px 20px",
                                    margin: "0 auto",
                                    gap: "8px",
                                }}
                            >
                                <i className="fa-light fa-earth-africa"></i>
                                <select
                                    className="form-select nice-select"
                                    defaultValue="English"
                                    style={{
                                        border: "none",
                                        background: "transparent",
                                        padding: 0,
                                        margin: 0,
                                        height: "auto",
                                        lineHeight: "inherit",
                                        width: "auto",
                                    }}
                                >
                                    <option>English</option>
                                    <option>Indonesian</option>
                                </select>
                            </div>
                            <div className="header-links justify-content-center">
                                <ul>
                                    <li>
                                        <Link href="/faq">FAQ</Link>
                                    </li>
                                    {auth?.user ? (
                                        <li>
                                            <Link href="/events/dashboard">
                                                Dashboard Event
                                            </Link>
                                        </li>
                                    ) : (
                                        <li>
                                            <Link href="/events/auth/login">
                                                Login Event
                                            </Link>
                                        </li>
                                    )}
                                </ul>
                            </div>
                            <div className="text-center mt-3">
                                <Link
                                    href="/contact"
                                    className="th-btn th-radius th-icon"
                                >
                                    Get In Touch{" "}
                                    <i className="fa-light fa-arrow-right-long"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <header className="th-header header-layout1 header-layout2">
                <div className="header-top">
                    <div className="container th-container">
                        <div className="row justify-content-center justify-content-xl-between align-items-center">
                            <div className="col-auto d-none d-xl-block">
                                <div className="header-links">
                                    <ul>
                                        <li className="d-none d-xl-inline-block">
                                            <i className="fa-brands fa-instagram"></i>
                                            <span>
                                                <a
                                                    href="https://www.instagram.com/activ_teknologi/"
                                                    target="__BLANK"
                                                >
                                                    @activ_teknologi
                                                </a>
                                            </span>
                                        </li>
                                        <li className="d-none d-xl-inline-block">
                                            <i className="fa-regular fa-clock"></i>
                                            <span>
                                                Sun to Friday: 8.30 am - 05.30
                                                pm
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div className="col-auto d-none d-xl-block">
                                <div className="header-right">
                                    <div className="currency-menu">
                                        <i className="fa-light fa-earth-africa"></i>
                                        <select
                                            className="form-select nice-select"
                                            defaultValue="English"
                                        >
                                            <option>English</option>
                                            <option>Indonesian</option>
                                        </select>
                                    </div>
                                    <div className="header-links">
                                        <ul>
                                            <li>
                                                <Link href="/faq">FAQ</Link>
                                            </li>
                                            {auth?.user ? (
                                                <li>
                                                    <Link href="/events/dashboard">
                                                        Dashboard Event
                                                    </Link>
                                                </li>
                                            ) : (
                                                <li>
                                                    <Link href="/events/auth/login">
                                                        Login Event
                                                    </Link>
                                                </li>
                                            )}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="sticky-wrapper">
                    <div
                        className="menu-area"
                        data-bg-src="/assets/img/bg/line-pattern.png"
                    >
                        <div className="container th-container">
                            <div className="row align-items-center justify-content-between">
                                <div className="col-xl-2 col-xxl-2 col-auto">
                                    <div className="header-logo">
                                        <Link href="/">
                                            <img
                                                src="/assets/img/logo2.svg"
                                                alt="ACTiV"
                                            />
                                        </Link>
                                    </div>
                                </div>
                                <div className="col-xl-8 col-xxl-7 col-auto text-start">
                                    <nav className="main-menu d-none d-lg-inline-block">
                                        <ul>
                                            {menus?.primary ? (
                                                renderMenuItems(menus.primary)
                                            ) : (
                                                <li>
                                                    <Link href="/">Home</Link>
                                                </li>
                                            )}
                                        </ul>
                                    </nav>
                                    <button
                                        type="button"
                                        className="th-menu-toggle d-block d-lg-none"
                                    >
                                        <i className="far fa-bars"></i>
                                    </button>
                                </div>
                                <div className="col-xl-2 col-xxl-3 col-auto d-none d-lg-block">
                                    <div className="header-button">
                                        <button
                                            type="button"
                                            className="icon-btn searchBoxToggler d-flex justify-content-center align-items-center"
                                        >
                                            <img
                                                src="/assets/img/icon/search.svg"
                                                alt="icon"
                                            />
                                        </button>
                                        <Link
                                            href="/contact"
                                            className="th-btn th-radius th-icon d-none d-xxl-block"
                                        >
                                            Get In Touch{" "}
                                            <i className="fa-light fa-arrow-right-long"></i>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {children}

            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type}
                onClose={() => setToast(null)}
            />

            <footer className="footer-wrapper bg-title footer-layout2 space-top">
                <div className="widget-area">
                    <div className="container">
                        <div className="row justify-content-between">
                            <div className="col-md-6 col-xl-3">
                                <div className="widget footer-widget">
                                    <div className="th-widget-about">
                                        <div className="about-logo">
                                            <Link href="/">
                                                <img
                                                    src="/assets/img/logo3.svg"
                                                    alt="ACTiV"
                                                />
                                            </Link>
                                        </div>
                                        <p className="about-text">
                                            we are dedicated to delivering the
                                            best comprehensive technology
                                            solutions to our clients.
                                        </p>
                                        <div className="th-social">
                                            <a href="https://www.facebook.com/">
                                                <i className="fab fa-facebook-f"></i>
                                            </a>
                                            <a href="https://www.twitter.com/">
                                                <i className="fab fa-twitter"></i>
                                            </a>
                                            <a href="https://www.linkedin.com/">
                                                <i className="fab fa-linkedin-in"></i>
                                            </a>
                                            <a href="https://www.whatsapp.com/">
                                                <i className="fab fa-whatsapp"></i>
                                            </a>
                                            <a href="https://instagram.com/">
                                                <i className="fab fa-instagram"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-xl-auto">
                                <div className="widget widget_nav_menu footer-widget">
                                    <h3 className="widget_title">
                                        Useful Link
                                    </h3>
                                    <div className="menu-all-pages-container">
                                        <ul className="menu">
                                            {menus?.footer ? (
                                                renderFooterLinks(menus.footer)
                                            ) : (
                                                <li>
                                                    <Link
                                                        href="/"
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                    >
                                                        Home
                                                    </Link>
                                                </li>
                                            )}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-xl-auto">
                                <div className="widget footer-widget">
                                    <h3 className="widget_title">
                                        Get In Touch
                                    </h3>
                                    <div className="th-widget-contact">
                                        <div className="info-box_text">
                                            <div className="icon">
                                                <img
                                                    src="/assets/img/icon/phone.svg"
                                                    alt="img"
                                                />
                                            </div>
                                            <div className="details">
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="tel:+622150110987"
                                                        className="info-box_link"
                                                    >
                                                        Tel: (+62) 2150110987
                                                    </a>
                                                </p>
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="https://api.whatsapp.com/send?phone=6285162994602"
                                                        className="info-box_link"
                                                    >
                                                        WA: (+62) 851-6299-4602
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                        <div className="info-box_text">
                                            <div className="icon">
                                                <img
                                                    src="/assets/img/icon/envelope.svg"
                                                    alt="img"
                                                />
                                            </div>
                                            <div className="details">
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="mailto:info@activ.co.id"
                                                        className="info-box_link"
                                                    >
                                                        info@activ.co.id
                                                    </a>
                                                </p>
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="mailto:sales@activ.co.id"
                                                        className="info-box_link"
                                                    >
                                                        sales@activ.co.id
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                        <div className="info-box_text">
                                            <div className="icon">
                                                <img
                                                    src="/assets/img/icon/location-dot.svg"
                                                    alt="img"
                                                />
                                            </div>
                                            <div className="details">
                                                <p>
                                                    <a
                                                        style={{
                                                            color: "#fff",
                                                        }}
                                                        href="https://maps.app.goo.gl/td2c6mkExW9zmY7C8"
                                                        target="_blank"
                                                    >
                                                        Infinity Office, Belleza
                                                        BSA 1st Floor Unit 106,
                                                        Jl. Letjen Soepeno, Keb.
                                                        Lama Jakarta Selatan
                                                        12210
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-md-6 col-xl-auto">
                                <div className="widget footer-widget footer-newsletter-style3">
                                    <h4 className="widget_title">
                                        Get updated the latest newsletter
                                    </h4>
                                    <div className="newsletter-widget">
                                        <div className="footer-search-contact">
                                            <h4 className="newsletter-title">
                                                Email Address
                                            </h4>
                                            <form action="#">
                                                <input
                                                    className="form-control"
                                                    type="email"
                                                    placeholder="Enter your email address...."
                                                />
                                                <button
                                                    type="submit"
                                                    className="icon-btn style2"
                                                >
                                                    <i className="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="copyright-wrap">
                    <div className="container">
                        <div className="row justify-content-between align-items-center">
                            <div className="col-lg-6">
                                <p className="copyright-text">
                                    Copyright © 2025 <Link href="/">ACTiV</Link>
                                    . All rights reserved.
                                </p>
                            </div>
                            <div className="col-lg-6 text-lg-end text-center">
                                <div className="footer-links">
                                    <ul>
                                        <li>
                                            <Link href="/contact">
                                                Terms &amp; Conditions
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="/contact">
                                                Partner Us
                                            </Link>
                                        </li>
                                        <li>
                                            <Link href="/contact">
                                                Privacy Policy
                                            </Link>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

            <div className="scroll-top">
                <svg
                    className="progress-circle svg-content"
                    width="100%"
                    height="100%"
                    viewBox="-1 -1 102 102"
                >
                    <path
                        d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                        style={{
                            transition: "stroke-dashoffset 10ms linear 0s",
                            strokeDasharray: "307.919, 307.919",
                            strokeDashoffset: "307.919",
                        }}
                    ></path>
                </svg>
            </div>

            {/* Modal Area */}
            <div id="login-form" className="popup-login-register mfp-hide">
                <ul className="nav" id="pills-tab" role="tablist">
                    <li className="nav-item" role="presentation">
                        <button
                            className="nav-menu"
                            id="pills-home-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-home"
                            type="button"
                            role="tab"
                            aria-controls="pills-home"
                            aria-selected="false"
                        >
                            Login
                        </button>
                    </li>
                    <li className="nav-item" role="presentation">
                        <button
                            className="nav-menu active"
                            id="pills-profile-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#pills-profile"
                            type="button"
                            role="tab"
                            aria-controls="pills-profile"
                            aria-selected="true"
                        >
                            Register
                        </button>
                    </li>
                </ul>
                <div className="tab-content" id="pills-tabContent">
                    <div
                        className="tab-pane fade"
                        id="pills-home"
                        role="tabpanel"
                        aria-labelledby="pills-home-tab"
                    >
                        <h3 className="box-title mb-30">
                            Sign in to your account
                        </h3>
                        <div className="th-login-form">
                            <form
                                action="#"
                                method="POST"
                                className="login-form ajax-contact"
                            >
                                <div className="row">
                                    <div className="form-group col-12">
                                        <label>Username or email</label>
                                        <input
                                            type="text"
                                            className="form-control"
                                            name="email"
                                            id="login_email"
                                            required
                                        />
                                    </div>
                                    <div className="form-group col-12">
                                        <label>Password</label>
                                        <input
                                            type="password"
                                            className="form-control"
                                            name="pasword"
                                            id="pasword"
                                            required
                                        />
                                    </div>
                                    <div className="form-btn mb-20 col-12">
                                        <button className="th-btn btn-fw th-radius2">
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                                <div id="forgot_url">
                                    <Link href="/my-account">
                                        Forgot password?
                                    </Link>
                                </div>
                                <p className="form-messages mb-0 mt-3"></p>
                            </form>
                        </div>
                    </div>
                    <div
                        className="tab-pane fade active show"
                        id="pills-profile"
                        role="tabpanel"
                        aria-labelledby="pills-profile-tab"
                    >
                        <h3 className="th-form-title mb-30">
                            Sign in to your account
                        </h3>
                        <form
                            action="#"
                            method="POST"
                            className="login-form ajax-contact"
                        >
                            <div className="row">
                                <div className="form-group col-12">
                                    <label>Username*</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="usename"
                                        id="usename"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label>First name*</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="firstname"
                                        id="firstname"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label>Last name*</label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="lastname"
                                        id="lastname"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label htmlFor="new_email">
                                        Your email*
                                    </label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="new_email"
                                        id="new_email"
                                        required
                                    />
                                </div>
                                <div className="form-group col-12">
                                    <label htmlFor="new_email_confirm">
                                        Confirm email*
                                    </label>
                                    <input
                                        type="text"
                                        className="form-control"
                                        name="new_email_confirm"
                                        id="new_email_confirm"
                                        required
                                    />
                                </div>
                                <div className="statement">
                                    <span className="register-notes">
                                        A password will be emailed to you.
                                    </span>
                                </div>
                                <div className="form-btn mt-20 col-12">
                                    <button className="th-btn btn-fw th-radius2">
                                        Sign up
                                    </button>
                                </div>
                            </div>
                            <p className="form-messages mb-0 mt-3"></p>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
