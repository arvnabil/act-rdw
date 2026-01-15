import React from "react";
import { Link } from "@inertiajs/react";

export default function BlogComments({ comments }) {
    return (
        <>
            <div className="th-comments-wrap ">
                <h2 className="blog-inner-title h4">
                    {" "}
                    Comments (
                    {comments.length < 10
                        ? "0" + comments.length
                        : comments.length}
                    )
                </h2>
                <ul className="comment-list">
                    {comments.map((comment) => (
                        <li className="th-comment-item" key={comment.id}>
                            <div className="th-post-comment">
                                <div className="comment-avater">
                                    <img
                                        src={comment.avatar}
                                        alt="Comment Author"
                                    />
                                </div>
                                <div className="comment-content">
                                    <h3 className="name">{comment.author}</h3>
                                    <span className="commented-on">
                                        {comment.date}
                                    </span>
                                    <p className="text">{comment.text}</p>
                                    <div className="reply_and_edit">
                                        <Link href="#" className="reply-btn">
                                            <i className="fas fa-reply"></i>
                                            Reply
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            {comment.replies && comment.replies.length > 0 && (
                                <ul className="children">
                                    {comment.replies.map((reply) => (
                                        <li
                                            className="th-comment-item"
                                            key={reply.id}
                                        >
                                            <div className="th-post-comment">
                                                <div className="comment-avater">
                                                    <img
                                                        src={reply.avatar}
                                                        alt="Comment Author"
                                                    />
                                                </div>
                                                <div className="comment-content">
                                                    <div className="">
                                                        <h3 className="name">
                                                            {reply.author}
                                                        </h3>
                                                        <span className="commented-on">
                                                            {reply.date}
                                                        </span>
                                                    </div>
                                                    <p className="text">
                                                        {reply.text}
                                                    </p>
                                                    <div className="reply_and_edit">
                                                        <Link
                                                            href="#"
                                                            className="reply-btn"
                                                        >
                                                            <i className="fas fa-reply"></i>
                                                            Reply
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </li>
                    ))}
                </ul>
            </div>

            <div className="th-comment-form ">
                <div className="row">
                    <h3 className="blog-inner-title h4 mb-2">Leave a Reply</h3>
                    <p className="mb-25">
                        Your email address will not be published. Required
                        fields are marked
                    </p>
                    <div className="col-md-6 form-group">
                        <input
                            type="text"
                            placeholder="Full Name*"
                            className="form-control"
                            required
                        />
                        <i className="far fa-user"></i>
                    </div>
                    <div className="col-md-6 form-group">
                        <input
                            type="text"
                            placeholder="Your Email*"
                            className="form-control"
                            required
                        />
                        <i className="far fa-envelope"></i>
                    </div>
                    <div className="col-12 form-group">
                        <input
                            type="text"
                            placeholder="Website"
                            className="form-control"
                            required
                        />
                        <i className="far fa-globe"></i>
                    </div>
                    <div className="col-12 form-group">
                        <textarea
                            placeholder="Comment*"
                            className="form-control"
                        ></textarea>
                        <i className="far fa-pencil"></i>
                    </div>
                    <div className="col-12 form-group">
                        <input type="checkbox" id="html" />
                        <label htmlFor="html">
                            Save my name, email, and website in this browser for
                            the next time I comment.
                        </label>
                    </div>
                    <div className="col-12 form-group mb-0">
                        <button className="th-btn">
                            Send Message
                            <img src="/assets/img/icon/plane2.svg" alt="" />
                        </button>
                    </div>
                </div>
            </div>
        </>
    );
}
