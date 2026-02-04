import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import { Head, Link } from "@inertiajs/react";
import SectionRenderer from "@/Components/SectionRenderer";
import Breadcrumb from "@/Components/Common/Breadcrumb";

export default function DynamicPage({ page, sections, seo }) {
    if (!page) return null;

    return (
        <MainLayout>
            <Head>
                {/* Meta Tags */}
                <title>{seo?.meta?.title}</title>
                <meta name="description" content={seo?.meta?.description} />
                <meta name="keywords" content={seo?.meta?.keywords} />
                <link rel="canonical" href={seo?.meta?.canonical_url} />
                {seo?.meta?.noindex && <meta name="robots" content="noindex" />}

                {/* Open Graph */}
                <meta property="og:title" content={seo?.og?.title} />
                <meta
                    property="og:description"
                    content={seo?.og?.description}
                />
                <meta property="og:image" content={seo?.og?.image} />
                <meta property="og:url" content={seo?.og?.url} />
                <meta property="og:type" content={seo?.og?.type} />

                {/* Twitter */}
                <meta name="twitter:card" content={seo?.og?.twitter_card} />
                <meta name="twitter:title" content={seo?.og?.title} />
                <meta
                    name="twitter:description"
                    content={seo?.og?.description}
                />
                <meta name="twitter:image" content={seo?.og?.image} />

                {/* Structured Data (JSON-LD) */}
                {seo.json_ld &&
                    (() => {
                        try {
                            const parsedJson = JSON.parse(seo.json_ld);
                            // Handle both single object and array of objects
                            const schemas = Array.isArray(parsedJson)
                                ? parsedJson
                                : [parsedJson];

                            return schemas.map((schema, index) => (
                                <script
                                    key={index}
                                    type="application/ld+json"
                                    dangerouslySetInnerHTML={{
                                        __html: JSON.stringify(schema),
                                    }}
                                />
                            ));
                        } catch (e) {
                            console.error("Invalid JSON-LD", e);
                            return null;
                        }
                    })()}
            </Head>

            {/* Breadcrumb - Rendered if enabled in Page model */}
            {page.show_breadcrumb && (
                <Breadcrumb
                    title={page.title}
                    items={[
                        { label: "Beranda", link: "/" },
                        { label: page.title }, // Current page label
                    ]}
                />
            )}

            <main>
                {/* Render Sections in Order */}
                {sections &&
                    sections.map((section) => (
                        <SectionRenderer
                            key={section.id}
                            sectionKey={section.section_key}
                            props={section.props}
                        />
                    ))}

                {/* If no sections, maybe mock a default empty state or just empty */}
                {(!sections || sections.length === 0) && (
                    <div className="container py-5 text-center">
                        <h2 className="text-2xl font-bold text-gray-300">
                            Halaman Kosong
                        </h2>
                    </div>
                )}
            </main>
        </MainLayout>
    );
}
