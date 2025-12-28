<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            <div class="flex-1">
                <h2 class="grid flex-1 gap-y-1 text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Event Module Features
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Welcome to the Event Module! Here you can manage your entire event lifecycle provided by the
                    Activion CMS.
                </p>
            </div>
        </div>

        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-x-2 text-primary-600 font-semibold mb-2"
                    style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--primary-600);">
                    <x-heroicon-o-calendar-days class="w-5 h-5" style="width: 1.5rem; height: 1.5rem;" />
                    Event Management
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Create and manage events starting from Draft, Published, to Archived. Handle speakers, prices, and
                    schedules easily.
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-x-2 text-primary-600 font-semibold mb-2"
                    style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--primary-600);">
                    <x-heroicon-o-users class="w-5 h-5" style="width: 1.5rem; height: 1.5rem;" />
                    Registrations
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Track participants, verify payments (with proof upload), and manage approval workflows seamlessly.
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-x-2 text-primary-600 font-semibold mb-2"
                    style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--primary-600);">
                    <x-heroicon-o-academic-cap class="w-5 h-5" style="width: 1.5rem; height: 1.5rem;" />
                    Certificates
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    Automatically generate PDF certificates for participants upon event completion using customizable
                    templates.
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-x-2 text-primary-600 font-semibold mb-2"
                    style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; color: var(--primary-600);">
                    <x-heroicon-o-presentation-chart-line class="w-5 h-5" style="width: 1.5rem; height: 1.5rem;" />
                    Analytics Dashboard
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    View real-time insights on active events, revenue, and registration trends via the dedicated Event
                    Dashboard.
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
