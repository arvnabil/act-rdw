import React from "react";
import { Link } from "@inertiajs/react";
import { ChevronLeft, ChevronRight } from "lucide-react";

export default function ProjectPagination({ links }) {
    if (!links || links.length <= 3) return null;

    return (
        <nav className="flex items-center justify-center gap-2" aria-label="Project Pagination">
            {links.map((link, index) => {
                const isFirst = index === 0;
                const isLast = index === links.length - 1;
                
                if (!link.url && !isFirst && !isLast) return null;

                return (
                    <Link
                        key={index}
                        href={link.url || "#"}
                        preserveScroll
                        className={`
                            inline-flex items-center justify-center min-w-[3rem] h-12 px-4 text-sm font-bold transition-all rounded-2xl
                            ${link.active 
                                ? "bg-teal-500 text-white shadow-lg shadow-teal-500/30" 
                                : "bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:border-teal-500 hover:text-teal-500"
                            }
                            ${!link.url ? "opacity-50 cursor-not-allowed pointer-events-none" : "cursor-pointer"}
                        `}
                    >
                        {isFirst ? (
                             <ChevronLeft className="w-5 h-5" />
                        ) : isLast ? (
                            <ChevronRight className="w-5 h-5" />
                        ) : (
                            <span dangerouslySetInnerHTML={{ __html: link.label }} />
                        )}
                    </Link>
                );
            })}
        </nav>
    );
}
