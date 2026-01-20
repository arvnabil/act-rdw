<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Primary Menu
        Menu::where('location', 'primary')->delete();
        $primaryMenu = Menu::create([
            'name' => 'Main Navigation',
            'location' => 'primary',
            'is_active' => true,
        ]);

        $this->createPrimaryItems($primaryMenu);

        // 2. Create Footer Menu
        Menu::where('location', 'footer')->delete();
        $footerMenu = Menu::create([
            'name' => 'Footer "Useful Link"',
            'location' => 'footer',
            'is_active' => true,
        ]);

        $this->createFooterItems($footerMenu);
    }

    private function createPrimaryItems(Menu $menu)
    {
        $items = [
            [
                'title' => 'Home',
                'type' => 'page',
                'url' => null, // Dynamic Page Resolution
                'page_slug' => 'home', // Assuming you have a page with this slug, or use '/' mapping
            ],
            [
                'title' => 'Our Services',
                'type' => 'page',
                // 'url' => '/services', // Can be page or custom
                'page_slug' => 'services',
            ],
            [
                'title' => 'About Us',
                'type' => 'page',
                'page_slug' => 'about-us', // Check your seeded pages
            ],
            [
                'title' => 'Our Partners',
                'type' => 'custom',
                'url' => '/partners',
            ],
            [
                'title' => 'Products',
                'type' => 'custom',
                'url' => '/products',
            ],
            [
                'title' => 'Projects',
                'type' => 'page',
                'page_slug' => 'projects',
            ],
            [
                'title' => 'Media',
                'type' => 'custom',
                'url' => '#',
                'children' => [
                   [
                       'title' => 'Shop',
                       'type' => 'custom',
                       'url' => 'https://accommerce.id',
                       'target' => '_blank',
                   ],
                   [
                       'title' => 'Events',
                       'type' => 'custom',
                       'url' => '/events',
                   ],
                   [
                       'title' => 'News',
                       'type' => 'page',
                       'page_slug' => 'news',
                   ],
                ],
            ],
        ];

        $this->seedItems($menu, $items);
    }

    private function createFooterItems(Menu $menu)
    {
        $items = [
            [
                'title' => 'Home',
                'type' => 'page',
                'page_slug' => 'home',
            ],
            [
                'title' => 'About us',
                'type' => 'page',
                'page_slug' => 'about-us',
            ],
            [
                'title' => 'Our Service', // Matches UI text
                'type' => 'page',
                'page_slug' => 'services',
            ],
            [
                'title' => 'Terms of Service',
                'type' => 'custom',
                'url' => '/contact', // Matches UI link
            ],
            [
                'title' => 'News & Media',
                'type' => 'page',
                'page_slug' => 'news',
            ],
        ];

        $this->seedItems($menu, $items);
    }

    private function seedItems($menu, $items, $parentId = null)
    {
        foreach ($items as $index => $item) {
            $pageId = null;
            $url = $item['url'] ?? null;

            // Try to resolve Page ID if type is 'page'
            if ($item['type'] === 'page' && isset($item['page_slug'])) {
                $page = Page::where('slug', $item['page_slug'])->first();
                if ($page) {
                    $pageId = $page->id;
                } else {
                    // Fallback if page doesn't exist yet, make it custom
                    $item['type'] = 'custom';
                    $url = $url ?? ('/' . $item['page_slug']); // Simple fallback
                }
            }

            $menuItem = MenuItem::create([
                'menu_id' => $menu->id,
                'parent_id' => $parentId,
                'type' => $item['type'],
                'title' => $item['title'],
                'url' => $url,
                'page_id' => $pageId,
                'order' => $index + 1,
                'target' => $item['target'] ?? '_self',
            ]);

            if (isset($item['children'])) {
                $this->seedItems($menu, $item['children'], $menuItem->id);
            }
        }
    }
}
