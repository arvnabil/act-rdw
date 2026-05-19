<?php

namespace Modules\AI\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Backoff;
use Modules\ProductCatalog\Models\Product;
use Modules\AI\Services\GeminiService;
use Modules\AI\Services\VectorService;
use Illuminate\Support\Facades\Log;

#[Tries(3)]
#[Timeout(300)]
#[Backoff(30)]
class IngestProductData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job to sync all products to the Vector DB
     */
    public function handle(GeminiService $gemini, VectorService $vector): void
    {
        Log::info("AI Ingestion: Starting product data sync...");

        Product::where('is_active', true)->with('brand')->chunk(10, function ($products) use ($gemini, $vector) {
            foreach ($products as $product) {
                try {
                    $content = "Nama Produk: {$product->name}\n" .
                               "Brand: " . ($product->brand->name ?? 'ICT') . "\n" .
                               "Deskripsi: {$product->description}\n" .
                               "Spesifikasi: {$product->specification_text}";

                    $embedding = $gemini->getEmbedding($content);
                    
                    $vector->upsert(
                        $product->id, 
                        $embedding, 
                        $content, 
                        [
                            'brand' => $product->brand->name ?? 'ICT',
                            'slug' => $product->slug
                        ]
                    );

                    Log::info("AI Ingestion: Synced product ID {$product->id}");

                    // Tambahkan jeda 2 detik untuk menghindari Rate Limit Gemini Free Tier
                    sleep(2);
                } catch (\Exception $e) {

                    Log::error("AI Ingestion Error for Product ID {$product->id}: " . $e->getMessage());
                }
            }
        });

        Log::info("AI Ingestion: Sync complete.");
    }
}
