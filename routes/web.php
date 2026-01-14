<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;



Route::get('/login', function () {
    return redirect()->route('filament.activioncms.auth.login');
})->name('login');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

// Services routes are now handled by Modules/ServiceSolutions

Route::get('/projects', function () {
    return Inertia::render('Projects');
})->name('projects');

Route::get('/partners', function () {
    return Inertia::render('Partners');
})->name('partners');

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

// Product and Configurator API routes are now handled by Modules/Core

// Projects Page
Route::get('/projects', function () {
    $page = request()->query('page', 1);
    $perPage = 6;
    $total = 18;
    $lastPage = ceil($total / $perPage);

    $projects = collect(range(1, $total))->map(function ($id) {
        $imgId = ($id - 1) % 9 + 1; // 1-9
        return [
            'id' => $id,
            'title' => [
                'Web & Mobile Development',
                'UiUx Design',
                'Website Design',
                'WordPress Development',
                'Game Development',
                'Python Development',
                'Java Development',
                'Php Development',
                'Digital Marketing'
            ][$id % 9],
            'subtitle' => ['Development', 'Design', 'Management', 'Project Analysis', 'Designer', 'Developer', 'Engineer', 'Backend', 'Marketing'][rand(0, 8)],
            'image' => '/assets/img/project/project-inner' . $imgId . '.jpg',
            'link' => '/projects/' . $id
        ];
    });

    $items = $projects->forPage($page, $perPage)->values();

    return Inertia::render('Projects', [
        'projects' => [
            'data' => $items,
            'current_page' => (int)$page,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total,
            'links' => [
                [
                    'url' => $page > 1 ? '/projects?page=' . ($page - 1) : null,
                    'label' => '<i class="far fa-arrow-left"></i>',
                    'active' => false,
                ],
                ...collect(range(1, $lastPage))->map(function ($p) use ($page) {
                    return [
                        'url' => '/projects?page=' . $p,
                        'label' => (string)$p,
                        'active' => $p === (int)$page,
                    ];
                }),
                [
                    'url' => $page < $lastPage ? '/projects?page=' . ($page + 1) : null,
                    'label' => '<i class="far fa-arrow-right"></i>',
                    'active' => false,
                    'className' => 'next-page'
                ],
            ]
        ]
    ]);
})->name('projects');

// Project Detail Configuration
Route::get('/projects/{id}', function ($id) {
    // Generate consistent dummy data based on ID
    srand($id);

    $titles = [
        'Cloud Infrastructure Migration',
        'Enterprise ERP Implementation',
        'Cybersecurity Audit & Hardening',
        'Mobile App Development for Retail',
        'AI-Powered Analytics Dashboard',
        'IoT Smart Building Solution',
        'Blockchain Supply Chain Platform',
        'DevOps Pipeline Automation',
        'Data Center Virtualization'
    ];

    $categories = ['Cloud Services', 'Enterprise Software', 'Security', 'Mobile Dev', 'AI & ML', 'IoT', 'Blockchain', 'DevOps', 'Infrastructure'];
    $clients = ['TechCorp Global', 'RetailGiant Inc.', 'SecureBank Ltd.', 'SmartCity Gov', 'Logistics Pro', 'HealthPlus Group'];

    $project = [
        'id' => $id,
        'title' => $titles[($id - 1) % count($titles)] ?? 'Custom IT Solution Project',
        'description' => 'Implementing a robust and scalable solution to meet the complex digital demands of modern enterprise. This project focuses on optimizing workflows, enhancing security, and delivering a seamless user experience through cutting-edge technology stacks.',
        'challenge_text' => 'The client faced significant challenges with legacy systems that were hindering scalability and performance. Key issues included data silos, slow processing times, and security vulnerabilities that needed immediate attention to prevent potential data breaches and operational downtime.',
        'image' => '/assets/img/project/project-inner' . (($id - 1) % 8 + 1) . '.jpg',
        // Random gallery images
        'gallery' => [
            '/assets/img/service/sv-sm-' . rand(1, 2) . '.jpg',
            '/assets/img/service/sv-sm-' . rand(1, 2) . '.jpg'
        ],
        'client' => $clients[rand(0, count($clients) - 1)],
        'category' => $categories[($id - 1) % count($categories)] ?? 'Technology',
        'date' => date('d F, Y', strtotime('-' . rand(10, 100) . ' days')),
        'address' => 'Jakarta, Indonesia',
        'map_url' => 'https://maps.google.com',
        'prev_id' => $id > 1 ? $id - 1 : null,
        'next_id' => $id + 1
    ];

    return Inertia::render('ProjectDetail', [
        'project' => $project
    ]);
})->name('projects.detail');

