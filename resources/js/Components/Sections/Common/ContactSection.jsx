import React, { useState, useEffect } from "react";
import axios from "axios";
import Swal from "sweetalert2";

export default function ContactSection({
    title = "Contact Information",
    subtitle = "Let's Collaborate",
    intro_text = "Thank you for your interest in ACTiV. We're ready to discuss your business success. Reach out using the details below.",
    phone = "(+62) 2150110987",
    whatsapp = "(+62) 851-6299-4602",
    email = "sales@activ.co.id",
    address_office = "Infinity Office, Belleza BSA 1st Floor Unit 106, Jl. Letjen Soepeno, Keb. Lama Jakarta Selatan 12210",
    address_representative = "Ruko Golden Boulevard Blok S 28, Jl Pahlawan Seribu, BSD City, Kec. , Tangerang, 15119, Kota Tangerang Selatan, Banten 15119",
    map_embed_url = "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d9894.100972827648!2d106.66287000000001!3d-6.276524!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fb227ca11a57%3A0x1d99e2c09955d44d!2sPT%20Alfa%20Cipta%20Teknologi%20Virtual%20(ACTiV)!5e1!3m2!1sid!2sid!4v1765353648986!5m2!1sid!2sid",
    enable_recaptcha = true,
    form_fields = [],
    id, // Section ID from DB
    isBuilder = false, // Boolean to detect builder mode
}) {
    const [formData, setFormData] = useState({});
    const [honeypot, setHoneypot] = useState(""); // Honeypot state
    const [status, setStatus] = useState("idle"); // idle, submitting, success, error

    // Track Form View on Mount
    useEffect(() => {
        if (!isBuilder) {
            axios
                .post("/form/view", {
                    form_key: "contact_section",
                    page_slug: window.location.pathname,
                })
                .catch((err) => console.error("Tracking error:", err)); // Silent fail
        }
    }, [isBuilder]);

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (isBuilder) {
            Swal.fire({
                title: "Builder Mode",
                text: "Form submission is disabled in preview mode.",
                icon: "info",
            });
            return;
        }

        setStatus("submitting");

        try {
            let recaptchaToken = null;

            // Execute reCAPTCHA if enabled and NOT in builder
            if (enable_recaptcha && window.grecaptcha && !isBuilder) {
                try {
                    recaptchaToken = await new Promise((resolve) => {
                        window.grecaptcha.ready(() => {
                            // Site Key should be available globally from script injection or environment
                            // For security, it's better if passed or available.
                            // Since we inject script with key, we assume grecaptcha uses that context or we need key again
                            // The execute call needs the site key.
                            // We can grab it from the script tag src if needed, or better, pass it via props?
                            // The user didn't explicitly pass the key to the component props.
                            // However, the script is loaded with the key.
                            // Typically: grecaptcha.execute('KEY', ...)
                            // We need the key here.

                            // Let's defer getting the key. We can parse it from the DOM script tag.
                            const scriptTag = document.querySelector(
                                'script[src^="https://www.google.com/recaptcha/api.js"]',
                            );
                            const urlParams = new URLSearchParams(
                                scriptTag?.src.split("?")[1],
                            );
                            const siteKey = urlParams.get("render");

                            if (siteKey) {
                                window.grecaptcha
                                    .execute(siteKey, { action: "form_submit" })
                                    .then((token) => {
                                        resolve(token);
                                    });
                            } else {
                                console.warn(
                                    "reCAPTCHA site key not found in DOM.",
                                );
                                resolve(null);
                            }
                        });
                    });
                } catch (err) {
                    console.error("reCAPTCHA execution failed", err);
                    // Decide if we should block submission or proceed without token (fail open/close)
                    // Failsafe: proceed without token, backend might reject if enforced.
                }
            }

            const response = await axios.post("/form/submit", {
                form_key: "contact_section", // Keep as identifier type
                section_id: id, // Pass unique DB ID for backend config lookup
                page_slug: window.location.pathname,
                fields: formData,
                recaptcha_token: recaptchaToken,
            });

            if (response.data.success) {
                setStatus("success");
                setFormData({}); // Clear form
                // Show Success Alert
                Swal.fire({
                    title: "Message Sent!",
                    text:
                        response.data.message || "Thank you for contacting us.",
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#10b981", // Match theme green usually
                });
            } else {
                setStatus("error");
                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong. Please try again.",
                    icon: "error",
                    confirmButtonText: "Close",
                });
            }
        } catch (error) {
            console.error("Submission error:", error);
            setStatus("error");
            Swal.fire({
                title: "Error!",
                text:
                    error.response?.data?.message ||
                    "Failed to send message. Please check your connection.",
                icon: "error",
                confirmButtonText: "Close",
            });
        }
    };

    return (
        <>
            {/* Contact Area */}
            <div className="space">
                <div className="container">
                    <div className="row gy-4">
                        <div className="col-xl-5">
                            <div className="contact-infobox smoke-bg">
                                <div className="title-area">
                                    <span className="sub-title">
                                        <span className="squre-shape left me-2"></span>
                                        {subtitle}
                                        <span className="squre-shape right ms-2"></span>
                                    </span>
                                    <h3 className="sec-title">{title}</h3>
                                    <p className="sec-text">{intro_text}</p>
                                </div>
                                <div className="about-contact-grid inner-style">
                                    <span className="about-contact-icon">
                                        <i className="fa-solid fa-headphones-simple"></i>
                                    </span>
                                    <div className="about-contact-details">
                                        <span className="sec-text">
                                            Call Us For Query
                                        </span>
                                        <p className="sec-text">
                                            <a
                                                href={`tel:${phone.replace(/[^\d+]/g, "")}`}
                                            >
                                                Tel: {phone}{" "}
                                            </a>
                                            <br />
                                            <a href="#">WA: {whatsapp}</a>
                                        </p>
                                    </div>
                                </div>
                                <div className="about-contact-grid inner-style">
                                    <span className="about-contact-icon">
                                        <i className="fa-light fa-envelope-open-text"></i>
                                    </span>
                                    <div className="about-contact-details">
                                        <span className="sec-text">
                                            Email Us Anytime
                                        </span>
                                        <p className="sec-text">{email}</p>
                                    </div>
                                </div>
                                <div className="about-contact-grid inner-style">
                                    <span className="about-contact-icon">
                                        <i className="fa-thin fa-map-location-dot"></i>
                                    </span>
                                    <div className="about-contact-details">
                                        <span className="sec-text">
                                            Visit Our Office
                                        </span>
                                        <p className="sec-text">
                                            <strong>Office :</strong>{" "}
                                            {address_office} <br />
                                            <strong>
                                                Representative Office :
                                            </strong>{" "}
                                            {address_representative}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-7">
                            <div className="contact-formbox ms-xl-3 ps-xl-3">
                                <form
                                    onSubmit={handleSubmit}
                                    className="contact-form ajax-contact"
                                >
                                    <div className="row">
                                        {/* Honeypot Field - Hidden from users, visible to bots */}
                                        <input
                                            type="text"
                                            name="company_name"
                                            tabIndex="-1"
                                            autoComplete="off"
                                            style={{
                                                display: "none",
                                                opacity: 0,
                                                position: "absolute",
                                                top: 0,
                                                left: 0,
                                                height: 0,
                                                width: 0,
                                                zIndex: -1,
                                            }}
                                            value={honeypot}
                                            onChange={(e) =>
                                                setHoneypot(e.target.value)
                                            }
                                        />
                                        {/* Debug Log - Active */}
                                        {console.log(
                                            "ContactSection form_fields:",
                                            form_fields,
                                        )}
                                        {(!form_fields ||
                                            Object.keys(form_fields).length ===
                                                0) && (
                                            <p style={{ color: "red" }}>
                                                DEBUG: form_fields is empty
                                            </p>
                                        )}

                                        {(() => {
                                            const fieldsArray = Array.isArray(
                                                form_fields,
                                            )
                                                ? form_fields
                                                : form_fields &&
                                                    typeof form_fields ===
                                                        "object"
                                                  ? Object.values(form_fields)
                                                  : [];

                                            return fieldsArray.filter(
                                                (f) =>
                                                    f && typeof f === "object",
                                            );
                                        })().map((field, index) => {
                                            const widthClass =
                                                field.width || "col-sm-6";

                                            const isTextarea =
                                                field.type === "textarea";
                                            const isSelect =
                                                field.type === "select";

                                            if (isSelect) {
                                                const options = field.options
                                                    ? field.options.split(",")
                                                    : [];
                                                return (
                                                    <div
                                                        key={index}
                                                        className={`${widthClass} form-group`}
                                                    >
                                                        <select
                                                            name={field.name}
                                                            id={field.name}
                                                            className="form-select nice-select"
                                                            onChange={
                                                                handleChange
                                                            }
                                                            value={
                                                                formData[
                                                                    field.name
                                                                ] || ""
                                                            }
                                                            required={
                                                                field.required ===
                                                                    "true" ||
                                                                field.required ===
                                                                    true
                                                            }
                                                        >
                                                            <option
                                                                value=""
                                                                disabled
                                                            >
                                                                {
                                                                    field.placeholder
                                                                }
                                                            </option>
                                                            {options.map(
                                                                (opt, i) => (
                                                                    <option
                                                                        key={i}
                                                                        value={opt.trim()}
                                                                    >
                                                                        {opt.trim()}
                                                                    </option>
                                                                ),
                                                            )}
                                                        </select>
                                                    </div>
                                                );
                                            }

                                            if (isTextarea) {
                                                return (
                                                    <div
                                                        key={index}
                                                        className="form-group col-12"
                                                    >
                                                        <textarea
                                                            name={field.name}
                                                            id={field.name}
                                                            cols="30"
                                                            rows="3"
                                                            className="form-control"
                                                            onChange={
                                                                handleChange
                                                            }
                                                            value={
                                                                formData[
                                                                    field.name
                                                                ] || ""
                                                            }
                                                            placeholder={
                                                                field.placeholder
                                                            }
                                                            required={
                                                                field.required ===
                                                                    "true" ||
                                                                field.required ===
                                                                    true
                                                            }
                                                        ></textarea>
                                                        {field.icon && (
                                                            <img
                                                                src={field.icon}
                                                                alt=""
                                                            />
                                                        )}
                                                    </div>
                                                );
                                            }

                                            return (
                                                <div
                                                    key={index}
                                                    className={`${widthClass} form-group`}
                                                >
                                                    <input
                                                        type={
                                                            field.type || "text"
                                                        }
                                                        className="form-control"
                                                        name={field.name}
                                                        id={field.name}
                                                        onChange={handleChange}
                                                        value={
                                                            formData[
                                                                field.name
                                                            ] || ""
                                                        }
                                                        placeholder={
                                                            field.placeholder
                                                        }
                                                        required={
                                                            field.required ===
                                                                "true" ||
                                                            field.required ===
                                                                true
                                                        }
                                                    />
                                                    {field.icon && (
                                                        <img
                                                            src={field.icon}
                                                            alt=""
                                                        />
                                                    )}
                                                </div>
                                            );
                                        })}
                                        <div className="form-btn col-12">
                                            <button
                                                type="submit"
                                                className="th-btn th-radius"
                                                disabled={
                                                    status === "submitting"
                                                }
                                            >
                                                {status === "submitting"
                                                    ? "Sending..."
                                                    : "Submit Message"}
                                                {status !== "submitting" && (
                                                    <img
                                                        src="/assets/img/icon/plane4.svg"
                                                        alt=""
                                                    />
                                                )}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Map Area */}
            <div className="space-bottom">
                <div className="container">
                    <div className="contact-map style2">
                        <iframe
                            src={map_embed_url}
                            allowFullScreen=""
                            loading="lazy"
                        ></iframe>
                        <div className="contact-icon">
                            <img
                                src="/assets/img/icon/location-dot3.svg"
                                alt=""
                            />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
