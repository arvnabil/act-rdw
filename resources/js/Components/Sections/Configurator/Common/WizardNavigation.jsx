import React from "react";

export default function WizardNavigation({
    onNext,
    onPrev,
    isFirstStep,
    isLastStep,
    themeColor = "#4AC15E",
}) {
    return (
        <div className="d-flex justify-content-between mt-5 pt-4 border-top">
            <button
                className="th-btn style4 th-radius"
                onClick={onPrev}
                disabled={isFirstStep}
                style={{
                    visibility: isFirstStep ? "hidden" : "visible",
                }}
            >
                <i className="fa-solid fa-arrow-left me-2"></i> Back
            </button>

            {!isLastStep && (
                <button
                    className="th-btn th-radius shadow-none"
                    style={{
                        backgroundColor: themeColor,
                        borderColor: themeColor,
                    }}
                    onClick={onNext}
                >
                    Next Step <i className="fa-solid fa-arrow-right ms-2"></i>
                </button>
            )}
        </div>
    );
}
