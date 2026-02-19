import React from 'react';
import { Link, Head, usePage } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';
import { getWhatsAppLink } from "@/Utils/whatsapp";

export default function Error({ status }) {
    const { settings } = usePage().props;

    const title = {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden',
    }[status] || 'Error';

    const description = {
        503: 'Kami sedang melakukan pemeliharaan rutin. Silakan cek kembali beberapa saat lagi.',
        500: 'Oops! Terjadi kesalahan pada server kami. Tim kami sedang menanganinya.',
        404: 'Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.',
        403: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.',
    }[status] || 'Terjadi kesalahan yang tidak terduga.';

    const waLink = getWhatsAppLink(settings?.whatsapp_number || "6285162994602", {
        cta_position: `error_page_${status}`,
        cta_label: 'Hubungi Kami (Error Page)',
        message: `Halo ACTiV, saya menemukan kendala ${status} di halaman ${window.location.href}. Mohon bantuannya.`
    });

    return (
        <MainLayout>
            <Head title={title} />
            <div className="error-v2-area">
                <div className="container">
                    <div className="error-v2-card text-center scale-up">
                        <a href={waLink} className="error-v2-icon slide-bottom" title="Hubungi Kami via WhatsApp">
                            <i className="fa-duotone fa-triangle-exclamation"></i>
                        </a>

                        <h1 className="error-v2-title">{title}</h1>

                        <p className="error-v2-text">
                            {description}
                        </p>

                        <div className="error-v2-btn-group fade-in-up">
                            <Link href="/" className="error-v2-btn-primary">
                                <i className="fa-regular fa-house me-2"></i> Ke Beranda
                            </Link>
                            <a href={waLink} className="error-v2-btn-secondary">
                                Hubungi Kami <i className="fa-brands fa-whatsapp ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
