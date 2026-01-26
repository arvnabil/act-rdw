@php
    $title = $get('title');
    $description = $get('description');

    // Attempt to find slug from parent/sibling fields
    $slug = $get('../../slug') ?? ($get('../slug') ?? ($get('slug') ?? 'sample-page'));
    $appUrl = config('app.url');
    $canonical = $get('canonical_url');

    // Construct display URL: defined canonical OR app_url/slug
    $url = filled($canonical) ? $canonical : rtrim($appUrl, '/') . '/' . ltrim($slug, '/');

    $siteName = config('app.name');
@endphp

<div x-data="{
    serpData: {
        title: @js($title),
        description: @js($description),
        url: @js($url),
        siteName: @js($siteName)
    },
    init() {
        // Watch for changes in Livewire data via $wire.entangle if possible,
        // OR simpler: watch the individual fields if we can access them.
        // Since this is a View Component in a Form, we might not have direct access to other fields easily via pure Alpine
        // unless we inject them.
        // BETTER APPROACH: Use `state` paths.

        // Dispatch initial data
        this.dispatchUpdate();

        // Watch specific Livewire properties if this component is inside the form context
        // Actually, Filament's `get()` in the PHP block gets the *initial* state or server state.
        // For real-time updates without server roundtrips, we rely on Filament's mechanism.
        // If the fields are `live()`, the server re-renders this view.
        // So every time this view re-renders, we should emit the new data.
    },
    dispatchUpdate() {
        window.dispatchEvent(new CustomEvent('serp-data-updated', {
            detail: this.serpData
        }));
    }
}" x-init="init()" x-effect="dispatchUpdate()" class="w-full">
    <div id="react-serp-root" wire:ignore
        class="min-h-[100px] border border-dashed border-gray-300 p-4 rounded bg-gray-50 flex items-center justify-center text-gray-500">
        Waiting for React Component... (If this persists, check Console)
    </div>
</div>