// News Page
Route::get('/news', function () {
    $page = request()->query('page', 1);
    $perPage = 4;
    $total = 12;
    $lastPage = ceil($total / $perPage);

    $posts = collect(range(1, $total))->map(function ($id) {
        return [
            'id' => $id,
            'title' => [
                'Top 10 IT Solutions Every Business Needs',
                'Exploring the Benefits of End-to-End IT Services',
                'The Impact of AI on IT Solutions',
                'The Benefits of 24/7 IT Support',
                'Cybersecurity Best Practices for 2025',
                'Cloud Migration Strategies',
                'Digital Transformation Trends',
                'Data Analytics for Growth'
            ][$id % 8],
            'author' => 'David Smith',
            'date' => '05 May, 2025',
            'category' => 'Technology',
            'image' => '/assets/img/blog/blog-s-1-' . ($id % 3 + 1) . '.jpg',
            'excerpt' => 'In today’s fast-evolving digital landscape, businesses need a clear IT strategy to align technology with their long-term goals. IT Strategy & Planning services help organizations optimize resources.',
            'link' => '/news/' . $id
        ];
    });

    $items = $posts->forPage($page, $perPage)->values();

    return Inertia::render('Blog', [
        'posts' => [
            'data' => $items,
            'current_page' => (int)$page,
            'last_page' => $lastPage,
            'per_page' => $perPage,
            'total' => $total,
            'links' => [
                [
                    'url' => $page > 1 ? '/news?page=' . ($page - 1) : null,
                    'label' => '<i class="far fa-arrow-left"></i>',
                    'active' => false,
                ],
                ...collect(range(1, $lastPage))->map(function ($p) use ($page) {
                    return [
                        'url' => '/news?page=' . $p,
                        'label' => (string)$p,
                        'active' => $p === (int)$page,
                    ];
                }),
                [
                    'url' => $page < $lastPage ? '/news?page=' . ($page + 1) : null,
                    'label' => '<i class="far fa-arrow-right"></i>',
                    'active' => false,
                    'className' => 'next-page'
                ],
            ]
        ]
    ]);
})->name('news');

