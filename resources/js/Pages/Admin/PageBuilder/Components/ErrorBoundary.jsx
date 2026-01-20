import React from "react";

/**
 * ErrorBoundary
 *
 * Catches render errors in child components (sections) to prevent
 * the entire builder from crashing.
 */
class ErrorBoundary extends React.Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true, error };
    }

    componentDidCatch(error, errorInfo) {
        console.error("Builder Section Error:", error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return (
                <div className="p-6 border-2 border-red-300 bg-red-50 text-red-700 rounded-lg text-center">
                    <i className="fa-solid fa-bug text-2xl mb-2"></i>
                    <h3 className="font-bold">Section Render Error</h3>
                    <p className="text-sm mt-1 mb-2">
                        This section failed to render.
                    </p>
                    <details className="text-xs text-left bg-white p-2 rounded border border-red-100 overflow-auto max-h-32">
                        <summary className="cursor-pointer mb-1 font-medium">
                            Error Details
                        </summary>
                        {this.state.error?.toString()}
                    </details>
                    <button
                        onClick={() => this.setState({ hasError: false })}
                        className="mt-3 text-xs bg-red-100 hover:bg-red-200 text-red-800 px-3 py-1 rounded transition"
                    >
                        Try Retry
                    </button>
                </div>
            );
        }

        return this.props.children;
    }
}

export default ErrorBoundary;
