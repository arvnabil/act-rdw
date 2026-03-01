import React from "react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ClientListSection from "@/Components/Sections/Clients/ClientListSection";
import Seo from "@/Components/Common/Seo";

export default function Clients({ clients, categories, filters, seo, breadcrumb_image, show_breadcrumb = true, page_title }) {
    return (
        <MainLayout>
            <Seo seo={seo} />
            {show_breadcrumb && (
                <Breadcrumb
                    title={page_title || "Penghargaan & Client"}
                    items={[
                        { label: "Home", link: "/" },
                        { label: page_title || "Clients" },
                    ]}
                    bgImage={breadcrumb_image}
                />
            )}

            <ClientListSection 
                clients={clients.data} 
                pagination={clients.links}
                categories={categories} 
                filters={filters}
            />
        </MainLayout>
    );
}
