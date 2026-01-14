import React, { useEffect } from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import MainLayout from "@/Layouts/MainLayout";

export default function Login() {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    useEffect(() => {
        return () => {
            reset("password");
        };
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post(route("events.login"));
    };

    return (
        <MainLayout>
            <Head title="Login Event" />

            <section className="space-top space-extra-bottom bg-light-2">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-5 col-lg-7 col-md-9">
                            <div className="th-login-form bg-white shadow-lg rounded-20 p-5">
                                <div className="text-center mb-4">
                                    <h3 className="fw-bold">Welcome Back!</h3>
                                    <p className="text-body mb-0">
                                        Sign in to access your event dashboard
                                    </p>
                                </div>

                                <form onSubmit={submit}>
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

                                    <div className="form-group mb-4">
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
                                            placeholder="Enter your password"
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

                                    <div className="form-group mb-4 d-flex justify-content-between align-items-center">
                                        <div className="form-check custom-checkbox">
                                            <input
                                                className="form-check-input"
                                                type="checkbox"
                                                id="remember"
                                                checked={data.remember}
                                                onChange={(e) =>
                                                    setData(
                                                        "remember",
                                                        e.target.checked
                                                    )
                                                }
                                            />
                                            <label
                                                className="form-check-label"
                                                htmlFor="remember"
                                            >
                                                Remember me
                                            </label>
                                        </div>
                                        <Link
                                            href="#"
                                            className="text-theme fs-xs fw-semibold"
                                        >
                                            Forgot Password?
                                        </Link>
                                    </div>

                                    <div className="form-group mb-4">
                                        <button
                                            type="submit"
                                            className="th-btn w-100 style8 th-radius"
                                            disabled={processing}
                                        >
                                            {processing
                                                ? "Logging in..."
                                                : "Login Now"}
                                        </button>
                                    </div>

                                    <div className="text-center">
                                        <p className="mb-0 text-body">
                                            Don't have an account?{" "}
                                            <Link
                                                href={route("events.register")}
                                                className="text-theme fw-bold"
                                            >
                                                Sign Up
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
