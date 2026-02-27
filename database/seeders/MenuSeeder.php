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
            'name' => 'Footer Menu',
            'location' => 'footer',
            'is_active' => true,
        ]);

        $this->createFooterItems($footerMenu);

        // 3. Create Top Header Menu
        Menu::where('location', 'top_header')->delete();
        $topHeaderMenu = Menu::create([
            'name' => 'Top Header Navigation',
            'location' => 'top_header',
            'is_active' => true,
        ]);

        $this->createTopHeaderItems($topHeaderMenu);
    }

    private function createTopHeaderItems(Menu $menu)
    {
        $items = [
            [
                'title' => 'FAQ',
                'type' => 'custom',
                'url' => '/faq',
                'visibility' => 'all',
            ],
            [
                'title' => 'Masuk Event',
                'type' => 'custom',
                'url' => '/events/auth/login',
                'visibility' => 'guest',
            ],
            [
                'title' => 'Dasbor Event',
                'type' => 'custom',
                'url' => '/events/dashboard',
                'visibility' => 'auth',
            ],
        ];

        $this->seedItems($menu, $items);
    }

    private function createPrimaryItems(Menu $menu)
    {
        $items = [
            [
                'title' => 'Beranda',
                'type' => 'page',
                'page_slug' => 'home',
            ],
            [
                'title' => 'Layanan',
                'type' => 'page',
                'page_slug' => 'services',
            ],
            [
                'title' => 'Tentang Kami',
                'type' => 'page',
                'page_slug' => 'about-us',
            ],
            [
                'title' => 'Mitra',
                'type' => 'custom',
                'url' => '/partners',
            ],
            [
                'title' => 'Produk',
                'type' => 'custom',
                'url' => '/products',
            ],
            [
                'title' => 'Proyek',
                'type' => 'page',
                'page_slug' => 'projects',
            ],
            [
                'title' => 'Media',
                'type' => 'custom',
                'url' => '#',
                'children' => [
                   [
                       'title' => 'Toko',
                       'type' => 'custom',
                       'url' => 'https://accommerce.id',
                       'target' => '_blank',
                   ],
                   [
                       'title' => 'Event',
                       'type' => 'custom',
                       'url' => '/events',
                   ],
                   [
                       'title' => 'Berita',
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
                'title' => 'Beranda',
                'type' => 'page',
                'page_slug' => 'home',
            ],
            [
                'title' => 'Tentang Kami',
                'type' => 'page',
                'page_slug' => 'about-us',
            ],
            [
                'title' => 'Layanan Kami',
                'type' => 'page',
                'page_slug' => 'services',
            ],
            [
                'title' => 'Syarat Layanan',
                'type' => 'custom',
                'url' => '/contact',
            ],
            [
                'title' => 'Berita & Media',
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

            if ($item['type'] === 'page' && isset($item['page_slug'])) {
                $page = Page::where('slug', $item['page_slug'])->first();
                if ($page) {
                    $pageId = $page->id;
                } else {
                    $item['type'] = 'custom';
                    $url = $url ?? ('/' . $item['page_slug']);
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
                'visibility' => $item['visibility'] ?? 'all',
            ]);

            if (isset($item['children'])) {
                $this->seedItems($menu, $item['children'], $menuItem->id);
            }
        }
    }
}
