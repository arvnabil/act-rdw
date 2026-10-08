<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// Models
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\NewsTag;
use Modules\Projects\Models\Project;
use Modules\Campaign\Models\Campaign;
use Modules\CMS\Models\Page;
use Modules\Services\Models\Service;
use Modules\Services\Models\Configurator;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\ProductCategory;
use Modules\Clients\Models\Client;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventCategory;
use Modules\Events\Models\Organizer;
use Modules\Events\Models\EventDocumentation;
use Modules\Events\Models\EventRegistration;
use Modules\Events\Models\EventCertificate;
use Modules\Events\Models\EventUser;
use Modules\Analytics\Models\AnalyticsClickEvent;
use Modules\Analytics\Models\AnalyticsWhatsapp;
use Modules\Settings\Models\Setting;
use Modules\Settings\Models\ApiKey;
use Modules\Menu\Models\Menu;
use Modules\FormBuilder\Models\FormSubmission;
use Modules\SEO\Models\SeoMeta;
use Modules\SEO\Models\SeoWhitelistDomain;
use Modules\AI\Models\ChatSession;
use App\Models\User;
use Spatie\Permission\Models\Role;

// Policies
use App\Policies\NewsPolicy;
use App\Policies\NewsCategoryPolicy;
use App\Policies\NewsTagPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\CampaignPolicy;
use App\Policies\PagePolicy;
use App\Policies\ServicePolicy;
use App\Policies\ConfiguratorPolicy;
use App\Policies\ProductPolicy;
use App\Policies\BrandPolicy;
use App\Policies\ProductCategoryPolicy;
use App\Policies\ClientPolicy;
use App\Policies\EventPolicy;
use App\Policies\EventCategoryPolicy;
use App\Policies\OrganizerPolicy;
use App\Policies\EventDocumentationPolicy;
use App\Policies\EventRegistrationPolicy;
use App\Policies\EventCertificatePolicy;
use App\Policies\EventUserPolicy;
use App\Policies\AnalyticsClickEventPolicy;
use App\Policies\AnalyticsWhatsappPolicy;
use App\Policies\SettingPolicy;
use App\Policies\ApiKeyPolicy;
use App\Policies\MenuPolicy;
use App\Policies\FormSubmissionPolicy;
use App\Policies\SeoMetaPolicy;
use App\Policies\SeoWhitelistDomainPolicy;
use App\Policies\ChatSessionPolicy;
use App\Policies\UserPolicy;
use App\Policies\RolePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // News module
        News::class         => NewsPolicy::class,
        NewsCategory::class => NewsCategoryPolicy::class,
        NewsTag::class      => NewsTagPolicy::class,

        // Projects module
        Project::class => ProjectPolicy::class,

        // Campaign module
        Campaign::class => CampaignPolicy::class,

        // CMS module
        Page::class => PagePolicy::class,

        // Services module
        Service::class      => ServicePolicy::class,
        Configurator::class => ConfiguratorPolicy::class,

        // Product Catalog module
        Product::class         => ProductPolicy::class,
        Brand::class           => BrandPolicy::class,
        ProductCategory::class => ProductCategoryPolicy::class,

        // Clients module
        Client::class => ClientPolicy::class,

        // Events module
        Event::class             => EventPolicy::class,
        EventCategory::class     => EventCategoryPolicy::class,
        Organizer::class         => OrganizerPolicy::class,
        EventDocumentation::class => EventDocumentationPolicy::class,
        EventRegistration::class => EventRegistrationPolicy::class,
        EventCertificate::class  => EventCertificatePolicy::class,
        EventUser::class         => EventUserPolicy::class,

        // Analytics module
        AnalyticsClickEvent::class => AnalyticsClickEventPolicy::class,
        AnalyticsWhatsapp::class   => AnalyticsWhatsappPolicy::class,

        // Settings module
        Setting::class => SettingPolicy::class,
        ApiKey::class  => ApiKeyPolicy::class,

        // Menu module
        Menu::class => MenuPolicy::class,

        // Form Builder module
        FormSubmission::class => FormSubmissionPolicy::class,

        // SEO module
        SeoMeta::class            => SeoMetaPolicy::class,
        SeoWhitelistDomain::class => SeoWhitelistDomainPolicy::class,

        // AI module
        ChatSession::class => ChatSessionPolicy::class,

        // User & Role management
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}



