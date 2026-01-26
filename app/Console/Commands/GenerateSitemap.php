<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Page;
use App\Models\News;
use App\Models\Project;
use Modules\Core\Models\Brand;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\Product; // Adjust namespace if needed
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'seo:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml file via Spatie Sitemap';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Sitemap Generation...');
        $startTime = microtime(true);

        $sitemap = Sitemap::create();

        // 1. Static Pages (Home, key routes)
        // Add Home manually to ensure it's top priority
        $sitemap->add(Url::create('/')
            ->setLastModificationDate(Carbon::now())
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
            ->setPriority(1.0));

        // 2. Dynamic Pages (from Page model)
        // Filter: Published + NOT deleted + NOT noindex
        $this->info('Processing Pages...');
        Page::query()
            ->where('status', 'published') // Assuming 'status' column exists, adjust if needed
            ->with('seo')
            ->chunk(100, function ($pages) use ($sitemap) {
                foreach ($pages as $page) {
                    if ($this->shouldIndex($page)) {
                        $url = $page->is_homepage ? '/' : "/{$page->slug}";
                        $priority = $page->is_homepage ? 1.0 : 0.8;

                        $sitemap->add(Url::create($url)
                            ->setLastModificationDate($page->updated_at)
                            ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                            ->setPriority($priority));
                    }
                }
            });

        // 3. News / Articles
        $this->info('Processing News...');
        if (class_exists(News::class)) {
            News::query()
                ->where('status', 'published')
                ->with('seo')
                ->chunk(100, function ($items) use ($sitemap) {
                    foreach ($items as $item) {
                        if ($this->shouldIndex($item)) {
                            $sitemap->add(Url::create("/news/{$item->slug}")
                                ->setLastModificationDate($item->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.7));
                        }
                    }
                });
        }

        // 4. Projects
        $this->info('Processing Projects...');
        if (class_exists(Project::class)) {
            Project::query()
                ->where('status', 'published')
                ->with('seo')
                ->chunk(100, function ($items) use ($sitemap) {
                    foreach ($items as $item) {
                        if ($this->shouldIndex($item)) {
                            $sitemap->add(Url::create("/project/{$item->slug}") // Verify route prefix
                                ->setLastModificationDate($item->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.6));
                        }
                    }
                });
        }

        // 5. Services
        $this->info('Processing Services...');
        if (class_exists(Service::class)) {
             Service::query()
                ->with('seo')
                ->chunk(100, function ($items) use ($sitemap) {
                    foreach ($items as $item) {
                        if ($this->shouldIndex($item)) {
                            $sitemap->add(Url::create("/services/{$item->slug}")
                                ->setLastModificationDate($item->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.8));
                        }
                    }
                });
        }

        // 6. Products (from ServiceSolutions/Core modules)
        // Need to check exact namespace/model for 'Products' mentioned in requirement
        // Assuming App\Models\Product or Modules\ServiceSolutions\Models\Product based on previous context
        // I will use a generic check
        $productModel = 'App\\Models\\Product';
        if (!class_exists($productModel)) {
            $productModel = 'Modules\\ServiceSolutions\\Models\\Product';
        }

        if (class_exists($productModel)) {
            $this->info('Processing Products...');
            $productModel::query()
                ->where('is_active', true)
                ->with('seo')
                ->chunk(100, function ($items) use ($sitemap) {
                    foreach ($items as $item) {
                        if ($this->shouldIndex($item)) {
                             // Assuming route is /products/{slug}
                            $sitemap->add(Url::create("/products/{$item->slug}")
                                ->setLastModificationDate($item->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.7));
                        }
                    }
                });
        }

        // 7. Brand Landing pages
        if (class_exists(Brand::class)) {
             $this->info('Processing Brands...');
             Brand::query()
                ->with('seo')
                ->chunk(100, function ($items) use ($sitemap) {
                    foreach ($items as $item) {
                        if ($this->shouldIndex($item)) {
                            // Assuming brands have a listing or detail page.
                            // If they are just landing pages, maybe /brand/{slug}
                            $sitemap->add(Url::create("/brand/{$item->slug}")
                                ->setLastModificationDate($item->updated_at)
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                                ->setPriority(0.6));
                        }
                    }
                });
        }

        // Write content
        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Sitemap generated successfully at {$path} in {$duration}s.");
    }

    /**
     * Check if model should be indexed.
     */
    protected function shouldIndex($model): bool
    {
        // Check 1: Explicit NoIndex in SeoMeta
        if ($model->seo && $model->seo->noindex) {
            return false;
        }

        // Check 2: Implicit checks (e.g. empty slug, draft status - handled in query)
        if (empty($model->slug)) {
            return false;
        }

        return true;
    }
}
