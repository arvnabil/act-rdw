# ACT RDW - Professional CMS & SEO Platform

Advanced content management system built with **Laravel 11**, **Filament v3**, and a modular architecture. Designed for performance, scalability, and deep SEO optimization.

---

## 🚀 Key Features

### 🔍 SEO & Indexing (Search Console Style)

- **Index Coverage Dashboard**: Real-time internal monitoring of URL indexation (Indexed vs Noindex).
- **Automated Sitemap**: Intelligent `sitemap.xml` generation with priority logic and frequency management.
- **Active Route Detector**: Centralized engine that scans all modules (News, Products, Services) for public routes.
- **SEO Audit System**: Scoring engine to identify missing metadata, missing canonicals, and SEO issues.
- **SERP Google Preview**: Real-time interactive preview of Google search results in the admin panel.
- **Meta Management**: Granular control over OpenGraph, Meta Robots, and Canonical URLs.

### 📝 Content Management (CMS)

- **Modular Architecture**: Clean separation between Core, News, Projects, and Services.
- **News & Blogging**: Categorized articles with SEO-ready slugs and rich media support.
- **Project Portfolio**: Showcase technical and architectural projects.
- **Menu Builder**: Multi-level drag-and-drop menu management for site navigation.
- **Client Management**: Managing client relationships and industry partner logos.

### 🛠️ Specialized Modules

- **Product Ecosystem**: Full catalog management for Brands, Categories, and Products.
- **Service Solutions**: Deep-nested service listings (Service -> Solutions) with dedicated routing.
- **Event Management Engine**:
    - Full registration & reservation workflow.
    - Automated PDF Certificate generation.
    - Event Documentation & Media galleries.
    - Organizer and Participant management.
- **Interactive Configurator**: Custom logic for room/space configurations (Logi Room Configurator).

### ⚙️ Technical Stack

- **Framework**: Laravel 11 (Latest)
- **Admin UI**: Filament v3 (Modern, responsive, and blazing fast)
- **Frontend**: Inertia.js + React (for high-interaction components)
- **Modular System**: `nwidart/laravel-modules` for domain-driven architecture.
- **Persistence**: Custom SEO monitoring records with background sync triggers.

---

## 📦 Navigation & Administration

The Admin Panel is divided into strategic groups:

1.  **Site Management**: Primary content (Pages, News, Projects, Menus).
2.  **Core**: Enterprise data (Brands, Products, Categories).
3.  **Services**: Service catalog and sub-solutions.
4.  **Events**: Complete event lifecycle management.
5.  **SEO**: Dedicated suite for Index Coverage, Settings, and Audit.
6.  **Settings**: Global site configurations.

---

## ⚡ Commands

| Command                            | Action                                             |
| :--------------------------------- | :------------------------------------------------- |
| `php artisan seo:generate-sitemap` | Rebuilds sitemap.xml and syncs Index Coverage data |
| `php artisan migrate`              | Updates database schema                            |
| `php artisan module:list`          | Lists all active domain modules                    |

---

_Developed for professional web standards and search excellence._
