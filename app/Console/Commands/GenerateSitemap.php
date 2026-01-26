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
    public function handle(\App\Services\ActivePublicRouteDetector $detector)
    {
        $this->info('Starting Sitemap Generation...');
        $startTime = microtime(true);

        $sitemap = Sitemap::create();

        // Use the centralized detector service to get all valid public routes
        $routes = $detector->detect();

        $this->info("Detected {$routes->count()} public routes. Adding to sitemap...");

        foreach ($routes as $route) {
            $sitemap->add(Url::create($route['url'])
                ->setChangeFrequency($route['changefreq'])
                ->setPriority($route['priority']));
        }

        // Write content
        $path = public_path('sitemap.xml');
        $sitemap->writeToFile($path);

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Sitemap generated successfully at {$path} in {$duration}s.");
    }
}
