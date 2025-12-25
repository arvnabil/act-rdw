import React from 'react';
import { Link } from '@inertiajs/react';

export default function Breadcrumb({ title, items }) {
    return (
        <div className="breadcumb-area">
            <div className="breadcumb-wrapper" data-bg-src="/assets/img/bg/breadcumb-bg.jpg">
                <div className="container">
                    <div className="breadcumb-content">
                        <h1 className="breadcumb-title">{title}</h1>
                        <ul className="breadcumb-menu">
                            {items.map((item, index) => (
                                <li key={index}>
                                    {item.link ? (
                                        <Link href={item.link}>{item.label}</Link>
                                    ) : (
                                        item.label
                                    )}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
}
