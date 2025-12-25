<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/services', function () {
    return Inertia::render('Services');
})->name('services');

Route::get('/projects', function () {
    return Inertia::render('Projects');
})->name('projects');

Route::get('/partners', function () {
    return Inertia::render('Partners');
})->name('partners');

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

Route::get('/products', function (Request $request) {
    // Generate Dummy Data (simulating a database)
    $allProducts = collect(range(1, 50))->map(function ($id) {
        return [
            'id' => $id,
            'name' => 'Product Name ' . $id,
            'price' => '$' . rand(100, 500) . '.00',
            'original_price' => rand(0, 1) ? '$' . rand(501, 1000) . '.00' : null,
            'image' => '/assets/img/product/product_1_' . (rand(1, 8)) . '.png',
            'rating' => 5.0,
            'category' => 'Technology',
            'tag' => rand(0, 1) ? 'Sale' : null,
        ];
    });

    // Pagination Logic
    $page = $request->input('page', 1);
    $perPage = 9; // Grid: 3x3 or similar
    $offset = ($page - 1) * $perPage;
    
    $paginatedItems = $allProducts->slice($offset, $perPage)->values();
    
    $products = new LengthAwarePaginator(
        $paginatedItems,
        $allProducts->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return Inertia::render('Products', [
        'products' => $products
    ]);
})->name('products');

Route::get('/products/{id}', function ($id) {
    // Simulate fetching a specific product
    $product = [
        'id' => $id,
        'name' => 'Product Name ' . $id,
        'price' => '$' . rand(100, 500) . '.00',
        'original_price' => rand(0, 1) ? '$' . rand(501, 1000) . '.00' : null,
        'description' => 'Bluetooth headphones are a wireless audio accessory that connects to your devices, like smartphones, tablets, or computers, via Bluetooth technology. Here\'s a typical description Introducing our Bluetooth Headphones, the ultimate companion for your on-the-go audio experience. Immerse yourself in high-fidelity sound without the hassle of wires, thanks to the latest Bluetooth technology.',
        'image' => '/assets/img/product/product_1_' . ((($id - 1) % 8) + 1) . '.png',
        'rating' => 5.0,
        'review_count' => rand(1, 20),
        'category' => 'Technology',
        'tags' => ['Wireless', 'Sports', 'Gaming'],
        'sku' => 'BH-100' . $id . '-BT',
        'stock_status' => 'In Stock',
        'features_text' => 'Our product features are designed to enhance your experience with cutting-edge technology and user-centric design.',
        'features' => [
            ['name' => 'Rightsight', 'description' => 'Advanced automated camera control', 'additional' => 'Link Youtube'],
            ['name' => 'RightLight', 'description' => 'Light compensation technology', 'additional' => 'More Info'],
            ['name' => 'RightSound', 'description' => 'Modular audio for clear conversations', 'additional' => 'Audio Demo'],
            ['name' => 'Bluetooth Connectivity', 'description' => 'Wireless connection up to 30ft', 'additional' => 'Specs'],
            ['name' => 'Battery Life', 'description' => 'Up to 20 hours of listening time', 'additional' => 'Details']
        ],
        'specification_text' => 'Below are the detailed specifications of the product, ensuring you have all the technical information needed.',
        'specification' => [
            ['name' => 'Brand', 'value' => 'Logitech'],
            ['name' => 'Model', 'value' => 'MeetUp'],
            ['name' => 'Color', 'value' => 'Black'],
            ['name' => 'Connectivity', 'value' => 'Bluetooth 5.0, USB-C'],
            ['name' => 'Microphone', 'value' => 'Integrated beamforming mic'],
            ['name' => 'Warranty', 'value' => '2 Years Limited Hardware'],
            ['name' => 'Dimensions', 'value' => '400mm x 104mm x 85mm'],
            ['name' => 'Weight', 'value' => '1.04 kg'],
            ['name' => 'Camera Lens', 'value' => 'Custom Logitech lens with 5x HD zoom'],
            ['name' => 'Video Quality', 'value' => '4K Ultra HD video calling']
        ],
        'solution_type' => 'Small Room',
        'datasheet_url' => '#',
        'related_products' => collect(range(1, 6))->map(function ($id) {
            return [
                'id' => $id,
                'name' => 'Product Name ' . $id,
                'price' => '$' . rand(100, 500) . '.00',
                'original_price' => rand(0, 1) ? '$' . rand(501, 1000) . '.00' : null,
                'image' => '/assets/img/product/product_1_' . (rand(1, 8)) . '.png',
                'category' => 'Technology',
                'tag' => rand(0, 1) ? 'Sale' : null,
            ];
        })
    ];

    return Inertia::render('ProductDetail', [
        'product' => $product
    ]);
})->name('product.detail');

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

// Blog Page
Route::get('/blog', function () {
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
            'link' => '/blog/' . $id
        ];
    });

    $items = $posts->forPage($page, $perPage)->values();

    return Inertia::render('Blog', [
        'posts' => [
            'data' => $items,
            'current_page' => (int)$page,
            'last_page' => $lastPage,
            'links' => [
                [
                    'url' => $page > 1 ? '/blog?page=' . ($page - 1) : null,
                    'label' => '<i class="far fa-arrow-left"></i>',
                    'active' => false,
                ],
                ...collect(range(1, $lastPage))->map(function ($p) use ($page) {
                    return [
                        'url' => '/blog?page=' . $p,
                        'label' => (string)$p,
                        'active' => $p === (int)$page,
                    ];
                }),
                [
                    'url' => $page < $lastPage ? '/blog?page=' . ($page + 1) : null,
                    'label' => '<i class="far fa-arrow-right"></i>',
                    'active' => false,
                    'className' => 'next-page'
                ],
            ]
        ]
    ]);
})->name('blog');

