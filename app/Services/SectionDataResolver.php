<?php

namespace App\Services;

use App\Models\PageSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SectionDataResolver
{
    /*
    |--------------------------------------------------------------------------
    | CORE SYSTEM FILE - DO NOT EDIT WITHOUT ARCHITECTURE REVIEW
    |--------------------------------------------------------------------------
    | This file is the bridge between Database Config and Frontend Props.
    | Changing keys here will BREAK the Frontend.
    */
    public function resolve(PageSection $section): array
    {
        $key = $section->section_key;
        $config = $section->config ?? [];

        // Common props from config
        $common = [
            'id' => 'section-' . $section->id,
            'className' => $config['className'] ?? '',
        ];

        $data = match ($key) {
            'slider' => $this->resolveSlider($config),
            'about' => $this->resolveAbout($config),
            'services' => $this->resolveServices($config),
            'why_choose_us' => $this->resolveWhyChooseUs($config),
            'projects' => $this->resolveProjects($config),
            'brand_partners' => $this->resolveBrandPartners($config),
            'clients' => $this->resolveClients($config),
            'cta_clients' => $this->resolveClients($config), // Alias
            'news' => $this->resolveNews($config),

            // Legacy About Sections (Pass-through config)
            'about_content' => $config,
            'vision_mission' => $config,
            'testimonial' => $config,
            'contact' => $this->resolveContact($config),

            // Fallback
            'brands' => $this->resolveBrandPartners($config),
            'cta' => $this->resolveClients($config),

            // Services Page Specific
            'service_list' => $this->resolveServices($config),
            'service_clients' => $this->resolveClients($config),
            'service_solution' => $config,
            'service_cta' => $config,

            default => $config,
        };

        return [
            'id' => 'section-' . $section->id,
            'section_key' => $key,
            'props' => array_merge($common, $data),
        ];
    }

    protected function resolveSlider(array $config): array
    {
        // Pure Hero functionality now preferred, but keeping variant support if needed
        return [
            'variant' => $config['variant'] ?? 'hero',
            'slides' => $config['slides'] ?? [],
            'delay' => $config['delay'] ?? 5000,
            // If variant is brand, we might need brand logic here,
            // but we are moving to brand_partners.
            // Leaving legacy support just in case:
            'brands' => $config['brands'] ?? [],
        ];
    }

    protected function resolveAbout(array $config): array
    {
        return [
            'title' => $config['title'] ?? null,
            'subtitle' => $config['subtitle'] ?? null,
            'description' => $config['description'] ?? null,
            'images' => $config['images'] ?? [],
            'features' => $config['features'] ?? [],
            'button_text' => $config['button_text'] ?? null,
            'button_url' => $config['button_url'] ?? null,
            'show_button' => $config['show_button'] ?? true,
            'variant' => $config['variant'] ?? 'default',
        ];
    }

    protected function resolveServices(array $config): array
    {
        $limit = $config['limit'] ?? 12; // Increased from 6 to 12
        $order = $config['order'] ?? 'asc';
        $services = collect([]);
        $total = 0;

        // 1. Prioritize Services Table (Parent/Category)
        if (Schema::hasTable('services')) {
             $query = DB::table('services');
             $total = $query->count();
             
             $query->select('id', 'name', 'slug', 'thumbnail', 'icon', 'description');

            if ($order === 'desc') $query->orderBy('id', 'desc');
            else $query->orderBy('id', 'asc');

            $services = $query->limit($limit)->get();
        }

        // 2. Fallback to Service Solutions ONLY if Services is empty/missing
        if ($services->isEmpty() && Schema::hasTable('service_solutions')) {
            $query = DB::table('service_solutions');
            $total = $query->count();
            
            $query->select('id', 'title as name', 'slug', 'thumbnail', 'description', DB::raw('NULL as icon'));

            if ($order === 'desc') $query->orderBy('id', 'desc');
            else $query->orderBy('id', 'asc');

            $services = $query->limit($limit)->get();
        }

        // 3. Process Paths & Icons (Smart Logic)
        $services = $services->map(function($s) {
            // Smart Path helper
            $resolvePath = function($path) {
                if (!$path) return null;
                if (str_starts_with($path, 'http')) return $path;
                if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                    return str_starts_with($path, '/') ? $path : "/{$path}";
                }
                return "/storage/{$path}";
            };

            $s->thumbnail = $resolvePath($s->thumbnail);

            // Icon handling: Use processed icon path OR default asset
            $s->icon = $resolvePath($s->icon) ?? '/assets/img/icon/service_1_1.svg';

            return $s;
        });

        return [
            'title' => $config['title'] ?? null,
            'subtitle' => $config['subtitle'] ?? null,
            'cta_text' => $config['cta_text'] ?? 'Lihat Selengkapnya',
            'cta_url' => $config['cta_url'] ?? '/services',
            'services' => $services,
            'has_more' => $total > $limit,
        ];
    }

    protected function resolveWhyChooseUs(array $config): array
    {
        return [
            'title' => $config['title'] ?? null,
            'subtitle' => $config['subtitle'] ?? null,
            'description' => $config['description'] ?? null,
            'features' => $config['features'] ?? [],
            'images' => $config['images'] ?? [],
            'video_url' => $config['video_url'] ?? null,
            'show_button' => $config['show_button'] ?? true,
            'button_text' => $config['button_text'] ?? 'Pelajari Selengkapnya',
            'button_url' => $config['button_url'] ?? '/about',
        ];
    }

    protected function resolveProjects(array $config): array
    {
        $limit = $config['limit'] ?? 8;
        $projects = [];

        if (Schema::hasTable('projects')) {
            $projects = DB::table('projects')
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($p) {
                    return [
                        'title' => $p->title,
                        'subtitle' => $p->title,
                        'image' => (function($path) {
                            if (!$path) return null;
                            if (str_starts_with($path, 'http')) return $path;
                            if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                                return str_starts_with($path, '/') ? $path : "/{$path}";
                            }
                            return "/storage/{$path}";
                        })($p->thumbnail),
                        'link' => "/projects/{$p->slug}",
                        'category' => $p->is_featured ? 'Featured' : 'Project'
                    ];
                });
        }

        return [
            'title' => ($config['title'] ?? null),
            'subtitle' => $config['subtitle'] ?? null,
            'cta_text' => $config['cta_text'] ?? null,
            'cta_url' => $config['cta_url'] ?? null,
            'projects' => $projects,
        ];
    }

    protected function resolveBrandPartners(array $config): array
    {
        $limit = $config['limit'] ?? 10;
        $brands = [];

        // Dynamic Query
        // Dynamic Query
        if (Schema::hasTable('brands')) {
             $dbBrands = DB::table('brands')
                ->select('image', 'name')
                // ->where('is_active', true) // Assuming is_active column exists? verify later.
                ->limit($limit)
                ->get()
                ->map(function($b) {
                    return [
                        'image' => (function($path) {
                            if (!$path) return null;
                            if (str_starts_with($path, 'http')) return $path;
                            if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                                return str_starts_with($path, '/') ? $path : "/{$path}";
                            }
                            return "/storage/{$path}";
                        })($b->image) ?? "/assets/default.png",
                        'name' => $b->name
                    ];
                })
                ->toArray();

             $brands = array_merge($brands, $dbBrands);
        }


        return [
            'id' => 'section-brand-partners', // Specific ID for scrolling if needed
            'title' => $config['title'] ?? null,
            'subtitle' => $config['subtitle'] ?? null,
            'show_button' => $config['show_button'] ?? true, // Default to true if not set
            'button_text' => $config['button_text'] ?? 'Lihat Semua Partner',
            'button_url' => $config['button_url'] ?? '/partners',
            'brands' => $brands,
        ];
    }

    protected function resolveClients(array $config): array
    {
        $limit = $config['limit'] ?? 10;
        $clients = [];

        if (Schema::hasTable('clients')) {
             $clients = DB::table('clients')
                ->select('logo', 'name', 'website_url')
                ->where('is_active', true)
                ->orderBy('position', 'asc')
                ->limit($limit)
                ->get()
                ->map(function($c) {
                     return [
                        'image' => (function($path) {
                            if (!$path) return null;
                            if (str_starts_with($path, 'http')) return $path;
                            if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                                return str_starts_with($path, '/') ? $path : "/{$path}";
                            }
                            return "/storage/{$path}";
                        })($c->logo) ?? "/assets/default.png",
                        'name' => $c->name,
                        'url' => $c->website_url ?? '#'
                    ];
                })
                ->toArray();
        }

        return [
            'title' => $config['title'] ?? null,
            'subtitle' => $config['subtitle'] ?? null,
            'show_button' => $config['show_button'] ?? false,
            // Fallback to cta_text/url for legacy sections
            'button_text' => $config['button_text'] ?? $config['cta_text'] ?? 'Lihat Semua Klien',
            'button_url' => $config['button_url'] ?? $config['cta_url'] ?? '/clients',
            'clients' => $clients,
        ];
    }

    protected function resolveNews(array $config): array
    {
        $limit = $config['limit'] ?? 3;
        $posts = [];

        if (Schema::hasTable('news')) { // Table 'news' or 'posts'? Previous code (Step 921) used 'posts'.
            // Checking models... App/Models/News.php exists. Table is usually 'news'.
            // Step 921 code checked 'posts'.
            // I'll check 'news' first then 'posts'.
            $table = 'news';
            if (!Schema::hasTable($table) && Schema::hasTable('posts')) {
                $table = 'posts';
            }

            if (Schema::hasTable($table)) {
                  $posts = DB::table($table)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->limit($limit)
                    ->get()
                    ->map(function($p) {
                        // Handle renamed column (thumbnail -> featured_image)
                        $thumbnail = $p->featured_image ?? $p->thumbnail ?? null;

                        return [
                            'title' => $p->title,
                            'slug' => $p->slug,
                            'thumbnail' => (function($path) {
                                if (!$path) return null;
                                if (str_starts_with($path, 'http')) return $path;
                                if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                                    return str_starts_with($path, '/') ? $path : "/{$path}";
                                }
                                return "/storage/{$path}";
                            })($thumbnail),
                            'image' => (function($path) { // standardized
                                if (!$path) return null;
                                if (str_starts_with($path, 'http')) return $path;
                                if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                                    return str_starts_with($path, '/') ? $path : "/{$path}";
                                }
                                return "/storage/{$path}";
                            })($thumbnail),
                            'published_at' => $p->published_at,
                            'published_at_formatted' => $p->published_at ? date('M d, Y', strtotime($p->published_at)) : '',
                            'read_time' => '5 min read',
                            'link' => "/{$p->slug}" // Dynamic resolving
                        ];
                    });
            }
        }

        return [
            'title' => $config['title'] ?? null,
            'subtitle' => $config['subtitle'] ?? null,
            'cta_text' => $config['cta_text'] ?? null,
            'cta_url' => $config['cta_url'] ?? null,
            'posts' => $posts,
        ];
    }

    protected function resolveContact(array $config): array
    {
        $defaults = [
            'title' => "Contact Information",
            'subtitle' => "Let's Collaborate",
            'intro_text' => "We appreciate your interest in ACTiV. Our team is ready to discuss how we can support your business goals. Please contact us via the channels below.",
            'phone' => "(+62) 2150110987",
            'whatsapp' => "(+62) 851-6299-4602",
            'email' => "sales@activ.co.id",
            'address_office' => "Infinity Office, Belleza BSA 1st Floor Unit 106, Jl. Letjen Soepeno, Keb. Lama Jakarta Selatan 12210",
            'address_representative' => "Ruko Golden Boulevard Blok S 28, Jl Pahlawan Seribu, BSD City, Kec. , Tangerang, 15119, Kota Tangerang Selatan, Banten 15119",
            'map_embed_url' => "https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d9894.100972827648!2d106.66287000000001!3d-6.276524!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fb227ca11a57%3A0x1d99e2c09955d44d!2sPT%20Alfa%20Cipta%20Teknologi%20Virtual%20(ACTiV)!5e1!3m2!1sid!2sid!4v1765353648986!5m2!1sid!2sid",
            'enable_recaptcha' => true,
            'form_fields' => [
                [
                    'label' => "Your Name",
                    'name' => "name",
                    'type' => "text",
                    'placeholder' => "Your Name",
                    'width' => "col-sm-6",
                    'required' => "true",
                    'icon' => "/assets/img/icon/user.svg",
                ],
                [
                    'label' => "Email Address",
                    'name' => "email",
                    'type' => "email",
                    'placeholder' => "Email Address",
                    'width' => "col-sm-6",
                    'required' => "true",
                    'icon' => "/assets/img/icon/mail.svg",
                ],
                [
                    'label' => "Phone Number",
                    'name' => "number",
                    'type' => "tel",
                    'placeholder' => "Phone Number",
                    'width' => "col-sm-6",
                    'required' => "false",
                    'icon' => "/assets/img/icon/call.svg",
                ],
                [
                    'label' => "Subject",
                    'name' => "subject",
                    'type' => "select",
                    'options' => "Quotation,Technical Support,Billing",
                    'placeholder' => "Select Subject",
                    'width' => "col-sm-6",
                    'required' => "true",
                ],
                [
                    'label' => "Your Message",
                    'name' => "message",
                    'type' => "textarea",
                    'placeholder' => "Your Message",
                    'width' => "col-12",
                    'required' => "true",
                    'icon' => "/assets/img/icon/chat.svg",
                ],
            ]
        ];

        $resolved = array_merge($defaults, $config);

        // Safety: If form_fields ends up empty (e.g. empty array in DB), force defaults
        if (empty($resolved['form_fields'])) {
            $resolved['form_fields'] = $defaults['form_fields'];
        }

        return $resolved;
    }
}
