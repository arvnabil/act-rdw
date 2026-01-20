<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Logitech Award
        News::create([
            'title' => 'ACTiV Raih Penghargaan Terbaik Logitech di Asia Tenggara',
            'slug' => 'activ-raih-penghargaan-terbaik-logitech-di-asia-tenggara',
            'excerpt' => 'Kemenangan gemilang di tahun 2025 ini bukanlah sebuah kebetulan. Ini adalah puncak dari sebuah perjalanan panjang yang dibangun di atas fondasi kerja keras.',
            'content' => '<h2>Sebuah Jejak Sejarah: Perjalanan ACTiV Menuju Puncak Bersama Logitech</h2>
<p>Kemenangan gemilang di tahun 2025 ini bukanlah sebuah kebetulan. Ini adalah puncak dari sebuah perjalanan panjang yang dibangun di atas fondasi kerja keras, pembelajaran, dan pencapaian berkelanjutan dari tahun ke tahun. PT Alfa Cipta Teknologi Virtual (ACTiV), sejak awal kemitraannya, telah menunjukkan komitmen yang luar biasa untuk menjadi yang terdepan.</p>
<p>Mari kita telusuri kembali jejak prestasi PT Alfa Cipta Teknologi Virtual (ACTiV) bersama Logitech di tahun-tahun sebelumnya:</p>
<ul>
    <li><strong>Tahun 2022: Meraih ‘Logitech Growth Partner of the Year’</strong> Di tahun ini, ACTiV mulai menunjukkan taringnya dengan meraih penghargaan sebagai mitra dengan pertumbuhan tertinggi.</li>
    <li><strong>Tahun 2023: Dianugerahi ‘Best Video Collaboration (VC) Partner Indonesia’</strong> ACTiV semakin dikenal sebagai Reseller Resmi Logitech Indonesia yang paling ahli di segmen kolaborasi.</li>
    <li><strong>Tahun 2024: Mencapai Status ‘Elite Partner’ untuk Pertama Kalinya</strong> PT Alfa Cipta Teknologi Virtual (ACTiV) akhirnya berhasil menembus level kemitraan tertinggi.</li>
</ul>
<p>Rangkaian penghargaan ini menunjukkan sebuah narasi yang jelas: PT Alfa Cipta Teknologi Virtual (ACTiV) tidak pernah berhenti berinovasi.</p>
<h2>Suara di Balik Kesuksesan</h2>
<p>Menanggapi pencapaian ini, Adi Syafitra, selaku CEO PT Alfa Cipta Teknologi Virtual (ACTiV), menyatakan, “Ini adalah momen yang luar biasa membanggakan bagi kami semua. Tiga penghargaan ini kami dedikasikan untuk setiap anggota tim—semua ACTiVers—yang telah bekerja tanpa lelah dengan semangat dan integritas."</p>',
            'thumbnail' => 'https://activ.co.id/wp-content/uploads/2024/09/activ-raih-penghargaan-terbaik-logitech-di-asia-tenggara.jpg',
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        // 2. Maxhub Dongle
        News::create([
            'title' => 'Cara Kerja dan Manfaat Screen Sharing Dongle Maxhub',
            'slug' => 'cara-kerja-dan-manfaat-screen-sharing-dongle-maxhub',
            'excerpt' => 'Secara sederhana, Screen Sharing Dongle adalah perangkat nirkabel yang memungkinkan Anda untuk menampilkan (mirroring) layar laptop ke layar yang lebih besar.',
            'content' => '<h2>Apa Itu Screen Sharing Dongle dan Mengapa Anda Membutuhkannya?</h2>
<p>Secara sederhana, Screen Sharing Dongle adalah perangkat nirkabel yang memungkinkan Anda untuk menampilkan (mirroring) layar laptop, tablet, atau smartphone ke layar yang lebih besar seperti TV, proyektor, atau Interactive Flat Panel (IFP) tanpa memerlukan kabel. Ini adalah bentuk evolusi dari Dongle Presentasi konvensional.</p>
<h2>Mengenal MAXHUB: Inovasi untuk Kolaborasi Modern</h2>
<p>MAXHUB telah dikenal luas dalam menyediakan teknologi kolaboratif. Dengan filosofi untuk menciptakan efisiensi, MAXHUB menawarkan ekosistem produk yang saling terintegrasi.</p>
<h3>MAXHUB Wireless Dongle WT13 – Plug, Tap, and Share!</h3>
<p>MAXHUB Wireless Dongle WT13 adalah jawaban bagi mereka yang mendambakan kesederhanaan. Dongle ini dirancang dengan konsep plug-and-play sejati.</p>
<ul>
    <li><strong>Koneksi Cepat & Tanpa Driver:</strong> Tidak perlu instalasi apa pun.</li>
    <li><strong>Kualitas 4K Ultra HD:</strong> Menyajikan tampilan yang tajam dan jernih.</li>
</ul>
<h3>MAXHUB Screen Sharing Box WB05 – Upgrade Ruang Meeting Anda</h3>
<p>Jika WT13 adalah tentang kesederhanaan individu, maka MAXHUB Screen Sharing Box WB05 adalah pusat kolaborasi untuk tim. Perangkat ini berfungsi sebagai jembatan yang memungkinkan berbagai perangkat untuk terhubung dan berbagi layar secara bersamaan.</p>',
            'thumbnail' => 'https://activ.co.id/wp-content/uploads/2024/08/cara-kerja-dan-manfaat-screen-sharing-dongle-maxhub.jpg',
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        // 3. Maxhub Solution
        News::create([
            'title' => 'Solusi Meeting Modern Konferensi Maxhub',
            'slug' => 'solusi-meeting-modern-konferensi-maxhub',
            'excerpt' => 'Solusi Meeting Modern Konferensi Maxhub sebuah ekosistem terintegrasi di mana perangkat keras (hardware) dan perangkat lunak (software) bekerja bersama.',
            'content' => '<h2>Apa Sebenarnya “Solusi Konferensi Maxhub”?</h2>
<h3>Solusi Berawal dari Sini: Produk Unggulan Kami</h3>
<p>Solusi Konferensi Maxhub dibangun di atas jajaran produk unggulan yang masing-masing memegang peranan penting dalam ekosistem.</p>
<h4>1. Platform Interaktif Visual: Interactive Flat Panel (IFP) Seri V6</h4>
<p>IFP adalah otak dan jantung dari solusi ini. Mulai dari V6 Classic Series hingga V6 Transcend Series yang merupakan puncak inovasi Maxhub.</p>
<h4>2. Audio & Video Imersif: Jajaran Perangkat Unified Communication (UC)</h4>
<p>Untuk memastikan setiap peserta merasa hadir, solusi ini dilengkapi dengan perangkat audio-visual canggih seperti Conference Bars (UC S10 Pro) dan Kamera PTZ (UC P25).</p>
<h3>Bagaimana Solusi Ini “Menciptakan Ruang Meeting Modern”?</h3>
<ul>
    <li><strong>Menghilangkan Hambatan Teknis:</strong> Waktu persiapan rapat kini menjadi beberapa detik saja.</li>
    <li><strong>Meningkatkan Kolaborasi Tim:</strong> Papan tulis digital tak terbatas pada IFP Seri V6 memungkinkan brainstorming real-time.</li>
    <li><strong>Menyatukan Tim Hybrid:</strong> Kamera AI dan mikrofon peredam bising memastikan peserta jarak jauh terlibat aktif.</li>
</ul>
<p>Memilih Solusi Konferensi Maxhub adalah langkah paling direct untuk mentransformasi cara tim Anda berkolaborasi, berinovasi, dan meraih kesuksesan.</p>',
            'thumbnail' => 'https://activ.co.id/wp-content/uploads/2024/07/solusi-meeting-modern-konferensi-maxhub.jpg',
            'status' => 'published',
            'published_at' => now()->subDays(8),
        ]);
    }
}
