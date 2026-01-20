import React from "react";
import { Head } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import PartnerListSection from "@/Components/Sections/Partners/PartnerListSection";

export default function Partners({ brands }) {
    return (
        <MainLayout>
            <Head title="Partners" />
            <Breadcrumb
                title="Partners"
                items={[{ label: "Home", link: "/" }, { label: "Partners" }]}
            />

            <PartnerListSection brands={brands} />
        </MainLayout>
    );
}
