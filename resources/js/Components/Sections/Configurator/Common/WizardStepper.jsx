import React from "react";

export default function WizardStepper({
    steps,
    currentStep,
    themeColor = "#4AC15E",
}) {
    return (
        <div className="stepper mb-5">
            <div className="d-flex justify-content-between position-relative">
                {/* Background Line */}
                <div
                    className="position-absolute w-100 top-50 start-0 translate-middle-y"
                    style={{
                        height: "2px",
                        background: "#E5E7EB",
                        zIndex: 0,
                    }}
                ></div>

                {/* Active Progress Line */}
                <div
                    className="position-absolute top-50 start-0 translate-middle-y"
                    style={{
                        height: "2px",
                        background: themeColor,
                        width: `${
                            ((currentStep - 1) / (steps.length - 1)) * 100
                        }%`,
                        transition: "width 0.4s",
                        zIndex: 0,
                    }}
                ></div>

                {steps.map((s) => (
                    <div
                        key={s.id}
                        className="text-center position-relative"
                        style={{ zIndex: 1, width: "40px" }}
                    >
                        <div
                            className="d-flex align-items-center justify-content-center mx-auto mb-2 fw-bold"
                            style={{
                                width: "32px",
                                height: "32px",
                                borderRadius: "50%",
                                backgroundColor:
                                    currentStep >= s.id ? themeColor : "#fff",
                                color: currentStep >= s.id ? "#fff" : "#9CA3AF",
                                border: `2px solid ${
                                    currentStep >= s.id ? themeColor : "#E5E7EB"
                                }`,
                                transition: "all 0.3s",
                                fontSize: "12px",
                            }}
                        >
                            {currentStep > s.id ? (
                                <i className="fa-solid fa-check"></i>
                            ) : (
                                s.id
                            )}
                        </div>
                        <small
                            className={`d-none d-lg-block fw-bold pt-1 ${
                                currentStep >= s.id ? "text-dark" : "text-muted"
                            }`}
                            style={{ fontSize: "10px" }}
                        >
                            {s.label}
                        </small>
                    </div>
                ))}
            </div>
        </div>
    );
}
