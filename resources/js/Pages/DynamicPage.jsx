import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import SectionRenderer from "@/Components/SectionRenderer";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import Seo from "@/Components/Common/Seo";

export default function DynamicPage({ page, sections, seo }) {
    if (!page) return null;

    return (
        <MainLayout>
            <Seo seo={seo} />

            {/* Breadcrumb - Rendered if enabled in Page model */}
            {page.show_breadcrumb && (
                <Breadcrumb
                    title={page.title}
                    bgImage={page.breadcrumb_image} // Pass the custom thumbnail
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
