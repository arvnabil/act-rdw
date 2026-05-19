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
use Modules\AI\Services\GeminiService;
use Modules\AI\Models\ChatSession;
use Illuminate\Support\Facades\Log;

/**
 * ProcessChatSummaryJob
 *
 * Memproses pembuatan ringkasan percakapan chat untuk tim Sales secara ASINKRON.
 * Sebelumnya ini dijalankan secara sinkron di controller (memblokir respons 2-4 detik).
 *
 * Dengan Queue Attributes baru Laravel 13:
 *  - #[Tries(3)]   : Coba ulang hingga 3 kali jika gagal
 *  - #[Timeout(60)]: Batas waktu eksekusi 60 detik per attempt
 *  - #[Backoff(10)]: Tunggu 10 detik sebelum retry (exponential: 10s, 20s, 40s)
 */
#[Tries(3)]
#[Timeout(60)]
#[Backoff(10)]
class ProcessChatSummaryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param array $history  Array riwayat chat dari frontend
     * @param int   $sessionId ID sesi chat untuk menyimpan summary ke DB
     */
    public function __construct(
        protected array $history,
        protected int   $sessionId,
    ) {}

    /**
     * Jalankan proses pembuatan ringkasan secara asinkron di background.
     */
    public function handle(GeminiService $gemini): void
    {
        Log::info("ProcessChatSummaryJob: Starting summary for session #{$this->sessionId}");

        try {
            $summary = $gemini->summarizeChat($this->history);

            // Simpan ringkasan ke session record di DB
            ChatSession::where('id', $this->sessionId)->update([
                'summary' => $summary,
            ]);

            Log::info("ProcessChatSummaryJob: Summary saved for session #{$this->sessionId}");

        } catch (\Exception $e) {
            Log::error("ProcessChatSummaryJob: Failed for session #{$this->sessionId}", [
                'error'   => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Re-throw agar queue menjalankan retry sesuai #[Tries(3)]
            throw $e;
        }
    }

    /**
     * Handler saat job gagal setelah semua retry habis.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("ProcessChatSummaryJob: PERMANENTLY FAILED for session #{$this->sessionId}", [
            'error' => $exception->getMessage(),
        ]);
    }
}
