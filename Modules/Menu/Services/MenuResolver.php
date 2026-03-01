<?php

namespace Modules\Menu\Services;

use Modules\Menu\Models\Menu;

class MenuResolver
{
    /**
     * Resolve menu structure for frontend by location.
     */
    public function resolve(string $location, $user = null): array
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

        return $menu->items
            ->filter(function ($item) use ($user) {
                return $this->shouldShow($item, $user);
            })
            ->map(function ($item) use ($user) {
                return $this->formatItem($item, $user);
            })
            ->values()
            ->toArray();
    }

    protected function shouldShow($item, $user): bool
    {
        $visibility = $item->visibility ?? 'all';

        if ($visibility === 'all') {
            return true;
        }

        if ($visibility === 'auth') {
            return $user !== null;
        }

        if ($visibility === 'guest') {
            return $user === null;
        }

        return true;
    }

    protected function formatItem($item, $user = null)
    {
        $url = $item->type === 'page' && $item->page
            ? ($item->page->is_homepage ? '/' : '/' . $item->page->slug)
            : $item->url;

        return [
            'title' => $item->title,
            'url' => $url ?? '#',
            'target' => $item->target,
            'visibility' => $item->visibility ?? 'all',
            'children' => $item->children
                ->filter(function ($child) use ($user) {
                    return $this->shouldShow($child, $user);
                })
                ->map(function ($child) use ($user) {
                    return $this->formatItem($child, $user);
                })
                ->values()
                ->toArray(),
        ];
    }
}
