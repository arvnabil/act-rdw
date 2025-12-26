import React, { useRef, useState, useEffect } from "react";
import { Head, useForm, usePage } from "@inertiajs/react";
import DashboardLayout from "./Layout";
import { route } from "ziggy-js";
import Toast from "@/Components/Common/Toast";

export default function Profile({ auth, status }) {
    const { flash } = usePage().props;
    const [toast, setToast] = useState(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: auth.user.name,
        email: auth.user.email,
        phone: auth.user.phone || "",
        password: "",
        password_confirmation: "",
        avatar: null,
        _method: "POST", // Use POST for file upload spoofing if needed, but here we can just post or patch
    });

    useEffect(() => {
        if (flash?.success || status === "profile-updated") {
            setToast({
                message: "Profile updated successfully!",
                type: "success",
            });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
        if (flash?.error) {
            setToast({ message: flash.error, type: "danger" });
            const timer = setTimeout(() => setToast(null), 3000);
            return () => clearTimeout(timer);
        }
    }, [flash, status]);

    const [avatarPreview, setAvatarPreview] = useState(
        auth.user.avatar ||
            "https://ui-avatars.com/api/?name=" +
                auth.user.name +
                "&background=0D5EF4&color=fff"
    );
    const fileInputRef = useRef(null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData("avatar", file);
            setAvatarPreview(URL.createObjectURL(file));
        }
    };

    const submit = (e) => {
        e.preventDefault();
        post(route("events.profile.update"), {
            onSuccess: () => {
                reset("password", "password_confirmation");
                // Toast handled by useEffect watching status/flash
            },
        });
    };

    return (
        <DashboardLayout pageTitle="Edit Profile">
            <Head title="Edit Profile" />
            <Toast
                show={!!toast}
                message={toast?.message}
                type={toast?.type}
                onClose={() => setToast(null)}
            />

            <div className="row justify-content-center">
                <div className="col-lg-12">
                    <form onSubmit={submit}>
                        {/* Profile Card */}
                        <div className="card border-0 shadow-sm rounded-20 mb-4 overflow-hidden">
                            <div className="card-header bg-white border-bottom-0 p-4 pb-0">
                                <h5 className="fw-bold mb-0">
                                    Profile Information
                                </h5>
                            </div>
                            <div className="card-body p-4">
                                {/* Avatar Upload */}
                                <div className="d-flex align-items-center mb-5">
                                    <div className="position-relative me-4">
                                        <div
                                            className="avatar rounded-circle overflow-hidden border-4 border-white shadow-sm"
                                            style={{
                                                width: "100px",
                                                height: "100px",
                                            }}
                                        >
                                            <img
                                                src={avatarPreview}
                                                alt="Profile"
                                                className="w-100 h-100 object-fit-cover"
                                            />
                                        </div>
                                        <button
                                            type="button"
                                            className="th-btn btn-sm btn-theme rounded-circle position-absolute bottom-0 end-0 shadow-sm"
                                            style={{
                                                width: "32px",
                                                height: "32px",
                                                padding: 0,
                                            }}
                                            onClick={() =>
                                                fileInputRef.current.click()
                                            }
                                        >
                                            <i className="fa-solid fa-camera"></i>
                                        </button>
                                        <input
                                            type="file"
                                            ref={fileInputRef}
                                            className="d-none"
                                            accept="image/*"
                                            onChange={handleFileChange}
                                        />
                                    </div>
                                    <div>
                                        <h6 className="fw-bold mb-1">
                                            Profile Photo
                                        </h6>
                                        <p className="text-muted small mb-0">
                                            Update your profile picture. Max
                                            size 1MB.
                                        </p>
                                        {errors.avatar && (
                                            <div className="text-danger small mt-1">
                                                {errors.avatar}
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <div className="row g-4">
                                    <div className="col-md-6">
                                        <label className="form-label fw-medium">
                                            Full Name
                                        </label>
                                        <input
                                            type="text"
                                            className={`form-control ${
                                                errors.name ? "is-invalid" : ""
                                            }`}
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            placeholder="Enter your name"
                                        />
                                        {errors.name && (
                                            <div className="invalid-feedback">
                                                {errors.name}
                                            </div>
                                        )}
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label fw-medium">
                                            Email Address
                                        </label>
                                        <input
                                            type="email"
                                            className={`form-control ${
                                                errors.email ? "is-invalid" : ""
                                            }`}
                                            value={data.email}
                                            onChange={(e) =>
                                                setData("email", e.target.value)
                                            }
                                            placeholder="Enter your email"
                                        />
                                        {errors.email && (
                                            <div className="invalid-feedback">
                                                {errors.email}
                                            </div>
                                        )}
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label fw-medium">
                                            Phone Number
                                        </label>
                                        <input
                                            type="text"
                                            className={`form-control ${
                                                errors.phone ? "is-invalid" : ""
                                            }`}
                                            value={data.phone}
                                            onChange={(e) =>
                                                setData("phone", e.target.value)
                                            }
                                            placeholder="Enter your phone number"
                                        />
                                        {errors.phone && (
                                            <div className="invalid-feedback">
                                                {errors.phone}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Password Change */}
                        <div className="card border-0 shadow-sm rounded-20 mb-4 overflow-hidden">
                            <div className="card-header bg-white border-bottom-0 p-4 pb-0">
                                <h5 className="fw-bold mb-0">
                                    Change Password
                                </h5>
                            </div>
                            <div className="card-body p-4">
                                <div className="row g-4">
                                    <div className="col-md-6">
                                        <label className="form-label fw-medium">
                                            New Password
                                        </label>
                                        <input
                                            type="password"
                                            className={`form-control ${
                                                errors.password
                                                    ? "is-invalid"
                                                    : ""
                                            }`}
                                            value={data.password}
                                            onChange={(e) =>
                                                setData(
                                                    "password",
                                                    e.target.value
                                                )
                                            }
                                            placeholder="Leave empty to keep current"
                                        />
                                        {errors.password && (
                                            <div className="invalid-feedback">
                                                {errors.password}
                                            </div>
                                        )}
                                    </div>
                                    <div className="col-md-6">
                                        <label className="form-label fw-medium">
                                            Confirm Password
                                        </label>
                                        <input
                                            type="password"
                                            className="form-control"
                                            value={data.password_confirmation}
                                            onChange={(e) =>
                                                setData(
                                                    "password_confirmation",
                                                    e.target.value
                                                )
                                            }
                                            placeholder="Confirm new password"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="text-end mb-5">
                            <button
                                type="submit"
                                className="th-btn th-radius"
                                disabled={processing}
                            >
                                {processing ? (
                                    <>
                                        <span
                                            className="spinner-border spinner-border-sm me-2"
                                            role="status"
                                            aria-hidden="true"
                                        ></span>
                                        Saving...
                                    </>
                                ) : (
                                    "Save Changes"
                                )}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
