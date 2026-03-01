<?php

namespace Modules\Services\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class ConfiguratorController extends Controller
{
    /**
     * Dynamic Configurator completion page.
     */
    public function complete(Request $request)
    {
        $selection = $request->input('selection', []);
        $configurator = $request->input('configurator');
        $summaryItems = collect();
        $quantities = $selection['quantities'] ?? [];

        if ($configurator && isset($configurator['steps'])) {
            foreach ($configurator['steps'] as $step) {
                if (!isset($step['questions'])) continue;

                foreach ($step['questions'] as $question) {
                    $varName = $question['variable_name'];
                    if (!isset($selection[$varName])) continue;

                    $selectedValue = $selection[$varName];
                    $selectedValues = is_array($selectedValue) ? $selectedValue : [$selectedValue];

                    foreach ($selectedValues as $val) {
                        $option = collect($question['options'])->firstWhere('value', $val);

                        if ($option) {
                            if (!empty($option['products'])) {
                                foreach ($option['products'] as $product) {
                                    $summaryItems->push([
                                        'id' => $product['id'],
                                        'step_label' => $step['title'] ?? 'Config',
                                        'question_label' => $question['label'],
                                        'name' => $product['name'],
                                        'image' => $product['image_path'] ?? $product['image'] ?? null,
                                        'sku' => $product['sku'] ?? '',
                                        'quantity' => $quantities[$product['id']] ?? 1,
                                        'type' => 'product'
                                    ]);
                                }
                            } else {
                                $summaryItems->push([
                                    'id' => $option['id'] ?? uniqid(),
                                    'step_label' => $step['title'] ?? 'Config',
                                    'question_label' => $question['label'],
                                    'name' => $option['label'],
                                    'image' => $option['image_path'] ?? $option['image'] ?? null,
                                    'sku' => '-',
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
                $products = \Modules\ProductCatalog\Models\Product::whereIn('id', $ids)->get();
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
    }

    /**
     * Static configurator completion pages (room, server, surveillance).
     */
    public function simpleComplete(Request $request)
    {
        return Inertia::render('ConfiguratorComplete', [
            'selection' => $request->input('selection'),
            'userInfo' => $request->input('userInfo'),
            'uuid' => $request->input('uuid')
        ]);
    }
}
