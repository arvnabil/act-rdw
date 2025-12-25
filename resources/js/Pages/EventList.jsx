import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import Breadcrumb from '@/Components/Common/Breadcrumb';
import Lottie from 'lottie-react';
import noDataAnimation from '../Components/Animations/No-Data.json';

export default function EventList({ events }) {
    // State for filters
    const [selectedCategories, setSelectedCategories] = useState([]);
    const [searchQuery, setSearchQuery] = useState('');
    const [sortBy, setSortBy] = useState('newest');

    // List of dummy categories
    const dummyCategories = [
        'Beauty', 'Book', 'Business', 'Charity & Social Enterprise',
        'Education', 'Entertainment', 'Family & Child', 'Food & Drink',
        'Health & Wellness', 'Home & Decor', 'Industrial', 'IT & Tech',
        'Job & Career', 'Money & Wealth', 'Others'
    ];

    // randomize categories for dummy events once on mount
    const enhancedEvents = React.useMemo(() => {
        return events.data.map(event => {
            const randomCategory = dummyCategories[Math.floor(Math.random() * dummyCategories.length)];
            return { ...event, category: randomCategory };
        });
    }, [events.data]);

    // Get unique categories from enhanced events
    const categories = [...new Set([...dummyCategories])].sort();

    // Toggle category selection
    const handleCategoryChange = (category) => {
        setSelectedCategories(prev => {
            if (prev.includes(category)) return prev.filter(c => c !== category);
            return [...prev, category];
        });
    };

    // Filter and Sort events
    const processedEvents = React.useMemo(() => {
        let result = enhancedEvents;

        // 1. Filter by Category
        if (selectedCategories.length > 0) {
            result = result.filter(event => selectedCategories.includes(event.category));
        }

        // 2. Filter by Search Query
        if (searchQuery) {
            const lowerQuery = searchQuery.toLowerCase();
            result = result.filter(event =>
                event.title.toLowerCase().includes(lowerQuery) ||
                event.location.toLowerCase().includes(lowerQuery)
            );
        }

        // 3. Sort
        result = [...result].sort((a, b) => {
            if (sortBy === 'newest') {
                return new Date(b.date_full) - new Date(a.date_full); // Event Terbaru
            } else if (sortBy === 'updated') {
                return 0; // Update Terbaru (Mock: No change for now)
            } else if (sortBy === 'ending') {
                return new Date(a.date_full) - new Date(b.date_full); // Segera Berakhir (Closest date first)
            }
            return 0;
        });

        return result;
    }, [enhancedEvents, selectedCategories, searchQuery, sortBy]);


    return (
        <MainLayout>
            <Head title="Events" />

            <section className="space-top space-extra-bottom">
                <div className="container" style={{ position: 'relative' }}>
                    <div className="row">
                        {/* Sidebar (Sticky Title & Filters) */}
                        <div className="col-lg-3 mb-5 mb-lg-0">
                            <aside className="sidebar-area py-3" style={{ position: 'sticky', top: '100px', height: 'fit-content' }}>
                                {/* Moved Title & Description Here */}
                                <div className="mb-5">
                                    <h1 className="fw-bold text-dark mb-2" style={{ fontSize: '36px' }}>Daftar Event</h1>
                                    <p className="text-muted fs-6 mb-0" style={{ lineHeight: '1.5' }}>Kembangkan jaringan dan belajar dari developer terbaik</p>

                                </div>
                            </aside>
                        </div>

                        {/* Event Grid & Toolbar */}
                        <div className="col-lg-9">
                            {/* Toolbar: Search & Sort - Moved Here */}
                            <div className="d-flex flex-column flex-md-row gap-3 justify-content-between align-items-center mb-5">
                                {/* Search Bar */}
                                <div className="position-relative flex-grow-1 w-100" style={{ maxWidth: '100%' }}>
                                    <input
                                        type="text"
                                        className="form-control shadow-none border-0 bg-light py-2 ps-4 pe-5 rounded-pill"
                                        placeholder="Cari Event"
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                        style={{ fontSize: '15px' }}
                                    />
                                    <button className="btn position-absolute top-50 end-0 translate-middle-y me-2 text-muted border-0 bg-transparent">
                                        <i className="fa-regular fa-magnifying-glass"></i>
                                    </button>
                                </div>

                                {/* Category Filter */}
                                <div className="d-flex align-items-center">
                                    <span className="text-muted me-2 small fw-medium" style={{ fontSize: '13px' }}>Kategori:</span>
                                    <select
                                        className="form-select shadow-none border text-dark fw-medium"
                                        style={{
                                            backgroundColor: '#fff',
                                            cursor: 'pointer',
                                            fontSize: '14px',
                                            width: 'auto',
                                            minWidth: '200px',
                                            borderRadius: '8px',
                                            borderColor: '#e5e5e5'
                                        }}
                                        onChange={(e) => {
                                            const val = e.target.value;
                                            setSelectedCategories(val === 'all' ? [] : [val]);
                                        }}
                                        value={selectedCategories.length > 0 ? selectedCategories[0] : 'all'}
                                    >
                                        <option value="all">Semua Kategori</option>
                                        {categories.map((cat) => (
                                            <option key={cat} value={cat}>{cat}</option>
                                        ))}
                                    </select>
                                </div>

                                {/* Sort Filter */}
                                <div className="d-flex align-items-center">
                                    <span className="text-muted me-2 small fw-medium" style={{ fontSize: '13px' }}>Urutkan:</span>
                                    <select
                                        className="form-select shadow-none border text-dark fw-medium"
                                        style={{
                                            backgroundColor: '#fff',
                                            cursor: 'pointer',
                                            fontSize: '14px',
                                            width: 'auto',
                                            minWidth: '200px',
                                            borderRadius: '8px',
                                            borderColor: '#e5e5e5'
                                        }}
                                        value={sortBy}
                                        onChange={(e) => setSortBy(e.target.value)}
                                    >
                                        <option value="newest">Event Terbaru</option>
                                        <option value="updated">Update Terbaru</option>
                                        <option value="ending">Segera Berakhir</option>
                                    </select>
                                </div>


                            </div>

                            {processedEvents.length > 0 ? (
                                <div className="row gy-30">
                                    {processedEvents.map((event) => {
                                        const quota = Math.floor(Math.random() * 500) + 10;
                                        const daysLeft = Math.floor(Math.random() * 30) + 1;
                                        const organizer = "ACTiV Team";

                                        return (
                                            <div className="col-md-6" key={event.id}>
                                                <div className="event-card bg-white h-100 d-flex flex-column" style={{
                                                    borderRadius: '16px',
                                                    overflow: 'hidden',
                                                    transition: 'all 0.3s ease',
                                                    boxShadow: '0 10px 30px rgba(0,0,0,0.05)',
                                                    border: '1px solid #f0f0f0'
                                                }}>
                                                    {/* Clickable Image Area */}
                                                    <Link href={event.link} className="event-img position-relative d-block overflow-hidden">
                                                        <img
                                                            src={event.image}
                                                            alt={event.title}
                                                            className="w-100"
                                                            style={{ height: '220px', objectFit: 'cover', transition: 'transform 0.5s ease' }}
                                                            onMouseOver={(e) => e.target.style.transform = 'scale(1.05)'}
                                                            onMouseOut={(e) => e.target.style.transform = 'scale(1)'}
                                                        />
                                                    </Link>

                                                    <div className="event-content d-flex flex-column flex-grow-1 p-4">
                                                        {/* Category Badge */}
                                                        <div className="mb-3">
                                                            <span className="d-inline-block px-3 py-1 fw-medium text-dark" style={{
                                                                border: '1px solid #e0e0e0',
                                                                borderRadius: '8px',
                                                                fontSize: '12px',
                                                                backgroundColor: '#fff'
                                                            }}>
                                                                {event.category || 'Seminar'}
                                                            </span>
                                                        </div>

                                                        {/* Title */}
                                                        <h3 className="event-title h5 mb-2 fw-bold" style={{ lineHeight: '1.4' }}>
                                                            <Link href={event.link} className="text-decoration-none" style={{ color: 'var(--theme-color)', transition: '0.2s' }}>
                                                                {event.title}
                                                            </Link>
                                                        </h3>

                                                        {/* Organizer */}
                                                        <p className="text-muted small mb-3" style={{ fontSize: '13px' }}>
                                                            oleh: <span className="fw-medium text-dark">{organizer}</span>
                                                        </p>

                                                        {/* Description (Truncated) */}
                                                        <p className="event-desc text-muted mb-4 flex-grow-1" style={{ fontSize: '14px', lineHeight: '1.6', overflow: 'hidden', display: '-webkit-box', WebkitLineClamp: '3', WebkitBoxOrient: 'vertical' }}>
                                                            {event.description}
                                                        </p>

                                                        {/* Divider */}
                                                        <div className="w-100 bg-light mb-3" style={{ height: '1px' }}></div>

                                                        {/* Footer - Entire row clickable */}
                                                        <Link href={event.link} className="d-flex justify-content-between align-items-center text-decoration-none w-100">
                                                            <span className="text-muted small hover-theme">
                                                                Sisa Kuota: <span className="fw-bold text-dark">{quota}</span>
                                                            </span>

                                                            <span className="text-muted small">
                                                                {daysLeft} Hari Lagi
                                                            </span>
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        )
                                    })}
                                </div>
                            ) : (
                                <div className="text-center py-5">
                                    <div className="mb-3 d-flex justify-content-center">
                                        <div style={{ width: '250px', height: '250px' }}>
                                            {/* Local Lottie Animation */}
                                            <Lottie
                                                animationData={noDataAnimation}
                                                loop={true}
                                            />
                                        </div>
                                    </div>
                                    <h3 className="h5 fw-bold text-dark mb-2">No Events Found</h3>
                                    <p className="text-muted small mb-4">Try adjusting your search or filters.</p>
                                    <button
                                        onClick={() => {
                                            setSelectedCategories([]);
                                            setSearchQuery('');
                                        }}
                                        className="th-btn style4 sm-btn"
                                    >
                                        Clear All Filters
                                    </button>
                                </div>
                            )}

                            {/* Pagination - Only show if NO fitlers are active (server side pagination) */}
                            {selectedCategories.length === 0 && searchQuery === '' && events.links && events.links.length > 3 && (
                                <div className="th-pagination mt-60 text-center">
                                    <ul>
                                        {events.links.map((link, index) => (
                                            <li key={index}>
                                                {link.url ? (
                                                    <Link
                                                        href={link.url}
                                                        className={`${link.active ? 'active' : ''} ${link.className || ''}`}
                                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                                        preserveScroll
                                                    />
                                                ) : (
                                                    null
                                                )}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
