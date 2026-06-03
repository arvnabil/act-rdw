# Vion AI - ICT Solutions Consultant Documentation

## Overview
**Vion** adalah asisten AI (Chatbot) yang dirancang khusus untuk platform ACTiV. Berperan sebagai *ICT Solutions Consultant*, Vion membantu pengunjung memahami solusi teknologi mulai dari perangkat meeting, infrastruktur jaringan, hingga solusi cloud.

---

## Fitur Utama & Persona

### 1. Persona & Tone of Voice
*   **Nama**: Vion.
*   **Peran**: ICT Solutions Consultant.
*   **Gaya Bahasa**: Profesional, ringkas, solutif, dan ramah.
*   **Fokus**: Membantu kebutuhan bisnis (B2B) dan teknis ICT.

### 2. Strict Scope Filtering (Filter Konteks)
Vion memiliki filter keamanan untuk menjaga relevansi percakapan:
*   **Penolakan Sopan**: Vion akan menolak pertanyaan di luar topik ICT (misal: matematika, gosip, atau topik umum lainnya) dengan gaya profesional ala *Zoom Virtual Agent*.
*   **WhatsApp Redirection**: Setiap penolakan atau permintaan teknis yang sangat mendetail (seperti *wiring diagram*) akan diarahkan ke tim spesialis via WhatsApp menggunakan trigger `[HUBUNGI_SALES]`.

### 3. Lead Generation Workflow
*   **Form Data Diri**: Pengunjung diwajibkan mengisi Nama, WhatsApp, dan Perusahaan sebelum memulai chat.
*   **Data Persistence**: Data tersimpan di database dan dihubungkan dengan riwayat chat melalui `session_id`.

---

## Implementasi Teknis & Optimasi UI

### 1. Responsive Markdown Tables
*   **Horizontal Scroll**: Mendukung tabel perbandingan teknis yang kompleks (misal: paket Zoom) agar tetap terbaca di perangkat mobile.
*   **Minimum Width**: Dipatok minimal `500px` untuk menjaga integritas data.
*   **Visual Styling**: Dilengkapi dengan *zebra striping* dan *premium header design*.
*   **Trailing Text Fix**: Parser cerdas yang mampu memisahkan kalimat penutup dari badan tabel meskipun ditulis tanpa baris baru oleh AI.

### 2. 24-Hour Session Expiration
*   **Keamanan Data**: Sesi chat hanya berlaku selama 24 jam sejak pertama kali dibuat.
*   **Auto-Reset**: Jika pengunjung kembali setelah lewat 1 hari, sistem akan menghapus sesi lama dan meminta pengisian ulang form data diri.

### 3. Integrasi Produk (RAG)
*   **Product Cards**: Menampilkan kartu produk yang relevan langsung di dalam chat.
*   **Flexible ID Extraction**: Mendukung format `ID: {id}` atau `ID_PRODUK: {id}` dengan pembersihan otomatis sebelum ditampilkan ke user.

---

## Pengelolaan di Admin Panel (Filament v4)

Menu **AI Chat Session** di bawah grup *Client Management* memungkinkan Admin untuk:
1.  **Melihat Leads**: Daftar calon pelanggan yang telah mengisi data.
2.  **Monitoring Chat**: Membaca seluruh log percakapan antara Vion dan pengunjung.
3.  **AI Summary**: Membaca ringkasan kebutuhan pengunjung yang dihasilkan secara otomatis oleh AI.
4.  **Quick Follow-up**: Menghubungi calon pelanggan via WhatsApp secara langsung dari dashboard.

### 4. Migrasi Arsitektur Supabase (REST API)
*   **Motivasi**: Menghindari pemblokiran port database (5432/6543) pada shared hosting (Dewaweb) yang memiliki aturan firewall ketat dan biaya tambahan untuk pembukaan port.
*   **Solusi**: Transisi dari *Direct Database Connection* ke **Supabase REST API (HTTPS/Port 443)**.
*   **Perubahan**: 
    *   `VectorService.php` kini menggunakan `Illuminate\Support\Facades\Http` untuk operasi `upsert` dan `search`.
    *   Implementasi fungsi RPC `match_products` di Supabase untuk memfasilitasi pencarian *semantic vector* via API.
    *   Penyesuaian izin akses (`GRANT SELECT`) pada tabel `product_embeddings` untuk role `anon` agar API dapat diakses secara publik.
*   **Benefit**: Aplikasi lebih stabil, lebih aman, dan 100% kompatibel dengan *shared hosting* tanpa konfigurasi port khusus.

---

## Struktur File Penting
*   `Modules/AI/Services/GeminiService.php`: Pusat logika persona dan system prompt.
*   `Modules/AI/Services/VectorService.php`: Handler koneksi Supabase REST API.
*   `Modules/AI/Http/Controllers/ChatbotController.php`: API Handler, sistem kadaluarsa, dan ekstraksi produk.
*   `Modules/AI/Filament/Resources/ChatSessionResource.php`: Manajemen dashboard admin.
*   `resources/js/Components/AiChatbot.jsx`: Parser Markdown, UI Chat, dan logika responsivitas.
php artisan ai:sync-products --force
---
*Terakhir diperbarui: 12 Mei 2026*
