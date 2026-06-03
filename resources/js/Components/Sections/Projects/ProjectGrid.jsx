import React, { useState } from "react";
import { Link } from "@inertiajs/react";

export default function ProjectGrid({ projects }) {
    const [selectedImage, setSelectedImage] = useState(null);

    if (!projects || !projects.data || projects.data.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center py-20 bg-slate-50/50 backdrop-blur-sm rounded-[2.5rem] border border-dashed border-slate-200">
                <div className="p-5 mb-4 bg-white rounded-2xl shadow-sm">
                    <i className="fa-light fa-building-circle-exclamation text-4xl text-slate-300"></i>
                </div>
                <h3 className="text-xl font-bold text-slate-800">No Projects Found</h3>
                <p className="max-w-xs mt-2 text-center text-slate-500">
                    Try adjusting your filters to see more results.
                </p>
            </div>
        );
    }

    return (
        <div className="row gy-30">
            <style>{`
                .project-card {
                    background: #ffffff;
                    border: 1px solid rgba(226, 232, 240, 0.8);
                    border-radius: 2rem;
                    overflow: hidden;
                    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                    height: 100%;
                }
                .project-card:hover {
                    border-color: rgba(74, 193, 94, 0.2);
                    box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(74, 193, 94, 0.05);
                    transform: translateY(-8px);
                }
                .project-thumb-box {
                    position: relative;
                    aspect-ratio: 16/10;
                    overflow: hidden;
                }
                .project-thumb-box img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 1s cubic-bezier(0.16, 1, 0.3, 1);
                }
                .project-card:hover .project-thumb-box img {
                    transform: scale(1.08);
                }
                .glass-overlay {
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(to top, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0) 60%);
                    opacity: 0;
                    transition: opacity 0.5s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .project-card:hover .glass-overlay {
                    opacity: 1;
                }
                .preview-btn {
                    width: 54px;
                    height: 54px;
                    background: rgba(255, 255, 255, 0.15);
                    backdrop-filter: blur(12px);
                    border: 1px solid rgba(255, 255, 255, 0.3);
                    border-radius: 18px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #fff;
                    font-size: 20px;
                    transform: translateY(20px);
                    opacity: 0;
                    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
                    cursor: pointer;
                }
                .project-card:hover .preview-btn {
                    transform: translateY(0);
                    opacity: 1;
                }
                .preview-btn:hover {
                    background: #4ac15e;
                    border-color: #4ac15e;
                    transform: scale(1.1);
                }
                .brand-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 6px 14px;
                    background: rgba(248, 250, 252, 0.8);
                    backdrop-filter: blur(10px);
                    border: 1px solid #e2e8f0;
                    border-radius: 50px;
                    font-size: 11px;
                    font-weight: 700;
                    color: #475569;
                    transition: all 0.3s ease;
                    white-space: nowrap;
                }
                .brand-pill img {
                    height: 14px;
                    width: auto;
                    object-fit: contain;
                    filter: grayscale(0.8);
                    transition: filter 0.3s ease;
                }
                .brand-pill:hover {
                    background: #ffffff;
                    border-color: #4ac15e;
                    color: #0b1422;
                    transform: scale(1.05);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                }
                .brand-pill:hover img {
                    filter: grayscale(0);
                }
                .tech-badge {
                    position: absolute;
                    top: 1.5rem;
                    left: 1.5rem;
                    padding: 6px 14px;
                    background: rgba(255, 255, 255, 0.9);
                    backdrop-filter: blur(8px);
                    border-radius: 50px;
                    font-size: 10px;
                    font-weight: 800;
                    letter-spacing: 0.05em;
                    text-transform: uppercase;
                    color: #0b1422;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    z-index: 2;
                }
                .project-title {
                    font-size: 1.25rem;
                    font-weight: 800;
                    line-height: 1.3;
                    margin-bottom: 0.75rem;
                    color: #0f172a;
                    transition: color 0.3s ease;
                }
                .project-card:hover .project-title {
                    color: #4ac15e;
                }
                .project-excerpt {
                    font-size: 0.9rem;
                    line-height: 1.6;
                    color: #64748b;
                    margin-bottom: 1.5rem;
                }

                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .lightbox-overlay {
                    position: fixed;
                    top: 0; left: 0; width: 100%; height: 100%;
                    background-color: rgba(15, 23, 42, 0.9);
                    z-index: 2000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    backdrop-filter: blur(10px);
                    animation: fadeIn 0.3s ease;
                }
            `}</style>

            {projects.data.map((project) => (
                <div key={project.id} className="col-xl-4 col-md-6 mb-4">
                    <div className="project-card">
                        
                        {/* Thumbnail */}
                        <div className="project-thumb-box">
                            {project.industry && (
                                <div className="tech-badge">
                                    {project.industry}
                                </div>
                            )}
                            <Link href={project.link}>
                                <img 
                                    src={project.image || "/assets/img/normal/shape.svg"} 
                                    alt={project.title} 
                                />
                                <div className="glass-overlay">
                                    <button 
                                        className="preview-btn"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            setSelectedImage({
                                                src: project.image,
                                                title: project.title,
                                                client: project.client
                                            });
                                        }}
                                    >
                                        <i className="fa-light fa-magnifying-glass-plus"></i>
                                    </button>
                                </div>
                            </Link>
                        </div>

                        {/* Content */}
                        <div className="p-6 md:p-8 d-flex flex-column" style={{ minHeight: '300px' }}>
                            <div className="flex-grow-1">
                                <div className="mb-3 d-flex align-items-center gap-2">
                                    <div className="w-8 h-8 rounded-full bg-slate-50 d-flex align-items-center justify-content-center text-slate-400 border border-slate-100">
                                        <i className="fa-light fa-building text-xs"></i>
                                    </div>
                                    <span className="text-xs font-bold tracking-wide uppercase text-slate-400">
                                        {project.client || "Success Story"}
                                    </span>
                                </div>

                                <h3 className="project-title line-clamp-2">
                                    <Link href={project.link}>
                                        {project.title}
                                    </Link>
                                </h3>

                                <p className="project-excerpt line-clamp-2">
                                    {project.excerpt}
                                </p>
                            </div>

                            {/* Brands as Pills */}
                            {project.brands && project.brands.length > 0 && (
                                <div className="pt-6 mt-2 border-t border-slate-50">
                                    <div className="mb-2 text-[10px] font-black tracking-widest text-slate-300 uppercase">
                                        Deployed Solutions
                                    </div>
                                    <div className="flex flex-wrap gap-2">
                                        {project.brands.slice(0, 2).map((brand) => (
                                            <div key={brand.id} className="brand-pill">
                                                {brand.thumbnail && <img src={`/storage/${brand.thumbnail}`} alt={brand.name} />}
                                                <span>{brand.name}</span>
                                            </div>
                                        ))}
                                        {project.brands.length > 2 && (
                                            <div className="brand-pill !bg-slate-100 !border-transparent">
                                                <span className="text-slate-500">+{project.brands.length - 2} More</span>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Arrow Link */}
                            <div className="mt-6 d-flex align-items-center justify-content-between">
                                <div className="text-[11px] font-bold text-slate-400 d-flex align-items-center gap-2">
                                    <i className="fa-light fa-calendar-check"></i>
                                    {project.year || "2024"}
                                </div>
                                <Link 
                                    href={project.link} 
                                    className="w-12 h-12 rounded-2xl bg-slate-900 d-flex align-items-center justify-content-center text-white transition-all hover:bg-[#3b82f6] hover:scale-110 active:scale-95 group"
                                >
                                    <i className="fa-light fa-arrow-right-long transition-transform group-hover:translate-x-1"></i>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            ))}

            {/* Lightbox / Popup */}
            {selectedImage && (
                <div 
                    className="lightbox-overlay" 
                    onClick={() => setSelectedImage(null)}
                >
                    <div className="position-relative" style={{ maxWidth: '90%', maxHeight: '90%' }} onClick={e => e.stopPropagation()}>
                        <button 
                            className="btn-close btn-close-white position-absolute" 
                            style={{ top: '-50px', right: '0', padding: '10px' }}
                            onClick={() => setSelectedImage(null)}
                        ></button>
                        
                        <div className="bg-white p-3 rounded-[2.5rem] shadow-2xl">
                            <img 
                                src={selectedImage.src || "/assets/img/normal/shape.svg"} 
                                alt={selectedImage.title} 
                                style={{ width: 'auto', height: 'auto', maxWidth: '100%', maxHeight: '75vh', borderRadius: '1.5rem' }}
                            />
                            <div className="pt-4 pb-2 px-4">
                                <span className="text-[10px] font-black tracking-widest text-[#4ac15e] uppercase mb-1 d-block">
                                    {selectedImage.client || "Success Story"}
                                </span>
                                <h4 className="text-xl font-bold text-slate-900 m-0">
                                    {selectedImage.title}
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
