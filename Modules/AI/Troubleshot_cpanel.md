# 🛠️ cPanel Troubleshooting Log - ACTiV

Dokumen ini mencatat masalah teknis yang ditemukan selama proses deployment di server cPanel (MariaDB & PHP 8.3) dan solusi yang telah teruji berhasil.

---

## 1. Git Pull Conflict (Overwritten Files)
**Masalah**: Error `Your local changes to the following files would be overwritten by merge: public/sitemap.xml`. Terjadi karena file sitemap atau folder project lain (staging/backup) terdeteksi sebagai perubahan lokal.

**Solusi**:
1.  Buang perubahan pada file yang bentrok:
    ```bash
    git checkout public/sitemap.xml
    ```
2.  Pastikan folder lain diabaikan lewat `.gitignore`:
    ```text
    /staging/
    /wp-backup/
    /bio/
    error_log
    public/sitemap.xml
    ```
3.  Lakukan Pull ulang:
    ```bash
    git pull
    ```

---

## 2. Migration Error (MariaDB vs PostgreSQL)
**Masalah**: Error `Syntax error or access violation` saat menjalankan `php artisan migrate` karena perintah `CREATE EXTENSION IF NOT EXISTS vector`. (MariaDB tidak mendukung extension vector).

**Solusi**:
1.  Lewati migrasi tersebut secara manual dengan memasukkan data ke tabel `migrations` melalui phpMyAdmin atau Terminal:
    ```sql
    INSERT INTO migrations (migration, batch) VALUES ('2022_08_03_000000_create_vector_extension', 1);
    ```
2.  Jika `php artisan migrate` tetap bilang **"Nothing to migrate"** padahal tabel modul masih hilang, gunakan path spesifik:
    ```bash
    php artisan migrate --path=Modules/AI/Database/Migrations
    ```
    *(Ulangi untuk modul lain jika perlu, ganti nama folder AI dengan nama modul terkait).*

---

## 3. Dashboard TypeError (Google Protobuf & PHP 8.3)
**Masalah**: Error `strlen(): Argument #1 ($string) must be of type string, Google\Analytics\Data\V1beta\RunReportResponse given`. Terjadi karena ketidakcocokan tipe data pada library Google di PHP 8.3.

**Solusi**:
Paksa update library Google ke versi yang mendukung PHP 8.3:
```bash
composer require google/protobuf:^4.31 google/gax:^1.38 --with-all-dependencies
php artisan optimize:clear
```
*Catatan: Jika error masih ada, matikan widget analytics di `config/google-analytics.php` dengan mengubah 'filament_dashboard' => false.*

---

## 4. Google Application Credentials Path
**Masalah**: File JSON kredensial tidak terbaca atau path tidak valid.

**Solusi**:
Gunakan path absolut lengkap mulai dari root home cPanel di file `.env`:
```env
GOOGLE_APPLICATION_CREDENTIALS=/home/USERNAME_CPANEL/public_html/storage/gcp-service-account.json
```
Gunakan perintah `pwd` di terminal cPanel untuk mengetahui path folder yang tepat.

---
*Log terakhir diperbarui: 12 Mei 2026*
