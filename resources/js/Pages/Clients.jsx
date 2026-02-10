import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ClientListSection from "@/Components/Sections/Clients/ClientListSection";
import Seo from "@/Components/Common/Seo";

export default function Clients({ clients, categories, seo }) {
    return (
        <MainLayout>
            <Seo seo={seo} />
            <Breadcrumb
                title="Our Clients"
                items={[{ label: "Home", link: "/" }, { label: "Clients" }]}
            />

            <ClientListSection clients={clients} categories={categories} />
        </MainLayout>
    );
}
