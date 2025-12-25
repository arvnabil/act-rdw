import React from 'react';

export default function ContactSection() {
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
                                        <span className="squre-shape left me-2"></span>Let's Collaborate
                                        <span className="squre-shape right ms-2"></span>
                                    </span>
                                    <h3 className="sec-title">Contact Information</h3>
                                    <p className="sec-text">
                                        Thank you for your interest in ACTiV. We're ready to discuss your business success. Reach out using the details below.
                                    </p>
                                </div>
                                <div className="about-contact-grid inner-style">
                                    <span className="about-contact-icon">
                                        <i className="fa-solid fa-headphones-simple"></i>
                                    </span>
                                    <div className="about-contact-details">
                                        <span className="sec-text">Call Us For Query</span>
                                        <p className="sec-text">
                                            <a href="tel:+622150110987">Tel: (+62) 2150110987 </a>
                                            <br />
                                            <a href="#">WA: (+62) 851-6299-4602</a>
                                        </p>
                                    </div>
                                </div>
                                <div className="about-contact-grid inner-style">
                                    <span className="about-contact-icon">
                                        <i className="fa-light fa-envelope-open-text"></i>
                                    </span>
                                    <div className="about-contact-details">
                                        <span className="sec-text">Email Us Anytime</span>
                                        <p className="sec-text">sales@activ.co.id</p>
                                    </div>
                                </div>
                                <div className="about-contact-grid inner-style">
                                    <span className="about-contact-icon">
                                        <i className="fa-thin fa-map-location-dot"></i>
                                    </span>
                                    <div className="about-contact-details">
                                        <span className="sec-text">Visit Our Office</span>
                                        <p className="sec-text">
                                            <strong>Office :</strong> Infinity Office, Belleza BSA 1st Floor Unit 106, Jl. Letjen Soepeno, Keb. Lama Jakarta Selatan 12210 <br />
                                            <strong>Representative Office :</strong> Ruko Golden Boulevard Blok S 28, Jl Pahlawan Seribu, BSD City, Kec. , Tangerang, 15119, Kota Tangerang Selatan, Banten 15119
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-7">
                            <div className="contact-formbox ms-xl-3 ps-xl-3">
                                <form action="mail.php" method="POST" className="contact-form ajax-contact">
                                    <div className="row">
                                        <div className="col-sm-6 form-group">
                                            <input type="text" className="form-control" name="name" id="name3" placeholder="Your Name" />
                                            <img src="/assets/img/icon/user.svg" alt="" />
                                        </div>
                                        <div className="col-sm-6 form-group">
                                            <input type="email" className="form-control" name="email" id="contact_email" placeholder="Email Address" />
                                            <img src="/assets/img/icon/mail.svg" alt="" />
                                        </div>
                                        <div className="col-sm-6 form-group">
                                            <input type="text" className="form-control" name="number" id="number" placeholder="Phone Number" />
                                            <img src="/assets/img/icon/call.svg" alt="" />
                                        </div>
                                        <div className="col-sm-6 form-group">
                                            <select name="subject" id="subject" className="form-select nice-select">
                                                <option value="Select Subject" disabled>Select Subject</option>
                                                <option value="Quotation">Quotation</option>
                                                <option value="Technical Support">Technical Support</option>
                                                <option value="Billing">Billing</option>
                                            </select>
                                        </div>
                                        <div className="form-group col-12">
                                            <textarea name="message" id="message" cols="30" rows="3" className="form-control" placeholder="Your Message"></textarea>
                                            <img src="/assets/img/icon/chat.svg" alt="" />
                                        </div>
                                        <div className="form-btn col-12">
                                            <button type="submit" className="th-btn th-radius">
                                                Submit Message
                                                <img src="/assets/img/icon/plane4.svg" alt="" />
                                            </button>
                                        </div>
                                    </div>
                                    <p className="form-messages mb-0 mt-3"></p>
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
                            src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d9894.100972827648!2d106.66287000000001!3d-6.276524!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fb227ca11a57%3A0x1d99e2c09955d44d!2sPT%20Alfa%20Cipta%20Teknologi%20Virtual%20(ACTiV)!5e1!3m2!1sid!2sid!4v1765353648986!5m2!1sid!2sid"
                            allowFullScreen=""
                            loading="lazy"
                        ></iframe>
                        <div className="contact-icon">
                            <img src="/assets/img/icon/location-dot3.svg" alt="" />
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
