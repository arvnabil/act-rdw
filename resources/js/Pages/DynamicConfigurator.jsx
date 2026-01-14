import React, { useState, useEffect, useMemo } from "react";
import { Head, Link, router } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";
import Breadcrumb from "@/Components/Common/Breadcrumb";
import Toast from "@/Components/Common/Toast";
import ConfiguratorSubmitModal from "@/Components/Configurator/ConfiguratorSubmitModal";
import { createPortal } from "react-dom";

export default function DynamicConfigurator({ configurator }) {
    const [currentStepIndex, setCurrentStepIndex] = useState(0);
    const [selection, setSelection] = useState({
        quantities: {},
    });
    const [showToast, setShowToast] = useState(false);
    const [toastMessage, setToastMessage] = useState("");
    const [userInfo, setUserInfo] = useState({
        name: "",
        phone: "",
        sustainability: "",
    });
    const [showSubmitModal, setShowSubmitModal] = useState(false);
    // Modal State
    const [modalState, setModalState] = useState({
        show: false,
        product: null,
        loading: false,
        onConfirm: null,
        isSelected: false,
    });
    const themeColor = "#4AC15E";

    // --- Handlers for Finalization ---
    const handleUserInfoChange = (e) => {
        const { name, value } = e.target;
        setUserInfo((prev) => ({ ...prev, [name]: value }));
    };

    const handleSubmitConfiguration = (e) => {
        e.preventDefault();
        router.post(
            route("configurator.complete"),
            {
                selection,
                userInfo,
                configurator, // Pass configurator data if needed
            },
            {
                onError: (errors) => {
                    console.error("Submission errors:", errors);
                    setToastMessage(
                        "Failed to submit configuration. Please try again."
                    );
                    setShowToast(true);
                },
            }
        );
    };
    const openModal = async (
        productOrId,
        onConfirm = null,
        isSelected = false
    ) => {
        if (!productOrId) return;

        setModalState({
            show: true,
            product: null,
            loading: true,
            onConfirm,
            isSelected,
        });

        try {
            let productData = null;
            if (typeof productOrId === "object") {
                productData = productOrId;
            } else {
                // Fetch from API
                const res = await fetch(
                    `/configurator/product-details/${productOrId}`
                );
                if (res.ok) {
                    productData = await res.json();
                }
            }
            setModalState((prev) => ({
                ...prev,
                loading: false,
                product: productData,
            }));
        } catch (error) {
            console.error("Failed to load product details", error);
            setModalState((prev) => ({ ...prev, loading: false }));
        }
    };

    const closeModal = () => {
        setModalState((prev) => ({ ...prev, show: false }));
    };

    // Flatten steps to manage navigation easier if we need to skip steps
    const steps = configurator.steps || [];
    const currentStep = steps[currentStepIndex];

    // Helper: Check conditions
    // Helper: Check conditions (Supports ==, !=, in, not_in)
    const checkCondition = (conditions) => {
        if (!conditions || Object.keys(conditions).length === 0) return true;

        for (const [key, requirement] of Object.entries(conditions)) {
            const userValue = selection[key];

            // 1. Simple Equality check: { "platform": "zoom" }
            if (
                typeof requirement !== "object" ||
                requirement === null ||
                Array.isArray(requirement)
            ) {
                if (userValue !== requirement) return false;
            }
            // 2. Complex check: { "platform": { "operator": "!=", "value": "byod" } }
            else if (requirement.operator && requirement.value !== undefined) {
                switch (requirement.operator) {
                    case "==":
                        if (userValue !== requirement.value) return false;
                        break;
                    case "!=":
                        if (userValue === requirement.value) return false;
                        break;
                    case "in": // Value should be an array
                        if (
                            !Array.isArray(requirement.value) ||
                            !requirement.value.includes(userValue)
                        )
                            return false;
                        break;
                    case "not_in":
                        if (
                            Array.isArray(requirement.value) &&
                            requirement.value.includes(userValue)
                        )
                            return false;
                        break;
                    default:
                        // Fallback to strict equality if operator unknown
                        if (userValue !== requirement.value) return false;
                }
            }
        }
        return true;
    };

    // Filter visible questions for current step
    const visibleQuestions = useMemo(() => {
        if (!currentStep) return [];
        return (currentStep.questions || []).filter((q) =>
            checkCondition(q.conditions)
        );
    }, [currentStep, selection]);

    // Handlers
    const handleSelection = (variableName, value) => {
        setSelection((prev) => ({ ...prev, [variableName]: value }));
    };

    const handleToggle = (variableName, value) => {
        setSelection((prev) => {
            const list = prev[variableName] || [];
            const exists = list.includes(value);
            const newList = exists
                ? list.filter((i) => i !== value)
                : [...list, value];
            return { ...prev, [variableName]: newList };
        });
    };

    const handleQuantity = (id, value) => {
        const qty = parseInt(value);
        if (!isNaN(qty) && qty > 0) {
            setSelection((prev) => ({
                ...prev,
                quantities: { ...prev.quantities, [id]: qty },
            }));
        }
    };

    const nextStep = () => {
        // Validation: Check mandatory visible questions
        for (const q of visibleQuestions) {
            if (q.is_mandatory && !selection[q.variable_name]) {
                const isListEmpty =
                    Array.isArray(selection[q.variable_name]) &&
                    selection[q.variable_name].length === 0;
                if (!selection[q.variable_name] || isListEmpty) {
                    setToastMessage(`Please complete: ${q.label}`);
                    setShowToast(true);
                    return;
                }
            }
        }

        // Find next step that meets conditions
        let nextIndex = currentStepIndex + 1;
        while (nextIndex < steps.length) {
            if (checkCondition(steps[nextIndex].conditions)) {
                setCurrentStepIndex(nextIndex);
                return;
            }
            nextIndex++;
        }

        // If no more steps, show submit
        setShowSubmitModal(true);
    };

    const prevStep = () => {
        let prevIndex = currentStepIndex - 1;
        while (prevIndex >= 0) {
            if (checkCondition(steps[prevIndex].conditions)) {
                setCurrentStepIndex(prevIndex);
                return;
            }
            prevIndex--;
        }
    };

    const renderQuantityControl = (id) => {
        const qty = selection.quantities?.[id] || 1;
        return (
            <div
                className="d-flex align-items-center bg-light rounded p-1 border"
                onClick={(e) => e.stopPropagation()}
                style={{ width: "fit-content" }}
            >
                <button
                    className="btn btn-sm btn-link text-decoration-none p-0 px-2 text-dark fw-bold hover-scale"
                    onClick={() => handleQuantity(id, qty - 1)}
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
                    onClick={() => handleQuantity(id, qty + 1)}
                >
                    <i
                        className="fa-solid fa-plus"
                        style={{ fontSize: "10px" }}
                    ></i>
                </button>
            </div>
        );
    };

    // Render Logic for different Question Types
    const renderQuestion = (q, index, allQuestions) => {
        // Filter options based on conditions
        const visibleOptions = (q.options || []).filter((opt) =>
            checkCondition(opt.conditions)
        );

        // --- Service Checklist Mode (Accordion Wizard) ---
        if (q.type === "service_checklist") {
            const isAnswered = !!selection[q.variable_name];
            // Logic: Visible if it's the first question OR previous question is answered.
            const isFirst = index === 0;
            const prevQ = index > 0 ? allQuestions[index - 1] : null;
            const isPrevAnswered = prevQ
                ? !!selection[prevQ.variable_name]
                : true;

            if (!isFirst && !isPrevAnswered) return null; // Hide if previous not answered

            // If answered, render Collapsed
            if (isAnswered) {
                return (
                    <div
                        className="d-flex align-items-center bg-white border rounded-3 p-3 mb-3 shadow-sm animation-fadeIn cursor-pointer"
                        key={q.id}
                        style={{ borderLeft: `4px solid ${themeColor}` }}
                        onClick={() => handleSelection(q.variable_name, null)} // Click to re-edit (clear value)
                    >
                        <div className="me-3 text-success">
                            <i className="fa-solid fa-circle-check fa-lg"></i>
                        </div>
                        <div className="grow">
                            <small
                                className="text-muted d-block text-uppercase fw-bold"
                                style={{ fontSize: "10px" }}
                            >
                                Question {index + 1}
                            </small>
                            <div className="fw-bold text-dark mb-0">
                                {q.label}
                            </div>
                            <div className="text-muted small mt-1">
                                <span className="fw-semibold text-dark">
                                    Selected:
                                </span>{" "}
                                {visibleOptions.find(
                                    (o) =>
                                        o.value === selection[q.variable_name]
                                )?.label || selection[q.variable_name]}
                            </div>
                        </div>
                        <div className="ms-3 text-muted">
                            <i className="fa-solid fa-pen-to-square"></i>
                        </div>
                    </div>
                );
            }

            // Expanded View
            return (
                <div
                    className="card border-0 shadow-sm rounded-4 mb-4 animation-fadeIn"
                    key={q.id}
                    style={{ borderLeft: `4px solid ${themeColor}` }}
                >
                    <div className="card-body p-4">
                        <h6 className="fw-bold mb-3">
                            {index + 1}. {q.label}
                        </h6>
                        <div className="row g-3">
                            {visibleOptions.map((opt) => (
                                <div className="col-md-6" key={opt.id}>
                                    <div
                                        className="p-3 border rounded-3 cursor-pointer d-flex align-items-center justify-content-between transition-all hover-scale"
                                        style={{
                                            borderColor: "#E5E7EB",
                                            backgroundColor: "#fff",
                                        }}
                                        onClick={() =>
                                            handleSelection(
                                                q.variable_name,
                                                opt.value
                                            )
                                        }
                                    >
                                        <span className="small fw-medium">
                                            {opt.label}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            );
        }

        // --- Card Multi Selection (Add-ons) ---
        if (q.type === "card_multi_selection") {
            return (
                <div key={q.id} className="mb-5 animation-fadeIn">
                    <h5 className="mb-4 text-center fw-bold">{q.label}</h5>
                    <div className="row g-4 justify-content-center">
                        {visibleOptions.map((opt) => {
                            const currentList =
                                selection[q.variable_name] || [];
                            const isSelected = currentList.includes(opt.value);
                            const meta = opt.metadata || {};
                            // Smart Fetch Logic
                            const displayImage =
                                opt.service_solution?.thumbnail || meta.image;
                            const displayLabel =
                                opt.service_solution?.title || opt.label;
                            const displayDesc =
                                opt.service_solution?.subtitle ||
                                meta.description;

                            return (
                                <div className="col-md-4 col-lg-3" key={opt.id}>
                                    <div
                                        className="selection-card p-4 rounded-4 text-center h-100 position-relative transition-all hover-scale"
                                        style={{
                                            border: "2px solid",
                                            ...(isSelected
                                                ? activeStyle
                                                : defaultStyle),
                                            cursor: "pointer",
                                        }}
                                        onClick={() =>
                                            handleToggle(
                                                q.variable_name,
                                                opt.value
                                            )
                                        }
                                    >
                                        {isSelected && (
                                            <div
                                                className="position-absolute top-0 end-0 m-3"
                                                style={{ color: themeColor }}
                                            >
                                                <i className="fa-solid fa-circle-check fa-xl"></i>
                                            </div>
                                        )}
                                        {meta.recommended && (
                                            <div className="position-absolute top-0 start-0 m-3 badge rounded-pill bg-info p-1 px-2 text-dark shadow-sm">
                                                Recommended
                                            </div>
                                        )}

                                        {displayImage ? (
                                            <div
                                                className="mb-3 d-flex align-items-center justify-content-center bg-white rounded-3 mx-auto shadow-sm"
                                                style={{
                                                    width:
                                                        meta.image_width ||
                                                        "80px",
                                                    height:
                                                        meta.image_height ||
                                                        "80px",
                                                }}
                                            >
                                                <img
                                                    src={displayImage}
                                                    alt={displayLabel}
                                                    className="img-fluid"
                                                    style={{ maxHeight: "60%" }}
                                                />
                                            </div>
                                        ) : (
                                            <div
                                                className="mb-3 d-flex align-items-center justify-content-center bg-light rounded-3 mx-auto"
                                                style={{
                                                    width: "60px",
                                                    height: "60px",
                                                }}
                                            >
                                                <i className="fa-solid fa-puzzle-piece fa-lg text-muted"></i>
                                            </div>
                                        )}

                                        <h6 className="mb-1 fw-bold text-dark">
                                            {displayLabel}
                                        </h6>
                                        {displayDesc && (
                                            <small className="text-muted d-block">
                                                {displayDesc}
                                            </small>
                                        )}

                                        {/* Generic Attributes Renderer */}
                                        {meta.attributes &&
                                            meta.attributes.length > 0 && (
                                                <div className="mt-2 text-start">
                                                    {meta.attributes.map(
                                                        (attr, attrIdx) => {
                                                            switch (attr.type) {
                                                                case "badge":
                                                                    return (
                                                                        <span
                                                                            key={
                                                                                attrIdx
                                                                            }
                                                                            className={`badge bg-${
                                                                                attr.color ||
                                                                                "primary"
                                                                            } me-1 mb-1`}
                                                                        >
                                                                            {
                                                                                attr.text
                                                                            }
                                                                        </span>
                                                                    );
                                                                case "icon_text":
                                                                    return (
                                                                        <div
                                                                            key={
                                                                                attrIdx
                                                                            }
                                                                            className={`small ${
                                                                                attr.color
                                                                                    ? "text-" +
                                                                                      attr.color
                                                                                    : "text-muted"
                                                                            } mb-1`}
                                                                        >
                                                                            {attr.icon && (
                                                                                <i
                                                                                    className={`fa-solid ${attr.icon} me-2`}
                                                                                ></i>
                                                                            )}
                                                                            {
                                                                                attr.text
                                                                            }
                                                                        </div>
                                                                    );
                                                                case "list":
                                                                    return (
                                                                        <ul
                                                                            key={
                                                                                attrIdx
                                                                            }
                                                                            className="list-unstyled small text-muted mb-2"
                                                                        >
                                                                            {(
                                                                                attr.items ||
                                                                                []
                                                                            ).map(
                                                                                (
                                                                                    item,
                                                                                    i
                                                                                ) => (
                                                                                    <li
                                                                                        key={
                                                                                            i
                                                                                        }
                                                                                        className="mb-1"
                                                                                    >
                                                                                        <i
                                                                                            className="fa-solid fa-check text-success me-2"
                                                                                            style={{
                                                                                                fontSize:
                                                                                                    "10px",
                                                                                            }}
                                                                                        ></i>
                                                                                        {
                                                                                            item
                                                                                        }
                                                                                    </li>
                                                                                )
                                                                            )}
                                                                        </ul>
                                                                    );
                                                                case "link":
                                                                    return (
                                                                        <a
                                                                            key={
                                                                                attrIdx
                                                                            }
                                                                            href={
                                                                                attr.url
                                                                            }
                                                                            target="_blank"
                                                                            rel="noopener noreferrer"
                                                                            className={`btn btn-sm ${
                                                                                attr.style ===
                                                                                "button"
                                                                                    ? "btn-outline-primary w-100 mt-2"
                                                                                    : "text-primary text-decoration-none"
                                                                            } mb-1`}
                                                                            onClick={(
                                                                                e
                                                                            ) =>
                                                                                e.stopPropagation()
                                                                            }
                                                                        >
                                                                            {attr.icon && (
                                                                                <i
                                                                                    className={`fa-solid ${attr.icon} me-2`}
                                                                                ></i>
                                                                            )}
                                                                            {
                                                                                attr.text
                                                                            }
                                                                        </a>
                                                                    );
                                                                case "separator":
                                                                    return (
                                                                        <hr
                                                                            key={
                                                                                attrIdx
                                                                            }
                                                                            className="my-2 border-secondary-subtle"
                                                                        />
                                                                    );
                                                                case "image":
                                                                    return (
                                                                        <div
                                                                            key={
                                                                                attrIdx
                                                                            }
                                                                            className="my-2"
                                                                        >
                                                                            <img
                                                                                src={
                                                                                    attr.url
                                                                                }
                                                                                alt={
                                                                                    attr.alt ||
                                                                                    ""
                                                                                }
                                                                                className="img-fluid rounded border"
                                                                            />
                                                                        </div>
                                                                    );
                                                                default:
                                                                    return null;
                                                            }
                                                        }
                                                    )}
                                                </div>
                                            )}

                                        {/* Optional: Quantity Control if selected */}
                                        {/* We can reuse renderQuantityControl if we enable it for this type */}
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
                                                    opt.value
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            );
        }

        if (q.type === "card_selection" || q.type === "image_grid") {
            return (
                <div key={q.id} className="mb-5 animation-fadeIn">
                    <h5 className="mb-4 text-center fw-bold">{q.label}</h5>
                    <div className="row g-4 justify-content-center">
                        {visibleOptions.map((opt) => {
                            const isSelected =
                                selection[q.variable_name] === opt.value ||
                                (Array.isArray(selection[q.variable_name]) &&
                                    selection[q.variable_name].includes(
                                        opt.value
                                    ));

                            const meta = opt.metadata || {};

                            // Smart Fetch Logic
                            const displayImage =
                                opt.service_solution?.thumbnail || meta.image;
                            const displayLabel =
                                opt.service_solution?.title || opt.label;
                            const displayDesc =
                                opt.service_solution?.subtitle ||
                                meta.description;

                            return (
                                <div className="col-md-4 col-lg-3" key={opt.id}>
                                    <div
                                        className="selection-card p-4 rounded-4 h-100 position-relative transition-all hover-scale"
                                        style={{
                                            border: "2px solid",
                                            borderColor: isSelected
                                                ? themeColor
                                                : "#E5E7EB",
                                            backgroundColor: "#fff", // Keep white bg for premium feel
                                            cursor: "pointer",
                                            overflow: "visible", // Allow badge to hang out
                                        }}
                                        onClick={() =>
                                            handleSelection(
                                                q.variable_name,
                                                opt.value
                                            )
                                        }
                                    >
                                        {/* Floating Checkmark Badge */}
                                        {isSelected && (
                                            <div
                                                className="position-absolute shadow-sm d-flex align-items-center justify-content-center text-white"
                                                style={{
                                                    top: "-12px",
                                                    right: "-12px",
                                                    width: "32px",
                                                    height: "32px",
                                                    borderRadius: "50%",
                                                    backgroundColor: themeColor,
                                                    fontSize: "14px",
                                                    zIndex: 10,
                                                }}
                                            >
                                                <i className="fa-solid fa-check"></i>
                                            </div>
                                        )}

                                        <div className="d-flex align-items-start text-start h-100 flex-column">
                                            {/* Main Content: Row Layout (Image Left - Text Right) for wider cards, or Stacked for smaller */}
                                            <div className="d-flex w-100 mb-3 align-items-center">
                                                {displayImage && (
                                                    <div
                                                        className="shrink-0 me-3 bg-light rounded p-2 d-flex align-items-center justify-content-center"
                                                        style={{
                                                            width: "80px",
                                                            height: "80px",
                                                        }}
                                                    >
                                                        <img
                                                            src={displayImage}
                                                            alt={displayLabel}
                                                            className="img-fluid"
                                                            style={{
                                                                maxHeight:
                                                                    "100%",
                                                            }}
                                                        />
                                                    </div>
                                                )}
                                                <div>
                                                    <h6
                                                        className="fw-bold mb-1"
                                                        style={{
                                                            color: isSelected
                                                                ? themeColor
                                                                : "inherit",
                                                        }}
                                                    >
                                                        {displayLabel}
                                                    </h6>
                                                    {displayDesc && (
                                                        <p
                                                            className="text-muted small mb-0 lh-sm"
                                                            style={{
                                                                fontSize:
                                                                    "13px",
                                                            }}
                                                        >
                                                            {displayDesc}
                                                        </p>
                                                    )}
                                                    {/* Price display removed */}
                                                </div>

                                                {/* Info Icon (Bottom Right of top section) */}
                                                {opt.products &&
                                                    opt.products.length > 0 && (
                                                        <div
                                                            className="ms-auto align-self-end text-info"
                                                            style={{
                                                                cursor: "pointer",
                                                            }}
                                                            onClick={(e) => {
                                                                e.stopPropagation();
                                                                openModal(
                                                                    opt
                                                                        .products[0],
                                                                    () =>
                                                                        handleSelection(
                                                                            q.variable_name,
                                                                            opt.value
                                                                        ),
                                                                    selection[
                                                                        q
                                                                            .variable_name
                                                                    ] ===
                                                                        opt.value
                                                                );
                                                            }}
                                                        >
                                                            <i className="fa-solid fa-circle-info fa-lg"></i>
                                                        </div>
                                                    )}
                                            </div>
                                            {meta.recommended && (
                                                <div className="position-absolute top-0 start-0 m-3 badge rounded-pill bg-info p-1 px-2 text-dark shadow-sm">
                                                    Recommended
                                                </div>
                                            )}

                                            {/* Generic Attributes Renderer */}
                                            {meta.attributes &&
                                                meta.attributes.length > 0 && (
                                                    <div className="mt-2 text-start">
                                                        {meta.attributes.map(
                                                            (attr, attrIdx) => {
                                                                switch (
                                                                    attr.type
                                                                ) {
                                                                    case "badge":
                                                                        return (
                                                                            <span
                                                                                key={
                                                                                    attrIdx
                                                                                }
                                                                                className={`badge rounded-pill bg-${
                                                                                    attr.color ||
                                                                                    "primary"
                                                                                } me-1 mb-1`}
                                                                            >
                                                                                {
                                                                                    attr.text
                                                                                }
                                                                            </span>
                                                                        );
                                                                    case "icon_text":
                                                                        return (
                                                                            <div
                                                                                key={
                                                                                    attrIdx
                                                                                }
                                                                                className={`small ${
                                                                                    attr.color
                                                                                        ? "text-" +
                                                                                          attr.color
                                                                                        : "text-muted"
                                                                                } mb-1`}
                                                                            >
                                                                                {attr.icon && (
                                                                                    <i
                                                                                        className={`fa-solid ${attr.icon} me-2`}
                                                                                    ></i>
                                                                                )}
                                                                                {
                                                                                    attr.text
                                                                                }
                                                                            </div>
                                                                        );
                                                                    case "list":
                                                                        return (
                                                                            <ul
                                                                                key={
                                                                                    attrIdx
                                                                                }
                                                                                className="list-unstyled small text-muted mb-2"
                                                                            >
                                                                                {(
                                                                                    attr.items ||
                                                                                    []
                                                                                ).map(
                                                                                    (
                                                                                        item,
                                                                                        i
                                                                                    ) => (
                                                                                        <li
                                                                                            key={
                                                                                                i
                                                                                            }
                                                                                            className="mb-1"
                                                                                        >
                                                                                            <i
                                                                                                className="fa-solid fa-check text-success me-2"
                                                                                                style={{
                                                                                                    fontSize:
                                                                                                        "10px",
                                                                                                }}
                                                                                            ></i>
                                                                                            {
                                                                                                item
                                                                                            }
                                                                                        </li>
                                                                                    )
                                                                                )}
                                                                            </ul>
                                                                        );
                                                                    case "link":
                                                                        return (
                                                                            <a
                                                                                key={
                                                                                    attrIdx
                                                                                }
                                                                                href={
                                                                                    attr.url
                                                                                }
                                                                                target="_blank"
                                                                                rel="noopener noreferrer"
                                                                                className={`btn btn-sm ${
                                                                                    attr.style ===
                                                                                    "button"
                                                                                        ? "btn-outline-primary w-100 mt-2"
                                                                                        : "text-primary text-decoration-none"
                                                                                } mb-1`}
                                                                                onClick={(
                                                                                    e
                                                                                ) =>
                                                                                    e.stopPropagation()
                                                                                }
                                                                            >
                                                                                {attr.icon && (
                                                                                    <i
                                                                                        className={`fa-solid ${attr.icon} me-2`}
                                                                                    ></i>
                                                                                )}
                                                                                {
                                                                                    attr.text
                                                                                }
                                                                            </a>
                                                                        );
                                                                    case "separator":
                                                                        return (
                                                                            <hr
                                                                                key={
                                                                                    attrIdx
                                                                                }
                                                                                className="my-2 border-secondary-subtle"
                                                                            />
                                                                        );
                                                                    case "image":
                                                                        return (
                                                                            <div
                                                                                key={
                                                                                    attrIdx
                                                                                }
                                                                                className="my-2"
                                                                            >
                                                                                <img
                                                                                    src={
                                                                                        attr.url
                                                                                    }
                                                                                    alt={
                                                                                        attr.alt ||
                                                                                        ""
                                                                                    }
                                                                                    className="img-fluid rounded border"
                                                                                />
                                                                            </div>
                                                                        );
                                                                    default:
                                                                        return null;
                                                                }
                                                            }
                                                        )}
                                                    </div>
                                                )}

                                            {/* Nested Accessories (if selected) */}
                                            {isSelected &&
                                                meta.accessories &&
                                                meta.accessories.length > 0 && (
                                                    <div className="mt-3 text-start border-top border-dashed pt-3 w-100">
                                                        <h6 className="small fw-bold mb-2 text-muted">
                                                            Add Accessories:
                                                        </h6>
                                                        {meta.accessories.map(
                                                            (acc) => {
                                                                const isAccSelected =
                                                                    (
                                                                        selection.accessories ||
                                                                        []
                                                                    ).includes(
                                                                        acc.id
                                                                    );
                                                                return (
                                                                    <div
                                                                        key={
                                                                            acc.id
                                                                        }
                                                                        className="mb-3"
                                                                    >
                                                                        <div
                                                                            className="position-relative p-3 rounded-4 bg-white transition-all"
                                                                            style={{
                                                                                border: "2px solid",
                                                                                borderColor:
                                                                                    isAccSelected
                                                                                        ? themeColor
                                                                                        : "#E5E7EB",
                                                                                cursor: "pointer",
                                                                            }}
                                                                            onClick={(
                                                                                e
                                                                            ) => {
                                                                                e.stopPropagation();
                                                                                handleToggle(
                                                                                    "accessories",
                                                                                    acc.id
                                                                                );
                                                                            }}
                                                                        >
                                                                            {/* Floating Checkmark Badge */}
                                                                            {isAccSelected && (
                                                                                <div
                                                                                    className="position-absolute shadow-sm d-flex align-items-center justify-content-center text-white"
                                                                                    style={{
                                                                                        top: "-10px",
                                                                                        right: "-10px",
                                                                                        width: "24px",
                                                                                        height: "24px",
                                                                                        borderRadius:
                                                                                            "50%",
                                                                                        backgroundColor:
                                                                                            themeColor,
                                                                                        fontSize:
                                                                                            "10px",
                                                                                        zIndex: 5,
                                                                                    }}
                                                                                >
                                                                                    <i className="fa-solid fa-check"></i>
                                                                                </div>
                                                                            )}

                                                                            <div className="d-flex align-items-center text-start">
                                                                                {/* Accessory Image (if available or placeholder) */}
                                                                                <div
                                                                                    className="shrink-0 me-3 d-flex align-items-center justify-content-center"
                                                                                    style={{
                                                                                        width: "60px",
                                                                                        height: "60px",
                                                                                    }}
                                                                                >
                                                                                    {acc.image ? (
                                                                                        <img
                                                                                            src={
                                                                                                acc.image
                                                                                            }
                                                                                            alt={
                                                                                                acc.name
                                                                                            }
                                                                                            className="img-fluid"
                                                                                            style={{
                                                                                                maxHeight:
                                                                                                    "100%",
                                                                                            }}
                                                                                        />
                                                                                    ) : (
                                                                                        <i className="fa-solid fa-box-open text-muted opacity-25 fa-2x"></i>
                                                                                    )}
                                                                                </div>

                                                                                <div className="grow">
                                                                                    <div
                                                                                        className="small fw-bold lh-sm mb-1"
                                                                                        style={{
                                                                                            color: isAccSelected
                                                                                                ? themeColor
                                                                                                : "inherit",
                                                                                        }}
                                                                                    >
                                                                                        {
                                                                                            acc.name
                                                                                        }
                                                                                    </div>
                                                                                    {acc.description && (
                                                                                        <div
                                                                                            className="text-muted small lh-1"
                                                                                            style={{
                                                                                                fontSize:
                                                                                                    "11px",
                                                                                            }}
                                                                                        >
                                                                                            {
                                                                                                acc.description
                                                                                            }
                                                                                        </div>
                                                                                    )}
                                                                                    {/* Price removed */}
                                                                                </div>

                                                                                {acc.product_id && (
                                                                                    <div
                                                                                        className="ms-2 align-self-end text-info"
                                                                                        style={{
                                                                                            cursor: "pointer",
                                                                                        }}
                                                                                        onClick={(
                                                                                            e
                                                                                        ) => {
                                                                                            e.stopPropagation();
                                                                                            openModal(
                                                                                                acc.product_id
                                                                                            );
                                                                                        }}
                                                                                    >
                                                                                        <i className="fa-solid fa-circle-info"></i>
                                                                                    </div>
                                                                                )}
                                                                            </div>
                                                                        </div>

                                                                        {isAccSelected && (
                                                                            <div className="d-flex justify-content-end mt-2">
                                                                                {renderQuantityControl(
                                                                                    acc.id
                                                                                )}
                                                                            </div>
                                                                        )}
                                                                    </div>
                                                                );
                                                            }
                                                        )}
                                                    </div>
                                                )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            );
        }

        // Default: List/Select
        return (
            <div key={q.id} className="mb-4">
                <label className="form-label fw-bold">{q.label}</label>
                <select
                    className="form-select"
                    value={selection[q.variable_name] || ""}
                    onChange={(e) =>
                        handleSelection(q.variable_name, e.target.value)
                    }
                >
                    <option value="">Select...</option>
                    {visibleOptions.map((opt) => (
                        <option key={opt.id} value={opt.value}>
                            {opt.label}
                        </option>
                    ))}
                </select>
            </div>
        );
    };

    // --- Styles ---
    const activeStyle = { borderColor: themeColor, backgroundColor: "#ECFDF5" };
    const defaultStyle = { borderColor: "#E5E7EB", backgroundColor: "#fff" };

    return (
        <MainLayout>
            <ProductDetailModal
                show={modalState.show}
                product={modalState.product}
                loading={modalState.loading}
                onClose={closeModal}
                onAdd={modalState.onConfirm}
                isSelected={modalState.isSelected}
                themeColor={themeColor}
            />
            <Head title={configurator.name} />
            <Breadcrumb
                title={configurator.name}
                items={[
                    { label: "Home", link: "/" },
                    { label: configurator.name },
                ]}
            />
            <Toast
                show={showToast}
                onClose={() => setShowToast(false)}
                message={toastMessage}
            />

            <section className="space-top space-bottom bg-light">
                <div className="container">
                    {/* Header */}
                    <div className="row justify-content-center mb-5">
                        <div className="col-lg-8 text-center">
                            <span className="sub-title text-success">
                                Configurator
                            </span>
                            <h2 className="sec-title">{configurator.name}</h2>
                            <p className="text-muted">
                                {configurator.description}
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
                                        width: `${
                                            (currentStepIndex /
                                                (steps.length - 1)) *
                                            100
                                        }%`,
                                        height: "2px",
                                        background: themeColor,
                                        zIndex: 0,
                                        transition: "width 0.3s ease",
                                    }}
                                ></div>
                                {steps.map((s, idx) => (
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
                                                    idx <= currentStepIndex
                                                        ? themeColor
                                                        : "#fff",
                                                color:
                                                    idx <= currentStepIndex
                                                        ? "#fff"
                                                        : "#9CA3AF",
                                                border: `2px solid ${
                                                    idx <= currentStepIndex
                                                        ? themeColor
                                                        : "#E5E7EB"
                                                }`,
                                                transition: "all 0.3s",
                                            }}
                                        >
                                            {idx < currentStepIndex ? (
                                                <i className="fa-solid fa-check"></i>
                                            ) : (
                                                idx + 1
                                            )}
                                        </div>
                                        <small
                                            className={`d-none d-md-block fw-bold pt-3 ${
                                                idx <= currentStepIndex
                                                    ? "text-dark"
                                                    : "text-muted"
                                            }`}
                                            style={{ fontSize: "12px" }}
                                        >
                                            {s.name}
                                        </small>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Content */}
                        <div
                            className="step-content py-4"
                            style={{ minHeight: "400px" }}
                        >
                            {currentStep ? (
                                <div className="animation-fadeIn">
                                    {currentStep.title && (
                                        <h4 className="mb-2 text-center fw-bold">
                                            {currentStep.title}
                                        </h4>
                                    )}
                                    {currentStep.description && (
                                        <p className="text-center text-muted mb-5">
                                            {currentStep.description}
                                        </p>
                                    )}

                                    {visibleQuestions.map((q, idx) =>
                                        renderQuestion(q, idx, visibleQuestions)
                                    )}
                                </div>
                            ) : (
                                <div className="text-center py-5">
                                    <h4 className="fw-bold">
                                        Configuration Complete!
                                    </h4>
                                    <button
                                        className="th-btn th-radius shadow-none style8 mt-3"
                                        onClick={() => setShowSubmitModal(true)}
                                    >
                                        Finalize Request
                                    </button>
                                </div>
                            )}
                        </div>

                        {/* Footer */}
                        <div className="d-flex justify-content-between mt-5 pt-4 border-top">
                            {currentStepIndex > 0 ? (
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

                            <button
                                className={`th-btn th-radius shadow-none ${
                                    currentStepIndex === steps.length - 1
                                        ? "style8"
                                        : ""
                                }`}
                                onClick={
                                    currentStepIndex === steps.length - 1
                                        ? () => setShowSubmitModal(true)
                                        : nextStep
                                }
                                style={
                                    currentStepIndex !== steps.length - 1
                                        ? {
                                              backgroundColor: themeColor,
                                              borderColor: themeColor,
                                          }
                                        : {}
                                }
                            >
                                {currentStepIndex === steps.length - 1
                                    ? "Finish"
                                    : "Next"}{" "}
                                {currentStepIndex === steps.length - 1 ? (
                                    <i className="fa-solid fa-check ms-2"></i>
                                ) : (
                                    <i className="fa-solid fa-arrow-right ms-2"></i>
                                )}
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            {/* Finalization Modal */}
            <ConfiguratorSubmitModal
                show={showSubmitModal}
                onClose={() => setShowSubmitModal(false)}
                userInfo={userInfo}
                onChange={handleUserInfoChange}
                onSubmit={handleSubmitConfiguration}
            />
        </MainLayout>
    );
}

// --- Sub Components ---

const ProductDetailModal = ({
    show,
    product,
    loading,
    onClose,
    themeColor,
    onAdd,
    isSelected,
}) => {
    // Add state for tabs
    const [activeTab, setActiveTab] = useState("description");
    // Lock body scroll when modal is open
    useEffect(() => {
        if (show) {
            document.body.style.overflow = "hidden";
            document.documentElement.style.overflow = "hidden";
        } else {
            document.body.style.overflow = "unset";
            document.documentElement.style.overflow = "unset";
        }
        return () => {
            document.body.style.overflow = "unset";
            document.documentElement.style.overflow = "unset";
        };
    }, [show]);

    if (!show) return null;

    // Loading State
    if (loading) {
        return createPortal(
            <div
                className="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                style={{
                    zIndex: 9999,
                    backgroundColor: "rgba(0,0,0,0.5)",
                    backdropFilter: "blur(2px)",
                }}
            >
                <div className="spinner-border text-white" role="status"></div>
            </div>,
            document.body
        );
    }

    if (!product) return null;

    // Standardize data for view (handle potential differences in naming vs Seeder)
    const features = product.features || [];
    const specs = product.specs || []; // Assumes available in product object

    return createPortal(
        <div
            className="position-fixed top-0 start-0 w-100 h-100 animation-fadeIn custom-scrollbar"
            style={{
                zIndex: 9999,
                backgroundColor: "rgba(0,0,0,0.5)",
                backdropFilter: "blur(5px)",
                overflowY: "auto",
            }}
            onClick={onClose}
        >
            <div className="d-flex min-vh-100 p-3 pt-5 pb-5">
                <div
                    className="bg-white rounded-4 shadow-lg w-100 position-relative m-auto"
                    style={{
                        maxWidth: "1000px",
                        // Height grows with content
                    }}
                    onClick={(e) => e.stopPropagation()}
                >
                    {/* Header / Close */}
                    <div
                        className="d-flex justify-content-end p-3 position-absolute top-0 end-0"
                        style={{ zIndex: 10 }}
                    >
                        <button
                            className="btn btn-light rounded-circle shadow-sm"
                            onClick={onClose}
                            style={{ width: "40px", height: "40px" }}
                        >
                            <i className="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    {/* Custom Scrollbar Style */}
                    <style>
                        {`
                        .custom-scrollbar::-webkit-scrollbar {
                            width: 10px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-track {
                            background: rgba(0,0,0,0.1);
                        }
                        .custom-scrollbar::-webkit-scrollbar-thumb {
                            background: rgba(0,0,0,0.4);
                            border-radius: 5px;
                        }
                        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                            background: rgba(0,0,0,0.6);
                        }
                        `}
                    </style>

                    {/* Content */}
                    <div className="p-4 p-lg-5">
                        {/* Top Section: Image + Info (Mimicking ProductDetail.jsx) */}
                        <div className="row">
                            {/* Left Column: Image */}
                            <div className="col-md-5 mb-4">
                                <div className="th-team team-grid">
                                    <div className="team-img m-auto text-center border rounded-3 p-3">
                                        <img
                                            src={
                                                product.image_path ||
                                                product.image ||
                                                "/assets/img/product/product_1_1.png"
                                            }
                                            alt={product.name}
                                            className="img-fluid"
                                            style={{
                                                maxHeight: "300px",
                                                objectFit: "contain",
                                            }}
                                        />
                                    </div>
                                </div>
                            </div>

                            {/* Right Column: Info */}
                            <div className="col-md-7">
                                <div className="team-right ms-xxl-3">
                                    <h3 className="box-title fw-bold mb-2">
                                        {product.name}
                                    </h3>
                                    <div
                                        className="text-muted small mb-3"
                                        dangerouslySetInnerHTML={{
                                            __html: product.description,
                                        }}
                                    />

                                    {/* SKU / Solution Type / Brand / Datasheet Grid */}
                                    <div className="team-infobox mb-4">
                                        <div className="row gx-0">
                                            {/* SKU */}
                                            <div className="col-xl-6 col-lg-6 col-md-6">
                                                <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                                    <span className="team-info-icon">
                                                        <i className="fa-solid fa-barcode"></i>
                                                    </span>
                                                    <div className="team-info-content">
                                                        <span className="team-info-subtitle">
                                                            SKU:
                                                        </span>
                                                        <h4 className="team-info-title">
                                                            {product.sku}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Solution Type */}
                                            <div className="col-xl-6 col-lg-6 col-md-6">
                                                <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                                    <span className="team-info-icon">
                                                        <i className="fa-solid fa-fire"></i>
                                                    </span>
                                                    <div className="team-info-content">
                                                        <span className="team-info-subtitle">
                                                            Solution Type
                                                        </span>
                                                        <h4 className="team-info-title">
                                                            <a href="#">
                                                                {
                                                                    product.solution_type
                                                                }
                                                            </a>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Brand */}
                                            <div className="col-xl-6 col-lg-6 col-md-6">
                                                <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                                    <span className="team-info-icon">
                                                        <i className="fa-solid fa-copyright"></i>
                                                    </span>
                                                    <div className="team-info-content">
                                                        <span className="team-info-subtitle">
                                                            Brand
                                                        </span>
                                                        <h4 className="team-info-title">
                                                            <a href="#">
                                                                {product.brand
                                                                    ?.name ||
                                                                    product.specification?.find(
                                                                        (s) =>
                                                                            s.name ===
                                                                            "Brand"
                                                                    )?.value ||
                                                                    "Logitech"}
                                                            </a>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Datasheet */}
                                            <div className="col-xl-6 col-lg-6 col-md-6">
                                                <div className="team-info-item d-sm-flex align-items-center text-center text-sm-start">
                                                    <span className="team-info-icon">
                                                        <i className="fa-solid fa-download"></i>
                                                    </span>
                                                    <div className="team-info-content">
                                                        <span className="team-info-subtitle">
                                                            Datasheet
                                                        </span>
                                                        <h4 className="team-info-title">
                                                            {product.datasheet_url ? (
                                                                <a
                                                                    href={
                                                                        product.datasheet_url
                                                                    }
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                >
                                                                    See
                                                                    datasheet
                                                                </a>
                                                            ) : (
                                                                <span className="text-muted">
                                                                    Not
                                                                    available
                                                                </span>
                                                            )}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Product Meta (Category & Tags) */}
                                    <div className="product_meta mb-4">
                                        {product.service?.name && (
                                            <div className="mb-2">
                                                <span className="text-muted small me-2">
                                                    Category:
                                                </span>
                                                <span
                                                    className="fw-bold"
                                                    style={{
                                                        color: themeColor,
                                                    }}
                                                >
                                                    {product.service.name}
                                                </span>
                                            </div>
                                        )}
                                        {product.tags &&
                                            product.tags.length > 0 && (
                                                <div>
                                                    <span className="text-muted small me-2">
                                                        Tags:
                                                    </span>
                                                    {product.tags.map(
                                                        (tag, index) => (
                                                            <span
                                                                key={index}
                                                                className="badge bg-light text-dark me-1 border fw-normal"
                                                            >
                                                                {tag}
                                                            </span>
                                                        )
                                                    )}
                                                </div>
                                            )}
                                    </div>

                                    {/* Custom Add Button Area */}
                                    {onAdd && (
                                        <div className="mt-3">
                                            <button
                                                className={`th-btn th-radius w-100 ${
                                                    isSelected ? "disabled" : ""
                                                }`}
                                                style={{
                                                    backgroundColor: isSelected
                                                        ? "#ccc"
                                                        : themeColor,
                                                    borderColor: isSelected
                                                        ? "#ccc"
                                                        : themeColor,
                                                    cursor: isSelected
                                                        ? "not-allowed"
                                                        : "pointer",
                                                }}
                                                disabled={isSelected}
                                                onClick={() => {
                                                    if (!isSelected) {
                                                        onAdd(product);
                                                        onClose();
                                                    }
                                                }}
                                            >
                                                {isSelected
                                                    ? "Selected"
                                                    : "Add to room"}
                                            </button>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Tabs Section */}
                        <div className="mt-5">
                            <ul
                                className="nav nav-tabs product-tab-style2 mb-3"
                                role="tablist"
                            >
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "description"
                                                ? "active"
                                                : ""
                                        }`}
                                        onClick={() =>
                                            setActiveTab("description")
                                        }
                                        style={{ cursor: "pointer" }}
                                    >
                                        Description
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "specification"
                                                ? "active"
                                                : ""
                                        }`}
                                        onClick={() =>
                                            setActiveTab("specification")
                                        }
                                        style={{ cursor: "pointer" }}
                                    >
                                        Specification
                                    </button>
                                </li>
                                <li className="nav-item">
                                    <button
                                        className={`nav-link ${
                                            activeTab === "features"
                                                ? "active"
                                                : ""
                                        }`}
                                        onClick={() => setActiveTab("features")}
                                        style={{ cursor: "pointer" }}
                                    >
                                        Features
                                    </button>
                                </li>
                            </ul>

                            <div className="tab-content border p-4 rounded-bottom">
                                {/* Description Tab */}
                                {activeTab === "description" && (
                                    <div className="animation-fadeIn">
                                        <div
                                            className="mb-0 text-muted"
                                            dangerouslySetInnerHTML={{
                                                __html: product.description,
                                            }}
                                        />
                                        {/* If there's a longer description field, use that here */}
                                    </div>
                                )}

                                {/* Specification Tab */}
                                {activeTab === "specification" && (
                                    <div className="animation-fadeIn">
                                        {specs.length > 0 ? (
                                            <table className="table table-striped table-bordered mb-0 small">
                                                <thead>
                                                    <tr>
                                                        <th>Specification</th>
                                                        <th>Details</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {specs.map((spec, idx) => (
                                                        <tr key={idx}>
                                                            {/* Handle both object structure {name, value} and possible string checks */}
                                                            <td className="fw-bold">
                                                                {spec.name ||
                                                                    spec.key ||
                                                                    "Spec"}
                                                            </td>
                                                            <td>
                                                                {spec.value ||
                                                                    spec.val ||
                                                                    ""}
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        ) : (
                                            <p className="text-muted text-center mb-0">
                                                No specifications available.
                                            </p>
                                        )}
                                    </div>
                                )}

                                {/* Features Tab */}
                                {activeTab === "features" && (
                                    <div className="animation-fadeIn">
                                        {features.length > 0 ? (
                                            <table className="table table-bordered mb-0 small">
                                                <thead className="table-light">
                                                    <tr>
                                                        <th>Feature</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {features.map(
                                                        (feat, idx) => (
                                                            <tr key={idx}>
                                                                <td
                                                                    style={{
                                                                        width: "30%",
                                                                    }}
                                                                >
                                                                    {feat.image && (
                                                                        <img
                                                                            src={
                                                                                feat.image
                                                                            }
                                                                            alt={
                                                                                feat.title
                                                                            }
                                                                            className="mb-2"
                                                                            style={{
                                                                                height: "30px",
                                                                            }}
                                                                        />
                                                                    )}
                                                                    <div className="fw-bold">
                                                                        {feat.title ||
                                                                            feat.name}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    {
                                                                        feat.description
                                                                    }
                                                                </td>
                                                            </tr>
                                                        )
                                                    )}
                                                </tbody>
                                            </table>
                                        ) : (
                                            <p className="text-muted text-center mb-0">
                                                No features listed.
                                            </p>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>,
        document.body
    );
};
