<div class="space-y-4">
    <div class="flex items-center justify-between p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
        <div>
            <h3 class="text-lg font-bold">SEO Score</h3>
            <p class="text-sm text-gray-500">Based on Meta Tags and JSON-LD structure</p>
        </div>
        <div
            class="text-3xl font-black {{ $audit['score'] >= 90 ? 'text-green-600' : ($audit['score'] >= 70 ? 'text-yellow-500' : 'text-red-500') }}">
            {{ $audit['score'] }}
        </div>
    </div>

    @if (count($audit['issues']) > 0)
        <div class="space-y-2">
            @foreach ($audit['issues'] as $issue)
                <div
                    class="flex items-start gap-3 p-3 border rounded-md border-{{ $issue['severity'] === 'error' ? 'red-200 bg-red-50' : ($issue['severity'] === 'warning' ? 'yellow-200 bg-yellow-50' : 'blue-200 bg-blue-50') }} dark:bg-transparent">
                    <x-filament::icon :icon="$issue['severity'] === 'error' ? 'heroicon-o-x-circle' : ($issue['severity'] === 'warning' ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-information-circle')"
                        class="w-5 h-5 mt-0.5 {{ $issue['severity'] === 'error' ? 'text-red-500' : ($issue['severity'] === 'warning' ? 'text-yellow-500' : 'text-blue-500') }}" />
                    <div>
                        <p
                            class="font-medium {{ $issue['severity'] === 'error' ? 'text-red-700' : ($issue['severity'] === 'warning' ? 'text-yellow-700' : 'text-blue-700') }}">
                            {{ $issue['message'] }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Recommendation: {{ $issue['recommendation'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div
            class="flex flex-col items-center justify-center p-6 text-center rounded-lg bg-green-50 dark:bg-green-900/20">
            <x-filament::icon icon="heroicon-o-check-badge" class="w-12 h-12 text-green-500" />
            <p class="mt-2 font-medium text-green-700 dark:text-green-400">Perfect! No issues found.</p>
        </div>
    @endif
</div>
