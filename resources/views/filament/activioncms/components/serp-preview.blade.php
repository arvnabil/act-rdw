@php
    // Get live values from form state
    $title = $get('title');
    $description = $get('description');
    $canonical = $get('canonical_url');
    $siteName = config('app.name');

    // Default placeholders
    $defaultTitle = 'Page Title Preview | ' . $siteName;
    $defaultDesc =
        'Please enter a meta description to see how it looks in search results. Google typically displays up to 160 characters.';
    $defaultUrl = config('app.url') . '/sample-slug';

    // Final values for display
    $displayTitle = filled($title) ? $title : $defaultTitle;
    $displayDesc = filled($description) ? $description : $defaultDesc;
    $displayUrl = filled($canonical) ? $canonical : $defaultUrl;
@endphp

<div x-data="{ mode: 'desktop' }"
    class="rounded-xl bg-gray-50 border border-gray-200 dark:bg-gray-900 dark:border-gray-700 overflow-hidden">
    <!-- Header / Toggle -->
    <div
        class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">Google Search Preview</h3>

        <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1 gap-1">
            <button @click="mode = 'desktop'"
                :class="mode === 'desktop' ? 'bg-white shadow-sm text-gray-900 dark:bg-gray-600 dark:text-white' :
                    'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                class="px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2" type="button">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Desktop
            </button>
            <button @click="mode = 'mobile'"
                :class="mode === 'mobile' ? 'bg-white shadow-sm text-gray-900 dark:bg-gray-600 dark:text-white' :
                    'text-gray-500 hover:text-gray-700 dark:text-gray-400'"
                class="px-3 py-1.5 text-xs font-medium rounded-md transition-all flex items-center gap-2"
                type="button">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Mobile
            </button>
        </div>
    </div>

    <div class="p-6 flex justify-center bg-gray-50 dark:bg-gray-900">

        <!-- Desktop View Container -->
        <div x-show="mode === 'desktop'"
            class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700 w-full max-w-[650px]">
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 mb-1.5">
                <div class="bg-gray-100 rounded-full p-1.5 dark:bg-gray-700 shrink-0">
                    <img src="/assets/img/favicons/favicon-32x32.png" class="w-4 h-4 object-contain"
                        style="width: 16px; height: 16px;" alt="Icon">
                </div>
                <div class="flex flex-col min-w-0">
                    <span
                        class="text-[14px] text-[#202124] dark:text-[#dadce0] leading-tight truncate font-normal">{{ $siteName }}</span>
                    <span
                        class="text-[12px] text-[#5f6368] dark:text-[#bdc1c6] leading-tight truncate">{{ $displayUrl }}</span>
                </div>
                <div class="ml-auto shrink-0">
                    <svg class="w-4 h-4 text-gray-400" style="width: 18px; height: 18px;" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path
                            d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <div class="group relative">
                <h3 class="text-[20px] text-[#1a0dab] font-normal leading-snug dark:text-[#8ab4f8] cursor-pointer hover:underline truncate"
                    style="max-width: 600px;">
                    {{ $displayTitle }}
                </h3>
            </div>

            <!-- Description -->
            <div class="mt-1">
                <p class="text-[14px] text-[#4d5156] leading-[1.58] dark:text-[#bdc1c6] line-clamp-2"
                    style="max-width: 600px;">
                    {{ $displayDesc }}
                </p>
            </div>
        </div>

        <!-- Mobile View Container -->
        <div x-show="mode === 'mobile'"
            class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700 w-full max-w-[375px]">
            <!-- Header -->
            <div class="flex items-center gap-3 mb-3">
                <div class="bg-gray-100 rounded-full p-2 dark:bg-gray-700 shrink-0">
                    <img src="/assets/img/favicons/favicon-32x32.png" class="w-4 h-4 object-contain"
                        style="width: 16px; height: 16px;" alt="Icon">
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span
                        class="text-[14px] text-[#202124] dark:text-[#dadce0] font-normal truncate">{{ $siteName }}</span>
                    <span class="text-[12px] text-[#5f6368] dark:text-[#bdc1c6] truncate">{{ $displayUrl }}</span>
                </div>
                <div class="ml-auto shrink-0">
                    <svg class="w-5 h-5 text-gray-500" style="width: 20px; height: 20px;" viewBox="0 0 24 24"
                        fill="currentColor">
                        <path
                            d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h3 class="text-[18px] text-[#1a0dab] font-normal leading-snug mb-1 dark:text-[#8ab4f8] line-clamp-2">
                {{ $displayTitle }}
            </h3>

            <!-- Description -->
            <p class="text-[14px] text-[#4d5156] leading-[1.58] dark:text-[#bdc1c6] line-clamp-3">
                {{ $displayDesc }}
            </p>
        </div>

    </div>
</div>
