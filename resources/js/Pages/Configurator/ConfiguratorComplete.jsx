import React, { useState } from "react";
import { usePage, Link } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import ConfiguratorHero from "@/Components/Sections/Configurator/ConfiguratorHero";
import ConfiguratorSummarySection from "@/Components/Sections/Configurator/ConfiguratorSummarySection";

export default function ConfiguratorComplete() {
    // Get data passed from the controller/route
    const { selection, userInfo, uuid, summaryItems } = usePage().props;
    const [quantities, setQuantities] = useState({});
    const [showToast, setShowToast] = useState(false);
    const [showShareModal, setShowShareModal] = useState(false);
    const [toastMessage, setToastMessage] = useState("");

    // Detect Configurator Type based on route or selection properties
    const path = window.location.pathname;
    let configType = "room"; // default
    if (path.includes("server")) configType = "server";
    else if (path.includes("surveillance")) configType = "surveillance";

    const handleShare = async () => {
        const shareData = {
            title: "My Configuration",
            text: `Check out my ${configType} configuration on Act RDW!`,
            url: window.location.href, // Or a specific shareable link if generated
        };

        if (navigator.share) {
            try {
                await navigator.share(shareData);
                setToastMessage("Shared successfully!");
                setShowToast(true);
            } catch (err) {
                console.error("Error sharing:", err);
            }
        } else {
            handleCopyUrl();
        }
        setShowShareModal(false);
    };

    const handleCopyUrl = async () => {
        try {
            await navigator.clipboard.writeText(window.location.href);
            setToastMessage("URL Copied!");
            setShowToast(true);
        } catch (err) {
            console.error("Failed to copy:", err);
            setToastMessage("Failed to copy link.");
            setShowToast(true);
        }
    };

    const handleRequestConsultation = () => {
        window.location.href = "/contact";
    };

    return (
        <MainLayout>
            {/* Toast Notification */}
            <div
                className={`toast-container position-fixed bottom-0 end-0 p-3`}
                style={{ zIndex: 1055 }}
            >
                <div
                    className={`toast align-items-center text-white bg-theme border-0 ${
                        showToast ? "show" : ""
                    }`}
                    role="alert"
                    aria-live="assertive"
                    aria-atomic="true"
                >
                    <div className="d-flex">
                        <div className="toast-body">{toastMessage}</div>
                        <button
                            type="button"
                            className="btn-close btn-close-white me-2 m-auto"
                            onClick={() => setShowToast(false)}
                            aria-label="Close"
                        ></button>
                    </div>
                </div>
            </div>

            <ConfiguratorHero handleCopyUrl={handleCopyUrl} />

            <ConfiguratorSummarySection
                selection={selection}
                userInfo={userInfo}
                uuid={uuid}
                summaryItems={summaryItems}
                configType={configType}
                setToastMessage={setToastMessage}
                setShowToast={setShowToast}
            />
        </MainLayout>
    );
}
