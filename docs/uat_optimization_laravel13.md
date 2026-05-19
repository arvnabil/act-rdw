# 🧪 Dokumen Pengujian (UAT) - Optimasi Laravel 13

Dokumen ini berisi panduan *User Acceptance Testing* (UAT) untuk memverifikasi fungsionalitas dan optimasi arsitektural yang telah diterapkan pada modul AI Chatbot. Sistem saat ini dikonfigurasi untuk kompatibilitas penuh dengan **cPanel Shared Hosting** (menggunakan *database driver* untuk cache, session, dan queue).

---

## 🟢 1. Pengujian Fungsionalitas Dasar (Database Driver)
*Tujuan: Memastikan infrastruktur dasar (database cache & sessions) merespons dengan baik.*

- [ ] **Test Mulai Sesi Baru:** Masukkan nama dan nomor WhatsApp di form awal chat. 
  - **Ekspektasi:** Sesi berhasil dibuat dan Anda menerima pesan pembuka dari Vion.
  - **Validasi Backend:** Cek tabel `sessions` dan `cache` di database, pastikan terdapat *record* baru.

---

## ⚡ 2. Pengujian Semantic Cache (Respons Instan)
*Tujuan: Memvalidasi bahwa pertanyaan yang berulang akan langsung direspons dari cache tanpa memanggil API Vertex AI.*

- [ ] **Langkah 1:** Kirim pertanyaan baru, misalnya: "Jelaskan spesifikasi Meetup 2".
  - **Ekspektasi:** AI merespons dalam waktu normal (sekitar 2–5 detik).
- [ ] **Langkah 2:** Kirim kembali pertanyaan yang sama persis: "Jelaskan spesifikasi Meetup 2".
  - **Ekspektasi:** Respons AI harus muncul secara **instan (< 0.5 detik)**. 
  - **Validasi Log:** Di `storage/logs/laravel.log`, akan muncul log berbunyi `SemanticCache: HIT`.

---

## ⏱️ 3. Pengujian `Cache::touch()` (Ketahanan Sesi)
*Tujuan: Memastikan fitur perpanjangan batas waktu (TTL) sesi berjalan tanpa menambah beban query.*

- [ ] **Langkah 1:** Buka tab chat dan lakukan 2-3 percakapan singkat.
- [ ] **Langkah 2:** Tutup tab browser, lalu buka kembali halaman chat di browser yang sama.
- [ ] **Ekspektasi:** Riwayat *chat* sebelumnya harus tetap muncul dan Anda bisa langsung melanjutkan percakapan tanpa harus mengisi form Nama/WhatsApp lagi. (Membuktikan `Cache::touch()` berhasil memperbarui TTL sesi di latar belakang).

---

## 🚀 4. Pengujian Async Queue (Ringkasan Chat)
*Tujuan: Memastikan proses berat (merangkum chat untuk Sales) dieksekusi di latar belakang tanpa memblokir respons UI.*

- [ ] **Langkah 1:** Lakukan simulasi percakapan panjang (sekitar 3-5 pesan).
- [ ] **Langkah 2:** Picu *trigger* ringkasan (misalnya user menyatakan tertarik dan ingin dihubungi Sales).
- [ ] **Ekspektasi UI:** *Chatbot* langsung membalas (instan) bahwa tim Sales akan segera menghubungi.
- [ ] **Validasi Backend:** Buka `storage/logs/laravel.log` atau tabel `jobs`. Beberapa detik *setelah* UI merespons, Anda harus melihat log:
  * `ProcessChatSummaryJob: Starting summary for session #...`
  * `ProcessChatSummaryJob: Summary saved for session #...`

---

## 🔄 5. Pengujian Reliabilitas (Token Cache & Fallback)
*Tujuan: Memastikan token OAuth2 Google tidak di-request berulang kali, menghemat latensi jaringan.*

- [ ] **Langkah 1:** Kirim 5 pertanyaan berbeda secara berturut-turut dengan cepat.
- [ ] **Ekspektasi UI:** Tidak ada pesan *error* (seperti `500 Internal Server Error`).
- [ ] **Validasi Log:** Di `storage/logs/laravel.log`, Anda **hanya boleh melihat satu** pesan `Vertex AI: OAuth2 access token refreshed and cached.` (membuktikan token sukses di-cache selama 55 menit).

---

## 🛡️ 6. Pengujian Resiliensi (Supabase Offline Fallback)
*Tujuan: Memastikan chatbot tetap berfungsi memberikan jawaban cerdas meskipun Supabase Vector Store sedang offline, dipause, atau mengalami gangguan koneksi.*

- [ ] **Langkah 1 (Opsional/Simulasi):** Ubah sementara nama host Supabase di `.env` menjadi host fiktif yang tidak valid (misal: `SUPABASE_URL=https://invalid-supabase-host-test.co`).
- [ ] **Langkah 2:** Ajukan pertanyaan umum seputar ICT atau produk ke Chatbot, misalnya: "Bagaimana cara kerja teknologi videoconferencing?".
- [ ] **Ekspektasi UI:** Chatbot **tidak boleh crash** atau menampilkan pesan *"gangguan teknis"*. Chatbot harus tetap merespons dengan jawaban cerdas dan ramah dari Gemini (menggunakan pengetahuan umum ICT).
- [ ] **Validasi Log:** Di `storage/logs/laravel.log`, akan tercatat log warning: `VectorService Search Error (Supabase Connection Failed)`.

---

> **Catatan Pengujian Lokal:**  
> Selama melakukan tes ini di lingkungan lokal (Docker/Sail), pastikan terminal Anda sedang menjalankan antrean (*queue worker*) atau pastikan container `queue`/`sail` sedang berjalan agar *job* asinkron dapat dieksekusi secara *real-time*.
