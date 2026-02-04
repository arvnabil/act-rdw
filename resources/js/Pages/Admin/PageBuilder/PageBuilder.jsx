import React, { useState, useEffect, useCallback } from "react";
import { Head, router } from "@inertiajs/react";
import Swal from "sweetalert2";
import BuilderHeader from "./BuilderHeader";
import BuilderSidebar from "./BuilderSidebar";
import BuilderCanvas from "./BuilderCanvas";
import InspectorPanel from "./InspectorPanel";
import { sectionRegistry } from "./utils/sectionRegistry";
import useHistory from "@/hooks/useHistory";

export default function PageBuilder({
    page,
    sections: initialSections,
    allServices,
    allProjects,
}) {
    // 1. History State Management (Replacing simple useState)
    // Limits history to 50 steps
    const {
        state: sections,
        set: setSections,
        undo,
        redo,
        canUndo,
        canRedo,
    } = useHistory(initialSections || [], 50);

    const [selectedSectionId, setSelectedSectionId] = useState(null);
    const [isSaving, setIsSaving] = useState(false);

    // UI State
    const [leftSidebarOpen, setLeftSidebarOpen] = useState(true);
    const [rightSidebarOpen, setRightSidebarOpen] = useState(true);
    const [previewDevice, setPreviewDevice] = useState("desktop"); // desktop, tablet, mobile

    // Selected Section Object
    const selectedSection =
        sections.find((s) => s.id === selectedSectionId) || null;

    // Auto-open inspector when a section is selected
    useEffect(() => {
        if (selectedSectionId) {
            setRightSidebarOpen(true);
        }
    }, [selectedSectionId]);

    const handleAddSection = (key) => {
        const registryItem = sectionRegistry[key];
        if (!registryItem) return;

        const newSection = {
            id: `new-${Date.now()}`,
            section_key: key,
            config: registryItem.defaultConfig || {},
            position: sections.length,
            is_active: true,
        };

        const newSections = [...sections, newSection];
        setSections(newSections);
        setSelectedSectionId(newSection.id);
    };

    const handleUpdateSection = (updatedSection) => {
        setSections(
            sections.map((s) =>
                s.id === updatedSection.id ? updatedSection : s,
            ),
        );
    };

    const handleReorder = (newSections) => {
        const updated = newSections.map((s, index) => ({
            ...s,
            position: index,
        }));
        setSections(updated);
    };

    const handleRemoveSection = (sectionId) => {
        if (
            !confirm(
                "Are you sure you want to remove this section? This action will be saved when you click 'Save Changes'.",
            )
        )
            return;

        setSections((prev) => prev.filter((s) => s.id !== sectionId));
        if (selectedSectionId === sectionId) {
            setSelectedSectionId(null);
        }
    };

    const handleSave = useCallback(() => {
        setIsSaving(true);
        router.post(
            `/admin/page-builder/${page.id}/save`,
            {
                sections: sections.map((s, index) => ({
                    id: String(s.id).startsWith("new-") ? null : s.id,
                    section_key: s.section_key,
                    config: JSON.stringify(s.config || {}), // Stringify to bypass PHP max_input_vars limit
                    position: index,
                    is_active: s.is_active,
                })),
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsSaving(false);
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "Page saved successfully",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                },
                onError: (errors) => {
                    setIsSaving(false);
                    console.error(errors);
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "error",
                        title: "Failed to save page",
                        showConfirmButton: false,
                        timer: 3000,
                    });
                },
            },
        );
    }, [page.id, sections]);

    // Keyboard Shortcuts
    useEffect(() => {
        const handleKeyDown = (e) => {
            // CMD/CTRL + S (Save)
            if ((e.metaKey || e.ctrlKey) && e.key === "s") {
                e.preventDefault();
                handleSave();
            }
            // CMD/CTRL + Z (Undo)
            if ((e.metaKey || e.ctrlKey) && e.key === "z" && !e.shiftKey) {
                e.preventDefault();
                if (canUndo) undo();
            }
            // CMD/CTRL + SHIFT + Z (Redo)
            if (
                (e.metaKey || e.ctrlKey) &&
                ((e.key === "z" && e.shiftKey) || e.key === "y")
            ) {
                // Support both Shift+Z and Ctrl+Y
                e.preventDefault();
                if (canRedo) redo();
            }
            // ESC
            if (e.key === "Escape") {
                setSelectedSectionId(null);
            }
        };

        window.addEventListener("keydown", handleKeyDown);
        return () => window.removeEventListener("keydown", handleKeyDown);
    }, [handleSave, undo, redo, canUndo, canRedo]);

    return (
        <div className="h-screen flex flex-col bg-gray-100 font-sans overflow-hidden">
            <Head title={`Builder: ${page.title}`} />

            {/* Header - Fixed Height */}
            <div className="flex-none z-50">
                <BuilderHeader
                    page={page}
                    onSave={handleSave}
                    isSaving={isSaving}
                    previewDevice={previewDevice}
                    setPreviewDevice={setPreviewDevice}
                    onUndo={undo}
                    onRedo={redo}
                    canUndo={canUndo}
                    canRedo={canRedo}
                />
            </div>

            {/* Main Workspace - Fills remaining height */}
            <div className="flex-1 flex overflow-hidden">
                {/* Left Sidebar */}
                <BuilderSidebar
                    sections={sections}
                    onReorder={handleReorder}
                    onSelect={setSelectedSectionId}
                    selectedSectionId={selectedSectionId}
                    onAddSection={handleAddSection}
                    onRemove={handleRemoveSection}
                    isOpen={leftSidebarOpen}
                    onToggle={() => setLeftSidebarOpen(!leftSidebarOpen)}
                />

                {/* Canvas Area - Scrollable */}
                <main className="flex-1 bg-gray-100/50 relative overflow-hidden flex flex-col">
                    <BuilderCanvas
                        sections={sections}
                        selectedSectionId={selectedSectionId}
                        onSelectSection={setSelectedSectionId}
                        onMoveSection={handleReorder}
                        previewDevice={previewDevice}
                        allServices={allServices}
                        allProjects={allProjects}
                    />
                </main>

                {/* Right Inspector */}
                <InspectorPanel
                    section={selectedSection}
                    onUpdate={handleUpdateSection}
                    isOpen={rightSidebarOpen}
                    onToggle={() => setRightSidebarOpen(!rightSidebarOpen)}
                    onClose={() => setSelectedSectionId(null)}
                />
            </div>
        </div>
    );
}
