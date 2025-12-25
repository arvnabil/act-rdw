import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

import Modal from '@/Components/Common/Modal';

export default function EventDetail({ event }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        phone: '',
        tickets: 1
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        // Simulate booking submission
        console.log('Booking submitted:', data);
        alert('Thank you! Your booking request has been received.');
        setIsModalOpen(false);
        reset();
    };

    return (
        <MainLayout>
            <Head title={event.title} />



            {/* Event Hero Section */}
            <section className="space-top">
                <div className="container">
                    <div className="row">
                        <div className="col-12">
                            <div className="event-hero-img position-relative rounded-20 overflow-hidden mb-60">
                                <img src={event.image} alt={event.title} className="w-100" style={{ maxHeight: '500px', objectFit: 'cover' }} />
                                <div className="event-date-box" style={{
                                    position: 'absolute',
                                    bottom: '30px',
                                    left: '30px',
                                    background: '#fff',
                                    padding: '15px 25px',
                                    borderRadius: '12px',
                                    boxShadow: '0 10px 30px rgba(0,0,0,0.15)',
                                    textAlign: 'center'
                                }}>
                                    <span className="d-block text-theme fw-bold" style={{ fontSize: '32px', lineHeight: '1' }}>{event.date.day}</span>
                                    <span className="d-block text-uppercase fw-medium" style={{ fontSize: '14px', letterSpacing: '1px' }}>{event.date.month}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="space-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-8">
                            <div className="event-details-content">
                                <div className="meta-tags mb-30">
                                    <span className="th-btn style4 sm-btn me-2">{event.category}</span>
                                    <span className="text-theme fw-medium"><i className="fa-regular fa-location-dot me-2"></i>{event.location}</span>
                                </div>
                                <h2 className="entry-title mb-30">{event.title}</h2>
                                <p className="mb-40 text-lg">{event.description}</p>

                                <h3 className="h4 mb-20">Event Schedule</h3>
                                <div className="event-schedule mb-40" style={{ border: '1px solid #eee', borderRadius: '15px', overflow: 'hidden' }}>
                                    {event.schedule.map((item, index) => (
                                        <div className="schedule-item d-flex align-items-center" key={index} style={{
                                            padding: '20px',
                                            borderBottom: index !== event.schedule.length - 1 ? '1px solid #eee' : 'none',
                                            backgroundColor: index % 2 === 0 ? '#f9f9f9' : '#fff'
                                        }}>
                                            <div className="time text-theme fw-bold" style={{ minWidth: '100px' }}>{item.time}</div>
                                            <div className="activity fw-medium">{item.activity}</div>
                                        </div>
                                    ))}
                                </div>

                                <h3 className="h4 mb-4">Featured Speakers</h3>
                                <div className="row gy-4 mb-40">
                                    {event.speakers.map((speaker, index) => (
                                        <div className="col-md-4 col-sm-6" key={index}>
                                            <div
                                                className="speaker-card text-center bg-white rounded-4 h-100 position-relative"
                                                style={{
                                                    padding: '30px 20px',
                                                    boxShadow: '0 10px 30px rgba(0,0,0,0.03)',
                                                    transition: 'all 0.3s ease',
                                                    border: '1px solid #f0f0f0'
                                                }}
                                                onMouseEnter={(e) => {
                                                    e.currentTarget.style.transform = 'translateY(-10px)';
                                                    e.currentTarget.style.boxShadow = '0 20px 40px rgba(74, 193, 94, 0.15)'; // Theme color shadow hint
                                                    e.currentTarget.style.borderColor = 'var(--theme-color)';
                                                }}
                                                onMouseLeave={(e) => {
                                                    e.currentTarget.style.transform = 'translateY(0)';
                                                    e.currentTarget.style.boxShadow = '0 10px 30px rgba(0,0,0,0.03)';
                                                    e.currentTarget.style.borderColor = '#f0f0f0';
                                                }}
                                            >
                                                <div
                                                    className="img-wrap rounded-circle overflow-hidden mx-auto mb-4 position-relative"
                                                    style={{
                                                        width: '100px',
                                                        height: '100px',
                                                        border: '3px solid #fff',
                                                        boxShadow: '0 5px 15px rgba(0,0,0,0.1)'
                                                    }}
                                                >
                                                    <img src={speaker.image} alt={speaker.name} className="w-100 h-100 object-fit-cover" />
                                                </div>
                                                <h5 className="h6 mb-2 fw-bold text-dark">{speaker.name}</h5>
                                                <p className="text-sm text-theme mb-3 fw-medium text-uppercase" style={{ fontSize: '12px', letterSpacing: '1px' }}>{speaker.role}</p>

                                                <div className="social-links d-flex justify-content-center gap-2 opacity-0-hover show-on-hover">
                                                    {['linkedin', 'twitter', 'instagram'].map(icon => (
                                                        <a key={icon} href="#" className="rounded-circle d-flex align-items-center justify-content-center text-muted hover-theme"
                                                            style={{ width: '32px', height: '32px', background: '#f5f5f5', transition: '0.2s' }}>
                                                            <i className={`fa-brands fa-${icon}`}></i>
                                                        </a>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>

                                <div className="event-map rounded-20 overflow-hidden">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.521260322283!2d106.81956135000001!3d-6.194741399999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5390917b759%3A0x6b45e67356080477!2sPT%20Telkom%20Indonesia%20(Persero)%20Tbk!5e0!3m2!1sen!2sid!4v1684567890123!5m2!1sen!2sid" width="100%" height="350" style={{ border: 0 }} allowFullScreen="" loading="lazy"></iframe>
                                </div>
                            </div>
                        </div>

                        <div className="col-lg-4">
                            <aside className="sidebar-area pt-3" style={{ position: 'sticky', top: '100px' }}>
                                <div className="widget widget-event-info p-4 rounded-20" style={{ backgroundColor: '#f4f7fa' }}>
                                    <h3 className="widget_title">Booking Info</h3>
                                    <div className="event-info-list mb-30">
                                        <div className="info-item mb-3 d-flex align-items-center">
                                            <div className="icon-box me-3 rounded-circle d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px', background: '#fff', color: 'var(--theme-color)' }}>
                                                <i className="fa-regular fa-calendar"></i>
                                            </div>
                                            <div>
                                                <small className="d-block text-muted" style={{ fontSize: '12px' }}>Date</small>
                                                <span className="fw-bold">{event.date.full}</span>
                                            </div>
                                        </div>
                                        <div className="info-item mb-3 d-flex align-items-center">
                                            <div className="icon-box me-3 rounded-circle d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px', background: '#fff', color: 'var(--theme-color)' }}>
                                                <i className="fa-regular fa-clock"></i>
                                            </div>
                                            <div>
                                                <small className="d-block text-muted" style={{ fontSize: '12px' }}>Time</small>
                                                <span className="fw-bold">{event.time}</span>
                                            </div>
                                        </div>
                                        <div className="info-item mb-3 d-flex align-items-center">
                                            <div className="icon-box me-3 rounded-circle d-flex align-items-center justify-content-center" style={{ width: '40px', height: '40px', background: '#fff', color: 'var(--theme-color)' }}>
                                                <i className="fa-regular fa-ticket"></i>
                                            </div>
                                            <div>
                                                <small className="d-block text-muted" style={{ fontSize: '12px' }}>Cost</small>
                                                <span className="fw-bold text-theme">{event.price}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <button
                                        className="th-btn th-radius th-icon w-80"
                                        onClick={() => setIsModalOpen(true)}
                                    >
                                        Book Ticket Now <i className="fa-solid fa-ticket"></i>
                                    </button>
                                </div>

                                <div className="widget widget-organizer mt-30">
                                    <h3 className="widget_title">Organizer</h3>
                                    <div className="organizer-info d-flex align-items-center mb-20">
                                        <div className="org-img me-3 rounded-circle overflow-hidden" style={{ width: '50px', height: '50px' }}>
                                            <img src="/assets/img/team/team_1_1.jpg" alt="Organizer" className="w-100 h-100 object-fit-cover" />
                                        </div>
                                        <div>
                                            <h4 className="h6 mb-0">{event.organizer}</h4>
                                            <a href={`mailto:${event.email}`} className="text-xs">{event.email}</a>
                                        </div>
                                    </div>
                                    <a href={`tel:${event.phone}`} className="th-btn style6 w-100"><i className="fa-solid fa-phone me-2"></i>Contact</a>
                                </div>
                            </aside>
                        </div>
                    </div>
                </div>
            </section>

            {/* Booking Modal */}
            <Modal show={isModalOpen} onClose={() => setIsModalOpen(false)}>
                <div className="text-center mb-4">
                    <span className="sub-title text-theme mb-2 d-block">{event.title}</span>
                    <h3 className="box-title mb-0">Reserve Your Spot</h3>
                    <div className="divider mx-auto mt-3" style={{ width: '40px', height: '3px', backgroundColor: 'var(--theme-color)', borderRadius: '2px' }}></div>
                </div>

                <form onSubmit={handleSubmit} className="row g-3">
                    <div className="col-12">
                        <label className="form-label fw-bold text-dark mb-2">Full Name</label>
                        <div className="input-group">
                            <span className="input-group-text bg-light border-0 ps-3"><i className="fa-regular fa-user text-theme"></i></span>
                            <input
                                type="text"
                                className="form-control bg-light border-0 p-3"
                                placeholder="Enter your full name"
                                value={data.name}
                                onChange={e => setData('name', e.target.value)}
                                required
                                style={{ height: '52px', borderRadius: '0 10px 10px 0', fontSize: '15px' }}
                            />
                        </div>
                    </div>

                    <div className="col-12">
                        <label className="form-label fw-bold text-dark mb-2">Email Address</label>
                        <div className="input-group">
                            <span className="input-group-text bg-light border-0 ps-3"><i className="fa-regular fa-envelope text-theme"></i></span>
                            <input
                                type="email"
                                className="form-control bg-light border-0 p-3"
                                placeholder="Enter your email"
                                value={data.email}
                                onChange={e => setData('email', e.target.value)}
                                required
                                style={{ height: '52px', borderRadius: '0 10px 10px 0', fontSize: '15px' }}
                            />
                        </div>
                    </div>

                    <div className="col-md-6">
                        <label className="form-label fw-bold text-dark mb-2">Phone Number</label>
                        <div className="input-group">
                            <span className="input-group-text bg-light border-0 ps-3"><i className="fa-regular fa-phone text-theme"></i></span>
                            <input
                                type="number"
                                className="form-control bg-light border-0 p-3"
                                placeholder="+62..."
                                min="1"
                                value={data.phone}
                                onChange={e => setData('phone', e.target.value)}
                                required
                                style={{ height: '52px', borderRadius: '0 10px 10px 0', fontSize: '15px' }}
                            />
                        </div>
                    </div>

                    <div className="col-md-6">
                        <label className="form-label fw-bold text-dark mb-2">Quantity</label>
                        <div className="input-group">
                            <span className="input-group-text bg-light border-0 ps-3"><i className="fa-regular fa-ticket text-theme"></i></span>
                            <input
                                type="number"
                                className="form-control bg-light border-0 p-3"
                                min="1"
                                max="10"
                                value={data.tickets}
                                onChange={e => setData('tickets', e.target.value)}
                                style={{ height: '52px', borderRadius: '0 10px 10px 0', fontSize: '15px' }}
                            />
                        </div>
                    </div>

                    <div className="col-12 mt-4 pt-2">
                        <button type="submit" className="th-btn w-100 th-radius shadow-none" disabled={processing} style={{ height: '56px', fontSize: '16px' }}>
                            {processing ? 'Processing...' : `Confirm Booking • ${event.price === 'Free' ? 'Free' : event.price}`}
                            <i className="fa-solid fa-arrow-right ms-2"></i>
                        </button>
                        <p className="text-xs text-center text-muted mt-3 mb-0"><i className="fa-regular fa-lock me-1"></i> Your information is safe with us</p>
                    </div>
                </form>
            </Modal>
        </MainLayout>
    );
}
