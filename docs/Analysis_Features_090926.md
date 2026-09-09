# 🔍 Analisis Project: ACT RDW — CMS & SEO Platform

## 📋 Ringkasan Umum

**ACT RDW** adalah platform CMS profesional berbasis **Laravel 13** dengan arsitektur modular. Dirancang untuk manajemen konten enterprise-level dengan fitur SEO mendalam, admin panel Filament v4, dan integrasi AI.

| Atribut         | Detail                               |
| --------------- | ------------------------------------ |
| **Framework**   | Laravel 13 (PHP ^8.2)                |
| **Admin Panel** | Filament v4                          |
| **Frontend**    | Inertia.js + Vite                    |
| **AI**          | Google Gemini (Vertex AI) + pgvector |
| **Auth**        | JWT Auth (`tymon/jwt-auth`)          |
| **DB**          | PostgreSQL (dengan pgvector)         |
| **Arsitektur**  | Domain-driven Modular (`Modules/`)   |

---

## 📦 Daftar Modul (15 Modul)

### 1. 🤖 **AI** — Kecerdasan Buatan

**Path**: `Modules/AI/`

**Fitur:**

- Chatbot berbasis Gemini AI (`ChatbotController`)
- Semantic search dengan pgvector embeddings
- Product embedding untuk pencarian semantik
- Semantic cache service (singleton per request)

**Models:** `ChatMessage`, `ChatSession`, `ProductEmbedding`

**Services:**

- `GeminiService` — Koneksi ke Google Gemini / Vertex AI
- `SemanticCacheService` — Cache hasil embedding
- `VectorService` — Operasi vector database

**Artisan Command:** `SyncProductEmbeddings` (sync embedding produk)

**Routes:** `api/ai/*` (API) + web routes

---

### 2. 📊 **Analytics** — Pelacakan & Analitik

**Path**: `Modules/Analytics/`

**Fitur:**

- Tracking klik event (`AnalyticsClickEvent`)
- Tracking WhatsApp analytics (`AnalyticsWhatsapp`)
- Background jobs untuk async tracking
- Dashboard Filament dengan stats

**Models:** `AnalyticsClickEvent`, `AnalyticsWhatsapp`

---

### 3. 🏗️ **CMS** — Content Management System

**Path**: `Modules/CMS/`

**Fitur:**

- Dynamic Page Builder (`PageBuilderController`)
- Section-based content management
- Dynamic routing resolver (`DynamicResolverController`)
- SectionDataResolver service untuk flexible content

**Models:** `Page`, `PageSection`

**Controllers:** `PageController`, `DynamicResolverController`, `Admin/PageBuilderController`

---

### 4. 👥 **Clients** — Manajemen Klien

**Path**: `Modules/Clients/`

**Fitur:**

- Manajemen profil klien & partner
- Logo client untuk showcasing
- CRUD via Filament admin

**Models:** `Client`

---

### 5. 🎪 **Events** — Manajemen Event

**Path**: `Modules/Events/` _(Modul terbesar)_

**Fitur:**

- Pendaftaran & registrasi event
- Sistem sertifikat PDF otomatis (`CertificateGenerator`)
- Validasi sertifikat (QR Code support)
- Designer sertifikat interaktif
- Absensi/attendance tracking
- Dokumentasi & galeri media event
- Manajemen organizer & peserta

**Models:**

- `Event`, `EventCategory`
- `EventRegistration`, `EventUser`
- `EventCertificate`, `EventUserCertificate`
- `EventDocumentation`
- `Organizer`

**Controllers:**

- `EventController`, `EventRegistrationController`
- `AttendanceController`
- `CertificateDesignerController`, `CertificateValidationController`
- `DashboardController`, `AuthController`

**Services:** `CertificateGenerator` (PDF generation)

---

### 6. 📝 **FormBuilder** — Form Dinamis

**Path**: `Modules/FormBuilder/`

**Fitur:**

- Form submission handling
- Email notification via queue jobs
- Mail templates untuk konfirmasi

**Models:** `FormSubmission`

**Jobs:** Background job untuk kirim email

---

### 7. 🧭 **Menu** — Navigasi Situs

**Path**: `Modules/Menu/`

**Fitur:**

- Multi-level menu builder
- Drag-and-drop ordering (via Filament)
- MenuResolver service

**Models:** `Menu`, `MenuItem`

**Services:** `MenuResolver`

---

### 8. 📰 **News** — Berita & Blog

**Path**: `Modules/News/`

**Fitur:**

- Artikel berita dengan kategori & tag
- SEO-ready slugs
- REST API endpoint (`NewsApiController`)
- API Transformers
- Rich media support

**Models:** `News`, `NewsCategory`, `NewsTag`

**Controllers:** `NewsController`, `Api/NewsApiController`

**Routes:** Web + API

---

