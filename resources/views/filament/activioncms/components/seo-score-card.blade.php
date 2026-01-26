@php
    $record = $getRecord();
    $score = $record?->seo_score ?? 0;

    // Live calculation if record is not saved yet or form updated?
    // Actually, for "real-time", we need to recalculate based on *form data*, not just saved record.
    // But since `SeoScoreCalculator` needs a model instance, we can dehydrate form data into a temporary model instance.
    // For now, let's display the *saved* score, or "Save to see score" if new.
// Improving: We can try to calculate based on $get() values.

$formData = [
    'title' => $get('title'),
    'description' => $get('description'),
    'keywords' => $get('keywords'),
    'og_image' => $get('og_image'),
    'og_title' => $get('og_title'),
    'og_description' => $get('og_description'),
    'canonical_url' => $get('canonical_url'),
    'noindex' => $get('noindex'),
    'twitter_card' => $get('twitter_card'),
];

// Create temporary model for calculation
$tempModel = new \App\Models\SeoMeta($formData);
$calculation = \App\Services\Seo\SeoScoreCalculator::calculate($tempModel);
$currentScore = $calculation['score'];
$checks = $calculation['checks'];

// Color logic
$color = 'text-red-600 bg-red-50 border-red-200';
$barColor = 'bg-red-500';
if ($currentScore >= 50 && $currentScore < 80) {
    $color = 'text-yellow-600 bg-yellow-50 border-yellow-200';
    $barColor = 'bg-yellow-500';
} elseif ($currentScore >= 80) {
    $color = 'text-green-600 bg-green-50 border-green-200';
    $barColor = 'bg-green-500';
    }
@endphp

<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:bg-gray-900 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">SEO Score</h3>
        <span class="px-3 py-1 rounded-full text-sm font-bold border {{ $color }}">
            {{ $currentScore }}/100
        </span>
    </div>

    <!-- Progress Bar -->
    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-6 dark:bg-gray-700">
        <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500"
            style="width: {{ $currentScore }}%"></div>
    </div>

    <!-- Checklist -->
    <div class="space-y-3">
        @foreach ($checks as $key => $check)
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex-shrink-0">
                    @if ($check['pass'])
                        <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    @endif
                </div>
                <div class="text-sm">
                    <p
                        class="font-medium {{ $check['pass'] ? 'text-gray-900 dark:text-gray-100' : 'text-red-700 dark:text-red-400' }}">
                        {{ $check['message'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
