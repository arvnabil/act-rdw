import React, { useState, useRef, useEffect } from "react";
import { Head, usePage, router } from "@inertiajs/react";
import DraggableElement from "@/Components/Certificate/DraggableElement";

export default function CertificateDesigner({ certificate, event }) {
    const [elements, setElements] = useState(certificate.content_layout || []);
    const [selectedId, setSelectedId] = useState(null);
    const [saving, setSaving] = useState(false);
    const [zoom, setZoom] = useState(1);

    const canvasRef = useRef(null);
    const selectedElement = elements.find((el) => el.id === selectedId);

    const addElement = (type, defaultText) => {
        const newElement = {
            id: Date.now(),
            type: type, // 'text' or 'variable'
            text: defaultText,
            x: 50,
            y: 50,
            fontSize: 24,
            color: "#000000",
            fontWeight: "normal",
            fontFamily: "Inter, sans-serif",
        };
        setElements([...elements, newElement]);
        setSelectedId(newElement.id);
    };

    const updateElement = (id, changes) => {
        setElements(
            elements.map((el) => (el.id === id ? { ...el, ...changes } : el))
        );
    };

    const deleteElement = (id) => {
        setElements(elements.filter((el) => el.id !== id));
        if (selectedId === id) setSelectedId(null);
    };

    const handleSave = () => {
        setSaving(true);
        router.post(
            route("events.certificates.update-design", certificate.id),
            {
                content_layout: elements,
            },
            {
                onFinish: () => setSaving(false),
                preserveScroll: true,
                onSuccess: () => {
                    // Optional: Show a fancy toast here
                },
            }
        );
    };

    const variables = [
        {
            label: "Participant Name",
            value: "{{ participant_name }}",
            icon: "fa-user",
        },
        {
            label: "Event Title",
            value: "{{ event_title }}",
            icon: "fa-calendar-alt",
        },
        { label: "Date", value: "{{ date }}", icon: "fa-clock" },
        {
            label: "Certificate Code",
            value: "{{ certificate_code }}",
            icon: "fa-barcode",
        },
    ];

    return (
        <div
            className="d-flex flex-column vh-100 bg-light"
            style={{ fontFamily: "'Inter', sans-serif" }}
        >
            <Head title={`Design Certificate - ${event.title}`} />

            {/* Top Bar */}
            <header className="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center shadow-sm z-1">
                <div className="d-flex align-items-center gap-3">
                    <button
                        className="btn btn-light rounded-circle shadow-sm"
                        onClick={() => window.close()}
                    >
                        <i className="fas fa-arrow-left text-muted"></i>
                    </button>
                    <div>
                        <h5 className="mb-0 fw-bold text-dark">
                            Certificate Designer
                        </h5>
                        <small
                            className="text-muted d-block"
                            style={{ fontSize: "0.8rem" }}
                        >
                            {event.title}
                        </small>
                    </div>
                </div>
                <div className="d-flex align-items-center gap-3">
                    <div className="btn-group shadow-sm">
                        <button
                            className="btn btn-light border"
                            onClick={() =>
                                setZoom((z) => Math.max(0.5, z - 0.1))
                            }
                            title="Zoom Out"
                        >
                            <i className="fas fa-minus small"></i>
                        </button>
                        <span
                            className="btn btn-light border border-start-0 border-end-0 bg-white pe-none"
                            style={{ minWidth: "60px" }}
                        >
                            {Math.round(zoom * 100)}%
                        </span>
                        <button
                            className="btn btn-light border"
                            onClick={() => setZoom((z) => Math.min(2, z + 0.1))}
                            title="Zoom In"
                        >
                            <i className="fas fa-plus small"></i>
                        </button>
                    </div>
                    <button
                        className="btn btn-dark px-4 fw-medium shadow-sm d-flex align-items-center gap-2"
                        onClick={handleSave}
                        disabled={saving}
                    >
                        {saving ? (
                            <i className="fas fa-circle-notch fa-spin"></i>
                        ) : (
                            <i className="fas fa-save"></i>
                        )}
                        <span>Save Design</span>
                    </button>
                </div>
            </header>

            <div className="d-flex flex-grow-1 overflow-hidden position-relative">
                {/* Left Sidebar: Tools */}
                <aside
                    className="bg-white border-end d-flex flex-column shadow-sm"
                    style={{ width: "280px", zIndex: 10 }}
                >
                    <div className="p-4 border-bottom">
                        <h6 className="fw-bold mb-3 text-uppercase small text-muted ls-1">
                            Variables
                        </h6>
                        <div className="d-grid gap-2">
                            {variables.map((v) => (
                                <button
                                    key={v.value}
                                    className="btn btn-outline-light text-dark text-start border d-flex align-items-center gap-3 py-2 px-3 shadow-sm hover-elevate"
                                    onClick={() =>
                                        addElement("variable", v.value)
                                    }
                                    style={{ transition: "all 0.2s" }}
                                >
                                    <div
                                        className="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                        style={{
                                            width: "32px",
                                            height: "32px",
                                        }}
                                    >
                                        <i
                                            className={`fas ${v.icon} text-primary small`}
                                        ></i>
                                    </div>
                                    <span className="small fw-medium">
                                        {v.label}
                                    </span>
                                </button>
                            ))}
                        </div>
                    </div>
                    <div className="p-4 flex-grow-1">
                        <h6 className="fw-bold mb-3 text-uppercase small text-muted ls-1">
                            Elements
                        </h6>
                        <button
                            className="btn btn-outline-light text-dark text-start border w-100 d-flex align-items-center gap-3 py-2 px-3 shadow-sm hover-elevate"
                            onClick={() => addElement("text", "New Text")}
                        >
                            <div
                                className="bg-light rounded-circle d-flex align-items-center justify-content-center"
                                style={{ width: "32px", height: "32px" }}
                            >
                                <i className="fas fa-font text-secondary small"></i>
                            </div>
                            <span className="small fw-medium">
                                Add Text Item
                            </span>
                        </button>
                    </div>
                </aside>

                {/* Main Canvas Area */}
                <main className="flex-grow-1 bg-light position-relative d-flex align-items-center justify-content-center overflow-hidden">
                    {/* Grid Pattern Background */}
                    <div
                        className="position-absolute w-100 h-100"
                        style={{
                            backgroundImage:
                                "radial-gradient(#cbd5e1 1px, transparent 1px)",
                            backgroundSize: "20px 20px",
                            opacity: 0.5,
                        }}
                    ></div>

                    <div className="overflow-auto w-100 h-100 d-flex align-items-center justify-content-center p-5">
                        <div
                            ref={canvasRef}
                            className="bg-white shadow-lg position-relative user-select-none transition-transform"
                            style={{
                                width: "842px",
                                height: "595px",
                                minWidth: "842px",
                                minHeight: "595px",
                                backgroundImage: `url(/storage/${certificate.certificate_background})`,
                                backgroundSize: "cover",
                                backgroundPosition: "center",
                                transform: `scale(${zoom})`,
                                transformOrigin: "center center",
                            }}
                            onClick={() => setSelectedId(null)}
                        >
                            {!certificate.certificate_background && (
                                <div className="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted border border-2 border-dashed bg-light bg-opacity-50">
                                    <i className="fas fa-image fa-4x mb-3 text-secondary opacity-50"></i>
                                    <p className="fw-medium">
                                        No Background Image
                                    </p>
                                    <small>
                                        Upload one in the admin panel first.
                                    </small>
                                </div>
                            )}

                            {elements.map((el) => (
                                <DraggableElement
                                    key={el.id}
                                    element={el}
                                    isSelected={selectedId === el.id}
                                    onSelect={setSelectedId}
                                    onChange={updateElement}
                                />
                            ))}
                        </div>
                    </div>
                </main>

                {/* Right Sidebar: properties (Floating if selected) */}
                {selectedElement ? (
                    <aside
                        className="bg-white border-start shadow-lg position-relative d-flex flex-column"
                        style={{ width: "320px", zIndex: 20 }}
                    >
                        <div className="p-4 bg-white border-bottom">
                            <h6 className="fw-bold mb-0">Properties</h6>
                            <small className="text-muted">
                                Edit selected element
                            </small>
                        </div>
                        <div className="p-4 flex-grow-1 overflow-auto">
                            <div className="mb-4">
                                <label className="form-label small fw-bold text-muted text-uppercase ls-1">
                                    Content
                                </label>
                                <div className="input-group input-group-sm">
                                    <span className="input-group-text bg-light border-end-0">
                                        <i className="fas fa-pen small text-muted"></i>
                                    </span>
                                    <input
                                        type="text"
                                        className="form-control border-start-0 ps-0"
                                        value={selectedElement.text}
                                        onChange={(e) =>
                                            updateElement(selectedElement.id, {
                                                text: e.target.value,
                                            })
                                        }
                                        placeholder="Type text here..."
                                    />
                                </div>
                            </div>

                            <div className="row g-3 mb-4">
                                <div className="col-12">
                                    <label className="form-label small fw-bold text-muted text-uppercase ls-1">
                                        Typography
                                    </label>
                                </div>
                                <div className="col-6">
                                    <div className="form-floating">
                                        <input
                                            type="number"
                                            className="form-control form-control-sm"
                                            id="fontSizeInput"
                                            value={selectedElement.fontSize}
                                            onChange={(e) =>
                                                updateElement(
                                                    selectedElement.id,
                                                    {
                                                        fontSize: parseInt(
                                                            e.target.value
                                                        ),
                                                    }
                                                )
                                            }
                                        />
                                        <label htmlFor="fontSizeInput">
                                            Size (px)
                                        </label>
                                    </div>
                                </div>
                                <div className="col-6">
                                    <div className="form-floating">
                                        <select
                                            className="form-select form-select-sm"
                                            id="fontWeightInput"
                                            value={selectedElement.fontWeight}
                                            onChange={(e) =>
                                                updateElement(
                                                    selectedElement.id,
                                                    {
                                                        fontWeight:
                                                            e.target.value,
                                                    }
                                                )
                                            }
                                        >
                                            <option value="normal">
                                                Regular
                                            </option>
                                            <option value="bold">Bold</option>
                                            <option value="300">Light</option>
                                            <option value="600">
                                                Semi Bold
                                            </option>
                                            <option value="800">
                                                Extra Bold
                                            </option>
                                        </select>
                                        <label htmlFor="fontWeightInput">
                                            Weight
                                        </label>
                                    </div>
                                </div>

                                <div className="col-12">
                                    <label className="form-label small mb-1">
                                        Color
                                    </label>
                                    <div className="d-flex align-items-center gap-2 p-2 border rounded bg-light">
                                        <input
                                            type="color"
                                            className="form-control form-control-color border-0 p-0 rounded-circle"
                                            style={{
                                                width: "32px",
                                                height: "32px",
                                            }}
                                            value={selectedElement.color}
                                            onChange={(e) =>
                                                updateElement(
                                                    selectedElement.id,
                                                    { color: e.target.value }
                                                )
                                            }
                                            title="Choose color"
                                        />
                                        <input
                                            type="text"
                                            className="form-control form-control-sm border-0 bg-transparent font-monospace"
                                            value={selectedElement.color}
                                            onChange={(e) =>
                                                updateElement(
                                                    selectedElement.id,
                                                    { color: e.target.value }
                                                )
                                            }
                                        />
                                    </div>
                                </div>

                                <div className="col-12">
                                    <label className="form-label small fw-bold text-muted text-uppercase ls-1 mt-2">
                                        Font Family
                                    </label>
                                    <select
                                        className="form-select form-select-sm"
                                        value={selectedElement.fontFamily}
                                        onChange={(e) =>
                                            updateElement(selectedElement.id, {
                                                fontFamily: e.target.value,
                                            })
                                        }
                                    >
                                        <option value="Arial, sans-serif">
                                            Arial
                                        </option>
                                        <option value="'Times New Roman', serif">
                                            Times New Roman
                                        </option>
                                        <option value="'Courier New', monospace">
                                            Courier New
                                        </option>
                                        <option value="'Inter', sans-serif">
                                            Inter
                                        </option>
                                        <option value="'Roboto', sans-serif">
                                            Roboto
                                        </option>
                                        <option value="'Playfair Display', serif">
                                            Playfair Display
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div className="p-4 border-top bg-light">
                            <button
                                className="btn btn-danger w-100 shadow-sm"
                                onClick={() =>
                                    deleteElement(selectedElement.id)
                                }
                            >
                                <i className="fas fa-trash-alt me-2"></i> Delete
                                Element
                            </button>
                        </div>
                    </aside>
                ) : (
                    <aside
                        className="bg-white border-start position-relative d-flex flex-column align-items-center justify-content-center text-center p-4 text-muted"
                        style={{ width: "320px", zIndex: 10 }}
                    >
                        <div className="mb-3 p-4 bg-light rounded-circle">
                            <i className="fas fa-mouse-pointer fa-2x text-primary op-50"></i>
                        </div>
                        <h6 className="fw-bold text-dark">No Selection</h6>
                        <p className="small mb-0 px-3">
                            Click on an element in the canvas to edit its
                            properties.
                        </p>
                    </aside>
                )}
            </div>
            <style>{`
                .hover-elevate:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
                }
                .op-50 { opacity: 0.5; }
            `}</style>
        </div>
    );
}
