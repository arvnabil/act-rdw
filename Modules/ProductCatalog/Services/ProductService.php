<?php

namespace Modules\ProductCatalog\Services;

use Modules\ProductCatalog\Repositories\ProductRepository;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\ProductCategory;
use Modules\Services\Models\ServiceSolution;
use Modules\CMS\Models\Page;

class ProductService
{
    protected $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get data for the product index page
     */
    public function getIndexData(array $filters)
    {
        $products = $this->repository->getPaginatedWithFilters($filters);

        $brands = Brand::orderBy('name')->get(['id', 'name']);
        $solutions = ServiceSolution::orderBy('title')->get(['id', 'title']);
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        
        $page = Page::where('slug', 'products')->first();

        return [
            'products' => $products,
            'brands' => $brands,
            'solutions' => $solutions,
            'categories' => $categories,
            'page_title' => $page?->title ?? 'All Products',
            'breadcrumb_image' => $page?->breadcrumb_image,
            'show_breadcrumb' => $page?->show_breadcrumb ?? true,
        ];
    }

    /**
     * Get data for a single product detail page
     */
    public function getDetailData(string $slug)
    {
        $product = $this->repository->findBySlug($slug);
        
        $relatedProducts = $this->repository->getRelatedProducts($product->service_id, $product->id)
            ->map(function ($related) {
                return [
                    'id' => $related->id,
                    'name' => $related->name,
                    'image_path' => $related->image_path,
                    'tag' => $related->tags ? $related->tags[0] ?? null : null,
                    'category' => $related->categories->pluck('name')->join(', ') ?: ($related->service?->name ?? 'General'),
                    'slug' => $related->slug,
                    'price' => $related->price,
                    'brand' => [
                         'name' => $related->brand?->name ?? '',
                         'logo' => $related->brand?->logo ?? ''
                    ]
                ];
            });

        return [
            'id' => $product->id,
            'slug' => $product->slug,
            'name' => $product->name,
            'subtitle' => $product->seo?->description,
            'cta_label' => "CTA Produk: {$product->name}",
            'image' => $product->image_path ? "/storage/" . $product->image_path : null,
            'image_path' => $product->image_path,
            'breadcrumb_image' => $product->breadcrumb_image ?: ($product->thumbnail_path ?: $product->image_path),
            'show_breadcrumb' => $product->show_breadcrumb ?? true,
            'sku' => $product->sku,
            'solution_type' => $product->solutions
                ->where('service_id', $product->service_id)
                ->pluck('title')
                ->unique()
                ->join(', ') ?: $product->solution_type,
            'solutions_list' => $product->solutions
                ->where('service_id', $product->service_id)
                ->unique('title')
                ->map(function($s) {
                    return ['id' => $s->id, 'title' => $s->title, 'slug' => $s->slug];
                })->values(),
            'datasheet_url' => $product->datasheet_url,
            'datasheet_rel' => \Modules\SEO\Helpers\SeoHelper::get_rel($product->datasheet_url),
            'description' => $product->description,
            'category' => $product->categories->pluck('name')->join(', ') ?: ($product->service?->name ?? 'General'),
            'categories_list' => $product->categories->map(function($c) {
                return ['id' => $c->id, 'name' => $c->name, 'slug' => $c->slug];
            })->values(),
            'brand' => [
                'name' => $product->brand->name,
                'logo' => $product->brand->logo,
                'slug' => $product->brand->slug,
                'config' => $product->brand->landing_config,
            ],
            'tags' => $product->tags ?? [],
            'specification' => $product->specs,
            'specification_text' => $product->specification_text,
            'features' => $product->features ?? [],
            'features_text' => $product->features_text,
            'related_products' => $relatedProducts,
            'link_accommerce' => $product->link_accommerce,
            'link_accommerce_rel' => \Modules\SEO\Helpers\SeoHelper::get_rel($product->link_accommerce),
            'whatsapp_note' => $product->whatsapp_note,
        ];
    }
}