// News Detail Page
Route::get('/news/{id}', function ($id) {
    srand($id);

    $titles = [
        'Top 10 IT Solutions Every Business Needs in 2025',
        'Exploring the Benefits of End-to-End IT Services',
        'The Impact of AI on IT Solutions',
        'Cybersecurity Best Practices for 2025',
        'Cloud Migration Strategies for Enterprise'
    ];

    $post = [
        'id' => $id,
        'title' => $titles[($id - 1) % count($titles)] ?? 'Technology News Post',
        'author' => 'David Smith',
        'date' => '05 May, 2025',
        'category' => 'Technology',
        'image' => '/assets/img/blog/blog-s-1-' . ($id % 3 + 1) . '.jpg',
        'location' => 'Sea Beach',
        'content_intro' => 'In today’s fast-evolving digital landscape, businesses need a clear IT strategy to align technology with their long-term goals. IT Strategy & Planning services help organizations optimize resources, improve efficiency, enhance security, and drive innovation through a structured approach to technology adoption.',
        'content_main' => 'In addition to protecting against external threats, cybersecurity also plays a crucial role in ensuring business continuity. In the event of a cyber attack or data breach, systems may be disrupted, data may be lost or corrupted, and downtime can result in significant financial losses. By investing in cybersecurity measures such as firewalls, intrusion detection systems, and security awareness training, businesses can reduce their susceptibility.',
        'quote' => [
            'text' => 'Join your neighbors for an eco-friendly social gathering as the day comes to a conclusion. Savor refreshments made with sustainable ingredients and have discussions on sustainable life.',
            'author' => 'Michel Clarck'
        ],
        'gallery_images' => [
            '/assets/img/blog/blog_inner_1.jpg',
            '/assets/img/blog/blog_inner_2.jpg'
        ],
        'tags' => ['Apartment', 'Buyer', 'Modern', 'Luxury'],
        'comments' => [
            [
                'id' => 1,
                'author' => 'Daniel Adam',
                'date' => '15 Jun, 2025 08:56pm',
                'text' => 'Empower multifunctional e-commerce for prospective applications. Seamlessly productivate plug-and-play markets whereas synergistic scenarios.',
                'avatar' => '/assets/img/blog/comment-author-1.jpg',
                'replies' => [
                    [
                        'id' => 2,
                        'author' => 'Zenelia Lozhe',
                        'date' => '25 Jun, 2025 08:56pm',
                        'text' => 'Empower multifunctional e-commerce for prospective applications. Seamlessly productivate plug-and-play markets whereas synergistic scenarios.',
                        'avatar' => '/assets/img/blog/comment-author-2.jpg'
                    ]
                ]
            ],
            [
                'id' => 3,
                'author' => 'Daniel Adam',
                'date' => '27 Jun, 2025 08:56pm',
                'text' => 'Empower multifunctional e-commerce for prospective applications. Seamlessly productivate plug-and-play markets whereas synergistic scenarios.',
                'avatar' => '/assets/img/blog/comment-author-3.jpg',
                'replies' => []
            ]
        ]
    ];

    $recentPosts = collect(range(1, 3))->map(function($i) {
        return [
            'id' => $i,
            'title' => ['5 Common IT Issues and How to Solve Them', 'Hybrid Cloud Solutions: The Best of Both Worlds', 'Top 10 IT Solutions Every Business.'][$i-1],
            'date' => (20 + $i) . ' Sep, 2025',
            'image' => '/assets/img/blog/recent-post-1-' . $i . '.jpg',
            'link' => '/news/' . $i
        ];
    });

    $categories = [
        'IT Strategy & Planning', 'Web Developments', 'Cloud Consulting',
        'Machine Learning', 'Database Security', 'IT Management'
    ];

    return Inertia::render('BlogDetail', [
        'post' => $post,
        'recentPosts' => $recentPosts,
        'categories' => $categories
    ]);
})->name('news.detail');

Route::get('/', function () {
    return Inertia::render('Home', [
        'services' => \Modules\ServiceSolutions\Models\Service::orderBy('sort_order', 'asc')->take(3)->get()
    ]);
})->name('home');

// Room Configurator Page
Route::get('/room-configurator', function () {
    return Inertia::render('RoomConfigurator');
})->name('room.configurator');

Route::match(['get', 'post'], '/room-configurator/complete', function (\Illuminate\Http\Request $request) {
    return Inertia::render('ConfiguratorComplete', [
        'selection' => $request->input('selection'),
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid')
    ]);
})->name('room.configurator.complete');

// Server Configurator Routes
Route::get('/server-configurator', function () {
    return Inertia::render('ServerConfigurator');
})->name('server.configurator');

Route::match(['get', 'post'], '/server-configurator/complete', function (\Illuminate\Http\Request $request) {
    return Inertia::render('ConfiguratorComplete', [
        'selection' => $request->input('selection'),
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid')
    ]);
})->name('server.configurator.complete');

