import React from 'react';
import { Link, Head } from '@inertiajs/react';
import MainLayout from '@/Layouts/MainLayout';

export default function Error({ status }) {
    const title = {
        503: '503: Service Unavailable',
        500: '500: Server Error',
        404: '404: Page Not Found',
        403: '403: Forbidden',
    }[status];

    const description = {
        503: 'Sorry, we are doing some maintenance. Please check back soon.',
        500: 'Whoops, something went wrong on our servers.',
        404: 'Sorry, the page you are looking for could not be found.',
        403: 'Sorry, you are forbidden from accessing this page.',
    }[status];

    return (
        <MainLayout>
            <Head title={title} />
            <div className="space" style={{ minHeight: '60vh', display: 'flex', alignItems: 'center' }}>
                <div className="container">
                    <div className="error-content text-center">
                        <h2 className="error-title transform-none">{status}</h2>
                        <h3 className="error-subtitle text-theme fw-semibold">{title}</h3>
                        <p className="error-text">{description}</p>
                        <Link href="/" className="th-btn style3">
                            Back To Home <i className="fa-regular fa-arrow-right ms-2"></i>
                        </Link>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
