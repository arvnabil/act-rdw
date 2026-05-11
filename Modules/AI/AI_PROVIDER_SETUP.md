# 🤖 Vion AI — Setup Guide: AI Provider Configuration

Dokumen ini menjelaskan cara mengonfigurasi **AI Provider** untuk sistem VION by ACTiV.  
Tersedia dua pilihan provider yang bisa di-switch kapan saja via `.env`.

---

## ⚡ Perbandingan Provider

| Fitur | Google AI Studio | Vertex AI (GCP) |
|---|---|---|
| **Autentikasi** | API Key | Service Account JSON |
| **Setup** | ✅ Sangat mudah | ⚙️ Perlu konfigurasi GCP |
| **Harga** | Freemium / Pay-as-you-go | Pay-as-you-go (lebih mahal) |
| **Cocok untuk** | Development & Small Production | Enterprise & Large Scale |
| **Data Privacy** | Standard | ✅ Lebih ketat (VPC, IAM) |
| **Rate Limit** | Ada | Lebih tinggi |
| **Region** | Global | Spesifik (us-central1, dll) |

---

## 🟢 Option 1: Google AI Studio (Default)

### Langkah 1 — Dapatkan API Key

1. Buka [https://aistudio.google.com/app/apikey](https://aistudio.google.com/app/apikey)
2. Klik **"Create API Key"**
3. Pilih project Google Cloud Anda (atau buat baru)
4. Copy API Key yang dihasilkan

### Langkah 2 — Konfigurasi `.env`

```env
# Pilih provider Google AI Studio
AI_PROVIDER=google

# API Key dari AI Studio
GEMINI_API_KEY=AIzaSy...your-api-key-here

# Model yang digunakan (opsional, default sudah disediakan)
GOOGLE_AI_MODEL=models/gemini-2.0-flash
GOOGLE_AI_EMBEDDING_MODEL=models/gemini-embedding-001
```

### Langkah 3 — Verifikasi

Tidak perlu install library tambahan. Cukup jalankan:

```bash
docker-compose exec activ.test php artisan config:clear
docker-compose exec activ.test php artisan cache:clear
```

### ✅ Selesai!

Chatbot VION by ACTiV sudah aktif menggunakan Google AI Studio.

---

## 🔵 Option 2: Vertex AI (Google Cloud Platform)

### Prasyarat
- Akun Google Cloud dengan **billing aktif**
- Project GCP yang sudah dibuat
- Vertex AI API sudah di-enable

### Langkah 1 — Enable Vertex AI API

1. Buka [Google Cloud Console](https://console.cloud.google.com)
2. Pilih project Anda
3. Pergi ke **APIs & Services → Library**
4. Cari **"Vertex AI API"** dan klik **Enable**

### Langkah 2 — Buat Service Account

1. Pergi ke **IAM & Admin → Service Accounts**
2. Klik **"Create Service Account"**
3. Isi nama (misal: `minva-ai-service`)
4. Tambahkan role: **`Vertex AI User`**
5. Klik **Done**
6. Buka service account yang dibuat → tab **Keys**
7. Klik **Add Key → Create new key → JSON**
8. File JSON akan ter-download otomatis

### Langkah 3 — Upload Service Account Key

Upload file JSON ke server/container Anda:

```bash
# Copy file key ke folder storage project
cp ~/Downloads/your-service-account.json \
   /path/to/project/storage/gcp-service-account.json

# Atau via Docker
docker cp your-service-account.json activ.test:/var/www/html/storage/gcp-service-account.json
```

> [!CAUTION]
> Jangan pernah commit file `.json` Service Account ke Git!  
> Pastikan `storage/*.json` sudah ada di `.gitignore`

### Langkah 4 — Install Library Google Cloud

```bash
docker-compose exec activ.test composer require google/cloud-aiplatform
```

### Langkah 5 — Konfigurasi `.env`

```env
# Ganti ke Vertex AI
AI_PROVIDER=vertex

# Vertex AI Configuration
VERTEX_PROJECT_ID=your-gcp-project-id
VERTEX_LOCATION=asia-southeast1
VERTEX_MODEL=gemini-2.0-flash-001
VERTEX_EMBEDDING_MODEL=text-embedding-005

# Path ke file Service Account JSON (di dalam container)
GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/storage/gcp-service-account.json
```

> [!TIP]
> Gunakan region `asia-southeast1` (Singapura) untuk latency terendah dari Indonesia.  
> Alternatif: `us-central1` jika model tertentu belum tersedia di Asia.

### Langkah 6 — Clear Config & Test

```bash
docker-compose exec activ.test php artisan config:clear
docker-compose exec activ.test php artisan cache:clear

# Tes koneksi (opsional)
docker-compose exec activ.test php artisan tinker --execute="\
    \$service = app(Modules\AI\Services\GeminiService::class); \
    echo 'Provider aktif: ' . \$service->activeProvider() . PHP_EOL;"
```

### ✅ Selesai!

VION by ACTiV sekarang berjalan di Vertex AI (Google Cloud).

---

## 🔄 Cara Switch Provider

Cukup ganti **satu baris** di `.env`:

```env
# Gunakan Google AI Studio
AI_PROVIDER=google

# Gunakan Vertex AI
AI_PROVIDER=vertex
```

Kemudian jalankan:

```bash
docker-compose exec activ.test php artisan config:clear
```

> [!NOTE]
> Tidak ada perubahan kode yang diperlukan! Seluruh logika switch ditangani otomatis oleh `GeminiService`.

---

## 🗂️ Struktur File Terkait

```
Modules/AI/
├── Interfaces/
│   └── GeminiDriverInterface.php   ← Kontrak standar semua driver
├── Drivers/
│   ├── GoogleAIDriver.php          ← Driver Google AI Studio
│   └── VertexAIDriver.php          ← Driver Vertex AI
├── Services/
│   └── GeminiService.php           ← Manager: resolve driver dari .env
config/
└── ai.php                          ← Semua konfigurasi AI provider
```

---

## ❓ Troubleshooting

### Error: `Class not found: Google\Cloud\AIPlatform`
```bash
# Install library yang dibutuhkan untuk Vertex AI
docker-compose exec activ.test composer require google/cloud-aiplatform
```

### Error: `Could not load the default credentials`
- Pastikan path di `GOOGLE_APPLICATION_CREDENTIALS` benar dan file bisa dibaca.
- Cek permission file: `chmod 644 storage/gcp-service-account.json`

### Error: `Vertex AI API has not been used in project`
- Enable Vertex AI API di Google Cloud Console terlebih dahulu.

### Chat tidak merespons setelah switch
```bash
docker-compose exec activ.test php artisan config:clear
docker-compose exec activ.test php artisan cache:clear
```
