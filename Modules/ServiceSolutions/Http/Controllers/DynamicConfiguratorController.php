<?php

namespace Modules\ServiceSolutions\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\ServiceSolutions\Models\Configurator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DynamicConfiguratorController extends Controller
{
    public function show($slug)
    {
        $configurator = Configurator::where('slug', $slug)
            ->with([
                'steps' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'steps.questions' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'steps.questions.options' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'steps.questions.options.products.brand', // Eager load linked prodcts and brand
                'steps.questions.options.products.service',
                'steps.questions.options.products',
                'steps.questions.options.serviceSolution' // Eager load text/image source
            ])
            ->firstOrFail();

        return Inertia::render('DynamicConfigurator', [
            'configurator' => $configurator
        ]);
    }
}