// Blog Detail Page
Route::get('/blog/{id}', function ($id) {
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
        'title' => $titles[($id - 1) % count($titles)] ?? 'Technology Blog Post',
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
            'link' => '/blog/' . $i
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
})->name('blog.detail');

// Events Landing Page
Route::get('/events', function () {
    // Generate some dummy events for the landing page teaser
    $events = collect(range(1, 6))->map(function ($id) {
        $dates = [
            ['day' => '15', 'month' => 'MAY', 'year' => '2025'],
            ['day' => '22', 'month' => 'JUN', 'year' => '2025'],
            ['day' => '05', 'month' => 'JUL', 'year' => '2025'],
            ['day' => '10', 'month' => 'AUG', 'year' => '2025']
        ];
        $date = $dates[$id % 4];

        return [
            'id' => $id,
            'title' => [
                'Global AI Summit 2025',
                'Cybersecurity Defense Hackathon',
                'Cloud Infrastructure Workshop',
                'Future of DevOps Conference',
                'Blockchain for Enterprise Seminar',
                'Tech Startup Networking Night',
                'Data Science Bootcamp',
                'IoT Smart City Expo'
            ][$id % 8],
            'description' => 'Join industry leaders and tech enthusiasts for an immersive experience.',
            'date' => $date,
            'time' => '09:00 AM - 05:00 PM',
            'location' => ['Grand Hall, Jakarta', 'Online Webinar', 'Tech Hub, Bandung', 'Convention Center, Bali'][$id % 4],
            'price' => $id % 3 === 0 ? 'Free' : '$' . ($id * 10 + 50),
            'image' => '/assets/img/blog/blog-s-1-' . ($id % 3 + 1) . '.jpg',
            'link' => '/events/' . $id,
            'category' => 'Technology' // Added for consistency
        ];
    });

    return Inertia::render('Events', [
        'events' => ['data' => $events]
    ]);
})->name('events');

