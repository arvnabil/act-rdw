<?php

namespace Modules\AI\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\AI\Services\GeminiService;
use Modules\AI\Services\VectorService;
use Modules\AI\Models\ChatSession;
use Modules\AI\Models\ChatMessage;

class ChatbotController extends Controller
{
    public function __construct(
        protected GeminiService $gemini,
        protected VectorService $vector,
    ) {
    }

    /**
     * Start a new chat session (Lead Capture)
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'whatsapp' => 'required|string|max:20',
            'email'    => 'nullable|email|max:100',
            'company'  => 'nullable|string|max:100',
        ]);

        $session = ChatSession::create([
            'name'     => $request->name,
            'whatsapp' => $request->whatsapp,
            'email'    => $request->email,
            'company'  => $request->company,
            'status'   => 'active',
        ]);

        // Simpan salam pembuka ke database agar riwayat tidak kosong saat di-refresh
        ChatMessage::create([
            'session_id' => $session->id,
            'role'       => 'assistant',
            'content'    => "Halo, Selamat datang **{$request->name}** 👋 saya **Vion**, ICT Solutions Consultant. Ada yang bisa saya bantu hari ini?",
        ]);

        return response()->json([
            'success'    => true,
            'session_id' => $session->id,
        ]);
    }

    /**
     * Handle chat message and return AI response
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:1000',
            'session_id' => 'required|exists:ai_chat_sessions,id',
            'persona'    => 'nullable|string|in:sales,analyst,doctor',
        ]);

        $message   = $request->input('message');
        $sessionId = $request->input('session_id');
        $persona   = $request->input('persona', 'sales');

        try {
            // 1. Simpan Pesan User
            ChatMessage::create([
                'session_id' => $sessionId,
                'role'       => 'user',
                'content'    => $message,
            ]);

            // 2. Ambil embedding dari pertanyaan user
            $queryVector = $this->gemini->getEmbedding($message);

            // 3. Cari produk paling relevan di Supabase (RAG)
            $results = $this->vector->search($queryVector, 5);

            // 4. Bangun konteks dengan ID Produk agar AI bisa referensi
            $context = $results->isEmpty()
                ? 'Tidak ada produk spesifik yang relevan di katalog untuk pertanyaan ini. Gunakan pengetahuan umummu sebagai pakar ICT untuk menjawab secara bijak.'
                : $results->map(fn($r) => "ID: {$r->product_id} | Info: {$r->content}")->join("\n\n---\n\n");

            // 5. Generate respons AI dengan persona yang dipilih
            $aiResponse = $this->gemini->generateResponse($message, $context, $persona);

            // LOG DIAGNOSA: Lihat jawaban asli AI
            \Log::info('VION Raw Response:', ['text' => $aiResponse]);

            // 6. Ekstrak kode produk (ID atau SKU) dari respon
            $recommendedProducts = [];
            // Regex lebih fleksibel: mendukung (ID: kode), ID: kode, SKU: kode, dsb
            if (preg_match_all('/(?:ID_PRODUK|ID|SKU):\s*([a-zA-Z0-9\-\.]+)/i', $aiResponse, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $productIdentifier = trim($match[1], " \t\n\r\0\x0B().");
                    \Log::info("VION Processing identifier:", ['raw' => $match[1], 'cleaned' => $productIdentifier]);
                    
                    $product = \Modules\ProductCatalog\Models\Product::where('sku', $productIdentifier)
                        ->orWhere('id', $productIdentifier)
                        ->first();
                        
                    if ($product) {
                        \Log::info("VION Product FOUND:", ['id' => $product->id, 'name' => $product->name]);
                        $recommendedProducts[] = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'image' => $product->image_path ? asset('storage/' . $product->image_path) : url('assets/default.png'),
                            'qty' => 1,
                            'link' => url("/products/{$product->slug}")
                        ];
                    } else {
                        \Log::warning("VION Product NOT FOUND in DB for identifier: " . $productIdentifier);
                    }
                }
            }

            \Log::info('VION Final Product Count:', ['count' => count($recommendedProducts)]);

            // Bersihkan tag produk dan trigger dari teks agar tidak tampil ke user
            $cleanResponse = preg_replace('/\(?(?:ID_PRODUK|ID|SKU):\s*[a-zA-Z0-9\-\.]+\)?/i', '', $aiResponse);
            
            // Ubah trigger sales ke format internal
            if (str_contains($cleanResponse, '[HUBUNGI_SALES]')) {
                $cleanResponse = str_replace('[HUBUNGI_SALES]', '[WA_TRIGGER]', $cleanResponse);
            }

            // Rapikan tanda baca yang rusak akibat penghapusan tag
            $cleanResponse = preg_replace('/[,:]\s*\./', '.', $cleanResponse); // Ubah ", ." atau ": ." jadi "."
            $cleanResponse = preg_replace('/\s+\./', '.', $cleanResponse);    // Ubah " ." jadi "."
            $cleanResponse = preg_replace('/\s\s+/', ' ', $cleanResponse);    // Ubah spasi ganda jadi spasi tunggal

            $cleanResponse = trim($cleanResponse);



            // Jika respon berakhir dengan titik dua tapi tidak ada produk, bersihkan titik duanya
            if (empty($recommendedProducts) && (str_ends_with($cleanResponse, ':') || str_ends_with($cleanResponse, ':-'))) {
                $cleanResponse = rtrim($cleanResponse, ':-') . '.';
            }

            // 7. Simpan Respons AI ke Database
            ChatMessage::create([
                'session_id' => $sessionId,
                'role'       => 'assistant',
                'content'    => $cleanResponse,
                'products'   => $recommendedProducts,
            ]);

            return response()->json([
                'success'  => true,
                'response' => $cleanResponse,
                'products' => $recommendedProducts,
            ]);

        } catch (\Exception $e) {
            \Log::error('Chatbot AI Error: ' . $e->getMessage(), [
                'user_message' => $message,
                'persona' => $persona,
                'exception' => $e
            ]);

            return response()->json([
                'success' => false,
                'response' => 'Maaf, saya sedang mengalami sedikit gangguan teknis. 🙏',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Chatbot settings for frontend
     */
    public function settings()
    {
        return response()->json([
            'is_active' => Setting::getValue('vion_is_active', '1') === '1',
            'welcome_message' => Setting::getValue('vion_welcome_message', 'Halo! Saya Vion, ICT Solutions Consultant Anda. Ada yang bisa saya bantu hari ini?'),
            'starter_buttons' => json_decode(Setting::getValue('vion_starter_buttons', '[]'), true),
            'whatsapp_number' => Setting::getValue('vion_whatsapp_number', '628123456789'),
        ]);
    }

