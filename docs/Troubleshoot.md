SQLSTATE[22007]: Invalid datetime format: 1366 Incorrect string value: '\x81\xDT\xDAZ\x0AX...' for column 'cache'.'value'
Penyebab:
Fitur Google Analytics Widget di dashboard mencoba menyimpan (cache) data statistik analytics dari Google ke tabel cache database MySQL. Karena respon Google Analytics mengandung data biner / terkompresi (gzip), MySQL menolaknya karena kolom cache.value bertipe teks biasa (MEDIUMTEXT UTF-8).

Cara Mengatasinya (Pilih Salah Satu):
Solusi 1: Ganti Cache Driver ke file di .env (Paling Direkomendasikan & Cepat)
Cache file di Laravel jauh lebih cepat untuk cPanel dan tidak pernah bermasalah dengan karakter biner Google Analytics.

Buka file .env di cPanel Anda (via File Manager atau Terminal).
Cari baris:
env
CACHE_STORE=database
(Atau CACHE_DRIVER=database)
Ubah menjadi:
env
CACHE_STORE=file
Simpan file, lalu jalankan di terminal cPanel:
bash
php artisan optimize:clear
Solusi 2: Ubah Kolom Tabel Cache via phpMyAdmin
Jika Anda tetap ingin menggunakan cache database, ubah tipe kolom value agar menerima data binary (MEDIUMBLOB):

Buka phpMyAdmin di cPanel, pilih database Anda (activcoi_larativ_prod).
Masuk ke tab SQL dan jalankan query berikut:
sql
ALTER TABLE `cache` MODIFY `value` MEDIUMBLOB;
