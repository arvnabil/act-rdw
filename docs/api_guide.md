# Panduan API (API Guide) - Act RDW

Dokumen ini berisi referensi teknis untuk menggunakan API pada proyek **Act RDW**. API ini dirancang untuk memfasilitasi integrasi dengan sistem eksternal seperti n8n, CRM, atau bot otomatisasi.

## Dasar (Fundamentals)

### Base URL
Semua request API dilakukan ke:
`https://[domain-anda]/api`

### Autentikasi
API ini menggunakan autentikasi berbasis **Static API Key**. Setiap request wajib menyertakan header berikut:

| Header | Value | Deskripsi |
| :--- | :--- | :--- |
| `X-API-KEY` | `[api_key_anda]` | API Key yang digenerate dari Dashboard Filament. |
| `Accept` | `application/json` | Memastikan respon dalam format JSON. |

---

## Endpoint Otomatisasi (Automation)

Endpoint ini berada di bawah prefix `/automation`.

### 1. Mengambil Data Prospek (Get Leads)
Digunakan untuk menarik data form submission terbaru. Sangat berguna untuk sinkronisasi ke CRM atau spreadsheet di n8n.

**Endpoint:** `GET /automation/leads`

**Parameter (Query):**
- `limit` (int, optional): Jumlah data per halaman (default: 50).
- `form_key` (string, optional): Filter berdasarkan key form tertentu.

**Respon Contoh:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "form_key": "contact_us",
            "payload": {
                "name": "John Doe",
                "email": "john@example.com"
            },
            "created_at": "2024-04-18T10:00:00Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "total": 245
    }
}
```

---

### 2. Mengirim Data Prospek (Push Lead)
Digunakan untuk memasukkan data prospek dari sumber eksternal ke dalam sistem Act RDW.

**Endpoint:** `POST /automation/leads`

**Payload (JSON):**
| Field | Type | Required | Deskripsi |
| :--- | :--- | :--- | :--- |
| `form_key` | string | Yes | Identifier form (misal: `external_landing`). |
| `payload` | array | Yes | Data prospek (key-value pair). |
| `page_slug` | string | No | Slug halaman asal (default: `external-api`). |

**Contoh Body:**
```json
{
    "form_key": "facebook_ads",
    "payload": {
        "full_name": "Jane Smith",
        "phone": "+628123456789",
        "interest": "Property A"
    }
}
```

---

### 3. Tracking Trigger WhatsApp (Track WA Trigger)
Digunakan untuk mencatat aktivitas ketika sebuah otomasi eksternal mengirim WhatsApp ke user, agar data analytics tetap sinkron.

**Endpoint:** `POST /automation/wa-trigger`

**Payload (JSON):**
| Field | Type | Required | Deskripsi |
| :--- | :--- | :--- | :--- |
| `phone` | string | Yes | Nomor telepon tujuan. |
| `text` | string | No | Isi pesan yang dikirim. |
| `source` | string | No | Label sumber (misal: `N8N Workflow`). |

---

## Endpoint Katalog & Berita (JSON:API)

Untuk memfasilitasi integrasi frontend modern, API Katalog Produk dan Berita menyajikan data yang mematuhi standar internasional **JSON:API** bawaan **Laravel 13**. 

Respons dari endpoint ini selalu menyertakan header `Content-Type: application/vnd.api+json` dan dibungkus di dalam struktur standar berikut:
* `data`: Berisi payload utama (objek tunggal atau array). Terdiri dari `id`, `type`, `attributes`, dan `relationships`.
* `included`: Menyimpan data relasi yang dimuat secara bersarang (*eager-loaded*) untuk menghindari query berulang (*N+1 problem*).
* `links`: Link navigasi pagination standar.
* `meta`: Metadata halaman seperti jumlah total record, halaman saat ini, dll.

### 1. Daftar Produk (Get Products)
Mendapatkan seluruh daftar produk aktif. Relasi `brand`, `service`, `categories`, `solutions`, dan `seo` otomatis diserialisasikan secara terstandardisasi.

**Endpoint:** `GET /products`

**Parameter (Query):**
* `limit` (int, optional): Batas item per halaman (default: 15).
* `category` (string, optional): Filter berdasarkan slug kategori (misal: `video-conference`).
* `brand` (string, optional): Filter berdasarkan slug brand (misal: `logitech`).

**Contoh Respon:**
```json
{
  "data": [
    {
      "id": "1",
      "type": "products",
      "attributes": {
        "name": "Logitech Meetup 2",
        "slug": "logitech-meetup-2",
        "sku": "LOGI-MEET2",
        "price": 15000000.0,
        "description": "A huddle room camera.",
        "image_url": "https://act-rdw.test/storage/products/meetup2.png",
        "datasheet_url": null,
        "specs": null,
        "features": null,
        "tags": null,
        "specification_text": null,
        "features_text": null,
        "link_accommerce": null,
        "whatsapp_note": null,
        "is_active": true,
        "is_featured": true
      },
      "relationships": {
        "brand": {
          "data": { "id": "1", "type": "brands" }
        },
        "categories": {
          "data": [
            { "id": "2", "type": "categories" }
          ]
        },
        "seo": {
          "data": { "id": "5", "type": "seo" }
        }
      }
    }
  ],
  "included": [
    {
      "id": "1",
      "type": "brands",
      "attributes": {
        "name": "Logitech",
        "slug": "logitech"
      }
    },
    {
      "id": "2",
      "type": "categories",
      "attributes": {
        "name": "Video Conference",
        "slug": "video-conference"
      }
    },
    {
      "id": "5",
      "type": "seo",
      "attributes": {
        "title": "Logitech Meetup 2 - ACTiV",
        "description": "Beli Logitech Meetup 2 bergaransi resmi.",
        "keywords": null,
        "og_title": null,
        "og_description": null,
        "og_image_url": null,
        "canonical_url": null,
        "noindex": false
      }
    }
  ],
  "links": {
    "first": "https://act-rdw.test/api/products?page=1",
    "last": "https://act-rdw.test/api/products?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "https://act-rdw.test/api/products",
    "per_page": 15,
    "to": 1,
    "total": 1
  }
}
```

---

### 2. Detail Produk (Get Product Detail)
Mendapatkan detail lengkap satu produk spesifik berdasarkan slug.

**Endpoint:** `GET /products/{slug}`

---

### 3. Daftar Berita (Get News)
Mendapatkan seluruh daftar berita/artikel yang diterbitkan. Relasi `categories`, `tags`, `author`, dan `seo` terintegrasi penuh.

**Endpoint:** `GET /news`

---

### 4. Detail Berita (Get News Detail)
Mendapatkan isi berita lengkap berdasarkan slug.

**Endpoint:** `GET /news/{slug}`

---

## Contoh Penggunaan (Code Snippet)

### cURL
```bash
curl -X GET "https://act-rdw.test/api/automation/leads?limit=10" \
     -H "X-API-KEY: YOUR_KEY_HERE" \
     -H "Accept: application/json"
```

### JavaScript (Fetch)
```javascript
const response = await fetch('https://act-rdw.test/api/automation/leads', {
    method: 'GET',
    headers: {
        'X-API-KEY': 'YOUR_KEY_HERE',
        'Accept': 'application/json'
    }
});
const result = await response.json();
console.log(result.data);
```

---

## Logging & Debugging

Setiap request API akan dicatat di dalam sistem. Jika **Debug Mode** diaktifkan pada setting API Key di dashboard:
1. Keseluruhan **Payload** (request body) akan disimpan.
2. Keseluruhan **Response** dari server akan disimpan.

Gunakan fitur ini hanya saat pengembangan untuk menjaga performa database.
