import React from "react";

const Toast = ({
    message,
    show,
    type = "success",
    onClose,
    duration = 3000,
}) => {
    const [fadingOut, setFadingOut] = React.useState(false);

    React.useEffect(() => {
        if (show) {
            setFadingOut(false);
            if (onClose && duration > 0) {
                const timer = setTimeout(() => {
                    setFadingOut(true);
                }, duration);
                return () => clearTimeout(timer);
            }
        }
    }, [show, duration, onClose]);

    React.useEffect(() => {
        if (fadingOut && onClose) {
            const timer = setTimeout(() => {
                onClose();
            }, 900); // 500ms matches transition duration
            return () => clearTimeout(timer);
        }
    }, [fadingOut, onClose]);

    if (!show) return null;

    const variants = {
        success: { icon: "fa-circle-check", color: "text-success" },
        danger: { icon: "fa-circle-xmark", color: "text-danger" },
        info: { icon: "fa-circle-info", color: "text-info" },
        warning: { icon: "fa-triangle-exclamation", color: "text-warning" },
    };

    const style = variants[type] || variants.success;

    return (
        <div
            className="position-fixed start-50 translate-middle-x bg-white d-flex align-items-center animation-fadeIn"
            style={{
                zIndex: 2019,
                top: "16px",
                width: "400px",
                padding: "12px",
                borderRadius: "8px",
                border: "1px solid #DFE3E8",
                color: "#2a2b2d",
                boxShadow:
                    "0 6px 12px rgba(0,0,0,0.08), 0 12px 24px rgba(0,0,0,0.08)",
                opacity: fadingOut ? 0 : 1,
                transition: "opacity 0.5s ease-in-out",
                pointerEvents: fadingOut ? "none" : "auto",
            }}
        >
            <i
                className={`fa-solid ${style.icon} ${style.color} me-3 fa-xl`}
            ></i>
            <span className="fw-medium" style={{ fontSize: "14px" }}>
                {message}
            </span>
            {onClose && (
                <button
                    onClick={() => setFadingOut(true)}
                    className="btn-close ms-auto"
                    style={{ fontSize: "10px" }}
                ></button>
            )}
        </div>
    );
};

export default Toast;
