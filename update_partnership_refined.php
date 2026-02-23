<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Page;

$page = Page::where('slug', 'kemitraan')->first();

if (!$page) {
    echo "Page not found\n";
    exit;
}

// 1. Update Partnership Hero
$heroSection = $page->sections()->where('section_key', 'partnership_hero')->first();
if ($heroSection) {
    $config = $heroSection->config;
    
    $config['subtitle'] = 'Kolaborasi Tanpa Batas';
    $config['description'] = 'Bergabunglah dengan ekosistem teknologi terdepan untuk menghadirkan solusi ICT dan Edukasi yang inovatif bagi klien Anda.';
    $config['title'] = 'Bangun Kemitraan Strategis Bersama ACTiV';
    
    $heroSection->config = $config;
    $heroSection->save();
    echo "Hero section updated.\n";
}

// 2. Update Partnership Details
$detailsSection = $page->sections()->where('section_key', 'partnership_section')->first();
if ($detailsSection) {
    $config = $detailsSection->config;
    
    $config['title'] = 'Bagian dari Ekosistem Digital ACTiV';
    $config['subtitle'] = 'Ekosistem Terintegrasi';
    
    $detailsSection->config = $config;
    $detailsSection->save();
    echo "Details section updated.\n";
}

echo "Database update completed successfully.\n";