// Events List Page
Route::get('/events/list', function () {
    $page = request()->query('page', 1);
    $perPage = 6;
    $total = 12;
    $lastPage = ceil($total / $perPage);

    $events = collect(range(1, $total))->map(function ($id) {
        $dates = [
            ['day' => '15', 'month' => 'MAY', 'year' => '2025'],
            ['day' => '22', 'month' => 'JUN', 'year' => '2025'],
            ['day' => '05', 'month' => 'JUL', 'year' => '2025'],
            ['day' => '10', 'month' => 'AUG', 'year' => '2025']
        ];
        $date = $dates[$id % 4];

        return [
            'id' => $id,
            'title' => [
                'Global AI Summit 2025',
                'Cybersecurity Defense Hackathon',
                'Cloud Infrastructure Workshop',
                'Future of DevOps Conference',
                'Blockchain for Enterprise Seminar',
                'Tech Startup Networking Night',
                'Data Science Bootcamp',
                'IoT Smart City Expo'
            ][$id % 8],
            'description' => 'Join industry leaders and tech enthusiasts for an immersive experience exploring the latest trends and innovations in the technology sector.',
            'date' => $date,
            'time' => '09:00 AM - 05:00 PM',
            'location' => ['Grand Hall, Jakarta', 'Online Webinar', 'Tech Hub, Bandung', 'Convention Center, Bali'][$id % 4],
            'price' => $id % 3 === 0 ? 'Free' : '$' . ($id * 10 + 50),
            'image' => '/assets/img/blog/blog-s-1-' . ($id % 3 + 1) . '.jpg', 
            'link' => '/events/' . $id,
            'category' => ['Business', 'Technology', 'Education', 'Social'][$id % 4] // Dummy categories
        ];
    });

    $items = $events->forPage($page, $perPage)->values();

    return Inertia::render('EventList', [
        'events' => [
            'data' => $items,
            'current_page' => (int)$page,
            'last_page' => $lastPage,
            'links' => [
                [
                    'url' => $page > 1 ? '/events/list?page=' . ($page - 1) : null,
                    'label' => '<i class="far fa-arrow-left"></i>',
                    'active' => false,
                ],
                ...collect(range(1, $lastPage))->map(function ($p) use ($page) {
                    return [
                        'url' => '/events/list?page=' . $p,
                        'label' => (string)$p,
                        'active' => $p === (int)$page,
                    ];
                }),
                [
                    'url' => $page < $lastPage ? '/events/list?page=' . ($page + 1) : null,
                    'label' => '<i class="far fa-arrow-right"></i>',
                    'active' => false,
                    'className' => 'next-page'
                ],
            ]
        ]
    ]);
})->name('events.list');

// Event Detail Page
Route::get('/events/{id}', function ($id) {
    srand($id);
    
    $titles = [
        'Global AI Summit 2025',
        'Cybersecurity Defense Hackathon',
        'Cloud Infrastructure Workshop',
        'Future of DevOps Conference',
        'Blockchain for Enterprise Seminar',
        'Tech Startup Networking Night',
        'Data Science Bootcamp',
        'IoT Smart City Expo'
    ];
    
    $locations = ['Grand Hall, Jakarta', 'Online Webinar', 'Tech Hub, Bandung', 'Convention Center, Bali'];
    $categories = ['Conference', 'Workshop', 'Hackathon', 'Networking'];

    $event = [
        'id' => $id,
        'title' => $titles[($id - 1) % count($titles)] ?? 'Tech Event',
        'description' => 'Join industry leaders and tech enthusiasts for an immersive experience exploring the latest trends and innovations in the technology sector. This event features keynote speeches, interactive workshops, and networking opportunities designed to empower professionals and businesses.',
        'date' => [
            'day' => '15', 
            'month' => 'MAY', 
            'year' => '2025', 
            'full' => 'May 15, 2025'
        ],
        'time' => '09:00 AM - 05:00 PM',
        'location' => $locations[$id % 4],
        'price' => $id % 3 === 0 ? 'Free' : '$' . ($id * 10 + 50),
        'image' => '/assets/img/blog/blog-s-1-' . ($id % 3 + 1) . '.jpg',
        'category' => $categories[$id % 4],
        'organizer' => 'ACTiV Tech Community',
        'phone' => '+62 812 3456 7890',
        'email' => 'events@activ.com',
        'map_url' => 'https://maps.google.com',
        'schedule' => [
            ['time' => '09:00 AM', 'activity' => 'Registration & Coffee'],
            ['time' => '10:00 AM', 'activity' => 'Keynote Speech: Future of Tech'],
            ['time' => '12:00 PM', 'activity' => 'Lunch Break'],
            ['time' => '01:30 PM', 'activity' => 'Breakout Sessions'],
            ['time' => '04:00 PM', 'activity' => 'Networking & Closing']
        ],
        'speakers' => [
            ['name' => 'Sarah Johnson', 'role' => 'AI Researcher', 'image' => '/assets/img/team/team_1_1.jpg'],
            ['name' => 'David Lee', 'role' => 'Cloud Architect', 'image' => '/assets/img/team/team_1_2.jpg'],
            ['name' => 'Michael Chen', 'role' => 'Cybersecurity Expert', 'image' => '/assets/img/team/team_1_3.jpg']
        ]
    ];

    return Inertia::render('EventDetail', [
        'event' => $event
    ]);
})->name('events.detail');

