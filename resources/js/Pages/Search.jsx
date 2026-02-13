import React from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import EmptyState from "@/Components/Common/EmptyState";

export default function Search({ groupedResults, query }) {
    const handleSearch = (e) => {
        e.preventDefault();
        const formData = new FormData(e.currentTarget);
        const q = formData.get('q');
        if (q) {
            router.get('/search', { q });
        }
    };

    const hasResults = Object.keys(groupedResults).length > 0;

    return (
        <MainLayout>
            <Head title={`Search Results for: ${query}`} />

            <Breadcrumb
                title="Search Results"
                items={[
                    { label: "Home", link: "/" },
                    { label: "Search" },
                ]}
            />

            <section className="space-top space-extra-bottom">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-lg-8">
                            <div className="search-form-wrap mb-50">
                                <form action="/search" method="GET" onSubmit={handleSearch} className="search-form d-flex flex-column flex-sm-row gap-3">
                                    <input
                                        type="text"
                                        name="q"
                                        defaultValue={query}
                                        placeholder="Cari artikel, produk, event, layanan..."
                                        className="form-control"
                                        autoComplete="off"
                                        style={{ height: '60px', borderRadius: '10px', fontSize: '18px' }}
                                    />
                                    <button type="submit" className="th-btn th-radius" style={{ whiteSpace: 'nowrap', height: '60px' }}>
                                        Cari Lagi
                                    </button>
                                </form>
                                {query && (
                                    <p className="mt-3 text-muted text-center text-sm-start">
                                        Menampilkan hasil untuk: <strong>"{query}"</strong>
                                    </p>
                                )}
                            </div>

                            {!hasResults ? (
                                <EmptyState
                                    title="Tidak Ada Hasil Ditemukan"
                                    message={`Maaf, kami tidak menemukan apa pun yang cocok dengan kata kunci "${query}". Coba gunakan kata kunci yang lebih umum.`}
                                />
                            ) : (
                                <div className="search-results-list">
                                    {Object.entries(groupedResults).map(([type, items]) => (
                                        <div key={type} className="search-category-group mb-50">
                                            <h2 className="search-category-title mb-30" style={{
                                                fontSize: '24px',
                                                textTransform: 'uppercase',
                                                letterSpacing: '1px',
                                                borderLeft: '4px solid var(--theme-color)',
                                                paddingLeft: '15px'
                                            }}>
                                                {type}
                                            </h2>
                                            <div className="row gy-4">
                                                {items.map((item) => (
                                                    <div key={`${type}-${item.id}`} className="col-12">
                                                        <div className="search-result-item d-flex flex-column flex-md-row gap-3 gap-md-4 p-3 p-md-4" style={{
                                                            background: '#fff',
                                                            borderRadius: '15px',
                                                            boxShadow: '0 5px 15px rgba(0,0,0,0.05)',
                                                            transition: 'transform 0.3s ease',
                                                            border: '1px solid #f1f5f9'
                                                        }}>
                                                            <div className="flex-shrink-0 mx-auto mx-md-0" style={{
                                                                width: '120px',
                                                                height: '120px',
                                                                background: '#fff',
                                                                borderRadius: '12px',
                                                                overflow: 'hidden',
                                                                display: 'flex',
                                                                alignItems: 'center',
                                                                justifyContent: 'center',
                                                                border: '1px solid #f1f5f9'
                                                            }}>
                                                                <img
                                                                    src={item.image}
                                                                    alt={item.title}
                                                                    onError={(e) => {
                                                                        e.target.onerror = null;
                                                                        e.target.src = '/assets/img/logo-icon3.svg';
                                                                        e.target.style.objectFit = 'contain';
                                                                        e.target.style.padding = '15px';
                                                                    }}
                                                                    style={{
                                                                        width: '100%',
                                                                        height: '100%',
                                                                        objectFit: item.type === 'Brand' ? 'contain' : 'cover',
                                                                        padding: item.type === 'Brand' ? '15px' : '0'
                                                                    }}
                                                                />
                                                            </div>
                                                            <div className="flex-grow-1 d-flex flex-column justify-content-center text-center text-md-start">
                                                                <div className="d-flex justify-content-center justify-content-md-start mb-2">
                                                                    <span className="badge" style={{
                                                                        backgroundColor: 'rgba(var(--theme-color-rgb), 0.1)',
                                                                        color: 'var(--theme-color)',
                                                                        fontSize: '12px',
                                                                    }}>
                                                                        {item.type}
                                                                    </span>
                                                                </div>
                                                                <h3 className="h5 mb-2">
                                                                    <Link href={item.url} className="text-inherit">
                                                                        {item.title}
                                                                    </Link>
                                                                </h3>
                                                                <p className="text-muted mb-0 small line-clamp-2">
                                                                    {item.excerpt}
                                                                </p>
                                                            </div>
                                                            <div className="align-self-center mt-3 mt-md-0">
                                                                <Link href={item.url} className="icon-btn" style={{
                                                                    width: '50px',
                                                                    height: '50px',
                                                                    lineHeight: '50px',
                                                                    borderRadius: '50%',
                                                                    border: '1px solid #e2e8f0',
                                                                    display: 'inline-block',
                                                                    textAlign: 'center'
                                                                }}>
                                                                    <i className="far fa-arrow-right"></i>
                                                                </Link>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