    /**
     * Return available personas
     */
    public function personas()
    {
        return response()->json($this->gemini->getAllPersonas());
    }

    /**
     * Summarize chat history
     */
    public function summarize(Request $request)
    {
        $request->validate(['history' => 'required|array']);

        try {
            $summary = $this->gemini->summarizeChat($request->input('history'));
            return response()->json([
                'success' => true,
                'summary' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'summary' => 'Tertarik diskusi lebih lanjut mengenai solusi ICT.',
            ]);
        }
    }

    /**
     * Get chat history for a session
     */
    public function getHistory(Request $request)
    {
        $sessionId = $request->query('session_id');
        $session = ChatSession::find($sessionId);

        if (!$session) {
            return response()->json(['messages' => []]);
        }

        // Cek apakah sesi sudah lebih dari 24 jam (Expire)
        if ($session->created_at->diffInHours(now()) >= 24) {
            return response()->json([
                'status' => 'expired',
                'messages' => [],
                'error' => 'Sesi Anda telah berakhir. Silakan isi data kembali.'
            ], 403);
        }

        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'role'     => $m->role,
                'content'  => $m->content,
                'products' => $m->products,
            ]);

        return response()->json([
            'messages' => $messages,
            'session'  => $session // Kembalikan data sesi agar frontend tahu Nama user
        ]);
    }
}
