import React from "react";
import { Head } from "@inertiajs/react";

/**
 * Enterprise SEO Component
 * Centralizes meta tags and JSON-LD structured data.
 */
export default function Seo({ seo }) {
    if (!seo) return null;

    // Use structured tags if available, otherwise fallback to meta array (which is deprecated but still supported)
    const tags = seo.tags || {};
    const metaArray = seo.meta || [];

    // JSON-LD handling
    const jsonLd = seo.json_ld || seo.jsonLd;

    return (
        <Head>
            {/* Primary Meta Tags */}
            <title>{tags.title}</title>
            <meta name="title" content={tags.title} />
            <meta name="description" content={tags.description} />
            {tags.keywords && <meta name="keywords" content={tags.keywords} />}
            <link rel="canonical" href={tags.canonical_url} />

            {/* Open Graph / Facebook */}
            <meta property="og:type" content={tags.og_type || "website"} />
            <meta property="og:url" content={tags.canonical_url} />
            <meta property="og:title" content={tags.og_title || tags.title} />
            <meta property="og:description" content={tags.og_description || tags.description} />
            <meta property="og:image" content={tags.og_image} />

            {/* Twitter */}
            <meta name="twitter:card" content={tags.twitter_card || "summary_large_image"} />
            <meta name="twitter:url" content={tags.canonical_url} />
            <meta name="twitter:title" content={tags.og_title || tags.title} />
            <meta name="twitter:description" content={tags.og_description || tags.description} />
            <meta name="twitter:image" content={tags.og_image} />

            {/* Indexing */}
            <meta name="robots" content={tags.noindex ? "noindex, nofollow" : "index, follow"} />

            {/* JSON-LD Structured Data */}
            {jsonLd && (() => {
                try {
                    const parsedJson = typeof jsonLd === 'string' ? JSON.parse(jsonLd) : jsonLd;
                    const schemas = Array.isArray(parsedJson) ? parsedJson : [parsedJson];

                    return schemas.map((schema, index) => (
                        <script
                            key={index}
                            type="application/ld+json"
                            dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
                        />
                    ));
                } catch (e) {
                    console.error("Invalid JSON-LD on page", e);
                    return null;
                }
            })()}

            {/* For backward compatibility with routes that might still use the old array format */}
            {metaArray.map((m, i) => {
                if (m.name === 'description' || m.name === 'robots' || m.rel === 'canonical') return null; // Already handled
                if (m.name) return <meta key={i} name={m.name} content={m.content} />;
                if (m.property) return <meta key={i} property={m.property} content={m.content} />;
                if (m.rel) return <link key={i} rel={m.rel} href={m.href} />;
                return null;
            })}
        </Head>
    );
}
