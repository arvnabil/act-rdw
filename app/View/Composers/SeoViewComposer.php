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

        // Try to find the primary model in Inertia props
        $model = $props['model'] ?? $props['page'] ?? $props['post'] ?? $props['service'] ?? $props['project'] ?? $props['brand'] ?? null;

        if ($model instanceof Model) {
            $this->processModel($model);
        } else {
            $this->processStatic($view, $props);
        }

        // Add global LocalBusiness schema (from settings)
        $this->addLocalBusinessSchema();

        $view->with('seoManager', $this->seoManager);
    }

    protected function processStatic(View $view, array $props): void
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

    protected function processModel(Model $model): void
    {
        $data = $this->seoService->extract($model);

        // Always add WebPage
        $this->seoManager->addSchema(new WebPageSchema(
            title: $data['title'],
            description: $data['description'],
            url: $data['url'],
            websiteId: $this->seoManager->getWebsiteId(),
            mainEntityId: $this->resolveMainEntityId($data)
        ));

        // Article Schema
        if ($data['type'] === 'Article' && isset($data['article'])) {
            $this->seoManager->addSchema(new ArticleSchema(
                headline: $data['title'],
                description: $data['description'],
                url: $data['url'],
                image: $data['og_image'],
                author: ['@type' => 'Organization', 'name' => config('app.name')],
                publisher: ['@id' => $this->seoManager->getOrganizationId()],
                datePublished: $data['article']['datePublished'],
                dateModified: $data['article']['dateModified'],
                keywords: $data['keywords'],
                articleSection: $data['article']['section']
            ));
        }

        // Service Schema
        if ($data['type'] === 'Service' && isset($data['service'])) {
            $this->seoManager->addSchema(new ServiceSchema(
                name: $data['title'],
                description: $data['description'],
                url: $data['url'],
                image: $data['og_image'],
                provider: ['@id' => $this->seoManager->getOrganizationId()],
                offers: $data['service']['offers']
            ));
        }

        // FAQ Schema (auto-detect from sections)
        $this->addFaqSchema($model);

        // Review Schema (auto-detect from sections or testimonials)
        $this->addReviewSchema($model);

        // Breadcrumbs
        $this->seoManager->addSchema(new BreadcrumbSchema(
            $this->generateBreadcrumbs($model, $data['url'])
        ));
    }

    protected function addFaqSchema(Model $model): void
    {
        $faqItems = [];

        // Priority 1: Check if model has sections with FAQ type
        if (method_exists($model, 'sections') || isset($model->sections)) {
            $sections = $model->sections ?? collect();
            foreach ($sections as $section) {
                $type = strtolower($section->type ?? '');
                if (in_array($type, ['faq', 'faqs', 'questions', 'accordion'])) {
                    $items = $section->settings['items'] ?? $section->settings['faqs'] ?? $section->settings['questions'] ?? [];
                    $faqItems = array_merge($faqItems, $items);
                }
            }
        }

        // Priority 2: Check seo_meta for manual override
        if (empty($faqItems) && $model->seo?->faq_items) {
            $faqItems = $model->seo->faq_items;
        }

        if (!empty($faqItems)) {
            $schema = new FaqSchema($faqItems);
            $result = $schema->toArray();
            if (!empty($result)) {
                $this->seoManager->addSchema($schema);
            }
        }
    }

    protected function addReviewSchema(Model $model): void
    {
        $reviews = [];

        // Check if model has sections with testimonial type
        if (method_exists($model, 'sections') || isset($model->sections)) {
            $sections = $model->sections ?? collect();
            foreach ($sections as $section) {
                $type = strtolower($section->type ?? '');
                if (in_array($type, ['testimonial', 'testimonials', 'review', 'reviews'])) {
                    $items = $section->settings['items'] ?? $section->settings['testimonials'] ?? $section->settings['reviews'] ?? [];
                    $reviews = array_merge($reviews, $items);
                }
            }
        }

        if (!empty($reviews)) {
            $schema = new ReviewSchema(
                itemName: $model->title ?? $model->name ?? config('app.name'),
                itemUrl: $model->url ?? url()->current(),
                reviews: $reviews
            );
            $result = $schema->toArray();
            if (!empty($result)) {
                $this->seoManager->addSchema($schema);
            }
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

    protected function resolveMainEntityId(array $data): ?string
    {
        if ($data['type'] === 'Article') return $data['url'] . '#article';
        if ($data['type'] === 'Service') return $data['url'] . '#service';
        return null;
    }

    protected function generateBreadcrumbs(Model $model, string $currentUrl): array
    {
        $baseUrl = config('app.url');
        $crumbs = [['name' => 'Home', 'item' => $baseUrl]];

        if ($model instanceof \App\Models\News) {
            $crumbs[] = ['name' => 'News', 'item' => $baseUrl . '/news'];
        } elseif ($model instanceof \Modules\ServiceSolutions\Models\Service) {
            $crumbs[] = ['name' => 'Services', 'item' => $baseUrl . '/services'];
        } elseif ($model instanceof \App\Models\Project) {
            $crumbs[] = ['name' => 'Projects', 'item' => $baseUrl . '/projects'];
        }

        $crumbs[] = ['name' => $model->title ?? $model->name, 'item' => $currentUrl];

        return $crumbs;
    }
}