// Service Category Page (e.g., /services/video-conference)
Route::get('/services/{service}', function ($service) {
    // Dummy Data for Service Category Page
    $serviceData = [
        'id' => $service,
        'title' => ucfirst(str_replace('-', ' ', $service)),
        'rooms' => [
            [
                'id' => 'huddle-room',
                'title' => 'Huddle room',
                'description' => 'Get a first-class experience and create inclusive experiences for all in your huddle meeting rooms.',
                'capacity' => '2-4',
                'size' => '8-12m2 / 85-130ft2',
                'image' => '/assets/img/rooms/huddle-room.jpg',
                'category' => 'meetings-rooms training-education-space'
            ],
            [
                'id' => 'small-room',
                'title' => 'Small Room',
                'description' => 'Get a first-class experience and create inclusive experiences for all in your Small meeting rooms.',
                'capacity' => '4-6',
                'size' => '12-28m2 / 130-300ft2',
                'image' => '/assets/img/rooms/small-room.jpg',
                'category' => 'meetings-rooms individual-space training-education-space'
            ],
             [
                'id' => 'medium-room',
                'title' => 'Medium Rooms',
                'description' => 'Medium rooms designed for effective team collaboration.',
                'capacity' => '6-10',
                'size' => '14-36m2 / 150-390ft2',
                'image' => '/assets/img/rooms/medium-room.jpg',
                'category' => 'meetings-rooms'
            ]
        ]
    ];

    return Inertia::render('ServiceDetail', [
        'service' => $serviceData
    ]);
})->name('services.detail');

// Service Item Detail Page (e.g., /services/video-conference/small-room)
Route::get('/services/{service}/{item}', function ($service, $item) {
    // Dummy Data for Service Item Detail Page
    $itemData = [
        'id' => $item,
        'title' => ucfirst(str_replace('-', ' ', $item)),
        'parent_service' => $service,
        'parent_title' => ucfirst(str_replace('-', ' ', $service)),
        'subtitle' => 'Smart, Simple, and Ready for Collaboration',
        'description' => 'Designed for 4–6 people, Small Room solutions deliver clear video, crisp audio, and an easy meeting experience in a compact space. With a streamlined setup of camera, mic, and speaker, your room becomes a modern collaboration hub that’s always ready to use.',
        'features' => [
            ['title' => 'People support', 'text' => '4-6 People capacity for small meeting rooms.', 'icon' => '/assets/img/icon/shield.svg'],
            ['title' => 'Size', 'text' => '12-28m2 / 130-300ft2', 'icon' => '/assets/img/icon/shield.svg']
        ],
        'images' => [
            '/assets/img/normal/about_6_1.jpg',
            '/assets/img/normal/about_6_1.jpg'
        ],
        'brands' => [
            ['name' => 'Logitech', 'image' => '/assets/img/partners/logi.jpg', 'desc' => 'Logitech device ready for small room'],
            ['name' => 'Yealink', 'image' => '/assets/img/partners/yealink.jpg', 'desc' => 'Yealink'],
            ['name' => 'Jabra', 'image' => '/assets/img/partners/jabra.jpg', 'desc' => 'Jabra']
        ],
        'products' => [
            ['name' => 'Logitech Meetup', 'image' => '/assets/img/product/logitech meetup.jpg', 'category' => 'Logitech'],
            ['name' => 'Yealink UVC30', 'image' => '/assets/img/product/logitech meetup.jpg', 'category' => 'Yealink'],
            ['name' => 'Jabra PanaCast', 'image' => '/assets/img/product/logitech meetup.jpg', 'category' => 'Jabra']
        ],
        'projects' => [
            ['title' => 'Purnomo', 'category' => 'Logitech', 'image' => '/assets/img/project/project_3_9_.jpg'],
            ['title' => 'Suharto', 'category' => 'Yealink', 'image' => '/assets/img/project/project_3_9_.jpg'],
            ['title' => 'Slamet', 'category' => 'Jabra', 'image' => '/assets/img/project/project_3_9_.jpg'],
            ['title' => 'Slamet', 'category' => 'Jabra', 'image' => '/assets/img/project/project_3_9_.jpg']
        ]
    ];

    return Inertia::render('ServiceItemDetail', [
        'item' => $itemData
    ]);
})->name('services.item.detail');

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
