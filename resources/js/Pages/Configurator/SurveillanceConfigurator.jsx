import React, { useState, useEffect, useRef } from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ConfiguratorSubmitModal from "@/Components/Configurator/ConfiguratorSubmitModal";
import SelectionCard from "@/Components/Sections/Configurator/Common/SelectionCard";
import WizardStepper from "@/Components/Sections/Configurator/Common/WizardStepper";
import WizardNavigation from "@/Components/Sections/Configurator/Common/WizardNavigation";

export default function SurveillanceConfigurator() {
    const [step, setStep] = useState(1);
    const [selection, setSelection] = useState({
        premise: null,
        areaSize: null,
        cameraType: [],
        features: [],
        brand: null,
        retention: null,
        quantities: {},
    });

    const [userInfo, setUserInfo] = useState({
        name: "",
        phone: "",
        email: "",
        company: "",
    });
    const [showModal, setShowModal] = useState(false);
    const [uuid, setUuid] = useState("");
    const hasBrandParam = useRef(false);

    const themeColor = "#4AC15E"; // Green for consistency

    // --- Data Definitions ---
    const metaData = {
        premises: [
            {
                id: "office",
                name: "Corporate Office",
                desc: "Indoor, Access Control",
                icon: "/assets/img/icon/service-2-1.svg",
            },
            {
                id: "retail",
                name: "Retail / Commercial",
                desc: "Loss Prevention, People Counting",
                icon: "/assets/img/icon/service-2-2.svg",
            },
            {
                id: "industrial",
                name: "Industrial / Warehouse",
                desc: "Large Area, Perimeter",
                icon: "/assets/img/icon/service-2-3.svg",
            },
            {
                id: "public",
                name: "Public Space",
                desc: "Outdoor, Traffic, Crowd",
                icon: "/assets/img/icon/service-2-4.svg",
            },
        ],
        areaSizes: [
            {
                id: "small",
                name: "Small (< 200m²)",
                desc: "1-4 Cameras",
                icon: "/assets/img/icon/feature_2_1.svg",
            },
            {
                id: "medium",
                name: "Medium (200-1000m²)",
                desc: "5-16 Cameras",
                icon: "/assets/img/icon/feature_2_2.svg",
            },
            {
                id: "large",
                name: "Large (> 1000m²)",
                desc: "16-64 Cameras",
                icon: "/assets/img/service/service_3_3.jpg",
            },
            {
                id: "multisite",
                name: "Multi-Site",
                desc: "Centralized Management",
                icon: "/assets/img/icon/feature_2_4.svg",
            },
        ],
        cameraTypes: [
            {
                id: "dome",
                name: "Dome Camera",
                desc: "Discreet Indoor Monitoring",
                icon: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "bullet",
                name: "Bullet Camera",
                desc: "Visible Deterrent Outdoor",
                icon: "/assets/img/icon/feature_1_2.svg",
            },
            {
                id: "ptz",
                name: "PTZ Camera",
                desc: "Active Tracking & Zoom",
                icon: "/assets/img/icon/feature_1_3.svg",
            },
            {
                id: "panoramic",
                name: "Panoramic (Fisheye)",
                desc: "360° Wide Coverage",
                icon: "/assets/img/icon/feature_1_4.svg",
            },
        ],
        smartFeatures: [
            {
                id: "motion",
                name: "Motion Detection",
                desc: "Basic Recording Trigger",
            },
            {
                id: "face",
                name: "Face Recognition",
                desc: "Access Control Integration",
            },
            {
                id: "lpr",
                name: "License Plate Recog.",
                desc: "Parking & Traffic",
            },
            {
                id: "perimeter",
                name: "Perimeter Defense",
                desc: "Intrusion Alerts",
            },
            {
                id: "thermal",
                name: "Thermal Imaging",
                desc: "Heat Detection / Night Vision",
            },
        ],
        brands: [
            {
                id: "hikvision",
                name: "Hikvision",
                logo: "/assets/img/partners/hikvision.png",
            },
            {
                id: "dahua",
                name: "Dahua",
                logo: "/assets/img/partners/dahua.png",
            },
            {
                id: "axis",
                name: "Axis Communications",
                logo: "/assets/img/partners/axis.png",
            },
            {
                id: "hanwha",
                name: "Hanwha Vision",
                logo: "/assets/img/partners/hanwha.png",
            },
        ],
        retentionPeriods: [
            { id: "7days", name: "7 Days", desc: "Basic Compliance" },
            { id: "14days", name: "14 Days", desc: "Standard Commercial" },
            { id: "30days", name: "30 Days", desc: "Banking / High Security" },
            { id: "90days", name: "90 Days+", desc: "Long Term Archiving" },
        ],
    };

    const steps = [
        { id: 1, label: "Environment" },
        { id: 2, label: "Area Size" },
        { id: 3, label: "Camera Types" },
        { id: 4, label: "Features" },
        { id: 5, label: "Retention" },
        { id: 6, label: "Brand" },
        { id: 7, label: "Summary" },
    ];

    // --- Init ---
    useEffect(() => {
        setUuid(
            crypto.randomUUID
                ? crypto.randomUUID()
                : Math.random().toString(36).substring(2) +
                      Date.now().toString(36)
        );

        // Auto-fill from URL
        const urlParams = new URLSearchParams(window.location.search);
        const premiseId = urlParams.get("premise");
        const brandId = urlParams.get("brand");

        if (premiseId) {
            const premiseObj = metaData.premises.find(
                (p) => p.id === premiseId
            );
            if (premiseObj) {
                setSelection((prev) => ({ ...prev, premise: premiseObj }));
            }
        }
        if (brandId) {
            hasBrandParam.current = true;
            const brandObj = metaData.brands.find((b) => b.id === brandId);
            if (brandObj) {
                setSelection((prev) => ({ ...prev, brand: brandObj }));
            }
        }

        if (premiseId && brandId) setStep(2); // Skip Step 1 if filled
    }, []);

    // --- Handlers ---
    const handleSelection = (key, value) => {
        setSelection((prev) => ({ ...prev, [key]: value }));
    };

    const handleToggle = (key, item) => {
        setSelection((prev) => {
            const list = prev[key] || [];
            const exists = list.some((i) => i.id === item.id);
            const newList = exists
                ? list.filter((i) => i.id !== item.id)
                : [...list, item];
            return { ...prev, [key]: newList };
        });
    };

    const handleUserInfoChange = (e) => {
        const { name, value } = e.target;
        setUserInfo((prev) => ({ ...prev, [name]: value }));
    };

    const nextStep = () => {
        if (step === 1 && !selection.premise)
            return alert("Please select an Environment.");
        if (step === 2 && !selection.areaSize)
            return alert("Please select an Area Size.");
        if (step === 3 && selection.cameraType.length === 0)
            return alert("Please select at least one Camera Type.");
        if (step === 5) {
            if (!selection.retention)
                return alert("Please select a Retention Period.");
            if (hasBrandParam.current) {
                setStep(7);
                return;
            }
        }
        if (step === 6 && !selection.brand)
            return alert("Please select a Brand.");
        setStep((prev) => prev + 1);
    };

    const prevStep = () => {
        if (step === 7 && hasBrandParam.current) {
            setStep(5);
            return;
        }
        setStep((prev) => prev - 1);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!userInfo.name || !userInfo.phone) {
            alert("Please fill in required fields.");
            return;
        }
        router.post("/surveillance-configurator/complete", {
            selection,
            userInfo,
            uuid,
        });
    };

    // --- Styles ---
    const activeStyle = { borderColor: themeColor, backgroundColor: "#ECFDF5" };
    const defaultStyle = { borderColor: "#E5E7EB", backgroundColor: "#fff" };

    return (
        <MainLayout>
            <Head title="Surveillance System Configurator" />
            <Breadcrumb
                title="Surveillance Configurator"
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    { label: "Surveillance Configurator" },
                ]}
            />

            <section className="space-top space-bottom bg-light">
                <div className="container">
                    <div className="row justify-content-center mb-5">
                        <div className="col-lg-8 text-center">
                            <span className="sub-title text-success">
                                Secure Your Premise
                            </span>
                            <h2 className="sec-title">
                                Smart Surveillance Configurator
                            </h2>
                            <p className="text-muted">
                                Design a comprehensive security solution
                                tailored to your environment.
                            </p>
                        </div>
                    </div>

                    <div className="wizard-container bg-white rounded-20 shadow-sm p-4 p-lg-5">
                        {/* Stepper */}
                        <WizardStepper
                            steps={steps}
                            currentStep={step}
                            themeColor={themeColor}
                        />

                        {/* Content */}
                        <div className="step-content animate-fade">
                            {/* Step 1: Premise */}
                            {step === 1 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Select Environment
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.premises.map((item) => (
                                            <div
                                                className="col-md-6 col-lg-3"
                                                key={item.id}
                                            >
                                                <SelectionCard
                                                    selected={
                                                        selection.premise
                                                            ?.id === item.id
                                                    }
                                                    onClick={() =>
                                                        handleSelection(
                                                            "premise",
                                                            item
                                                        )
                                                    }
                                                    icon={item.icon}
                                                    title={item.name}
                                                    subtitle={item.desc}
                                                    themeColor={themeColor}
                                                />
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Step 2: Area Size */}
                            {step === 2 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Estimated Area Size
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.areaSizes.map((item) => (
                                            <div
                                                className="col-md-4"
                                                key={item.id}
                                            >
                                                <SelectionCard
                                                    selected={
                                                        selection.areaSize
                                                            ?.id === item.id
                                                    }
                                                    onClick={() =>
                                                        handleSelection(
                                                            "areaSize",
                                                            item
                                                        )
                                                    }
                                                    icon={item.icon}
                                                    title={item.name}
                                                    subtitle={item.desc}
                                                    themeColor={themeColor}
                                                />
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Step 3: Camera Types (Multi-select) */}
                            {step === 3 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Preferred Camera Types
                                    </h4>
                                    <p className="text-center text-muted mb-4">
                                        Select all that apply.
                                    </p>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.cameraTypes.map((item) => {
                                            const isSelected =
                                                selection.cameraType.some(
                                                    (x) => x.id === item.id
                                                );
                                            return (
                                                <div
                                                    className="col-md-4"
                                                    key={item.id}
                                                >
                                                    <SelectionCard
                                                        selected={isSelected}
                                                        onClick={() =>
                                                            handleToggle(
                                                                "cameraType",
                                                                item
                                                            )
                                                        }
                                                        icon={item.icon}
                                                        title={item.name}
                                                        subtitle={item.desc}
                                                        themeColor={themeColor}
                                                    />
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}

                            {/* Step 4: Smart Features (Multi-select) */}
                            {step === 4 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Required Intelligent Features
                                    </h4>
                                    <div className="row g-3 justify-content-center">
                                        {metaData.smartFeatures.map((item) => {
                                            const isSelected =
                                                selection.features.some(
                                                    (x) => x.id === item.id
                                                );
                                            return (
                                                <div
                                                    className="col-md-6"
                                                    key={item.id}
                                                >
                                                    <SelectionCard
                                                        selected={isSelected}
                                                        onClick={() =>
                                                            handleToggle(
                                                                "features",
                                                                item
                                                            )
                                                        }
                                                        themeColor={themeColor}
                                                        className="d-flex align-items-center text-start p-3" // Override text-center with text-start, reduce padding
                                                    >
                                                        <div className="me-3">
                                                            <i className="fa-solid fa-microchip text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <h6 className="small fw-bold mb-0">
                                                                {item.name}
                                                            </h6>
                                                            <small className="text-muted">
                                                                {item.desc}
                                                            </small>
                                                        </div>
                                                    </SelectionCard>
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}

                            {/* Step 5: Retention */}
                            {step === 5 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Recording Retention Period
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.retentionPeriods.map(
                                            (item) => (
                                                <div
                                                    className="col-md-3 col-6"
                                                    key={item.id}
                                                >
                                                    <SelectionCard
                                                        selected={
                                                            selection.retention
                                                                ?.id === item.id
                                                        }
                                                        onClick={() =>
                                                            handleSelection(
                                                                "retention",
                                                                item
                                                            )
                                                        }
                                                        title={item.name}
                                                        subtitle={item.desc}
                                                        themeColor={themeColor}
                                                    >
                                                        <h5 className="h4 fw-bold mb-1">
                                                            <i className="fa-regular fa-clock me-2 text-muted"></i>
                                                        </h5>
                                                    </SelectionCard>
                                                </div>
                                            )
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Step 6: Brand */}
                            {step === 6 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Preferred Brand
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.brands.map((item) => (
                                            <div
                                                className="col-6 col-md-3"
                                                key={item.id}
                                            >
                                                <SelectionCard
                                                    selected={
                                                        selection.brand?.id ===
                                                        item.id
                                                    }
                                                    onClick={() =>
                                                        handleSelection(
                                                            "brand",
                                                            item
                                                        )
                                                    }
                                                    themeColor={themeColor}
                                                >
                                                    <div
                                                        className="p-2 mb-2 d-flex align-items-center justify-content-center"
                                                        style={{
                                                            height: "60px",
                                                        }}
                                                    >
                                                        {item.logo ? (
                                                            <img
                                                                src={item.logo}
                                                                alt={item.name}
                                                                style={{
                                                                    maxHeight:
                                                                        "100%",
                                                                    maxWidth:
                                                                        "100%",
                                                                }}
                                                            />
                                                        ) : (
                                                            <h5 className="mb-0">
                                                                {item.name}
                                                            </h5>
                                                        )}
                                                    </div>
                                                </SelectionCard>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Step 7: Summary */}
                            {step === 7 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Review Configuration
                                    </h4>
                                    <div className="bg-light p-4 rounded mb-4">
                                        <div className="row">
                                            <div className="col-md-6 mb-3">
                                                <small className="text-muted text-uppercase fw-bold">
                                                    Environment
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.premise?.name ||
                                                        "-"}
                                                </p>
                                            </div>
                                            <div className="col-md-6 mb-3">
                                                <small className="text-muted text-uppercase fw-bold">
                                                    Area Size
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.areaSize?.name ||
                                                        "-"}
                                                </p>
                                            </div>
                                            <div className="col-md-6 mb-3">
                                                <small className="text-muted text-uppercase fw-bold">
                                                    Retention
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.retention
                                                        ?.name || "-"}
                                                </p>
                                            </div>
                                            <div className="col-md-6 mb-3">
                                                <small className="text-muted text-uppercase fw-bold">
                                                    Brand
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.brand?.name ||
                                                        "-"}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="border-top pt-3 mt-2">
                                            <h6 className="small fw-bold">
                                                Camera Types:
                                            </h6>
                                            <ul className="mb-3 small text-muted">
                                                {selection.cameraType.map(
                                                    (item) => (
                                                        <li key={item.id}>
                                                            {item.name}
                                                        </li>
                                                    )
                                                )}
                                            </ul>
                                            <h6 className="small fw-bold">
                                                Features:
                                            </h6>
                                            <ul className="mb-0 small text-muted">
                                                {selection.features.length ? (
                                                    selection.features.map(
                                                        (item) => (
                                                            <li key={item.id}>
                                                                {item.name}
                                                            </li>
                                                        )
                                                    )
                                                ) : (
                                                    <li>None</li>
                                                )}
                                            </ul>
                                        </div>
                                    </div>
                                    <div className="text-center">
                                        <button
                                            className="th-btn th-radius shadow-none style8"
                                            onClick={() => setShowModal(true)}
                                        >
                                            Finalization Now{" "}
                                            <i className="fa-solid fa-check ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Navigation Buttons */}
                        <WizardNavigation
                            onNext={nextStep}
                            onPrev={prevStep}
                            isFirstStep={step === 1}
                            isLastStep={step === 7}
                            themeColor={themeColor}
                        />
                    </div>
                </div>
            </section>

            {/* Modal */}
            <ConfiguratorSubmitModal
                show={showModal}
                onClose={() => setShowModal(false)}
                userInfo={userInfo}
                onChange={handleUserInfoChange}
                onSubmit={handleSubmit}
            />
        </MainLayout>
    );
}
