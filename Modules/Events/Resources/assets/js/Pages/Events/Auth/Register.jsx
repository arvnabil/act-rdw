import React, { useEffect } from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    useEffect(() => {
        return () => {
            reset("password", "password_confirmation");
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route("events.register"));
    };

    return (
        <MainLayout>
            <Head title="Register Event" />

            <section className="space-top space-extra-bottom bg-light-2">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-5 col-lg-7 col-md-9">
                            <div className="th-login-form bg-white shadow-lg rounded-20 p-5">
                                <div className="text-center mb-4">
                                    <h3 className="fw-bold">Create Account</h3>
                                    <p className="text-body mb-0">
                                        Sign up to manage and join events
                                    </p>
                                </div>

                                <form onSubmit={submit}>
                                    <div className="form-group mb-3">
                                        <label
                                            htmlFor="name"
                                            className="form-label fw-semibold"
                                        >
                                            Full Name
                                        </label>
                                        <input
                                            type="text"
                                            className={`form-control style-border ${
                                                errors.name ? "is-invalid" : ""
                                            }`}
                                            id="name"
                                            placeholder="Enter your full name"
                                            value={data.name}
                                            onChange={(e) =>
                                                setData("name", e.target.value)
                                            }
                                            required
                                        />
                                        {errors.name && (
                                            <div className="invalid-feedback d-block">
                                                {errors.name}
                                            </div>
                                        )}
                                    </div>

                                    <div className="form-group mb-3">
                                        <label
                                            htmlFor="email"
                                            className="form-label fw-semibold"
                                        >
                                            Email Address
                                        </label>
                                        <input
                                            type="email"
                                            className={`form-control style-border ${
                                                errors.email ? "is-invalid" : ""
                                            }`}
                                            id="email"
                                            placeholder="Enter your email"
                                            value={data.email}
                                            onChange={(e) =>
                                                setData("email", e.target.value)
                                            }
                                            required
                                        />
                                        {errors.email && (
                                            <div className="invalid-feedback d-block">
                                                {errors.email}
                                            </div>
                                        )}
                                    </div>

                                    <div className="form-group mb-3">
                                        <label
                                            htmlFor="password"
                                            className="form-label fw-semibold"
                                        >
                                            Password
                                        </label>
                                        <input
                                            type="password"
                                            className={`form-control style-border ${
                                                errors.password
                                                    ? "is-invalid"
                                                    : ""
                                            }`}
                                            id="password"
                                            placeholder="Create a password"
                                            value={data.password}
                                            onChange={(e) =>
                                                setData(
                                                    "password",
                                                    e.target.value
                                                )
                                            }
                                            required
                                        />
                                        {errors.password && (
                                            <div className="invalid-feedback d-block">
                                                {errors.password}
                                            </div>
                                        )}
                                    </div>

                                    <div className="form-group mb-4">
                                        <label
                                            htmlFor="password_confirmation"
                                            className="form-label fw-semibold"
                                        >
                                            Confirm Password
                                        </label>
                                        <input
                                            type="password"
                                            className="form-control style-border"
                                            id="password_confirmation"
                                            placeholder="Confirm your password"
                                            value={data.password_confirmation}
                                            onChange={(e) =>
                                                setData(
                                                    "password_confirmation",
                                                    e.target.value
                                                )
                                            }
                                            required
                                        />
                                    </div>

                                    <div className="form-group mb-4">
                                        <button
                                            type="submit"
                                            className="th-btn w-100 style8 th-radius"
                                            disabled={processing}
                                        >
                                            {processing
                                                ? "Creating Account..."
                                                : "Sign Up"}
                                        </button>
                                    </div>

                                    <div className="text-center">
                                        <p className="mb-0 text-body">
                                            Already have an account?{" "}
                                            <Link
                                                href={route("events.login")}
                                                className="text-theme fw-bold"
                                            >
                                                Login
                                            </Link>
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </MainLayout>
    );
}
