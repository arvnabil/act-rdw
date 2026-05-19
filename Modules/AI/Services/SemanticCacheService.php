<?php

namespace Modules\AI\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * SemanticCacheService
 *
 * Menyimpan dan mengambil hasil respons AI berdasarkan pertanyaan yang sama/mirip.
 *
 * Strategi cache:
 *  - Kunci cache: hash SHA256 dari teks pertanyaan yang sudah dinormalisasi (lowercase + trim)
 *  - TTL: 24 jam (sesuai PRD)
 *  - Threshold: hanya cache pertanyaan yang IDENTIK (hash match = similarity 1.0)
 *    sehingga aman dan tidak berisiko memberikan jawaban yang tidak relevan.
 *
 * Catatan: Untuk similarity semantic (threshold 0.90) membutuhkan perbandingan
 * vektor embedding — ditandai sebagai TODO untuk fase berikutnya.
 */
class SemanticCacheService
{
    /**
     * Cache TTL: 24 jam sesuai PRD.
     */
    const TTL_HOURS = 24;

    /**
     * Redis cache key prefix.
     */
    const KEY_PREFIX = 'vion_semantic_cache:';

    /**
     * Coba ambil respons yang sudah di-cache untuk pertanyaan ini.
     * Mengembalikan null jika tidak ada cache (cache miss).
     *
     * @return array{response: string, products: array}|null
     */
    public function get(string $question, string $persona = 'sales'): ?array
    {
        $key = $this->buildKey($question, $persona);

        if (Cache::has($key)) {
            Log::info('SemanticCache: HIT', ['persona' => $persona, 'question_preview' => substr($question, 0, 60)]);
            return Cache::get($key);
        }

        Log::debug('SemanticCache: MISS', ['persona' => $persona]);
        return null;
    }

    /**
     * Simpan respons AI ke cache Redis untuk pertanyaan ini.
     *
     * @param array{response: string, products: array} $result
     */
    public function put(string $question, string $persona, array $result): void
    {
        // Jangan cache respons yang kosong atau error
        if (empty($result['response'])) {
            return;
        }

        $key = $this->buildKey($question, $persona);

        Cache::put($key, $result, now()->addHours(self::TTL_HOURS));

        Log::info('SemanticCache: STORED', [
            'persona'          => $persona,
            'question_preview' => substr($question, 0, 60),
            'ttl_hours'        => self::TTL_HOURS,
        ]);
    }

    /**
     * Hapus cache untuk pertanyaan tertentu (misal saat data produk diperbarui).
     */
    public function forget(string $question, string $persona = 'sales'): void
    {
        Cache::forget($this->buildKey($question, $persona));
    }

    /**
     * Hapus semua semantic cache (gunakan saat ada pembaruan besar pada katalog produk).
     */
    public function flush(): void
    {
        // Redis pattern flush via Cache tags tidak tersedia di semua driver,
        // gunakan artisan cache:clear atau flush via Redis facade jika diperlukan.
        Log::warning('SemanticCache: flush() dipanggil. Gunakan `php artisan cache:clear` untuk flush semua cache.');
    }

    /**
     * Bangun kunci cache unik dari pertanyaan + persona.
     * Normalisasi: lowercase + hapus spasi ganda untuk menangkap variasi pengetikan.
     */
    protected function buildKey(string $question, string $persona): string
    {
        $normalized = strtolower(trim(preg_replace('/\s+/', ' ', $question)));
        $hash       = hash('sha256', $normalized);

        return self::KEY_PREFIX . $persona . ':' . $hash;
    }
}
