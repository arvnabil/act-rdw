import React from "react";

export default function ProjectSkeleton() {
    return (
        <div className="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
            {[1, 2, 3, 4, 5, 6].map((i) => (
                <div key={i} className="flex flex-col h-full bg-slate-50 dark:bg-slate-900/40 rounded-[2rem] overflow-hidden animate-pulse">
                    <div className="bg-slate-200 dark:bg-slate-800 aspect-video w-full"></div>
                    <div className="p-6">
                        <div className="h-6 mb-3 bg-slate-200 dark:bg-slate-800 rounded-lg w-3/4"></div>
                        <div className="h-4 mb-2 bg-slate-200 dark:bg-slate-800 rounded-lg w-full"></div>
                        <div className="h-4 mb-6 bg-slate-200 dark:bg-slate-800 rounded-lg w-5/6"></div>
                        
                        <div className="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                            <div className="flex -space-x-2">
                                <div className="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 border-2 border-white dark:border-slate-900"></div>
                                <div className="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-800 border-2 border-white dark:border-slate-900"></div>
                            </div>
                            <div className="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-800"></div>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}
