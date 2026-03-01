import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import PartnerListSection from "@/Components/Sections/Partners/PartnerListSection";
import Seo from "@/Components/Common/Seo";

export default function Partners({ brands, categories, seo, breadcrumb_image, show_breadcrumb = true }) {
    return (
        <MainLayout>
            <Seo seo={seo} />
            {show_breadcrumb && (
                <Breadcrumb
                    title="Official Partners"
                    items={[
                        { label: "Home", link: "/" },
                        { label: "Partners" },
                    ]}
                    bgImage={breadcrumb_image}
                />
            )}

            <PartnerListSection brands={brands} categories={categories} />
        </MainLayout>
    );
}
