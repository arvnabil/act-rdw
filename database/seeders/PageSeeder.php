<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or Update Home Page
        $homePage = Page::updateOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'type' => 'home',
                'status' => 'published',
                'is_homepage' => true,
                'show_breadcrumb' => false,
            ]
        );

        // Clear existing sections for this page to avoid duplicates on re-seed
        PageSection::where('page_id', $homePage->id)->delete();

        // 2. Define Sections (Order matters)
        $sections = [
            [
                // New Slider Section (Variant: Hero, Source: Static)
                'key' => 'slider',
                'config' => [
                    'variant' => 'hero',
                    'source' => 'static',
                    'delay' => 5000,
                    'slides' => [
                        [
                            'title' => 'Seamless ICT Integration for Business',
                            'description' => 'Your trusted partner for digital transformation. We specialize in delivering tailored technology across Video Conferencing, Data Infrastructure, and Security systems.',
                            'bg_image' => '/assets/img/hero/hero_bg_3_1.png',
                            'buttons' => [
                                ['text' => 'Explore Our Services', 'url' => '/services', 'style' => 'style7'],
                                ['text' => 'Get In Touch', 'url' => '/contact', 'style' => 'style2']
                            ]
                        ],
                        [
                            'title' => 'Connect Your Teams, Wherever They Are',
                            'description' => 'Experience professional video conferencing for every space. Whether it’s a small huddle room or a large auditorium, we ensure crystal-clear communication.',
                            'bg_image' => '/assets/img/hero/hero_bg_2_1.png',
                            'buttons' => [
                                ['text' => 'Meeting Room Solutions', 'url' => '/contact', 'style' => 'style7'],
                                ['text' => 'View Products', 'url' => '/services', 'style' => 'style2']
                            ]
                        ],
                        [
                            'title' => 'A Strong Foundation for Your Data',
                            'description' => 'Keep your operations running smoothly with reliable servers and scalable storage. We provide the performance and capacity your business needs to stay ahead.',
                            'bg_image' => '/assets/img/hero/hero_bg_2_2.png',
                            'buttons' => [
                                ['text' => 'Infrastructure Solutions', 'url' => '/contact', 'style' => 'style7'],
                                ['text' => 'Contact Us', 'url' => '/services', 'style' => 'style2']
                            ]
                        ],
                        [
                            'title' => 'Smart Security for Peace of Mind',
                            'description' => 'Protect what matters most with advanced surveillance systems. Monitor your environment in real-time and ensure safety around the clock with our integrated solutions.',
                            'bg_image' => '/assets/img/hero/hero_bg_4_1.png',
                            'buttons' => [
                                ['text' => 'See Security Systems', 'url' => '/contact', 'style' => 'style7'],
                                ['text' => 'Contact Sales', 'url' => '/services', 'style' => 'style2']
                            ]
                        ]
                    ]
                ]
            ],
            [
                'key' => 'about',
                'config' => [
                    'variant' => 'home',
                    'title' => 'Bridging Technology and Education for a Better Future.',
                    'subtitle' => 'Who We Are',
                    'description' => 'ACTiV (PT Alfa Cipta Teknologi Virtual) is a dynamic company specializing in the sales and rental of software, hardware, and supporting accessories, with a primary focus on Information Communication Technology (ICT) and Education solutions. Backed by a team with over 6 years of experience and official partnerships with multinational ICT brands, we are dedicated to delivering the best comprehensive technology solutions to our clients.',
                    'images' => [
                        '/assets/img/normal/about_7_1.jpg',
                        '/assets/img/normal/about_7_2.jpg',
                        '/assets/img/normal/about_7_3.jpg'
                    ],
                    'features' => [
                        [
                            'title' => 'ICT & Education Product Supply',
                            'text' => 'Official provider of hardware and software tailored for education and ICT infrastructure.',
                            'icon' => '/assets/img/icon/shield.svg'
                        ],
                        [
                            'title' => 'Solution Services & Custom Development',
                            'text' => 'Expert technical solutions and custom software development tailored to your specific needs.',
                            'icon' => '/assets/img/icon/shield.svg'
                        ]
                    ]
                ]
            ],
            [
                'key' => 'services',
                'config' => [
                    'limit' => 6,
                    'order' => 'asc'
                ]
            ],
            [
                // Brand Partners Section
                'key' => 'brand_partners',
                'config' => [
                    'title' => '',
                    'subtitle' => '',
                    'brands' => [
                         ['image' => '/assets/img/brand/brand_1_1.svg', 'url' => '#', 'name' => 'Brand 1'],
                         ['image' => '/assets/img/brand/brand_1_2.svg', 'url' => '#', 'name' => 'Brand 2'],
                         ['image' => '/assets/img/brand/brand_1_3.svg', 'url' => '#', 'name' => 'Brand 3'],
                         ['image' => '/assets/img/brand/brand_1_4.svg', 'url' => '#', 'name' => 'Brand 4'],
                         ['image' => '/assets/img/brand/brand_1_5.svg', 'url' => '#', 'name' => 'Brand 5'],
                         ['image' => '/assets/img/brand/brand_1_6.svg', 'url' => '#', 'name' => 'Brand 6'],
                         ['image' => '/assets/img/brand/brand_1_7.svg', 'url' => '#', 'name' => 'Brand 7']
                    ]
                ]
            ],
            [
                'key' => 'why_choose_us',
                'config' => [
                    'title' => 'Empowering Your Future with Proven Technology.',
                    'subtitle' => 'Why Choose ACTiV',
                    'description' => 'We are dedicated to providing comprehensive ICT and Education solutions that are not only advanced but also reliable. By combining technical expertise with global support, we ensure every technology investment you make delivers real, sustainable value.',
                    'features' => [
                        [
                            'title' => 'Expert Team',
                            'text' => 'Backed by professionals with over 6 years of experience in ICT infrastructure',
                            'icon' => '/assets/img/icon/shield.svg'
                        ],
                        [
                            'title' => 'Certified Brand Partners',
                            'text' => 'Official partnerships ensuring product authenticity and certified technical support.',
                            'icon' => '/assets/img/icon/shield.svg'
                        ]
                    ],
                    'images' => [
                         '/assets/img/normal/about_4_1.jpg',
                         '/assets/img/normal/about_4_2.jpg'
                    ],
                    'video_url' => 'https://www.youtube.com/watch?v=hIIQbkkKnno'
                ]
            ],
            [
                'key' => 'projects',
                'config' => [
                    'limit' => 8
                ]
            ],
            [
                'key' => 'cta', // ClientSection
                'config' => [
                    'title' => 'Our Clients',
                    'subtitle' => 'Clients',
                    'clients' => ["1_1", "1_2", "1_3", "1_4", "1_5", "1_6", "1_7", "1_1", "1_4", "1_3", "1_2", "1_1", "1_1", "1_1"]
                ]
            ],
            [
                'key' => 'news',
                'config' => [
                    'title' => 'ACTiV News Articles',
                    'subtitle' => 'News',
                    'limit' => 3
                ]
            ]
        ];

        // 3. Insert Sections
        foreach ($sections as $index => $section) {
            PageSection::create([
                'page_id' => $homePage->id,
                'section_key' => $section['key'],
                'position' => $index + 1,
                'is_active' => true,
                'config' => $section['config'],
            ]);
        }

        // 4. Create or Update About Us Page
        $aboutPage = Page::updateOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'type' => 'dynamic', // Or whatever generic type implies dynamic
                'status' => 'published',
                'is_homepage' => false,
                'show_breadcrumb' => true, // Ensure breadcrumb is shown
            ]
        );

        PageSection::where('page_id', $aboutPage->id)->delete();

        $aboutSections = [
             // 1. About Content (Legacy)
            [
                'key' => 'about_content',
                'config' => [
                    'subtitle' => 'About ACTiV',
                    'title' => 'Bridging Technology and Education for a Better Future.',
                    'description' => 'ACTiV (PT Alfa Cipta Teknologi Virtual) is a premier provider of ICT and Educational solutions, specializing in hardware, software, and accessory rentals and sales. With over six years of industry experience and strategic partnerships with multinational brands, we deliver comprehensive, tailored technology solutions to empower our clients\' success.',
                    'images' => [
                        ['image' => '/assets/img/normal/about_7_1.jpg'],
                        ['image' => '/assets/img/normal/about_7_2.jpg'],
                        ['image' => '/assets/img/normal/about_7_3.jpg'],
                    ],
                    'video_url' => 'https://www.youtube.com/watch?v=hIIQbkkKnno',
                    'features' => [
                        [
                            'title' => 'Expert Team',
                            'description' => 'Our team consists of seasoned professionals with deep expertise in ICT and education sectors.',
                            'icon' => '/assets/img/icon/guide.svg',
                        ],
                        [
                            'title' => 'Certified Partnerships',
                            'description' => 'We hold official partnerships with leading global brands, ensuring authentic products and certified technical support.',
                            'icon' => '/assets/img/icon/policy.svg',
                        ],
                        [
                            'title' => 'Dedicated Support',
                            'description' => 'Our responsive support system operates across multiple channels to provide timely resolutions and ensure business continuity.',
                            'icon' => '/assets/img/icon/support.svg',
                        ],
                    ],
                ]
            ],
             // 2. Vision & Mission (Legacy)
            [
                'key' => 'vision_mission',
                'config' => [
                    'vision_title' => 'Our Vision',
                    'vision_text' => 'To be Indonesia\'s leading national provider of ICT and Education solutions, recognized for excellence in service, sales, and rentals.',
                    'mission_title' => 'Our Mission',
                    'mission_items' => [
                        ['text' => 'Deliver superior consultation services that optimize business processes and productivity through strategic ICT implementation.'],
                        ['text' => 'Provide a comprehensive range of well-designed, cost-effective solution blueprints.'],
                        ['text' => 'Ensure seamless and flexible transaction experiences across direct and marketplace channels.'],
                        ['text' => 'Offer dedicated after-sales support and maintenance to guarantee long-term operational success.'],
                    ],
                    'goal_title' => 'Our Goal',
                    'goal_text' => 'To become a trusted partner delivering consultative ICT solutions that drive productivity and resolve complex business challenges efficiently.',
                ]
            ],
             // 3. Testimonial (Legacy)
            [
                'key' => 'testimonial',
                'config' => [
                    'title' => 'What Client Say About us',
                    'subtitle' => 'Testimonials',
                    'testimonials' => [
                        [
                            'name' => 'Sarah Rahman',
                            'role' => 'UI/UX Designer',
                            'quote' => 'Cybersecurity is more critical than ever. We implemented a comprehensive security strategy that includes firewall protection and regular audits.',
                            'rating' => 5,
                            'image' => '/assets/img/testimonial/testi_4_1.png',
                        ],
                        [
                            'name' => 'Rina Sartika',
                            'role' => 'Product Manager',
                            'quote' => 'Cloud computing has revolutionized how we operate. It offers scalability and flexibility, allowing us to access data from anywhere.',
                            'rating' => 5,
                            'image' => '/assets/img/testimonial/testi_4_2.png',
                        ],
                        [
                            'name' => 'Michael Chen',
                            'role' => 'Developer',
                            'quote' => 'Data analytics is key to understanding our customers. We use advanced tools to analyze trends and make data-driven decisions.',
                            'rating' => 5,
                            'image' => '/assets/img/testimonial/testi_4_3.png',
                        ],
                        [
                            'name' => 'Dewi Santoso',
                            'role' => 'Data Analyst',
                            'quote' => 'Artificial Intelligence is transforming our industry. We are exploring AI solutions to automate routine tasks and improve efficiency.',
                            'rating' => 5,
                            'image' => '/assets/img/testimonial/testi_4_4.png',
                        ],
                    ],
                ]
            ],
             // 4. Contact & Map (Legacy)
            [
                'key' => 'contact',
                'config' => [
                    'title' => 'Contact Information',
                    'subtitle' => "Let's Collaborate",
                    'intro_text' => 'We appreciate your interest in ACTiV. Our team is ready to discuss how we can support your business goals. Please contact us via the following:',
                    'phone' => '(+62) 2150110987',
                    'whatsapp' => '(+62) 851-6299-4602',
                    'email' => 'sales@activ.co.id',
                    'address_office' => 'Infinity Office, Belleza BSA 1st Floor Unit 106, Jl. Letjen Soepeno, Kebayoran Lama, Jakarta Selatan 12210',
                    'address_representative' => 'Ruko Golden Boulevard Blok S 28, BSD City, Tangerang Selatan 15318',
                    'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.448530321454!2d106.78065057585093!3d-6.204407860775895!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f6dcc7d2c4ad%3A0x286e11158102c918!2sThe%20Bellezza%20Shopping%20Arcade!5e0!3m2!1sid!2sid!4v1716968007353!5m2!1sid!2sid',
                    'form_fields' => [
                        [
                            'label' => 'Your Name',
                            'name' => 'name',
                            'type' => 'text',
                            'placeholder' => 'Your Name',
                            'width' => 'col-sm-6',
                            'required' => true,
                            'icon' => '/assets/img/icon/user.svg'
                        ],
                        [
                            'label' => 'Email Address',
                            'name' => 'email',
                            'type' => 'email',
                            'placeholder' => 'Email Address',
                            'width' => 'col-sm-6',
                            'required' => true,
                            'icon' => '/assets/img/icon/mail.svg'
                        ],
                        [
                            'label' => 'Phone Number',
                            'name' => 'number',
                            'type' => 'tel',
                            'placeholder' => 'Phone Number',
                            'width' => 'col-sm-6',
                            'required' => false,
                            'icon' => '/assets/img/icon/call.svg'
                        ],
                        [
                            'label' => 'Subject',
                            'name' => 'subject',
                            'type' => 'select',
                            'options' => 'Quotation,Technical Support,Billing',
                            'placeholder' => 'Select Subject',
                            'width' => 'col-sm-6',
                            'required' => true
                        ],
                        [
                            'label' => 'Your Message',
                            'name' => 'message',
                            'type' => 'textarea',
                            'placeholder' => 'Your Message',
                            'width' => 'col-12',
                            'required' => true,
                            'icon' => '/assets/img/icon/chat.svg'
                        ]
                    ]
                ]
            ]
        ];

        foreach ($aboutSections as $index => $section) {
            PageSection::create([
                'page_id' => $aboutPage->id,
                'section_key' => $section['key'],
                'position' => $index + 1,
                'is_active' => true,
                'config' => $section['config'],
            ]);
        }

    }
}
