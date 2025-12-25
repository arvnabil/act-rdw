import React from "react";
import Modal from "@/Components/Common/Modal";

export default function ConfiguratorSubmitModal({
    show,
    onClose,
    userInfo,
    onChange,
    onSubmit,
}) {
    return (
        <Modal show={show} onClose={onClose} maxWidth="550px">
            <div className="text-start">
                <h5 className="modal-title fw-bold mb-2">
                    SHOW ME THE COMPLETE SETUP
                </h5>
                <p className="text-muted mb-4 small">
                    All finished! Complete the form below to view your project
                    summary page, including guidance on next steps and a
                    detailed shopping guide.
                </p>
                <form onSubmit={onSubmit}>
                    <div className="mb-3">
                        <label className="form-label small fw-bold text-uppercase">
                            Name
                        </label>
                        <input
                            type="text"
                            className="form-control rounded-3"
                            name="name"
                            value={userInfo.name || ""}
                            onChange={onChange}
                            required
                        />
                    </div>
                    <div className="mb-3">
                        <label className="form-label small fw-bold text-uppercase">
                            Telp
                        </label>
                        <input
                            type="tel"
                            className="form-control rounded-3"
                            name="phone"
                            value={userInfo.phone || ""}
                            onChange={onChange}
                            required
                        />
                    </div>
                    <div className="mb-4">
                        <label className="form-label small fw-bold text-uppercase">
                            Is Sustainability a priority for your company?{" "}
                            <span className="text-danger">*</span>
                        </label>
                        <select
                            className="form-select rounded-3"
                            name="sustainability"
                            value={userInfo.sustainability || ""}
                            onChange={onChange}
                            required
                        >
                            <option value="">Select Option</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <button
                        type="submit"
                        className="th-btn th-radius w-100 style8 shadow-none"
                    >
                        See My Result{" "}
                        <i className="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </Modal>
    );
}
