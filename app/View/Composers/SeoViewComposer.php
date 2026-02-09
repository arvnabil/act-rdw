<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Setting;
use App\Services\Seo\SeoManager;
use App\Services\Seo\SeoService;
use App\Services\Seo\Schemas\WebPageSchema;
use App\Services\Seo\Schemas\ArticleSchema;
use App\Services\Seo\Schemas\BreadcrumbSchema;
use App\Services\Seo\Schemas\ServiceSchema;
use App\Services\Seo\Schemas\FaqSchema;
use App\Services\Seo\Schemas\LocalBusinessSchema;
use App\Services\Seo\Schemas\ReviewSchema;
use Illuminate\Database\Eloquent\Model;

class SeoViewComposer
{
    public function __construct(
        protected SeoManager $seoManager,
        protected SeoService $seoService
    ) {}

    public function compose(View $view): void
    {
        $page = $view->page ?? [];
        $props = $page['props'] ?? [];

        // 1. Process Global Auto-detected Schemas (from Section Builder)
        $this->addFaqSchema($props);
        $this->addReviewSchema($props);
        $this->addLocalBusinessSchema();

        // 2. Add Static Fallback if no specific schemas were registered by Controller
        if (!$this->seoManager->hasSchemas()) {
            $this->processStatic($props);
        }

        $view->with('seoManager', $this->seoManager);
    }

    protected function processStatic(array $props): void
    {
        $seoData = $props['seo']['tags'] ?? [];
        $title = $seoData['title'] ?? config('app.name');
        $description = $seoData['description'] ?? '';
        $url = url()->current();

        $this->seoManager->addSchema(new WebPageSchema(
            title: $title,
            description: $description,
            url: $url,
            websiteId: $this->seoManager->getWebsiteId()
        ));

        $this->seoManager->addSchema(new BreadcrumbSchema([
            ['name' => 'Home', 'item' => config('app.url')],
            ['name' => $title, 'item' => $url]
        ]));
    }

    protected function addFaqSchema(array $props): void
    {
        $faqItems = [];
        $sections = $props['sections'] ?? [];

        foreach ($sections as $section) {
            $type = strtolower($section['type'] ?? '');
            if (in_array($type, ['faq', 'faqs', 'questions', 'accordion'])) {
                $items = $section['settings']['items'] ?? $section['settings']['faqs'] ?? $section['settings']['questions'] ?? [];
                $faqItems = array_merge($faqItems, $items);
            }
        }

        if (!empty($faqItems)) {
            $this->seoManager->addSchema(new FaqSchema($faqItems));
        }
    }

    protected function addReviewSchema(array $props): void
    {
        $reviews = [];
        $sections = $props['sections'] ?? [];

        foreach ($sections as $section) {
            $type = strtolower($section['type'] ?? '');
            if (in_array($type, ['testimonial', 'testimonials', 'review', 'reviews'])) {
                $items = $section['settings']['items'] ?? $section['settings']['testimonials'] ?? $section['settings']['reviews'] ?? [];
                $reviews = array_merge($reviews, $items);
            }
        }

        if (!empty($reviews)) {
            $title = $props['page']['title'] ?? $props['post']['title'] ?? $props['service']['title'] ?? config('app.name');
            $url = $props['page']['url'] ?? $props['post']['url'] ?? $props['service']['url'] ?? url()->current();

            $this->seoManager->addSchema(new ReviewSchema(
                itemName: $title,
                itemUrl: $url,
                reviews: $reviews
            ));
        }
    }

    protected function addLocalBusinessSchema(): void
    {
        // Only add on homepage
        $currentPath = parse_url(url()->current(), PHP_URL_PATH);
        if ($currentPath !== '/' && $currentPath !== '') {
            return;
        }

        try {
            $settings = Setting::whereIn('key', [
                'company_name', 'company_address', 'company_city', 'company_province',
                'company_postal_code', 'company_phone', 'company_email', 'company_logo'
            ])->pluck('value', 'key');

            if ($settings->isEmpty()) {
                return;
            }

            $this->seoManager->addSchema(new LocalBusinessSchema(
                name: $settings['company_name'] ?? config('app.name'),
                url: config('app.url'),
                logo: !empty($settings['company_logo']) ? asset('storage/' . $settings['company_logo']) : null,
                address: $settings['company_address'] ?? null,
                city: $settings['company_city'] ?? null,
                province: $settings['company_province'] ?? null,
                postalCode: $settings['company_postal_code'] ?? null,
                phone: $settings['company_phone'] ?? null,
                email: $settings['company_email'] ?? null
            ));
        } catch (\Exception $e) {
            // Silently skip if settings table doesn't exist or has issues
        }
    }
}
