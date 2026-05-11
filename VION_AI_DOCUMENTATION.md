# 🤖 Vion AI - Documentation

Vion AI adalah asisten chatbot cerdas yang dikembangkan untuk platform ACTiV sebagai **ICT Solutions Consultant**. Sistem ini menggunakan pendekatan **Hybrid Interaction** untuk memberikan pengalaman pengguna yang cepat namun tetap pintar.

---

## 🌟 Fitur Utama

### 1. Hybrid FAQ-AI System
*   **Instant Response**: Jika tombol dikonfigurasi dengan "Instant Response", Vion akan menjawab detik itu juga tanpa menggunakan API AI (Hemat token & Cepat).
*   **AI Analysis**: Jika "Instant Response" kosong, Vion akan menggunakan AI (Gemini/OpenAI) untuk menganalisa konteks dan menjawab secara dinamis.
*   **Improved Formatting**: Chatbot mendukung Markdown tingkat lanjut. List (poin-poin) dan teks tebal akan tampil rapi meskipun AI mengirimkannya dalam format gabungan.

### 2. Admin Management (Filament v4)
*   Kontrol penuh melalui menu **AI Management** di Dashboard Admin.
*   **Visibility Toggle**: Sembunyikan/Munculkan chatbot di seluruh website secara instan.
*   **Instant Sync**: Menggunakan *Inertia Shared Props* sehingga perubahan status di admin langsung berefek di website saat refresh tanpa jeda API.

### 3. Smart Session & Window Persistence
*   **Lead Form Requirement**: Pengunjung wajib mengisi Nama, Email, dan Perusahaan sebelum memulai chat (Sesi 24 jam).
*   **Window State Memory**: Vion mengingat apakah jendela chat sedang terbuka atau tertutup saat halaman direfresh.
*   **User Data Restoration**: Vion otomatis memulihkan data Nama/Email user sehingga tidak perlu mengisi form ulang saat refresh.

### 4. Quick Actions Menu (Sparkle ✨)
*   Tersedia tombol ikon bintang di area input untuk memanggil kembali menu utama (Starter Buttons) kapan saja tanpa harus memulai ulang sesi chat.

---

## 🛠️ Panduan Teknis

### Cara Menjalankan Seeder (Data Awal)
Untuk meriset atau memasukkan data contoh informasi perusahaan:
```bash
php artisan db:seed --class=Modules\\AI\\Database\\Seeders\\AiChatbotSettingsSeeder
```

### Proses Build Aset (Penting!)
Setiap kali ada perubahan pada file frontend (`AiChatbot.jsx` atau `app.jsx`), jalankan perintah:
```bash
docker compose exec activ.test npm run build
```

### Lokasi File Penting
*   **Frontend**: `resources/js/Components/AiChatbot.jsx`
*   **Controller**: `Modules/AI/Http/Controllers/ChatbotController.php`
*   **Admin Page**: `Modules/AI/Filament/Pages/ManageAiSettings.php`
*   **Seeder**: `Modules/AI/Database/Seeders/AiChatbotSettingsSeeder.php`

---

## ⚙️ Sinkronisasi Visibilitas
Sistem menggunakan **Inertia Shared Props** untuk memastikan status ON/OFF chatbot dari admin terbaca secara instan oleh browser saat halaman dimuat (Server-side check), menghilangkan jeda waktu pemuatan (flicker).

---
*Dokumentasi ini dibuat pada: 12 Mei 2026*
