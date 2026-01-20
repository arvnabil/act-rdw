<?php

namespace App\Services;

use App\Models\Menu;

class MenuResolver
{
    /**
     * Resolve menu structure for frontend by location.
     */
    public function resolve(string $location): array
    {
        $menu = Menu::where('location', $location)
            ->where('is_active', true)
            ->with(['items' => function ($query) {
                $query->orderBy('order')
                    ->with(['children' => function ($q) {
                        $q->orderBy('order')->with('page'); // Get Children
                    }, 'page']);
            }])
            ->first();

        if (!$menu) {
            return [];
        }

        return $menu->items->map(function ($item) {
            return $this->formatItem($item);
        })->toArray();
    }

    protected function formatItem($item)
    {
        $url = $item->type === 'page' && $item->page
            ? ($item->page->is_homepage ? '/' : '/' . $item->page->slug)
            : $item->url;

        return [
            'title' => $item->title,
            'url' => $url ?? '#',
            'target' => $item->target,
            'children' => $item->children->map(function ($child) {
                return $this->formatItem($child);
            })->toArray(),
        ];
    }
}
