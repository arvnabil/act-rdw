<?php

namespace Modules\AI\Console;

use Illuminate\Console\Command;
use Modules\ProductCatalog\Models\Product;
use Modules\AI\Services\GeminiService;
use Modules\AI\Services\VectorService;

class SyncProductEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:sync-products {--force : Force re-sync all products}';

    /**
     * The console command description.
     */
    protected $description = 'Sync all products into the Vector Database for AI search';

    /**
     * Execute the console command.
     */
    public function handle(GeminiService $ai, VectorService $vector)
    {
        $this->info('🚀 Memulai proses sinkronisasi produk ke Vector Database...');

        $products = Product::all();
        $bar = $this->output->createProgressBar($products->count());
        $bar->start();

        foreach ($products as $product) {
            try {
                $this->info("\nMemproses: {$product->name}");
                
                // 1. Ambil deskripsi dasar
                $content = "Nama: {$product->name}\nSKU: {$product->sku}\nDeskripsi: " . strip_tags($product->description);

                // 2. Jika ada datasheet_url, coba baca isi PDF-nya
                if (!empty($product->datasheet_url)) {
                    $this->comment("  - Membaca datasheet: {$product->datasheet_url}");
                    try {
                        // Gunakan Http client dengan User-Agent agar tidak di-block (403)
                        $pdfContent = \Illuminate\Support\Facades\Http::withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                        ])->get($product->datasheet_url)->body();

                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf    = $parser->parseContent($pdfContent);
                        $pdfText = $pdf->getText();
                        
                        // Bersihkan teks PDF dari spasi berlebih/karakter aneh
                        $pdfText = preg_replace('/\s+/', ' ', $pdfText);
                        
                        $content .= "\n\n=== SPESIFIKASI TEKNIS (DARI DATASHEET) ===\n" . $pdfText;
                        $this->info("  - ✅ Berhasil mengekstrak teks PDF.");
                    } catch (\Exception $pdfEx) {
                        $this->warn("  - ⚠️ Gagal baca PDF: " . $pdfEx->getMessage());
                    }
                }
                
                // 3. Generate Embedding via Gemini/Vertex
                $embedding = $ai->getEmbedding($content);

                // 4. Simpan ke Vector Database (Supabase)
                $vector->upsert($product->id, $embedding, $content, [
                    'name' => $product->name,
                    'slug' => $product->slug
                ]);

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\n❌ Gagal memproses produk ID {$product->id}: " . $e->getMessage());
            }
        }


        $bar->finish();
        $this->info("\n\n✅ Sinkronisasi selesai! " . $products->count() . " produk berhasil di-update.");
    }
}
