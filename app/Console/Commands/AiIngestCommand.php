<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\ProductCatalog\Models\Product;
use Modules\AI\Services\GeminiService;
use Modules\AI\Services\VectorService;

class AiIngestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:ingest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all products to the AI Vector database (Supabase)';

    /**
     * Execute the console command.
     */
    public function handle(GeminiService $gemini, VectorService $vector)
    {
        $products = Product::where('is_active', true)->with('brand')->get();
        $total = $products->count();

        $this->info("Menemukan {$total} produk aktif. Memulai sinkronisasi...");
        $this->output->progressStart($total);

        foreach ($products as $product) {
            try {
                $content = "Nama Produk: {$product->name}\n" .
                           "Brand: " . ($product->brand->name ?? 'ICT') . "\n" .
                           "Deskripsi: " . strip_tags($product->description);

                $embedding = $gemini->getEmbedding($content);
                
                $vector->upsert($product->id, $embedding, $content, [
                    'brand' => $product->brand->name ?? 'ICT',
                    'slug' => $product->slug
                ]);

                $this->output->progressAdvance();
                
                // Jeda 2 detik untuk menghindari rate limit Gemini Free
                sleep(2);
            } catch (\Exception $e) {
                $this->error("\nGagal di ID {$product->id}: " . $e->getMessage());
            }
        }

        $this->output->progressFinish();
        $this->info("Sinkronisasi AI selesai!");
    }
}
