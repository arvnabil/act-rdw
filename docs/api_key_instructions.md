# Instruksi Penggunaan API Key - Act RDW

Dokumen ini menjelaskan cara mengelola dan menggunakan API Key untuk keperluan integrasi dan otomatisasi pada sistem **Act RDW**.

## Cara Membuat API Key

1.  **Login ke Dashboard**: Masuk ke halaman admin (biasanya `/admin` atau melalui Filament dashboard).
2.  **Buka Menu Settings**: Di sidebar sebelah kiri, cari grup menu **Settings**.
3.  **Pilih API Keys**: Klik pada menu **API Keys**.
4.  **Tambah Key Baru**: Klik tombol **New API Key** di pojok kanan atas.
5.  **Isi Form**:
    *   **Name**: Berikan nama yang deskriptif (contoh: `Otomasi n8n`).
    *   **Expires At**: (Opsional) Tentukan kapan key ini akan kedaluwarsa. Kosongkan untuk penggunaan permanen.
6.  **Simpan**: Klik tombol **Create**.

---

## Detail Fields

| Nama Field | Fungsi |
| :--- | :--- |
| **API Key** | Token rahasia yang akan digunakan dalam header request. Key ini digenerate otomatis setelah data disimpan (berawalan `act_`). |
| **Active** | Switch untuk mengaktifkan atau menonaktifkan key secara instan tanpa menghapusnya. |
| **Debug Mode** | Jika diaktifkan, sistem akan mencatat isi **Request Payload** dan **Response** lengkap ke dalam database untuk setiap kali key ini digunakan. |
| **Last Used At** | Menampilkan kapan terakhir kali API Key ini digunakan. |

---

## Keamanan API Key

> [!CAUTION]
> API Key memberikan akses penuh ke endpoint otomatisasi. **Jangan pernah membagikan API Key** di tempat umum (seperti GitHub, forum, atau ke pihak yang tidak terpercaya).

Beberapa tips keamanan:
- **Gunakan Nama yang Spesifik**: Buatlah satu API Key untuk satu tujuan integrasi agar mudah dilacak dan dideaktivasi jika diperlukan.
- **Deaktivasi Jika Tidak Digunakan**: Jika sebuah integrasi sudah tidak aktif, segera matikan flag **Active**.
- **Mode Debug Seperlunya**: Gunakan **Debug Mode** hanya saat proses development. Matikan jika sudah masuk ke tahap produksi untuk menghemat penyimpanan database.

---

## Log Aktivitas API

Semua request yang menggunakan API Key dicatat di dalam tabel `api_logs` di database. Catatan ini meliputi:
- Waktu Request
- Endpoint yang diakses
- Method (GET/POST)
- Status Code (200, 401, 422, dsb)
- IP Address & User Agent
- Durasi eksekusi dalam milidetik

Data ini sangat berguna untuk memantau performa integrasi dan melakukan troubleshooting jika terjadi error pada otomasi.