### 9. 🛍️ **ProductCatalog** — Katalog Produk

**Path**: `Modules/ProductCatalog/`

**Fitur:**

- Manajemen produk lengkap (Brand → Category → Product)
- REST API endpoint (`ProductApiController`)
- Repository pattern (`Repositories/`)
- API Transformers
- Integrasi dengan AI embeddings

**Models:** `Product`, `ProductCategory`, `Brand`

**Controllers:** `ProductController`, `BrandController`, `Api/ProductApiController`

**Services:** `ProductService`

**Routes:** Web + API

---

### 10. 🏗️ **Projects** — Portfolio Proyek

**Path**: `Modules/Projects/`

**Fitur:**

- Showcase portfolio proyek
- Import CSV via Filament Importer
- CRUD lengkap di admin

**Models:** `Project`

**Filament:** `ProjectImporter` (bulk import)

---

### 11. 🔍 **SEO** — Optimasi Mesin Pencari

**Path**: `Modules/SEO/` _(Modul paling kompleks)_

**Fitur:**

- **Index Coverage Dashboard** — monitoring status indexasi URL (Indexed vs Noindex)
- **SEO Audit System** — scoring engine untuk isu SEO
- **SERP Preview** — preview Google search result real-time
- **Meta Management** — OpenGraph, Meta Robots, Canonical URLs
- **Active Route Detector** — scan semua module untuk public routes
- **Sitemap Generator** — otomatis buat sitemap.xml
- **SEO Monitoring** — background sync & recording
- **Whitelist Domain** — manajemen domain yang diizinkan

**Models:** `SeoMeta`, `SeoMonitoringRecord`, `SeoWhitelistDomain`

**Services:**

- `SeoManager`, `SeoService`, `SeoAuditService`
- `SeoMonitoringService`, `SeoResolver`
- `SeoLinkParser`, `ActivePublicRouteDetector`

**JSON-LD Schemas:**

- `ArticleSchema`, `BrandSchema`, `BreadcrumbSchema`
- `EventSchema`, `FaqSchema`, `HowToSchema`
- `LocalBusinessSchema`, `OrganizationSchema`
- `ReviewSchema`, `ServiceSchema`, `WebPageSchema`, `WebsiteSchema`

**Filament:**

- Pages: `IndexCoverage`, `ManageSeoSettings`
- Resources: `SeoMetaResource`, `SeoWhitelistDomainResource`
- Widgets: `SeoOverviewStats`, `SeoHealthChart`, `SeoIndexStatusChart`, `SeoContentTypeChart`, `SeoScoreStats`, `TopSeoIssues`

---

### 12. 🔎 **Search** — Global Search

**Path**: `Modules/Search/`

**Fitur:**

- Global search endpoint lintas konten
- Lightweight (hanya Config, Http, Routes)

**Controllers:** `SearchController`

---

### 13. 🛠️ **Services** — Layanan & Solusi

**Path**: `Modules/Services/`

**Fitur:**

- Katalog layanan dengan kategori
- Sub-solusi per layanan (Service → Solutions)
- **Interactive Configurator** — logic konfigurasi ruang/space (Logi Room Configurator)
- Import/Export CSV via Filament
- API Transformers

**Models:**

- `Service`, `ServiceCategory`, `ServiceSolution`
- `Configurator`, `ConfiguratorStep`, `ConfiguratorQuestion`, `ConfiguratorOption`

**Controllers:** `ServiceController`, `ConfiguratorController`, `DynamicConfiguratorController`

**Filament:** Import & Eksport untuk semua entitas

---

### 14. ⚙️ **Settings** — Konfigurasi Global

**Path**: `Modules/Settings/`

**Fitur:**

- Key-value global settings
- API Key management dengan logging
- API usage tracking

**Models:** `Setting`, `ApiKey`, `ApiLog`

---

### 15. 💬 **WhatsApp** — Integrasi WhatsApp

**Path**: `Modules/WhatsApp/`

**Fitur:**

- WhatsApp bubble/widget konfigurasi
- Setting nomor & pesan default
- Analytics tracking (terintegrasi dengan modul Analytics)

**Models:** `WhatsAppSetting`

**Filament:** `ManageWhatsAppBubble` (halaman pengaturan)

---

## 🏛️ Arsitektur Aplikasi

