import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import PartnerListSection from "@/Components/Sections/Partners/PartnerListSection";
import Seo from "@/Components/Common/Seo";

export default function Partners({ brands, categories, seo }) {
    return (
        <MainLayout>
            <Seo seo={seo} />
            <Breadcrumb
                title="Partners"
                items={[{ label: "Home", link: "/" }, { label: "Partners" }]}
            />

            <PartnerListSection brands={brands} categories={categories} />
        </MainLayout>
    );
}
