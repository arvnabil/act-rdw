# Event Module Documentation

## 1. Fitur Module Event

Module Event dirancang untuk menangani seluruh siklus hidup acara, mulai dari manajemen acara oleh organizer, pendaftaran peserta, hingga penerbitan sertifikat.

### Fitur Utama:

#### A. Frontend (Public & User)

-   **Event Listing**: Menampilkan daftar acara dengan fitur pencarian (Search), filter kategori, dan pengurutan (Sort by Newest, Ending Soon).
-   **Event Detail**: Halaman detail acara yang informatif, menampilkan deskripsi, jadwal, lokasi, harga tiket, dan sisa kuota.
-   **Registration Flow**:
    -   **Auth**: Login/Register untuk peserta.
    -   **Payment**: Integrasi status pembayaran (Pending -> Paid/Joined).
    -   **Ticket**: Tiket digital dengan QR Code unik.
-   **User Dashboard (Attendee)**:
    -   **My Events**: Daftar acara yang diikuti dengan status (Joined, Registered, Certified).
    -   **Ticket Download**: Unduh tiket dalam format PDF.
    -   **Certificate Claiming**: Klaim sertifikat setelah acara selesai (status Registered/Attended).
    -   **Profile Management**: Update data diri peserta (Nama, Email, Avatar).

#### B. Backend (Filament Admin)

-   **Manajemen Event**: CRUD (Create, Read, Update, Delete) data acara.
-   **Manajemen Kategori**: Pengelompokan acara.
-   **Manajemen Registrasi**: Memantau pendaftar, mengubah status pembayaran/kehadiran.
-   **Validasi Tiket**:
    -   **Scan QR**: Verifikasi kehadiran peserta di lokasi acara.
    -   **Manual Check-in**: Update status peserta via panel admin.

---

## 2. User Organizer

Fitur "User Organizer" difokuskan pada manajemen entitas penyelenggara acara. Dalam sistem ini, Organizer adalah entitas yang memiliki dan mengelola acara.

### Kapabilitas:

-   **Organizer Profile**: Manajemen data profil penyelenggara (Nama, Deskripsi, Kontak, Logo).
-   **Event Ownership**: Setiap acara dikaitkan dengan satu Organizer.
-   **User Management**: Melihat daftar peserta (Event Users) yang terdaftar pada acara milik Organizer tersebut.
-   **Reporting**: (Potensial) Melihat ringkasan penjualan tiket/pendaftaran per Organizer.

Di panel Admin (Filament), `OrganizerResource` memungkinkan admin untuk:

-   Membuat dan mengedit profil Organizer.
-   Memantau relasi antara Organizer dan Event Users.

---

## 3. Certificate Designer (Fitur Lengkap)

Certificate Designer adalah tool visual drag-and-drop yang memungkinkan admin/organizer untuk membuat template sertifikat kustom tanpa coding.

### Fitur Detail:

#### A. Interface & Tools

-   **Drag & Drop Canvas**: Area kerja visual yang merepresentasikan ukuran sertifikat (A4 Landscape/Portrait).
-   **Ruler & Guides**: Penggaris (Horizontal/Vertical) dan garis bantu (Guide Lines) yang dapat ditarik untuk memastikan tata letak yang presisi.
-   **Element Toolbar**: Panel untuk menambahkan elemen ke canvas.

#### B. Elemen yang Didukung

1.  **Text**: Teks statis (misal: "Sertifikat Penghargaan").
2.  **Variable (Dynamic Data)**: Placeholder yang akan diganti otomatis dengan data peserta/acara saat generate.
    -   `{participant_name}`
    -   `{event_title}`
    -   `{date}`
    -   `{certificate_code}`
3.  **Image**: Upload logo atau gambar pendukung.
4.  **Signature**: Upload tanda tangan panitia/pejabat.
5.  **QR Code**: Generate otomatis QR Code verifikasi sertifikat.

#### C. Kapabilitas Edit

-   **Positioning**: Geser elemen (Drag) atau atur koordinat X/Y presisi.
-   **Styling**:
    -   **Font**: Pilihan jenis font (Inter, Roboto, Serif, dll).
    -   **Size & Weight**: Ukuran teks dan ketebalan (Bold/Regular).
    -   **Color**: Color picker untuk teks.
    -   **Alignment**: Rata kiri, tengah, kanan.
-   **Undo/Redo**: Membatalkan atau mengulang aksi perubahan terakhir.
-   **Background**: Set gambar latar belakang sertifikat.

#### D. Preview & Save

-   **Live Preview**: Melihat hasil desain secara langsung.
-   **Save Template**: Menyimpan konfigurasi JSON desain ke database untuk digunakan pada Event tertentu.

---

---

## 4. Update Log (29 Desember 2025)

Berikut adalah ringkasan fitur dan perbaikan yang telah diimplementasikan dalam iterasi terakhir:

### A. Dashboard & Analytics

1.  **Event Dashboard**: Halaman khusus admin (`/activioncms/event-dashboard`) dengan statistik:
    -   **Overview Stats**: Total Active Events, Registrations, Revenue, Certificates Issued.
    -   **Registration Trend**: Grafik garis pendaftaran harian (30 hari terakhir).
    -   **Popular Events**: Grafik batang 5 event terpopuler.
    -   **Latest Registrations**: Tabel 5 pendaftar terbaru secara real-time.
2.  **Main Dashboard Widget**: Menambahkan widget "Event Module Features" di Dashboard Utama activioncms untuk ringkasan akses cepat.

### B. File Management System

1.  **Centralized Management**: Standardisasi upload file menggunakan trait `HasEventFileManagement`.
2.  **Konfigurasi Upload**:
    -   **Limit Size**: Dibatasi maksimal 1MB/2MB sesuai konteks.
    -   **Preserve Filename**: Nama file asli dipertahankan saat upload.
    -   **Disk Configuration**: Semua file publik (Logo, Avatar, Payment Proof) tersimpan di disk `public` dengan direktori dinamis.
3.  **Auto Cleanup**: Sistem otomatis menghapus file lama saat diganti atau saat data dihapus (termasuk gambar di dalam RichEditor).

### C. UI/UX & Navigation

1.  **Sidebar Reorganization**: Menu module Event dikelompokkan menjadi dua bagian logis:
    -   **Master Data**: Event Categories, Organizers, Events, Event Documentations.
    -   **Manage Data**: Event User, Event Certificates, Event Registrations.
2.  **Widget Styling**: Perbaikan tampilan widget dashboard menggunakan Inline Styles untuk memastikan kompatibilitas layout grid dan ukuran icon tanpa bergantung penuh pada JIT Compiler.

### D. Bug Fixes & Refactoring

1.  **Routing Fix**: Perbaikan error `RouteNotFoundException` pada Dashboard dengan mengubah `EventDashboard` menjadi `Page` standar.
2.  **Type Safety**: Perbaikan error tipe data (Static vs Non-Static, Getter Types) pada form dan widget Filament.

_Dokumentasi ini mencakup fitur per Tanggal 29 Desember 2025._