// Surveillance Configurator Routes
Route::get('/surveillance-configurator', function () {
    return Inertia::render('SurveillanceConfigurator');
})->name('surveillance.configurator');

Route::match(['get', 'post'], '/surveillance-configurator/complete', function (\Illuminate\Http\Request $request) {
    return Inertia::render('ConfiguratorComplete', [
        'selection' => $request->input('selection'),
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid')
    ]);
})->name('surveillance.configurator.complete');

// Dynamic Configurator Route
Route::get('/configurator/{slug}', [\Modules\ServiceSolutions\Http\Controllers\DynamicConfiguratorController::class, 'show'])->name('configurator.show');

Route::match(['get', 'post'], '/configurator/complete', function (\Illuminate\Http\Request $request) {
    $selection = $request->input('selection', []);
    $configurator = $request->input('configurator');
    $summaryItems = collect();
    $quantities = $selection['quantities'] ?? [];

    if ($configurator && isset($configurator['steps'])) {
        foreach ($configurator['steps'] as $step) {
            if (!isset($step['questions'])) continue;

            foreach ($step['questions'] as $question) {
                // Check if this question has a selection
                $varName = $question['variable_name'];
                if (!isset($selection[$varName])) continue;

                $selectedValue = $selection[$varName];

                // If selection represents multiple values (checkboxes), handle array
                $selectedValues = is_array($selectedValue) ? $selectedValue : [$selectedValue];

                foreach ($selectedValues as $val) {
                    // Find the matching option
                    $option = collect($question['options'])->firstWhere('value', $val);

                    if ($option) {
                        // Priority 1: Linked Product
                        if (!empty($option['products'])) {
                            foreach ($option['products'] as $product) {
                                $summaryItems->push([
                                    'id' => $product['id'],
                                    'step_label' => $step['title'] ?? 'Config',
                                    'question_label' => $question['label'], // "Select your Room Size"
                                    'name' => $product['name'],
                                    'image' => $product['image_path'] ?? $product['image'] ?? null,
                                    'sku' => $product['sku'] ?? '',
                                    'quantity' => $quantities[$product['id']] ?? 1,
                                    'type' => 'product'
                                ]);
                            }
                        } else {
                            // Priority 2: Standard Option (Text/Card)
                            $summaryItems->push([
                                'id' => $option['id'] ?? uniqid(),
                                'step_label' => $step['title'] ?? 'Config',
                                'question_label' => $question['label'], // "Select your Room Size"
                                'name' => $option['label'],
                                'image' => $option['image_path'] ?? $option['image'] ?? null,
                                'sku' => '-', // No SKU for generic option
                                'quantity' => 1,
                                'type' => 'option'
                            ]);
                        }
                    }
                }
            }
        }
    } else {
        // Fallback for direct product ID selections (Legacy/Simple Mode)
        $ids = collect($selection)->flatten()->filter(function ($value) {
            return is_string($value) || is_int($value);
        })->unique()->values();

        if ($ids->isNotEmpty()) {
            $products = \Modules\Core\Models\Product::whereIn('id', $ids)->get();
            $summaryItems = $products->map(function ($product) use ($quantities) {
                return [
                    'id' => $product->id,
                    'step_label' => 'Product Selection',
                    'question_label' => 'Selected Item',
                    'name' => $product->name,
                    'image' => $product->image_path,
                    'sku' => $product->sku,
                    'quantity' => $quantities[$product->id] ?? 1,
                    'type' => 'product'
                ];
            });
        }
    }

    return Inertia::render('ConfiguratorComplete', [
        'selection' => $selection,
        'userInfo' => $request->input('userInfo'),
        'uuid' => $request->input('uuid'),
        'configurator' => $request->input('configurator'),
        'summaryItems' => $summaryItems
    ]);
})->name('configurator.complete');

// Brand routes are now handled by Modules/Core
