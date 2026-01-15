import React, { useState, useEffect } from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import ConfiguratorSubmitModal from "@/Components/Configurator/ConfiguratorSubmitModal";
import SelectionCard from "@/Components/Sections/Configurator/Common/SelectionCard";
import WizardStepper from "@/Components/Sections/Configurator/Common/WizardStepper";
import WizardNavigation from "@/Components/Sections/Configurator/Common/WizardNavigation";

export default function ServerConfigurator() {
    const [step, setStep] = useState(1);
    const [selection, setSelection] = useState({
        scenario: null,
        formFactor: null,
        performance: null,
        storage: null,
        brand: null,
        software: [],
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

    const themeColor = "#4AC15E"; // Green for consistency

    // --- Data Definitions ---
    const metaData = {
        scenarios: [
            {
                id: "database",
                name: "Database Server",
                desc: "High IOPS & Processing",
                icon: "/assets/img/icon/service-2-1.svg",
            },
            {
                id: "virtualization",
                name: "Virtualization",
                desc: "Run multiple VMs",
                icon: "/assets/img/icon/service-2-2.svg",
            },
            {
                id: "file_storage",
                name: "File & Storage",
                desc: "High Capacity Storage",
                icon: "/assets/img/icon/service-2-3.svg",
            },
            {
                id: "app_web",
                name: "Application / Web",
                desc: "General Purpose",
                icon: "/assets/img/icon/service-2-4.svg",
            },
        ],
        formFactors: [
            {
                id: "rack",
                name: "Rackmount",
                desc: "For Server Rooms/Data Centers",
                icon: "/assets/img/icon/feature_2_1.svg",
            },
            {
                id: "tower",
                name: "Tower",
                desc: "Standalone Office Server",
                icon: "/assets/img/service/service_2_2.jpg",
            },
            {
                id: "blade",
                name: "Blade / Modular",
                desc: "High Density Computing",
                icon: "/assets/img/icon/feature_2_3.svg",
            },
        ],
        performanceLevels: [
            {
                id: "entry",
                name: "Entry Level",
                desc: "Small SMB, Print/File Server",
                specs: "Single CPU, <32GB RAM",
            },
            {
                id: "mid",
                name: "Mid-Range",
                desc: "Application Server, Small DB",
                specs: "Dual CPU, 64-128GB RAM",
            },
            {
                id: "enterprise",
                name: "Enterprise",
                desc: "Heavy Virtualization, Big Data",
                specs: "Quad CPU, >256GB RAM",
            },
        ],
        storageOptions: [
            {
                id: "all_flash",
                name: "All Flash (SSD/NVMe)",
                desc: "Maximum Speed & IOPS",
            },
            {
                id: "hybrid",
                name: "Hybrid (HDD + SSD)",
                desc: "Balanced Speed & Capacity",
            },
            {
                id: "bulk",
                name: "Bulk Storage (HDD)",
                desc: "Maximum Capacity, Archival",
            },
        ],
        brands: [
            {
                id: "dell",
                name: "Dell Technologies",
                logo: "/assets/img/partners/dell.png",
            }, // Placeholder path
            { id: "hpe", name: "HPE", logo: "/assets/img/partners/hpe.png" },
            {
                id: "lenovo",
                name: "Lenovo",
                logo: "/assets/img/partners/lenovo.png",
            },
            {
                id: "supermicro",
                name: "Supermicro",
                logo: "/assets/img/partners/supermicro.png",
            },
        ],
        software: [
            { id: "windows_server", name: "Windows Server 2022", type: "OS" },
            { id: "rhel", name: "Red Hat Enterprise Linux", type: "OS" },
            { id: "vmware", name: "VMware vSphere", type: "Virtualization" },
            { id: "veeam", name: "Veeam Backup", type: "Backup" },
        ],
    };

    const steps = [
        { id: 1, label: "Scenario" },
        { id: 2, label: "Form Factor" },
        { id: 3, label: "Performance" },
        { id: 4, label: "Storage" },
        { id: 5, label: "Brand" },
        { id: 6, label: "Software" },
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

        // Auto-fill from URL if coming from Grid
        const urlParams = new URLSearchParams(window.location.search);
        const formFactor = urlParams.get("formFactor");
        const brand = urlParams.get("brand");

        if (formFactor) setSelection((prev) => ({ ...prev, formFactor }));
        if (brand) setSelection((prev) => ({ ...prev, brand }));

        // If both present, jump to step 3 (Performance)
        if (formFactor && brand) setStep(3);
    }, []);

    // --- Handlers ---
    const handleSelection = (key, value) => {
        setSelection((prev) => ({ ...prev, [key]: value }));
    };

    const handleToggleSoftware = (item) => {
        setSelection((prev) => {
            const list = prev.software || [];
            const exists = list.find((i) => i.id === item.id);
            const newList = exists
                ? list.filter((i) => i.id !== item.id)
                : [...list, item];
            return { ...prev, software: newList };
        });
    };

    const handleUserInfoChange = (e) => {
        const { name, value } = e.target;
        setUserInfo((prev) => ({ ...prev, [name]: value }));
    };

    const nextStep = () => {
        if (step === 1 && !selection.scenario)
            return alert("Please select a Usage Scenario.");
        if (step === 2 && !selection.formFactor)
            return alert("Please select a Form Factor.");
        if (step === 3 && !selection.performance)
            return alert("Please select a Performance Level.");
        if (step === 4 && !selection.storage)
            return alert("Please select a Storage Type.");
        if (step === 5 && !selection.brand)
            return alert("Please select a Brand.");
        setStep((prev) => prev + 1);
    };

    const prevStep = () => {
        setStep((prev) => prev - 1);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!userInfo.name || !userInfo.phone) {
            alert("Please fill in required fields.");
            return;
        }
        router.post("/server-configurator/complete", {
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
            <Head title="Server Solutions Configurator" />
            <Breadcrumb
                title="Server Configurator"
                items={[
                    { label: "Home", link: "/" },
                    { label: "Services", link: "/services" },
                    { label: "Server Configurator" },
                ]}
            />

            <section className="space-top space-bottom bg-light">
                <div className="container">
                    <div className="row justify-content-center mb-5">
                        <div className="col-lg-8 text-center">
                            <span className="sub-title text-success">
                                Build Your Infrastructure
                            </span>
                            <h2 className="sec-title">
                                Server Solution Configurator
                            </h2>
                            <p className="text-muted">
                                Design the perfect server solution for your
                                business needs.
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
                            {/* Step 1: Scenario */}
                            {step === 1 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Select Primary Usage
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.scenarios.map((item) => (
                                            <div
                                                className="col-md-6 col-lg-3"
                                                key={item.id}
                                            >
                                                <SelectionCard
                                                    selected={
                                                        selection.scenario
                                                            ?.id === item.id
                                                    }
                                                    onClick={() =>
                                                        handleSelection(
                                                            "scenario",
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

                            {/* Step 2: Form Factor */}
                            {step === 2 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Select Form Factor
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.formFactors.map((item) => (
                                            <div
                                                className="col-md-4"
                                                key={item.id}
                                            >
                                                <SelectionCard
                                                    selected={
                                                        selection.formFactor
                                                            ?.id === item.id
                                                    }
                                                    onClick={() =>
                                                        handleSelection(
                                                            "formFactor",
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

                            {/* Step 3: Performance */}
                            {step === 3 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Required Performance Level
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.performanceLevels.map(
                                            (item) => (
                                                <div
                                                    className="col-md-4"
                                                    key={item.id}
                                                >
                                                    <SelectionCard
                                                        selected={
                                                            selection
                                                                .performance
                                                                ?.id === item.id
                                                        }
                                                        onClick={() =>
                                                            handleSelection(
                                                                "performance",
                                                                item
                                                            )
                                                        }
                                                        title={item.name}
                                                        subtitle={item.desc}
                                                        themeColor={themeColor}
                                                    >
                                                        <div className="badge bg-light text-dark border mt-3">
                                                            {item.specs}
                                                        </div>
                                                    </SelectionCard>
                                                </div>
                                            )
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Step 4: Storage */}
                            {step === 4 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Storage Requirement
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.storageOptions.map((item) => (
                                            <div
                                                className="col-md-4"
                                                key={item.id}
                                            >
                                                <SelectionCard
                                                    selected={
                                                        selection.storage
                                                            ?.id === item.id
                                                    }
                                                    onClick={() =>
                                                        handleSelection(
                                                            "storage",
                                                            item
                                                        )
                                                    }
                                                    title={item.name}
                                                    subtitle={item.desc}
                                                    themeColor={themeColor}
                                                >
                                                    <div className="mb-3">
                                                        <i className="fa-solid fa-server fa-2x text-muted"></i>
                                                    </div>
                                                </SelectionCard>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Step 5: Brand */}
                            {step === 5 && (
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
                                                        {/* If logo exists use it, else utilize text */}
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

                            {/* Step 6: Software */}
                            {step === 6 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Software & Licenses (Optional)
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.software.map((item) => {
                                            const isSelected =
                                                selection.software.some(
                                                    (i) => i.id === item.id
                                                );
                                            return (
                                                <div
                                                    className="col-md-6"
                                                    key={item.id}
                                                >
                                                    <div
                                                        className="selection-card p-3 rounded-3 h-100 d-flex align-items-center bg-white border"
                                                        style={{
                                                            borderColor:
                                                                isSelected
                                                                    ? themeColor
                                                                    : "#E5E7EB",
                                                            cursor: "pointer",
                                                        }}
                                                        onClick={() =>
                                                            handleToggleSoftware(
                                                                item
                                                            )
                                                        }
                                                    >
                                                        <div className="me-3">
                                                            <i className="fa-solid fa-compact-disc text-muted"></i>
                                                        </div>
                                                        <div>
                                                            <h6 className="small fw-bold mb-0">
                                                                {item.name}
                                                            </h6>
                                                            <small className="text-muted">
                                                                {item.type}
                                                            </small>
                                                        </div>
                                                        {isSelected && (
                                                            <i
                                                                className="fa-solid fa-check ms-auto"
                                                                style={{
                                                                    color: themeColor,
                                                                }}
                                                            ></i>
                                                        )}
                                                    </div>
                                                </div>
                                            );
                                        })}
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
                                                    Usage Scenario
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.scenario?.name ||
                                                        "-"}
                                                </p>
                                            </div>
                                            <div className="col-md-6 mb-3">
                                                <small className="text-muted text-uppercase fw-bold">
                                                    Form Factor
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.formFactor
                                                        ?.name || "-"}
                                                </p>
                                            </div>
                                            <div className="col-md-6 mb-3">
                                                <small className="text-muted text-uppercase fw-bold">
                                                    Performance
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.performance
                                                        ?.name || "-"}
                                                </p>
                                            </div>
                                            <div className="col-md-6 mb-3">
                                                <small className="text-muted text-uppercase fw-bold">
                                                    Storage
                                                </small>
                                                <p className="fw-bold">
                                                    {selection.storage?.name ||
                                                        "-"}
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
                                        {selection.software.length > 0 && (
                                            <div className="border-top pt-3 mt-2">
                                                <h6 className="small fw-bold">
                                                    Software:
                                                </h6>
                                                <ul className="mb-0 small text-muted">
                                                    {selection.software.map(
                                                        (s) => (
                                                            <li key={s.id}>
                                                                {s.name}
                                                            </li>
                                                        )
                                                    )}
                                                </ul>
                                            </div>
                                        )}
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
