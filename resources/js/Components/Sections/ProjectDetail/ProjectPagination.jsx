import React from "react";
import { Link } from "@inertiajs/react";

export default function ProjectPagination({ project }) {
    return (
        <div className="th-pagination">
            <div className="container">
                <div className="pagination-box2 d-flex justify-content-between">
                    <div className="pagination-prev">
                        {project.prev_id ? (
                            <Link
                                href={`/projects/${project.prev_id}`}
                                className="pagination-icon"
                            >
                                <i className="fa-solid fa-arrow-left"></i>
                            </Link>
                        ) : (
                            <span className="pagination-icon disabled">
                                <i className="fa-solid fa-arrow-left"></i>
                            </span>
                        )}
                        <p className="pagination-title">Previous Post</p>
                    </div>
                    <div className="pagination-next text-end">
                        <p className="pagination-title">Next Post</p>
                        <Link
                            href={`/projects/${project.next_id}`}
                            className="pagination-icon"
                        >
                            <i className="fa-solid fa-arrow-right"></i>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
