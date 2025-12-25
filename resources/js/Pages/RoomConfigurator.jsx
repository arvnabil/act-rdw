import React, { useState, useEffect } from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import Toast from "@/Components/Common/Toast";
import ConfiguratorSubmitModal from "@/Components/Configurator/ConfiguratorSubmitModal";

export default function RoomConfigurator() {
    const [step, setStep] = useState(1);
    const [selection, setSelection] = useState({
        roomType: null,
        brand: null,
        platform: null,
        deployment: null,
        products: [], // Cameras (Main Device)
        audio: [],
        controller: [],
        accessories: [], // Add-ons
        services: {}, // Questionnaire
        pc: null, // New: Selected PC
        license: null, // New: License
        warranty: null, // New: Warranty
        quantities: {}, // New: Map of item ID -> quantity
    });

    const [showToast, setShowToast] = useState(false);
    const [toastMessage, setToastMessage] = useState("");

    const themeColor = "#4AC15E";

    // --- Data Definitions ---
    const metaData = {
        roomTypes: [
            {
                id: "huddle_room",
                name: "Huddle Room",
                desc: "1-5 People",
                icon: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "small_room",
                name: "Small Room",
                desc: "4-6 People",
                icon: "/assets/img/icon/feature_1_2.svg",
            },
            {
                id: "medium_room",
                name: "Medium Room",
                desc: "6-10 People",
                icon: "/assets/img/icon/feature_1_3.svg",
            },
        ],
        brands: [
            {
                id: "logitech",
                name: "Logitech",
                logo: "/assets/img/brand/brand_1_1.svg",
            },
            {
                id: "yealink",
                name: "Yealink",
                logo: "/assets/img/brand/brand_1_2.svg",
            },
            {
                id: "jabra",
                name: "Jabra",
                logo: "/assets/img/brand/brand_1_3.svg",
            },
        ],
        platforms: [
            {
                id: "google",
                name: "Google Meet",
                icon: "/assets/img/brand/brand_1_4.svg",
            },
            {
                id: "zoom",
                name: "Zoom",
                icon: "/assets/img/brand/brand_1_5.svg",
            },
            {
                id: "teams",
                name: "Microsoft Teams",
                icon: "/assets/img/brand/brand_1_6.svg",
            },
            {
                id: "byod",
                name: "BYOD",
                icon: "/assets/img/icon/feature_1_4.svg",
            },
        ],
        deploymentTypes: [
            {
                id: "appliance",
                name: "Appliance Mode",
                icon: "/assets/img/icon/service-2-1.svg",
                desc: "Runs natively on the device (Android/CollabOS). No PC required.",
            },
            {
                id: "pc-based",
                name: "PC Based",
                icon: "/assets/img/icon/service-2-2.svg",
                desc: "Requires a dedicated meeting room PC (Windows/Mac).",
            },
        ],
        audioProducts: [
            {
                id: "mic-pod",
                name: "Expansion Mic Pod",
                desc: "Extend audio coverage",
                price: 3500000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "mic-pod-hub",
                name: "Mic Pod Hub",
                desc: "Clean cable management",
                price: 2500000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "spk-bar",
                name: "External Speaker Bar",
                desc: "Enhanced room audio",
                price: 5000000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
        ],
        controllers: [
            {
                id: "tap-usb",
                name: "Logitech Tap",
                desc: "USB-connected room controller with Cat5e kit",
                price: 10000000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "tap-ip",
                name: "Logitech Tap IP",
                desc: "Network-connected room controller",
                price: 12000000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
        ],
        controllerAccessories: [
            {
                id: "tap-table-mount",
                name: "Tap Table Mount",
                desc: "Secure Tap controllers to the table",
                price: 500000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "tap-riser-mount",
                name: "Tap Riser Mount",
                desc: "Elevated mounting for Tap",
                price: 600000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "tap-wall-mount",
                name: "Tap Wall Mount",
                desc: "Wall mounting for Tap",
                price: 500000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
        ],
        accessories: [
            // Step 7 Add-ons
            {
                id: "tap-scheduler",
                name: "Tap Scheduler",
                desc: "Room scheduling panel",
                price: 8000000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "scribe",
                name: "Logitech Scribe",
                desc: "AI-powered whiteboard camera",
                price: 15000000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "swytch",
                name: "Logitech Swytch",
                desc: "Bring-your-own-laptop connection",
                price: 6000000,
                image: "/assets/img/icon/feature_1_1.svg",
                recommended: true,
            },
            {
                id: "tv-mount",
                name: "TV Mount",
                desc: "Secure mounting above/below TV",
                price: 1000000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
            {
                id: "wall-mount",
                name: "Wall Mount",
                desc: "Space saving wall installation",
                price: 800000,
                image: "/assets/img/icon/feature_1_1.svg",
            },
        ],
        serviceQuestions: [
            {
                id: "expertise",
                question:
                    "What’s your support team’s level of AV/IT expertise?",
                options: [
                    "No expertise on staff",
                    "Qualified internal or partner team",
                ],
            },
            {
                id: "resources",
                question:
                    "What additional resources are needed for ongoing operations?",
                options: [
                    "Ongoing service management",
                    "Advanced, proactive software tools",
                    "Tech support when I need it",
                ],
            },
            {
                id: "troubleshooting",
                question: "How do you manage troubleshooting?",
                options: [
                    "We handle it internally",
                    "We rely on external support",
                    "Ad-hoc basis",
                ],
            },
            {
                id: "hours",
                question:
                    "What are the hours of support you’re expected to deliver?",
                options: [
                    "8/5 Standard Business Hours",
                    "24/7 Critical Support",
                ],
            },
            {
                id: "replacement",
                question:
                    "When you need to replace faulty products, how quickly does it need to arrive?",
                options: ["Next Business Day", "Standard Shipping (3-5 Days)"],
            },
        ],
    };

    // Modified productsData to include specific accessories
    const productsData = {
        logitech: {
            huddle_room: [
                {
                    id: "meetup",
                    name: "Logitech MeetUp",
                    image: "/assets/img/product/product_1_3.png",
                    desc: "All-in-One ConferenceCam",
                    price: 9500000,
                    accessories: [
                        {
                            id: "meetup-mount",
                            name: "MeetUp TV Mount",
                            price: 500000,
                            recommended: true,
                        },
                        {
                            id: "meetup-mic",
                            name: "Expansion Mic",
                            price: 3000000,
                        },
                    ],
                },
                {
                    id: "rally-bar-mini",
                    name: "Logitech Rally Bar Mini",
                    image: "/assets/img/product/product_1_1.png",
                    desc: "Premier Video Bar for Small Rooms",
                    price: 25000000,
                    accessories: [
                        {
                            id: "tv-mount-video-bar",
                            name: "TV Mount for Video Bars",
                            price: 800000,
                            recommended: true,
                        },
                        {
                            id: "wall-mount-video-bar",
                            name: "Wall Mount for Video Bars",
                            price: 600000,
                        },
                    ],
                },
                {
                    id: "bcc950",
                    name: "Logitech BCC950",
                    image: "/assets/img/product/product_1_4.png",
                    desc: "Desktop Video Conferencing",
                    price: 3500000,
                    accessories: [],
                },
                {
                    id: "connect",
                    name: "Logitech Connect",
                    image: "/assets/img/product/product_1_5.png",
                    desc: "Portable ConferenceCam",
                    price: 7500000,
                    accessories: [],
                },
            ],
            small_room: [
                {
                    id: "rally-bar",
                    name: "Logitech Rally Bar",
                    image: "/assets/img/product/product_1_2.png",
                    desc: "All-in-One Video Bar",
                    price: 45000000,
                    accessories: [
                        {
                            id: "tv-mount-video-bar",
                            name: "TV Mount",
                            price: 800000,
                            recommended: true,
                        },
                    ],
                },
                {
                    id: "connect",
                    name: "Logitech Connect",
                    image: "/assets/img/product/product_1_5.png",
                    desc: "Portable ConferenceCam",
                    price: 7500000,
                    accessories: [],
                },
                {
                    id: "group",
                    name: "Logitech Group",
                    image: "/assets/img/product/product_1_7.png",
                    desc: "Affordable Video Conferencing",
                    price: 15000000,
                    accessories: [
                        {
                            id: "group-mic",
                            name: "Expansion Mics",
                            price: 4000000,
                        },
                    ],
                },
            ],
            medium_room: [
                {
                    id: "rally-plus",
                    name: "Logitech Rally Plus",
                    image: "/assets/img/product/product_1_2.png",
                    desc: "Modular Video Conferencing System",
                    price: 65000000,
                    accessories: [
                        {
                            id: "rally-mic-pod-hub",
                            name: "Mic Pod Hub",
                            price: 2500000,
                        },
                        {
                            id: "rally-speakermount",
                            name: "Speaker Mount",
                            price: 500000,
                            recommended: true,
                        },
                    ],
                },
                {
                    id: "group",
                    name: "Logitech Group",
                    image: "/assets/img/product/product_1_7.png",
                    desc: "Affordable Video Conferencing",
                    price: 15000000,
                    accessories: [
                        {
                            id: "group-mic",
                            name: "Expansion Mics",
                            price: 4000000,
                        },
                    ],
                },
                {
                    id: "rally-camera",
                    name: "Logitech Rally Camera",
                    image: "/assets/img/product/product_1_6.png",
                    desc: "Premium PTZ Camera",
                    price: 22000000,
                    accessories: [
                        {
                            id: "rally-mount",
                            name: "Camera Mount",
                            price: 500000,
                        },
                    ],
                },
            ],
        },

        yealink: {
            medium_room: [
                {
                    id: "mvc840",
                    name: "Yealink MVC840",
                    image: "/assets/img/product/product_1_6.png", // Using placeholder
                    desc: "Microsoft Teams Room System for Medium Rooms",
                    price: 45000000,
                    accessories: [],
                },
                {
                    id: "mvc860",
                    name: "Yealink MVC860",
                    image: "/assets/img/product/product_1_7.png", // Using placeholder
                    desc: "Dual-Eye Intelligent Tracking System",
                    price: 55000000,
                    accessories: [],
                },
            ],
            large_room: [
                {
                    id: "mvc940",
                    name: "Yealink MVC940",
                    image: "/assets/img/product/product_1_6.png",
                    desc: "XTreme Performance for Extra Large Rooms",
                    price: 75000000,
                    accessories: [],
                },
            ],
        },
        jabra: {
            medium_room: [
                {
                    id: "panacast-50",
                    name: "Jabra PanaCast 50",
                    image: "/assets/img/product/product_1_5.png",
                    desc: "Intelligent Video Bar System",
                    price: 42000000,
                    accessories: [],
                },
            ],
            small_room: [
                {
                    id: "panacast-20",
                    name: "Jabra PanaCast 20",
                    image: "/assets/img/product/product_1_5.png",
                    desc: "Personal Video Conferencing",
                    price: 12000000,
                    accessories: [],
                },
            ],
        },
    };

    // --- New Data: PCs, Licenses, Warranty ---
    const pcOptions = [
        {
            id: "asus_mini_pc",
            name: "ASUS Mini PC",
            image: "/assets/img/product/product_1_8.png",
            desc: "Compact & Powerful",
            price: 12000000,
            specs: {
                processor: "Intel Core i7",
                ram: "16GB",
                storage: "256GB SSD",
            },
        },
        {
            id: "lenovo_mini_pc",
            name: "Lenovo ThinkCentre",
            image: "/assets/img/product/product_1_8.png",
            desc: "Enterprise Grade",
            price: 13500000,
            specs: {
                processor: "Intel Core i5 vPro",
                ram: "16GB",
                storage: "512GB SSD",
            },
        },
    ];

    const managementLicenses = [
        {
            id: "essential",
            name: "Essential License",
            price: 1500000,
            desc: "Basic device management & insights",
        },
        {
            id: "select",
            name: "Select License",
            price: 3000000,
            desc: "Premium support & advanced analytics",
        },
    ];

    const platformLicenses = [
        {
            id: "zoom",
            name: "Zoom Room License",
            price: 7500000,
            desc: "Official Zoom Rooms Software License (Annual)",
        },
        {
            id: "teams",
            name: "Microsoft Teams Pro",
            price: 8000000,
            desc: "Microsoft Teams Rooms Pro License (Annual)",
        },
    ];

    const warrantyData = [
        {
            id: "standard",
            name: "Standard Warranty (2 Years)",
            price: 0,
            desc: "Included",
        },
        {
            id: "extended_1yr",
            name: "Extended Warranty (+1 Year)",
            price: 2000000,
            desc: "Total 3 Years coverage",
        },
        {
            id: "extended_3yr",
            name: "Extended Warranty (+3 Years)",
            price: 5000000,
            desc: "Total 5 Years coverage",
        },
    ];

    // --- Steps Definition ---
    const steps = [
        { id: 1, label: "Room & Brand" },
        { id: 2, label: "Platform" },
        { id: 3, label: "Deployment" },
        { id: 4, label: "Main Device" }, // Renamed from Cameras
        { id: 5, label: "Audio" },
        { id: 6, label: "Controller" },
        { id: 7, label: "Add-Ons" },
        { id: 8, label: "Services" },
        { id: 9, label: "Summary" },
    ];

    const [showModal, setShowModal] = useState(false);
    const [userInfo, setUserInfo] = useState({
        name: "",
        phone: "",
        sustainability: "",
    });
    const [uuid, setUuid] = useState("");
    const [editingServiceId, setEditingServiceId] = useState(null); // Track which question is being edited

    // --- Init ---
    useEffect(() => {
        // Generate UUID
        setUuid(
            crypto.randomUUID
                ? crypto.randomUUID()
                : Math.random().toString(36).substring(2) +
                      Date.now().toString(36)
        );

        const urlParams = new URLSearchParams(window.location.search);
        const roomType = urlParams.get("roomType") || urlParams.get("room");
        const brand = urlParams.get("brand");
        if (roomType && brand) {
            setSelection((prev) => ({ ...prev, roomType, brand }));
            setStep(2);
        }

        // Set Default Warranty
        if (!selection.warranty) {
            const defaultWar = warrantyData[0]; // Standard
            setSelection((prev) => ({
                ...prev,
                warranty: defaultWar,
                quantities: { ...prev.quantities, [defaultWar.id]: 1 },
            }));
        }
    }, []);

    // --- Handlers ---
    const handleSelection = (key, value) => {
        setSelection((prev) => ({ ...prev, [key]: value }));
    };

    const handleQuantity = (id, value) => {
        // If value is passed as number (from buttons), use it directly.
        // If from input event, parse it.
        const qty = parseInt(value);
        if (!isNaN(qty) && qty > 0) {
            setSelection((prev) => ({
                ...prev,
                quantities: { ...prev.quantities, [id]: qty },
            }));
        }
    };

    // Helper to render Quantity Control with +/- buttons
    const renderQuantityControl = (id) => {
        const qty = selection.quantities[id] || 1;
        return (
            <div
                className="d-flex align-items-center bg-light rounded p-1 border"
                onClick={(e) => e.stopPropagation()}
                style={{ width: "fit-content" }}
            >
                <button
                    className="btn btn-sm btn-link text-decoration-none p-0 px-2 text-dark fw-bold hover-scale"
                    onClick={(e) => {
                        e.stopPropagation();
                        handleQuantity(id, qty - 1);
                    }}
                    disabled={qty <= 1}
                >
                    <i
                        className="fa-solid fa-minus"
                        style={{ fontSize: "10px" }}
                    ></i>
                </button>
                <input
                    type="number"
                    min="1"
                    className="form-control form-control-sm text-center border-0 bg-transparent p-0 fw-bold"
                    style={{
                        width: "30px",
                        appearance: "textfield",
                        fontSize: "12px",
                    }}
                    value={qty}
                    onChange={(e) => handleQuantity(id, e.target.value)}
                />
                <button
                    className="btn btn-sm btn-link text-decoration-none p-0 px-2 text-dark fw-bold hover-scale"
                    onClick={(e) => {
                        e.stopPropagation();
                        handleQuantity(id, qty + 1);
                    }}
                >
                    <i
                        className="fa-solid fa-plus"
                        style={{ fontSize: "10px" }}
                    ></i>
                </button>
            </div>
        );
    };

    // Generic toggle for arrays
    const handleToggle = (key, item) => {
        setSelection((prev) => {
            const list = prev[key] || [];
            const exists = list.find((i) => i.id === item.id);
            const newList = exists
                ? list.filter((i) => i.id !== item.id)
                : [...list, item];
            return { ...prev, [key]: newList };
        });
    };

    // Services
    const handleServiceAnswer = (qId, answer) => {
        setSelection((prev) => ({
            ...prev,
            services: { ...prev.services, [qId]: answer },
        }));
        setEditingServiceId(null); // Clear editing state when answered
    };

    const handleUserInfoChange = (e) => {
        const { name, value } = e.target;
        setUserInfo((prev) => ({ ...prev, [name]: value }));
    };

    const nextStep = () => {
        if (step === 1 && (!selection.roomType || !selection.brand))
            return alert("Please select both Room Type and Brand.");
        if (step === 2 && !selection.platform)
            return alert("Please select a Platform.");

        // Skip Logic
        if (step === 2 && selection.platform === "byod") {
            setStep(4); // Skip Deployment
            return;
        }

        if (step === 3 && !selection.deployment)
            return alert("Please select a Deployment Method.");
        if (step === 4 && selection.products.length === 0)
            return alert("Please select at least one camera.");

        if (step === 5) {
            if (selection.platform === "byod") {
                setStep(7); // Skip Controller if BYOD
                return;
            }
        }

        if (step === 8) {
            const answeredCount = Object.keys(selection.services).length;
            const questionCount = metaData.serviceQuestions.length;
            if (answeredCount < questionCount)
                return alert("Please answer all service questions to proceed.");
        }
        setStep((prev) => prev + 1);
    };

    const prevStep = () => {
        if (step === 4 && selection.platform === "byod") {
            setStep(2); // Back to Platform from Hardware if BYOD
            return;
        }
        if (step === 7 && selection.platform === "byod") {
            setStep(5); // Back to Audio from Add-ons if BYOD
            return;
        }

        setStep((prev) => prev - 1);
    };

    const handleFinalization = () => {
        setShowModal(true);
    };

    const handleSubmitConfiguration = (e) => {
        e.preventDefault();
        // Validation
        if (!userInfo.name || !userInfo.phone || !userInfo.sustainability) {
            alert("Please fill in all fields.");
            return;
        }
        router.post("/room-configurator/complete", {
            selection,
            userInfo,
            uuid,
        });
    };

    // --- Helpers ---
    const getAvailableProducts = () => {
        if (!selection.brand || !selection.roomType) return [];
        return productsData[selection.brand]?.[selection.roomType] || [];
    };

    const formatPrice = (price) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
        }).format(price);
    };

    // --- Styles ---
    const activeStyle = { borderColor: themeColor, backgroundColor: "#ECFDF5" }; // Light green bg
    const defaultStyle = { borderColor: "#E5E7EB", backgroundColor: "#fff" };

    return (
        <MainLayout>
            <Head title="Room Configurator" />
            <Breadcrumb
                title="Room Configurator"
                items={[
                    { label: "Home", link: "/" },
                    { label: "Room Configurator" },
                ]}
            />

            <section className="space-top space-bottom bg-light">
                <div className="container">
                    <div className="row justify-content-center mb-5">
                        <div className="col-lg-8 text-center">
                            <span className="sub-title text-success">
                                Design Your Space
                            </span>
                            <h2 className="sec-title">
                                Room Solution Configurator
                            </h2>
                            <p className="text-muted">
                                Follow the steps below to build your ideal
                                meeting room setup.
                            </p>
                        </div>
                    </div>

                    <div className="wizard-container bg-white rounded-20 shadow-sm p-4 p-lg-5">
                        {/* Stepper */}
                        <div className="stepper mb-5">
                            <div className="d-flex justify-content-between position-relative">
                                <div
                                    className="position-absolute w-100 top-50 start-0 translate-middle-y"
                                    style={{
                                        height: "2px",
                                        background: "#E5E7EB",
                                        zIndex: 0,
                                    }}
                                ></div>
                                <div
                                    className="position-absolute top-50 start-0 translate-middle-y"
                                    style={{
                                        height: "2px",
                                        background: themeColor,
                                        width: `${
                                            ((step - 1) / (steps.length - 1)) *
                                            100
                                        }%`,
                                        transition: "width 0.4s",
                                        zIndex: 0,
                                    }}
                                ></div>

                                {steps.map((s) => (
                                    <div
                                        key={s.id}
                                        className="text-center position-relative"
                                        style={{ zIndex: 1, width: "80px" }}
                                    >
                                        <div
                                            className="d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold"
                                            style={{
                                                width: "32px",
                                                height: "32px",
                                                borderRadius: "50%",
                                                backgroundColor:
                                                    step >= s.id
                                                        ? themeColor
                                                        : "#fff",
                                                color:
                                                    step >= s.id
                                                        ? "#fff"
                                                        : "#9CA3AF",
                                                border: `2px solid ${
                                                    step >= s.id
                                                        ? themeColor
                                                        : "#E5E7EB"
                                                }`,
                                                transition: "all 0.3s",
                                            }}
                                        >
                                            {step > s.id ? (
                                                <i className="fa-solid fa-check"></i>
                                            ) : (
                                                s.id
                                            )}
                                        </div>
                                        <small
                                            className={`d-none d-md-block fw-bold pt-3 ${
                                                step >= s.id
                                                    ? "text-dark"
                                                    : "text-muted"
                                            }`}
                                            style={{ fontSize: "12px" }}
                                        >
                                            {s.label}
                                        </small>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Step Content */}
                        <div className="step-content animate-fade">
                            {/* STEP 1: Room & Brand */}
                            {step === 1 && (
                                <div>
                                    <h4 className="mb-4">
                                        Select Room Type & Brand
                                    </h4>
                                    <div className="row g-4 mb-5">
                                        <div className="col-12">
                                            <h6 className="mb-3 text-muted">
                                                Room Size
                                            </h6>
                                        </div>
                                        {metaData.roomTypes.map((room) => (
                                            <div
                                                className="col-md-4"
                                                key={room.id}
                                            >
                                                <div
                                                    className="selection-card p-4 rounded-3 text-center h-100 transition-all"
                                                    style={{
                                                        border: "2px solid",
                                                        ...(selection.roomType ===
                                                        room.id
                                                            ? activeStyle
                                                            : defaultStyle),
                                                        cursor: "pointer",
                                                    }}
                                                    onClick={() =>
                                                        handleSelection(
                                                            "roomType",
                                                            room.id
                                                        )
                                                    }
                                                >
                                                    <img
                                                        src={room.icon}
                                                        alt={room.name}
                                                        className="mb-3"
                                                        style={{
                                                            height: "50px",
                                                        }}
                                                    />
                                                    <h5 className="h6 mb-1">
                                                        {room.name}
                                                    </h5>
                                                    <small className="text-muted">
                                                        {room.desc}
                                                    </small>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                    <div className="row g-4">
                                        <div className="col-12">
                                            <h6 className="mb-3 text-muted">
                                                Preferred Brand
                                            </h6>
                                        </div>
                                        {metaData.brands.map((brand) => (
                                            <div
                                                className="col-md-4"
                                                key={brand.id}
                                            >
                                                <div
                                                    className="selection-card p-4 rounded-3 text-center h-100 transition-all"
                                                    style={{
                                                        border: "2px solid",
                                                        ...(selection.brand ===
                                                        brand.id
                                                            ? activeStyle
                                                            : defaultStyle),
                                                        cursor: "pointer",
                                                    }}
                                                    onClick={() =>
                                                        handleSelection(
                                                            "brand",
                                                            brand.id
                                                        )
                                                    }
                                                >
                                                    <img
                                                        src={brand.logo}
                                                        alt={brand.name}
                                                        className="img-fluid"
                                                        style={{
                                                            maxHeight: "40px",
                                                        }}
                                                    />
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* STEP 2: Platform */}
                            {step === 2 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Select Video Conferencing Platform
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.platforms.map((platform) => (
                                            <div
                                                className="col-6 col-md-3"
                                                key={platform.id}
                                            >
                                                <div
                                                    className="selection-card p-4 rounded-3 text-center h-100 transition-all"
                                                    style={{
                                                        border: "2px solid",
                                                        ...(selection.platform ===
                                                        platform.id
                                                            ? activeStyle
                                                            : defaultStyle),
                                                        cursor: "pointer",
                                                    }}
                                                    onClick={() =>
                                                        handleSelection(
                                                            "platform",
                                                            platform.id
                                                        )
                                                    }
                                                >
                                                    <img
                                                        src={platform.icon}
                                                        alt={platform.name}
                                                        className="mb-3"
                                                        style={{
                                                            height: "40px",
                                                        }}
                                                    />
                                                    <h6 className="mb-0 fs-6">
                                                        {platform.name}
                                                    </h6>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* STEP 3: Deployment */}
                            {step === 3 && (
                                <div>
                                    <h4 className="mb-4 text-center">
                                        Deployment Preference
                                    </h4>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.deploymentTypes.map(
                                            (deploy) => (
                                                <div
                                                    className="col-md-5"
                                                    key={deploy.id}
                                                >
                                                    <div
                                                        className="selection-card p-4 rounded-3 text-center h-100 transition-all"
                                                        style={{
                                                            border: "2px solid",
                                                            ...(selection.deployment ===
                                                            deploy.id
                                                                ? activeStyle
                                                                : defaultStyle),
                                                            cursor: "pointer",
                                                        }}
                                                        onClick={() =>
                                                            handleSelection(
                                                                "deployment",
                                                                deploy.id
                                                            )
                                                        }
                                                    >
                                                        <img
                                                            src={deploy.icon}
                                                            alt={deploy.name}
                                                            className="mb-3"
                                                            style={{
                                                                height: "50px",
                                                            }}
                                                        />
                                                        <h5 className="h5 mb-2">
                                                            {deploy.name}
                                                        </h5>
                                                        <p className="small text-muted mb-0">
                                                            {deploy.desc}
                                                        </p>
                                                    </div>
                                                </div>
                                            )
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* STEP 4: Main Device (Camera + PC if PC-based) */}
                            {step === 4 && (
                                <div className="animation-fadeIn">
                                    <h4 className="mb-2 text-center fw-bold">
                                        Main Device
                                    </h4>
                                    <p className="text-center text-muted mb-5">
                                        Select your primary camera and compute
                                        device.
                                    </p>

                                    {/* Main Product Selection (Single Select) */}
                                    <h5 className="mb-3 fw-bold border-bottom pb-2">
                                        Main Camera
                                    </h5>
                                    <div className="row g-4 justify-content-center mb-5">
                                        {getAvailableProducts().length > 0 ? (
                                            getAvailableProducts().map(
                                                (product) => {
                                                    const isSelected =
                                                        selection.products.some(
                                                            (p) =>
                                                                p.id ===
                                                                product.id
                                                        );
                                                    return (
                                                        <div
                                                            className="col-lg-6"
                                                            key={product.id}
                                                        >
                                                            <div
                                                                className={`selection-card p-4 rounded-4 h-100 position-relative ${
                                                                    isSelected
                                                                        ? "shadow-lg"
                                                                        : "shadow-sm"
                                                                }`}
                                                                style={{
                                                                    border: "2px solid",
                                                                    borderColor:
                                                                        isSelected
                                                                            ? themeColor
                                                                            : "#E5E7EB",
                                                                    transition:
                                                                        "all 0.3s",
                                                                }}
                                                            >
                                                                {/* Main Product Selection (Single Select) */}
                                                                <div
                                                                    className="d-flex align-items-center mb-4"
                                                                    style={{
                                                                        cursor: "pointer",
                                                                    }}
                                                                    onClick={() =>
                                                                        setSelection(
                                                                            (
                                                                                prev
                                                                            ) => ({
                                                                                ...prev,
                                                                                accessories:
                                                                                    [],
                                                                                products:
                                                                                    [
                                                                                        product,
                                                                                    ],
                                                                                quantities:
                                                                                    {
                                                                                        ...prev.quantities,
                                                                                        [product.id]:
                                                                                            prev
                                                                                                .quantities[
                                                                                                product
                                                                                                    .id
                                                                                            ] ||
                                                                                            1,
                                                                                    },
                                                                            })
                                                                        )
                                                                    }
                                                                >
                                                                    <div
                                                                        className="flex-shrink-0 me-4 bg-white p-2 rounded border"
                                                                        style={{
                                                                            width: "100px",
                                                                            height: "100px",
                                                                            display:
                                                                                "flex",
                                                                            alignItems:
                                                                                "center",
                                                                            justifyContent:
                                                                                "center",
                                                                        }}
                                                                    >
                                                                        <img
                                                                            src={
                                                                                product.image
                                                                            }
                                                                            alt={
                                                                                product.name
                                                                            }
                                                                            className="img-fluid"
                                                                            style={{
                                                                                maxHeight:
                                                                                    "100%",
                                                                                objectFit:
                                                                                    "contain",
                                                                            }}
                                                                        />
                                                                    </div>
                                                                    <div className="flex-grow-1">
                                                                        <div className="d-flex justify-content-between align-items-center mb-1">
                                                                            <h5 className="h6 fw-bold mb-0">
                                                                                {
                                                                                    product.name
                                                                                }
                                                                            </h5>
                                                                            {isSelected && (
                                                                                <i
                                                                                    className="fa-solid fa-circle-check fa-xl"
                                                                                    style={{
                                                                                        color: themeColor,
                                                                                    }}
                                                                                ></i>
                                                                            )}
                                                                        </div>
                                                                        <p className="small text-muted mb-0">
                                                                            {
                                                                                product.desc
                                                                            }
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                {/* Quantity Input (Only if selected) */}
                                                                {isSelected && (
                                                                    <div className="mb-3 d-flex align-items-center bg-light p-2 rounded">
                                                                        <label className="small fw-bold me-3 mb-0">
                                                                            Qty:
                                                                        </label>
                                                                        {renderQuantityControl(
                                                                            product.id
                                                                        )}
                                                                    </div>
                                                                )}

                                                                {/* Accessories */}
                                                                {product.accessories && (
                                                                    <div className="mt-4 pt-3 border-top bg-light rounded-3 p-3">
                                                                        <h6 className="small text-uppercase text-muted fw-bold mb-3">
                                                                            Recommended
                                                                            Accessories
                                                                        </h6>
                                                                        {product.accessories.map(
                                                                            (
                                                                                acc
                                                                            ) => {
                                                                                const isAccSelected =
                                                                                    selection.accessories.some(
                                                                                        (
                                                                                            a
                                                                                        ) =>
                                                                                            a.id ===
                                                                                            acc.id
                                                                                    );
                                                                                return (
                                                                                    <div
                                                                                        key={
                                                                                            acc.id
                                                                                        }
                                                                                        className="d-flex justify-content-between align-items-center mb-2 p-2 rounded bg-white border"
                                                                                        style={{
                                                                                            cursor: "pointer",
                                                                                            borderColor:
                                                                                                isAccSelected
                                                                                                    ? themeColor
                                                                                                    : "#eee",
                                                                                        }}
                                                                                        onClick={(
                                                                                            e
                                                                                        ) => {
                                                                                            e.stopPropagation();
                                                                                            // Check if trying to select add-on without main product (camera)
                                                                                            if (
                                                                                                !isSelected
                                                                                            ) {
                                                                                                setToastMessage(
                                                                                                    "Please select the camera first."
                                                                                                );
                                                                                                setShowToast(
                                                                                                    true
                                                                                                );
                                                                                                return;
                                                                                            }
                                                                                            handleToggle(
                                                                                                "accessories",
                                                                                                acc
                                                                                            );
                                                                                        }}
                                                                                    >
                                                                                        <div className="d-flex align-items-center">
                                                                                            <div
                                                                                                className={`rounded-circle border d-flex align-items-center justify-content-center me-2 ${
                                                                                                    isAccSelected
                                                                                                        ? "bg-success text-white"
                                                                                                        : ""
                                                                                                }`}
                                                                                                style={{
                                                                                                    width: "20px",
                                                                                                    height: "20px",
                                                                                                    borderColor:
                                                                                                        isAccSelected
                                                                                                            ? themeColor
                                                                                                            : "#ddd",
                                                                                                }}
                                                                                            >
                                                                                                {isAccSelected && (
                                                                                                    <i
                                                                                                        className="fa-solid fa-check"
                                                                                                        style={{
                                                                                                            fontSize:
                                                                                                                "10px",
                                                                                                        }}
                                                                                                    ></i>
                                                                                                )}
                                                                                            </div>
                                                                                            <span className="small fw-medium">
                                                                                                {
                                                                                                    acc.name
                                                                                                }
                                                                                            </span>
                                                                                        </div>
                                                                                        {/* Quantity for Accessory? User said "accesories optional in card", "If Device add form quantity". Assuming main device has quantity. */}
                                                                                    </div>
                                                                                );
                                                                            }
                                                                        )}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </div>
                                                    );
                                                }
                                            )
                                        ) : (
                                            <div className="col-12 text-center text-muted py-5">
                                                No specific hardware bundles
                                                found for this combination.
                                            </div>
                                        )}
                                    </div>

                                    {/* PC Selection (Only if PC-Based) */}
                                    {selection.deployment === "pc-based" && (
                                        <div className="animation-fadeIn">
                                            <h5 className="mb-3 fw-bold border-bottom pb-2">
                                                Select Computing Device
                                            </h5>
                                            <div className="row g-4 justify-content-center">
                                                {pcOptions.map((pc) => {
                                                    const isSelected =
                                                        selection.pc?.id ===
                                                        pc.id;
                                                    return (
                                                        <div
                                                            className="col-md-6"
                                                            key={pc.id}
                                                        >
                                                            <div
                                                                className={`selection-card p-4 rounded-4 h-100 position-relative ${
                                                                    isSelected
                                                                        ? "shadow-lg"
                                                                        : "shadow-sm"
                                                                }`}
                                                                style={{
                                                                    border: "2px solid",
                                                                    borderColor:
                                                                        isSelected
                                                                            ? themeColor
                                                                            : "#E5E7EB",
                                                                    cursor: "pointer",
                                                                }}
                                                                onClick={() =>
                                                                    setSelection(
                                                                        (
                                                                            prev
                                                                        ) => ({
                                                                            ...prev,
                                                                            pc: pc,
                                                                            quantities:
                                                                                {
                                                                                    ...prev.quantities,
                                                                                    [pc.id]:
                                                                                        prev
                                                                                            .quantities[
                                                                                            pc
                                                                                                .id
                                                                                        ] ||
                                                                                        1,
                                                                                },
                                                                        })
                                                                    )
                                                                }
                                                            >
                                                                <div className="d-flex align-items-center mb-3">
                                                                    <div className="flex-shrink-0 me-3">
                                                                        <i className="fa-solid fa-desktop fa-3x text-muted"></i>
                                                                    </div>
                                                                    <div>
                                                                        <h6 className="fw-bold mb-1">
                                                                            {
                                                                                pc.name
                                                                            }
                                                                        </h6>
                                                                        <small className="text-muted d-block">
                                                                            {
                                                                                pc.desc
                                                                            }
                                                                        </small>
                                                                    </div>
                                                                    {isSelected && (
                                                                        <div className="ms-auto">
                                                                            <i
                                                                                className="fa-solid fa-circle-check fa-xl"
                                                                                style={{
                                                                                    color: themeColor,
                                                                                }}
                                                                            ></i>
                                                                        </div>
                                                                    )}
                                                                </div>

                                                                <div className="bg-light p-3 rounded small mb-3">
                                                                    <div className="d-flex justify-content-between mb-1">
                                                                        <span>
                                                                            Processor:
                                                                        </span>{" "}
                                                                        <strong>
                                                                            {
                                                                                pc
                                                                                    .specs
                                                                                    .processor
                                                                            }
                                                                        </strong>
                                                                    </div>
                                                                    <div className="d-flex justify-content-between mb-1">
                                                                        <span>
                                                                            RAM:
                                                                        </span>{" "}
                                                                        <strong>
                                                                            {
                                                                                pc
                                                                                    .specs
                                                                                    .ram
                                                                            }
                                                                        </strong>
                                                                    </div>
                                                                    <div className="d-flex justify-content-between">
                                                                        <span>
                                                                            Storage:
                                                                        </span>{" "}
                                                                        <strong>
                                                                            {
                                                                                pc
                                                                                    .specs
                                                                                    .storage
                                                                            }
                                                                        </strong>
                                                                    </div>
                                                                </div>

                                                                {isSelected && (
                                                                    <div
                                                                        className="d-flex align-items-center bg-white border p-2 rounded"
                                                                        onClick={(
                                                                            e
                                                                        ) =>
                                                                            e.stopPropagation()
                                                                        }
                                                                    >
                                                                        <label className="small fw-bold me-3 mb-0">
                                                                            Qty:
                                                                        </label>
                                                                        {renderQuantityControl(
                                                                            pc.id
                                                                        )}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}

                            {/* STEP 5: Add room-filling audio */}
                            {step === 5 && (
                                <div className="animation-fadeIn">
                                    <h4 className="mb-2 text-center fw-bold">
                                        Add room-filling audio
                                    </h4>
                                    <p className="text-center text-muted mb-5">
                                        Ensure everyone is heard clearly.
                                    </p>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.audioProducts.map((audio) => {
                                            const isSelected =
                                                selection.audio.some(
                                                    (a) => a.id === audio.id
                                                );
                                            return (
                                                <div
                                                    className="col-md-4 col-lg-3"
                                                    key={audio.id}
                                                >
                                                    <div
                                                        className="selection-card p-4 rounded-4 text-center h-100 position-relative"
                                                        style={{
                                                            border: "2px solid",
                                                            borderColor:
                                                                isSelected
                                                                    ? themeColor
                                                                    : "#E5E7EB",
                                                            cursor: "pointer",
                                                        }}
                                                        onClick={() =>
                                                            handleToggle(
                                                                "audio",
                                                                audio
                                                            )
                                                        }
                                                    >
                                                        {isSelected && (
                                                            <div
                                                                className="position-absolute top-0 end-0 m-3"
                                                                style={{
                                                                    color: themeColor,
                                                                }}
                                                            >
                                                                <i className="fa-solid fa-circle-check fa-xl"></i>
                                                            </div>
                                                        )}
                                                        <div
                                                            className="mb-3 d-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm mx-auto"
                                                            style={{
                                                                width: "60px",
                                                                height: "60px",
                                                            }}
                                                        >
                                                            <i className="fa-solid fa-microphone-lines fa-lg text-muted"></i>
                                                        </div>
                                                        <h6 className="mb-1 fw-bold">
                                                            {audio.name}
                                                        </h6>
                                                        <small className="text-muted d-block">
                                                            {audio.desc}
                                                        </small>

                                                        {isSelected && (
                                                            <div
                                                                className="mt-3 bg-light rounded p-2 d-flex align-items-center justify-content-center"
                                                                onClick={(e) =>
                                                                    e.stopPropagation()
                                                                }
                                                            >
                                                                <label className="small fw-bold me-2 mb-0">
                                                                    Qty:
                                                                </label>
                                                                {renderQuantityControl(
                                                                    audio.id
                                                                )}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}

                            {/* STEP 6: Controller */}
                            {step === 6 && (
                                <div className="animation-fadeIn">
                                    <h4 className="mb-2 text-center fw-bold">
                                        Choose your meeting controller
                                    </h4>
                                    <p className="text-center text-muted mb-5">
                                        Control meetings with a dedicated touch
                                        panel.
                                    </p>

                                    <div className="row g-4 justify-content-center mb-5">
                                        {metaData.controllers.map((ctrl) => {
                                            const isSelected =
                                                selection.controller.some(
                                                    (c) => c.id === ctrl.id
                                                );
                                            return (
                                                <div
                                                    className="col-md-6"
                                                    key={ctrl.id}
                                                >
                                                    <div
                                                        className="selection-card p-4 rounded-4 h-100 d-flex align-items-center"
                                                        style={{
                                                            border: "2px solid",
                                                            borderColor:
                                                                isSelected
                                                                    ? themeColor
                                                                    : "#E5E7EB",
                                                            cursor: "pointer",
                                                        }}
                                                        onClick={() =>
                                                            setSelection(
                                                                (prev) => ({
                                                                    ...prev,
                                                                    controller:
                                                                        [ctrl],
                                                                })
                                                            )
                                                        } // Single select for controller usually
                                                    >
                                                        <div className="me-4">
                                                            <i className="fa-solid fa-tablet-screen-button fa-3x text-muted"></i>
                                                        </div>
                                                        <div className="flex-grow-1">
                                                            <div className="d-flex justify-content-between align-items-center">
                                                                <h5 className="h6 fw-bold mb-1">
                                                                    {ctrl.name}
                                                                </h5>
                                                                {isSelected && (
                                                                    <i
                                                                        className="fa-solid fa-circle-check fa-xl"
                                                                        style={{
                                                                            color: themeColor,
                                                                        }}
                                                                    ></i>
                                                                )}
                                                            </div>
                                                            <p className="small text-muted mb-0">
                                                                {ctrl.desc}
                                                            </p>
                                                        </div>

                                                        {isSelected && (
                                                            <div
                                                                className="ms-3 bg-light rounded p-2 d-flex align-items-center"
                                                                onClick={(e) =>
                                                                    e.stopPropagation()
                                                                }
                                                            >
                                                                <label className="small fw-bold me-2 mb-0">
                                                                    Qty:
                                                                </label>
                                                                {renderQuantityControl(
                                                                    ctrl.id
                                                                )}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>

                                    {/* Controller Accessories */}
                                    <h5 className="h6 fw-bold mb-3">
                                        Optional Mounts
                                    </h5>
                                    <div className="row g-3">
                                        {metaData.controllerAccessories.map(
                                            (acc) => {
                                                const isSelected =
                                                    selection.accessories.some(
                                                        (a) => a.id === acc.id
                                                    );
                                                return (
                                                    <div
                                                        className="col-md-4"
                                                        key={acc.id}
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
                                                                handleToggle(
                                                                    "accessories",
                                                                    acc
                                                                )
                                                            }
                                                        >
                                                            <div className="me-3">
                                                                <i className="fa-solid fa-layer-group text-muted"></i>
                                                            </div>
                                                            <div>
                                                                <h6 className="small fw-bold mb-0">
                                                                    {acc.name}
                                                                </h6>
                                                                <small
                                                                    className="text-muted"
                                                                    style={{
                                                                        fontSize:
                                                                            "11px",
                                                                    }}
                                                                >
                                                                    {acc.desc}
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
                                                        {isSelected && (
                                                            <div className="mt-1 d-flex justify-content-end p-1">
                                                                <div
                                                                    className="d-flex align-items-center bg-light rounded p-1"
                                                                    onClick={(
                                                                        e
                                                                    ) =>
                                                                        e.stopPropagation()
                                                                    }
                                                                >
                                                                    <label
                                                                        className="small fw-bold me-2 mb-0"
                                                                        style={{
                                                                            fontSize:
                                                                                "10px",
                                                                        }}
                                                                    >
                                                                        Qty:
                                                                    </label>
                                                                    {renderQuantityControl(
                                                                        acc.id
                                                                    )}
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                );
                                            }
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* STEP 7: Add-Ons */}
                            {step === 7 && (
                                <div className="animation-fadeIn">
                                    <h4 className="mb-2 text-center fw-bold">
                                        Choose your add-ons
                                    </h4>
                                    <p className="text-center text-muted mb-5">
                                        Enhance the meeting experience.
                                    </p>
                                    <div className="row g-4 justify-content-center">
                                        {metaData.accessories.map((acc) => {
                                            const isSelected =
                                                selection.accessories.some(
                                                    (a) => a.id === acc.id
                                                );
                                            return (
                                                <div
                                                    className="col-md-4 col-lg-3"
                                                    key={acc.id}
                                                >
                                                    <div
                                                        className="selection-card p-4 rounded-4 text-center h-100 position-relative"
                                                        style={{
                                                            border: "2px solid",
                                                            borderColor:
                                                                isSelected
                                                                    ? themeColor
                                                                    : "#E5E7EB",
                                                            cursor: "pointer",
                                                        }}
                                                        onClick={() =>
                                                            handleToggle(
                                                                "accessories",
                                                                acc
                                                            )
                                                        }
                                                    >
                                                        {isSelected && (
                                                            <div
                                                                className="position-absolute top-0 end-0 m-3"
                                                                style={{
                                                                    color: themeColor,
                                                                }}
                                                            >
                                                                <i className="fa-solid fa-circle-check fa-xl"></i>
                                                            </div>
                                                        )}
                                                        {acc.recommended && (
                                                            <div className="position-absolute top-0 start-0 m-3 badge bg-warning text-dark">
                                                                Recommended
                                                            </div>
                                                        )}

                                                        <div
                                                            className="mb-3 d-flex align-items-center justify-content-center bg-light rounded-3 mx-auto"
                                                            style={{
                                                                height: "60px",
                                                                width: "60px",
                                                            }}
                                                        >
                                                            <i className="fa-solid fa-puzzle-piece fa-lg text-muted"></i>
                                                        </div>
                                                        <h6 className="mb-1 fw-bold">
                                                            {acc.name}
                                                        </h6>
                                                        <small className="text-muted d-block">
                                                            {acc.desc}
                                                        </small>

                                                        {isSelected && (
                                                            <div
                                                                className="mt-3 bg-light rounded p-2 d-flex align-items-center justify-content-center"
                                                                onClick={(e) =>
                                                                    e.stopPropagation()
                                                                }
                                                            >
                                                                <label className="small fw-bold me-2 mb-0">
                                                                    Qty:
                                                                </label>
                                                                {renderQuantityControl(
                                                                    acc.id
                                                                )}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            )}

                            {/* STEP 8: Services (Wizard Mode) */}
                            {step === 8 && (
                                <div className="animation-fadeIn">
                                    <h4 className="mb-2 text-center fw-bold">
                                        Finish by adding services and tools
                                    </h4>
                                    <p className="text-center text-muted mb-5">
                                        Ensure the deployment is always up and
                                        running.
                                    </p>

                                    <div className="row justify-content-center">
                                        <div className="col-lg-8">
                                            {/* Questions Wizard with Collapsible Checklist */}
                                            {metaData.serviceQuestions.map(
                                                (q, index) => {
                                                    const isAnswered =
                                                        !!selection.services[
                                                            q.id
                                                        ];
                                                    const isEditing =
                                                        editingServiceId ===
                                                        q.id;

                                                    // Determine visibility:
                                                    // 1. First question always visible (initially expanded or collapsed)
                                                    // 2. Subsequent questions visible only if previous is answered
                                                    const prevQuestionId =
                                                        index > 0
                                                            ? metaData
                                                                  .serviceQuestions[
                                                                  index - 1
                                                              ].id
                                                            : null;
                                                    const isVisible =
                                                        index === 0 ||
                                                        (prevQuestionId &&
                                                            selection.services[
                                                                prevQuestionId
                                                            ]);

                                                    if (!isVisible) return null;

                                                    // If answered and not editing, show Collapsed Checklist View
                                                    if (
                                                        isAnswered &&
                                                        !isEditing
                                                    ) {
                                                        // Only show this collapsed item if it's not "blocking" the current active question?
                                                        // Actually, user wants "checklist" style, so show all answered as collapsed.
                                                        return (
                                                            <div
                                                                className="d-flex align-items-center bg-white border rounded-3 p-3 mb-3 shadow-sm animation-fadeIn"
                                                                key={q.id}
                                                                style={{
                                                                    cursor: "pointer",
                                                                    borderLeft: `4px solid ${themeColor}`,
                                                                }}
                                                                onClick={() =>
                                                                    setEditingServiceId(
                                                                        q.id
                                                                    )
                                                                }
                                                            >
                                                                <div className="me-3 text-success">
                                                                    <i className="fa-solid fa-circle-check fa-lg"></i>
                                                                </div>
                                                                <div className="flex-grow-1">
                                                                    <small
                                                                        className="text-muted d-block text-uppercase fw-bold"
                                                                        style={{
                                                                            fontSize:
                                                                                "10px",
                                                                        }}
                                                                    >
                                                                        Question{" "}
                                                                        {index +
                                                                            1}
                                                                    </small>
                                                                    <div className="fw-bold text-dark mb-0">
                                                                        {
                                                                            q.question
                                                                        }
                                                                    </div>
                                                                    <div className="text-muted small mt-1">
                                                                        <span className="fw-semibold text-dark">
                                                                            Selected:
                                                                        </span>{" "}
                                                                        {
                                                                            selection
                                                                                .services[
                                                                                q
                                                                                    .id
                                                                            ]
                                                                        }
                                                                    </div>
                                                                </div>
                                                                <div className="ms-3 text-muted">
                                                                    <i className="fa-solid fa-pen-to-square"></i>
                                                                </div>
                                                            </div>
                                                        );
                                                    }

                                                    // Expanded View (Active Question)
                                                    return (
                                                        <div
                                                            className="card border-0 shadow-sm rounded-4 mb-4 animation-fadeIn"
                                                            key={q.id}
                                                            style={{
                                                                borderLeft: `4px solid ${themeColor}`,
                                                            }}
                                                        >
                                                            <div className="card-body p-4">
                                                                <div className="d-flex justify-content-between align-items-center mb-3">
                                                                    <h6 className="fw-bold mb-0">
                                                                        {index +
                                                                            1}
                                                                        .{" "}
                                                                        {
                                                                            q.question
                                                                        }
                                                                    </h6>
                                                                    {isEditing && (
                                                                        <button
                                                                            className="btn btn-sm btn-light text-muted"
                                                                            onClick={() =>
                                                                                setEditingServiceId(
                                                                                    null
                                                                                )
                                                                            }
                                                                        >
                                                                            Cancel
                                                                        </button>
                                                                    )}
                                                                </div>
                                                                <div className="row g-3">
                                                                    {q.options.map(
                                                                        (
                                                                            opt
                                                                        ) => (
                                                                            <div
                                                                                className="col-md-6"
                                                                                key={
                                                                                    opt
                                                                                }
                                                                            >
                                                                                <div
                                                                                    className="p-3 border rounded-3 cursor-pointer d-flex align-items-center justify-content-between transition-all hover-scale"
                                                                                    style={{
                                                                                        borderColor:
                                                                                            selection
                                                                                                .services[
                                                                                                q
                                                                                                    .id
                                                                                            ] ===
                                                                                            opt
                                                                                                ? themeColor
                                                                                                : "#E5E7EB",
                                                                                        backgroundColor:
                                                                                            selection
                                                                                                .services[
                                                                                                q
                                                                                                    .id
                                                                                            ] ===
                                                                                            opt
                                                                                                ? "#F0FDF4"
                                                                                                : "#fff",
                                                                                    }}
                                                                                    onClick={() =>
                                                                                        handleServiceAnswer(
                                                                                            q.id,
                                                                                            opt
                                                                                        )
                                                                                    }
                                                                                >
                                                                                    <span className="small fw-medium">
                                                                                        {
                                                                                            opt
                                                                                        }
                                                                                    </span>
                                                                                    {selection
                                                                                        .services[
                                                                                        q
                                                                                            .id
                                                                                    ] ===
                                                                                        opt && (
                                                                                        <i
                                                                                            className="fa-solid fa-circle-check"
                                                                                            style={{
                                                                                                color: themeColor,
                                                                                            }}
                                                                                        ></i>
                                                                                    )}
                                                                                </div>
                                                                            </div>
                                                                        )
                                                                    )}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    );
                                                }
                                            )}

                                            {/* Optional Management Licenses */}
                                            {Object.keys(selection.services)
                                                .length >=
                                                metaData.serviceQuestions
                                                    .length && (
                                                <div className="mt-5 animation-fadeIn">
                                                    {/* Room Platform License (Zoom/Teams only) */}
                                                    {(selection.platform ===
                                                        "zoom" ||
                                                        selection.platform ===
                                                            "teams") && (
                                                        <div className="mb-5">
                                                            <h5 className="fw-bold mb-3 border-bottom pb-2">
                                                                Room Platform
                                                                License
                                                            </h5>
                                                            <div className="row g-3">
                                                                {platformLicenses
                                                                    .filter(
                                                                        (lic) =>
                                                                            lic.id ===
                                                                            selection.platform
                                                                    )
                                                                    .map(
                                                                        (
                                                                            lic
                                                                        ) => {
                                                                            const isSelected =
                                                                                selection
                                                                                    .platformLicense
                                                                                    ?.id ===
                                                                                lic.id;
                                                                            return (
                                                                                <div
                                                                                    className="col-md-6"
                                                                                    key={
                                                                                        lic.id
                                                                                    }
                                                                                >
                                                                                    <div
                                                                                        className={`p-3 border rounded-3 position-relative ${
                                                                                            isSelected
                                                                                                ? "bg-light border-success"
                                                                                                : "bg-white"
                                                                                        }`}
                                                                                        style={{
                                                                                            cursor: "pointer",
                                                                                            borderColor:
                                                                                                isSelected
                                                                                                    ? themeColor
                                                                                                    : "#E5E7EB",
                                                                                        }}
                                                                                        onClick={() => {
                                                                                            const newSelection =
                                                                                                isSelected
                                                                                                    ? null
                                                                                                    : lic;
                                                                                            setSelection(
                                                                                                (
                                                                                                    prev
                                                                                                ) => ({
                                                                                                    ...prev,
                                                                                                    platformLicense:
                                                                                                        newSelection,
                                                                                                    quantities:
                                                                                                        {
                                                                                                            ...prev.quantities,
                                                                                                            [lic.id]:
                                                                                                                newSelection
                                                                                                                    ? prev
                                                                                                                          .quantities[
                                                                                                                          lic
                                                                                                                              .id
                                                                                                                      ] ||
                                                                                                                      1
                                                                                                                    : 0,
                                                                                                        },
                                                                                                })
                                                                                            );
                                                                                        }}
                                                                                    >
                                                                                        <div className="d-flex justify-content-between align-items-center mb-2">
                                                                                            <h6 className="fw-bold mb-0">
                                                                                                {
                                                                                                    lic.name
                                                                                                }
                                                                                            </h6>
                                                                                            {isSelected && (
                                                                                                <i
                                                                                                    className="fa-solid fa-circle-check"
                                                                                                    style={{
                                                                                                        color: themeColor,
                                                                                                    }}
                                                                                                ></i>
                                                                                            )}
                                                                                        </div>
                                                                                        <p className="small text-muted mb-2">
                                                                                            {
                                                                                                lic.desc
                                                                                            }
                                                                                        </p>

                                                                                        {isSelected && (
                                                                                            <div
                                                                                                className="mt-3 pt-2 border-top d-flex align-items-center"
                                                                                                onClick={(
                                                                                                    e
                                                                                                ) =>
                                                                                                    e.stopPropagation()
                                                                                                }
                                                                                            >
                                                                                                <label className="small fw-bold me-2 mb-0">
                                                                                                    Qty:
                                                                                                </label>
                                                                                                {renderQuantityControl(
                                                                                                    lic.id
                                                                                                )}
                                                                                            </div>
                                                                                        )}
                                                                                    </div>
                                                                                </div>
                                                                            );
                                                                        }
                                                                    )}
                                                            </div>
                                                        </div>
                                                    )}

                                                    {/* Room Management Licenses */}
                                                    <div className="mb-5">
                                                        <h5 className="fw-bold mb-3 border-bottom pb-2">
                                                            Room Management
                                                            Licenses
                                                        </h5>
                                                        <div className="row g-3">
                                                            {managementLicenses.map(
                                                                (lic) => {
                                                                    const isSelected =
                                                                        selection
                                                                            .managementLicense
                                                                            ?.id ===
                                                                        lic.id;
                                                                    return (
                                                                        <div
                                                                            className="col-md-6"
                                                                            key={
                                                                                lic.id
                                                                            }
                                                                        >
                                                                            <div
                                                                                className={`p-3 border rounded-3 position-relative ${
                                                                                    isSelected
                                                                                        ? "bg-light border-success"
                                                                                        : "bg-white"
                                                                                }`}
                                                                                style={{
                                                                                    cursor: "pointer",
                                                                                    borderColor:
                                                                                        isSelected
                                                                                            ? themeColor
                                                                                            : "#E5E7EB",
                                                                                }}
                                                                                onClick={() => {
                                                                                    const newSelection =
                                                                                        isSelected
                                                                                            ? null
                                                                                            : lic;
                                                                                    setSelection(
                                                                                        (
                                                                                            prev
                                                                                        ) => ({
                                                                                            ...prev,
                                                                                            managementLicense:
                                                                                                newSelection,
                                                                                            quantities:
                                                                                                {
                                                                                                    ...prev.quantities,
                                                                                                    [lic.id]:
                                                                                                        newSelection
                                                                                                            ? prev
                                                                                                                  .quantities[
                                                                                                                  lic
                                                                                                                      .id
                                                                                                              ] ||
                                                                                                              1
                                                                                                            : 0,
                                                                                                },
                                                                                        })
                                                                                    );
                                                                                }}
                                                                            >
                                                                                <div className="d-flex justify-content-between align-items-center mb-2">
                                                                                    <h6 className="fw-bold mb-0">
                                                                                        {
                                                                                            lic.name
                                                                                        }
                                                                                    </h6>
                                                                                    {isSelected && (
                                                                                        <i
                                                                                            className="fa-solid fa-circle-check"
                                                                                            style={{
                                                                                                color: themeColor,
                                                                                            }}
                                                                                        ></i>
                                                                                    )}
                                                                                </div>
                                                                                <p className="small text-muted mb-2">
                                                                                    {
                                                                                        lic.desc
                                                                                    }
                                                                                </p>

                                                                                {isSelected && (
                                                                                    <div
                                                                                        className="mt-3 pt-2 border-top d-flex align-items-center"
                                                                                        onClick={(
                                                                                            e
                                                                                        ) =>
                                                                                            e.stopPropagation()
                                                                                        }
                                                                                    >
                                                                                        <label className="small fw-bold me-2 mb-0">
                                                                                            Qty:
                                                                                        </label>
                                                                                        {renderQuantityControl(
                                                                                            lic.id
                                                                                        )}
                                                                                    </div>
                                                                                )}
                                                                            </div>
                                                                        </div>
                                                                    );
                                                                }
                                                            )}
                                                        </div>
                                                    </div>

                                                    <h5 className="fw-bold mb-3 border-bottom pb-2">
                                                        Extended Warranty
                                                    </h5>
                                                    <div className="row g-3">
                                                        {warrantyData.map(
                                                            (war) => {
                                                                const isSelected =
                                                                    selection
                                                                        .warranty
                                                                        ?.id ===
                                                                    war.id;
                                                                return (
                                                                    <div
                                                                        className="col-md-4"
                                                                        key={
                                                                            war.id
                                                                        }
                                                                    >
                                                                        <div
                                                                            className={`p-3 border rounded-3 h-100 position-relative ${
                                                                                isSelected
                                                                                    ? "bg-light border-success"
                                                                                    : "bg-white"
                                                                            }`}
                                                                            style={{
                                                                                cursor: "pointer",
                                                                                borderColor:
                                                                                    isSelected
                                                                                        ? themeColor
                                                                                        : "#E5E7EB",
                                                                            }}
                                                                            onClick={() =>
                                                                                setSelection(
                                                                                    (
                                                                                        prev
                                                                                    ) => ({
                                                                                        ...prev,
                                                                                        warranty:
                                                                                            war,
                                                                                        quantities:
                                                                                            {
                                                                                                ...prev.quantities,
                                                                                                [war.id]:
                                                                                                    prev
                                                                                                        .quantities[
                                                                                                        war
                                                                                                            .id
                                                                                                    ] ||
                                                                                                    1,
                                                                                            },
                                                                                    })
                                                                                )
                                                                            }
                                                                        >
                                                                            <div className="d-flex justify-content-between align-items-start mb-2">
                                                                                <h6 className="fw-bold mb-0 small">
                                                                                    {
                                                                                        war.name
                                                                                    }
                                                                                </h6>
                                                                                {isSelected && (
                                                                                    <i
                                                                                        className="fa-solid fa-circle-check"
                                                                                        style={{
                                                                                            color: themeColor,
                                                                                        }}
                                                                                    ></i>
                                                                                )}
                                                                            </div>
                                                                            <p
                                                                                className="small text-muted mb-2"
                                                                                style={{
                                                                                    fontSize:
                                                                                        "11px",
                                                                                }}
                                                                            >
                                                                                {
                                                                                    war.desc
                                                                                }
                                                                            </p>

                                                                            {isSelected && (
                                                                                <div
                                                                                    className="mt-3 pt-2 border-top d-flex align-items-center"
                                                                                    onClick={(
                                                                                        e
                                                                                    ) =>
                                                                                        e.stopPropagation()
                                                                                    }
                                                                                >
                                                                                    <label className="small fw-bold me-2 mb-0">
                                                                                        Qty:
                                                                                    </label>
                                                                                    {renderQuantityControl(
                                                                                        war.id
                                                                                    )}
                                                                                </div>
                                                                            )}
                                                                        </div>
                                                                    </div>
                                                                );
                                                            }
                                                        )}
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* STEP 9: Summary */}
                            {step === 9 && (
                                <div className="animation-fadeIn">
                                    <h4 className="mb-4 text-center fw-bold">
                                        Configuration Summary
                                    </h4>
                                    <div className="row justify-content-center">
                                        <div className="col-lg-10">
                                            <div className="summary-card bg-white border rounded-4 overflow-hidden shadow-sm">
                                                <div className="p-4 border-bottom bg-light">
                                                    <div className="row align-items-center">
                                                        <div className="col-md-3 mb-3 mb-md-0">
                                                            <small
                                                                className="text-muted text-uppercase d-block fw-bold"
                                                                style={{
                                                                    fontSize:
                                                                        "10px",
                                                                }}
                                                            >
                                                                Room Type
                                                            </small>
                                                            <strong className="text-dark">
                                                                {
                                                                    metaData.roomTypes.find(
                                                                        (r) =>
                                                                            r.id ===
                                                                            selection.roomType
                                                                    )?.name
                                                                }
                                                            </strong>
                                                        </div>
                                                        <div className="col-md-3 mb-3 mb-md-0">
                                                            <small
                                                                className="text-muted text-uppercase d-block fw-bold"
                                                                style={{
                                                                    fontSize:
                                                                        "10px",
                                                                }}
                                                            >
                                                                Brand
                                                            </small>
                                                            <strong className="text-dark">
                                                                {
                                                                    metaData.brands.find(
                                                                        (b) =>
                                                                            b.id ===
                                                                            selection.brand
                                                                    )?.name
                                                                }
                                                            </strong>
                                                        </div>
                                                        <div className="col-md-3 mb-3 mb-md-0">
                                                            <small
                                                                className="text-muted text-uppercase d-block fw-bold"
                                                                style={{
                                                                    fontSize:
                                                                        "10px",
                                                                }}
                                                            >
                                                                Platform
                                                            </small>
                                                            <div className="d-flex align-items-center">
                                                                {selection.platform !==
                                                                    "byod" && (
                                                                    <img
                                                                        src={
                                                                            metaData.platforms.find(
                                                                                (
                                                                                    p
                                                                                ) =>
                                                                                    p.id ===
                                                                                    selection.platform
                                                                            )
                                                                                ?.icon
                                                                        }
                                                                        className="me-2"
                                                                        style={{
                                                                            height: "16px",
                                                                        }}
                                                                    />
                                                                )}
                                                                <strong className="text-dark">
                                                                    {selection.platform ===
                                                                    "byod"
                                                                        ? "BYOD"
                                                                        : metaData.platforms.find(
                                                                              (
                                                                                  p
                                                                              ) =>
                                                                                  p.id ===
                                                                                  selection.platform
                                                                          )
                                                                              ?.name}
                                                                </strong>
                                                            </div>
                                                        </div>
                                                        <div className="col-md-3">
                                                            <small
                                                                className="text-muted text-uppercase d-block fw-bold"
                                                                style={{
                                                                    fontSize:
                                                                        "10px",
                                                                }}
                                                            >
                                                                Deployment
                                                            </small>
                                                            <strong className="text-dark">
                                                                {selection.deployment
                                                                    ? metaData.deploymentTypes.find(
                                                                          (d) =>
                                                                              d.id ===
                                                                              selection.deployment
                                                                      )?.name
                                                                    : "N/A"}
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="p-4">
                                                    <h6 className="border-bottom pb-2 mb-3 fw-bold">
                                                        Selected Hardware &
                                                        Services
                                                    </h6>

                                                    {/* Grouped Lists */}
                                                    <div className="row g-4">
                                                        <div className="col-md-6">
                                                            <h6 className="small text-muted fw-bold mb-3">
                                                                Hardware
                                                                (Camera, Audio,
                                                                Compute)
                                                            </h6>
                                                            <ul className="list-group list-group-flush">
                                                                {selection.products.map(
                                                                    (p) => (
                                                                        <li
                                                                            className="list-group-item px-0 py-2 border-0 d-flex align-items-center"
                                                                            key={
                                                                                p.id
                                                                            }
                                                                        >
                                                                            <img
                                                                                src={
                                                                                    p.image
                                                                                }
                                                                                className="rounded me-3 border"
                                                                                style={{
                                                                                    width: "40px",
                                                                                    height: "40px",
                                                                                    objectFit:
                                                                                        "contain",
                                                                                }}
                                                                            />
                                                                            <div className="lh-1 flex-grow-1">
                                                                                <span className="fw-medium d-block mb-1">
                                                                                    {
                                                                                        p.name
                                                                                    }{" "}
                                                                                    <span className="badge bg-light text-dark border ms-1">
                                                                                        x
                                                                                        {selection
                                                                                            .quantities[
                                                                                            p
                                                                                                .id
                                                                                        ] ||
                                                                                            1}
                                                                                    </span>
                                                                                </span>
                                                                                <small className="text-muted">
                                                                                    Camera
                                                                                </small>
                                                                            </div>
                                                                        </li>
                                                                    )
                                                                )}
                                                                {selection.pc && (
                                                                    <li className="list-group-item px-0 py-2 border-0 d-flex align-items-center">
                                                                        <div
                                                                            className="rounded me-3 bg-light border d-flex align-items-center justify-content-center"
                                                                            style={{
                                                                                width: "40px",
                                                                                height: "40px",
                                                                            }}
                                                                        >
                                                                            <i className="fa-solid fa-desktop text-muted"></i>
                                                                        </div>
                                                                        <div className="lh-1 flex-grow-1">
                                                                            <span className="fw-medium d-block mb-1">
                                                                                {
                                                                                    selection
                                                                                        .pc
                                                                                        .name
                                                                                }{" "}
                                                                                <span className="badge bg-light text-dark border ms-1">
                                                                                    x
                                                                                    {selection
                                                                                        .quantities[
                                                                                        selection
                                                                                            .pc
                                                                                            .id
                                                                                    ] ||
                                                                                        1}
                                                                                </span>
                                                                            </span>
                                                                            <small className="text-muted">
                                                                                Computing
                                                                            </small>
                                                                        </div>
                                                                    </li>
                                                                )}
                                                                {selection.audio.map(
                                                                    (a) => (
                                                                        <li
                                                                            className="list-group-item px-0 py-2 border-0 d-flex align-items-center"
                                                                            key={
                                                                                a.id
                                                                            }
                                                                        >
                                                                            <div
                                                                                className="rounded me-3 bg-light border d-flex align-items-center justify-content-center"
                                                                                style={{
                                                                                    width: "40px",
                                                                                    height: "40px",
                                                                                }}
                                                                            >
                                                                                <i className="fa-solid fa-microphone-lines text-muted"></i>
                                                                            </div>
                                                                            <div className="lh-1 flex-grow-1">
                                                                                <span className="fw-medium d-block mb-1">
                                                                                    {
                                                                                        a.name
                                                                                    }{" "}
                                                                                    <span className="badge bg-light text-dark border ms-1">
                                                                                        x
                                                                                        {selection
                                                                                            .quantities[
                                                                                            a
                                                                                                .id
                                                                                        ] ||
                                                                                            1}
                                                                                    </span>
                                                                                </span>
                                                                                <small className="text-muted">
                                                                                    Audio
                                                                                </small>
                                                                            </div>
                                                                        </li>
                                                                    )
                                                                )}
                                                            </ul>
                                                        </div>

                                                        <div className="col-md-6">
                                                            <h6 className="small text-muted fw-bold mb-3">
                                                                Control,
                                                                Services &
                                                                Accessories
                                                            </h6>
                                                            <ul className="list-group list-group-flush">
                                                                {selection.controller.map(
                                                                    (c) => (
                                                                        <li
                                                                            className="list-group-item px-0 py-2 border-0 d-flex align-items-center"
                                                                            key={
                                                                                c.id
                                                                            }
                                                                        >
                                                                            <div
                                                                                className="rounded me-3 bg-light border d-flex align-items-center justify-content-center"
                                                                                style={{
                                                                                    width: "40px",
                                                                                    height: "40px",
                                                                                }}
                                                                            >
                                                                                <i className="fa-solid fa-tablet-screen-button text-muted"></i>
                                                                            </div>
                                                                            <div className="lh-1 flex-grow-1">
                                                                                <span className="fw-medium d-block mb-1">
                                                                                    {
                                                                                        c.name
                                                                                    }{" "}
                                                                                    <span className="badge bg-light text-dark border ms-1">
                                                                                        x
                                                                                        {selection
                                                                                            .quantities[
                                                                                            c
                                                                                                .id
                                                                                        ] ||
                                                                                            1}
                                                                                    </span>
                                                                                </span>
                                                                                <small className="text-muted">
                                                                                    Controller
                                                                                </small>
                                                                            </div>
                                                                        </li>
                                                                    )
                                                                )}
                                                                {selection.accessories.map(
                                                                    (a) => (
                                                                        <li
                                                                            className="list-group-item px-0 py-2 border-0 d-flex align-items-center"
                                                                            key={
                                                                                a.id
                                                                            }
                                                                        >
                                                                            <div
                                                                                className="rounded me-3 bg-light border d-flex align-items-center justify-content-center"
                                                                                style={{
                                                                                    width: "40px",
                                                                                    height: "40px",
                                                                                }}
                                                                            >
                                                                                <i className="fa-solid fa-puzzle-piece text-muted"></i>
                                                                            </div>
                                                                            <div className="lh-1 flex-grow-1">
                                                                                <span className="fw-medium d-block mb-1">
                                                                                    {
                                                                                        a.name
                                                                                    }{" "}
                                                                                    <span className="badge bg-light text-dark border ms-1">
                                                                                        x
                                                                                        {selection
                                                                                            .quantities[
                                                                                            a
                                                                                                .id
                                                                                        ] ||
                                                                                            1}
                                                                                    </span>
                                                                                </span>
                                                                                <small className="text-muted">
                                                                                    Accessory
                                                                                </small>
                                                                            </div>
                                                                        </li>
                                                                    )
                                                                )}
                                                                {selection.license && (
                                                                    <li className="list-group-item px-0 py-2 border-0 d-flex align-items-center">
                                                                        <div
                                                                            className="rounded me-3 bg-light border d-flex align-items-center justify-content-center"
                                                                            style={{
                                                                                width: "40px",
                                                                                height: "40px",
                                                                            }}
                                                                        >
                                                                            <i className="fa-solid fa-file-contract text-muted"></i>
                                                                        </div>
                                                                        <div className="lh-1 flex-grow-1">
                                                                            <span className="fw-medium d-block mb-1">
                                                                                {
                                                                                    selection
                                                                                        .license
                                                                                        .name
                                                                                }{" "}
                                                                                <span className="badge bg-light text-dark border ms-1">
                                                                                    x
                                                                                    {selection
                                                                                        .quantities[
                                                                                        selection
                                                                                            .license
                                                                                            .id
                                                                                    ] ||
                                                                                        1}
                                                                                </span>
                                                                            </span>
                                                                            <small className="text-muted">
                                                                                License
                                                                            </small>
                                                                        </div>
                                                                    </li>
                                                                )}
                                                                {selection.warranty && (
                                                                    <li className="list-group-item px-0 py-2 border-0 d-flex align-items-center">
                                                                        <div
                                                                            className="rounded me-3 bg-light border d-flex align-items-center justify-content-center"
                                                                            style={{
                                                                                width: "40px",
                                                                                height: "40px",
                                                                            }}
                                                                        >
                                                                            <i className="fa-solid fa-shield-halved text-muted"></i>
                                                                        </div>
                                                                        <div className="lh-1 flex-grow-1">
                                                                            <span className="fw-medium d-block mb-1">
                                                                                {
                                                                                    selection
                                                                                        .warranty
                                                                                        .name
                                                                                }{" "}
                                                                                <span className="badge bg-light text-dark border ms-1">
                                                                                    x
                                                                                    {selection
                                                                                        .quantities[
                                                                                        selection
                                                                                            .warranty
                                                                                            .id
                                                                                    ] ||
                                                                                        1}
                                                                                </span>
                                                                            </span>
                                                                            <small className="text-muted">
                                                                                Warranty
                                                                            </small>
                                                                        </div>
                                                                    </li>
                                                                )}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Actions */}
                        <div className="d-flex justify-content-between mt-5 pt-3 border-top">
                            {step > 1 ? (
                                <button
                                    className="th-btn style4 th-radius"
                                    onClick={prevStep}
                                    style={{ padding: "12px 30px" }}
                                >
                                    <i className="fa-solid fa-arrow-left me-2"></i>{" "}
                                    Back
                                </button>
                            ) : (
                                <span></span>
                            )}

                            {step < 9 ? (
                                <button
                                    className="th-btn th-radius shadow-none"
                                    style={{
                                        backgroundColor: themeColor,
                                        borderColor: themeColor,
                                    }}
                                    onClick={nextStep}
                                >
                                    Next Step{" "}
                                    <i className="fa-solid fa-arrow-right ms-2"></i>
                                </button>
                            ) : (
                                <button
                                    className="th-btn th-radius shadow-none style8"
                                    onClick={handleFinalization}
                                >
                                    Finalization Now{" "}
                                    <i className="fa-solid fa-check ms-2"></i>
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {/* Finalization Modal */}
            <ConfiguratorSubmitModal
                show={showModal}
                onClose={() => setShowModal(false)}
                userInfo={userInfo}
                onChange={handleUserInfoChange}
                onSubmit={handleSubmitConfiguration}
            />
            {/* Toast Notification */}
            <Toast
                show={showToast}
                message={toastMessage}
                type="danger"
                onClose={() => setShowToast(false)}
            />
        </MainLayout>
    );
}