```
act-rdw/
├── app/
│   ├── Console/Commands/
│   │   ├── AiIngestCommand.php         ← Ingest data ke AI
│   │   ├── GenerateSitemap.php         ← Generate sitemap
│   │   └── ModuleSeedCommand.php       ← Custom seeder per modul
│   ├── Helpers/
│   │   ├── ImageHelper.php
│   │   ├── JsonHelper.php
│   │   ├── SanitizerHelper.php
│   │   └── UploadHelper.php
│   ├── Http/Middleware/
│   │   ├── HandleInertiaRequests.php
│   │   ├── SecurityHeadersMiddleware.php
│   │   └── VerifyApiKey.php            ← API key authentication
│   ├── Models/User.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── Filament/ActivioncmsPanelProvider.php
│   ├── Traits/HasImageCleanup.php
│   └── View/Composers/SeoViewComposer.php
│
├── Modules/                             ← 15 Domain Modules
│   ├── AI/
│   ├── Analytics/
│   ├── CMS/
│   ├── Clients/
│   ├── Events/
│   ├── FormBuilder/
│   ├── Menu/
│   ├── News/
│   ├── ProductCatalog/
│   ├── Projects/
│   ├── SEO/
│   ├── Search/
│   ├── Services/
│   ├── Settings/
│   └── WhatsApp/
│
├── resources/                           ← Blade/React views
├── routes/                              ← Global routes
└── public/                              ← Public assets
```

---

## 🔗 Dependency Stack

| Kategori             | Package                                                                      |
| -------------------- | ---------------------------------------------------------------------------- |
| **Admin UI**         | `filament/filament` v4                                                       |
| **Frontend**         | `inertiajs/inertia-laravel`, `tightenco/ziggy`                               |
| **AI / ML**          | `google-gemini-php/laravel`, `google/cloud-ai-platform`, `pgvector/pgvector` |
| **PDF**              | `barryvdh/laravel-dompdf`                                                    |
| **QR Code**          | `simplesoftwareio/simple-qrcode`                                             |
| **Sitemap**          | `spatie/laravel-sitemap`                                                     |
| **Auth**             | `tymon/jwt-auth`                                                             |
| **API Docs**         | `dedoc/scramble`                                                             |
| **Google Analytics** | `bezhansalleh/filament-google-analytics`                                     |
| **PDF Parser**       | `smalot/pdfparser`                                                           |

---

## 🗺️ Admin Panel Navigation

```
Admin Panel (Filament v4)
├── Site Management
│   ├── Pages (CMS)
│   ├── News
│   ├── Projects
│   ├── Clients
│   └── Menus
├── Core
│   ├── Brands
│   ├── Products
│   └── Product Categories
├── Services
│   ├── Services
│   ├── Service Categories
│   ├── Service Solutions
│   └── Configurators
├── Events
│   ├── Events
│   ├── Event Categories
│   ├── Registrations
│   ├── Certificates
│   └── Organizers
├── SEO
│   ├── Index Coverage Dashboard
│   ├── SEO Meta Management
│   ├── Whitelist Domains
│   └── SEO Settings
├── Analytics
│   └── Click Events / WhatsApp Analytics
├── Settings
│   ├── Global Settings
│   ├── API Keys
│   └── WhatsApp Settings
└── AI
    └── Chat Sessions & Embeddings
```

---

## 💡 Pola Arsitektur Yang Digunakan

| Pola                     | Implementasi                                                                                    |
| ------------------------ | ----------------------------------------------------------------------------------------------- |
| **Modular DDD**          | Setiap domain punya folder `Models/`, `Http/`, `Filament/`, `Routes/`, `Services/`, `Database/` |
| **Repository Pattern**   | `ProductCatalog/Repositories/`                                                                  |
| **Transformer/Resource** | `News/Transformers/`, `Services/Transformers/`, `ProductCatalog/Transformers/`                  |
| **Service Layer**        | Semua business logic di `Services/`                                                             |
| **Schema Classes**       | SEO JSON-LD schemas terpisah per tipe konten                                                    |
| **Singleton Services**   | `SemanticCacheService` didaftarkan sebagai singleton                                            |
| **Queue Jobs**           | FormBuilder email, AI embedding sync                                                            |
| **Middleware**           | API key auth, security headers, Inertia                                                         |

---

## 📊 Statistik Modul

| Modul          | Models | Controllers | Filament Resources | Routes    |
| -------------- | ------ | ----------- | ------------------ | --------- |
| AI             | 3      | 1           | -                  | web + api |
| Analytics      | 2      | -           | 1+                 | -         |
| CMS            | 2      | 3           | 1+                 | web       |
| Clients        | 1      | 1           | 1                  | web       |
| Events         | 8      | 6           | 5+                 | web       |
| FormBuilder    | 1      | 1           | -                  | web       |
| Menu           | 2      | -           | 2                  | -         |
| News           | 3      | 2           | 1                  | web + api |
| ProductCatalog | 3      | 3           | 3                  | web + api |
| Projects       | 1      | 1           | 1                  | web       |
| SEO            | 3      | -           | 2 + 6 widgets      | web       |
| Search         | -      | 1           | -                  | web       |
| Services       | 7      | 3           | 2                  | web       |
| Settings       | 3      | -           | 2                  | -         |
| WhatsApp       | 1      | 1           | 1 page             | web       |
