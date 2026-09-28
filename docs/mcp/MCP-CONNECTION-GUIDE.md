# Panduan Integrasi & Koneksi MCP (Model Context Protocol)
## ACT RDW Laravel MCP Server

Dokumen ini menjelaskan cara menghubungkan client AI (Hermes, Nexa, Claude Desktop, Cursor, atau script kustom) ke server Model Context Protocol (MCP) pada aplikasi ACT RDW Laravel.

---

## 1. Ringkasan Protokol & Endpoint

| Item | Detail |
|---|---|
| **Protocol** | Model Context Protocol (MCP) JSON-RPC 2.0 |
| **Transport** | Streamable HTTP |
| **Endpoint URL** | `https://your-domain.com/api/mcp` (atau `http://localhost:8000/api/mcp` untuk lokal) |
| **HTTP Method** | `POST` |
| **Headers** | `Content-Type: application/json`<br>`X-API-KEY: <API_KEY_ANDA>` |

---

## 2. Persiapan: Mendapatkan API Key

1. Masuk ke panel admin Filament: `/activioncms`
2. Buka menu **Settings &rarr; API Keys** (`/activioncms/api-keys`).
3. Klik tombol **New API Key**.
4. Isi **Name** (contoh: `Hermes AI Agent`).
5. Pilih **Capabilities** sesuai kebutuhan:
   - Tombol **Read Only MCP**: memilih `product.read` & `news.read`.
   - Tombol **Full MCP Access**: memilih semua izin akses (read, write, delete).
   - Atau centang manual kemampuan tertentu.
6. Simpan (**Create**).
7. Salin key token (`act_xxxx...`) yang muncul di notifikasi atau klik ikon **Copy** di tabel.

---

## 3. Cara Koneksi dari Berbagai Client AI

### A. Konfigurasi Cursor IDE (.cursor/mcp.json)
Jika Anda menggunakan fitur MCP di Cursor:
Tambahkan ke file konfigurasi MCP Anda (biasanya di Settings Cursor &rarr; MCP Servers atau `~/.cursor/mcp.json`):

```json
{
  "mcpServers": {
    "act-rdw": {
      "url": "http://localhost:8000/api/mcp",
      "headers": {
        "X-API-KEY": "act_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
      }
    }
  }
}
```

---

### B. Konfigurasi Hermes / Nexa AI Runtime
Pada environment runtime Hermes / Nexa, tentukan server MCP dengan tipe HTTP transport:

```yaml
mcp_servers:
  - name: act_rdw_mcp
    transport: http
    endpoint: "http://localhost:8000/api/mcp"
    headers:
      X-API-KEY: "act_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    timeout: 30
```

---

### C. Konfigurasi Claude Desktop
Gunakan wrapper HTTP MCP client (seperti `mcp-remote` via `npx`):
Di file `claude_desktop_config.json`:

```json
{
  "mcpServers": {
    "act-rdw": {
      "command": "npx",
      "args": [
        "-y",
        "mcp-remote",
        "http://localhost:8000/api/mcp",
        "--header",
        "X-API-KEY: act_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
      ]
    }
  }
}
```

---

## 4. Alur Protokol MCP (JSON-RPC 2.0)

Semua komunikasi menggunakan format JSON-RPC 2.0 standar.

### Langkah 1: Handshake / Inisialisasi (`initialize`)
Client mengirim negosiasi protokol:

**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "method": "initialize",
  "params": {
    "protocolVersion": "2024-11-05",
    "capabilities": {},
    "clientInfo": {
      "name": "hermes-agent",
      "version": "1.0.0"
    }
  }
}
```

**Response Sukses:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": {
    "protocolVersion": "2024-11-05",
    "capabilities": {
      "tools": []
    },
    "serverInfo": {
      "name": "LaravelMCP",
      "version": "1.0.0"
    }
  }
}
```

---

### Langkah 2: Menemukan Tools yang Tersedia (`tools/list`)
Client meminta daftar tools yang dapat dipanggil:

**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 2,
  "method": "tools/list",
  "params": {}
}
```

**Response:**
Mengembalikan daftar 8 tools beserta JSON Schema input masing-masing:
* `product.list`
* `product.get`
* `product.upsert`
* `product.delete`
* `news.list`
* `news.get`
* `news.upsert`
* `news.delete`

---

### Langkah 3: Menjalankan Tool (`tools/call`)

#### Contoh 1: Mengambil Daftar Produk (`product.list`)
**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 3,
  "method": "tools/call",
  "params": {
    "name": "product.list",
    "arguments": {
      "limit": 5,
      "category": "elektronik"
    }
  }
}
```

**Response:**
```json
{
  "jsonrpc": "2.0",
  "id": 3,
  "result": {
    "content": [
      {
        "type": "text",
        "text": "{\"current_page\":1,\"data\":[...]}"
      }
    ]
  }
}
```

#### Contoh 2: Mengambil Detail Berita (`news.get`)
**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 4,
  "method": "tools/call",
  "params": {
    "name": "news.get",
    "arguments": {
      "slug": "inovasi-ai-terbaru"
    }
  }
}
```

#### Contoh 3: Menghapus Produk (`product.delete`)
*Memerlukan capability `product.delete` atau `*`.*

**Request:**
```json
{
  "jsonrpc": "2.0",
  "id": 5,
  "method": "tools/call",
  "params": {
    "name": "product.delete",
    "arguments": {
      "slug": "produk-lama"
    }
  }
}
```

**Response:**
```json
{
  "jsonrpc": "2.0",
  "id": 5,
  "result": {
    "content": [
      {
        "type": "text",
        "text": "{\"success\":true}"
      }
    ]
  }
}
```

---

## 5. Daftar Lengkap Tools & Parameter

### Product Catalog Tools

| Tool Name | Capability Diperlukan | Argumen Wajib | Argumen Opsional | Deskripsi |
|---|---|---|---|---|
| `product.list` | `product.read` | - | `category` (string), `brand` (string), `limit` (int) | Menampilkan daftar produk aktif dengan filter & pagination |
| `product.get` | `product.read` | `slug` (string) | - | Mengambil detail 1 produk lengkap dengan relasinya |
| `product.upsert` | `product.write` | `name` (string), `slug` (string) | `brand_id`, `service_id`, `description`, `categories` (array) | Membuat baru atau memperbarui produk |
| `product.delete` | `product.delete` | `slug` (string) | - | Menghapus produk berdasarkan slug |

### News Tools

| Tool Name | Capability Diperlukan | Argumen Wajib | Argumen Opsional | Deskripsi |
|---|---|---|---|---|
| `news.list` | `news.read` | - | `category` (string), `tag` (string), `limit` (int) | Menampilkan daftar artikel berita yang sudah dipublikasikan |
| `news.get` | `news.read` | `slug` (string) | - | Mengambil detail 1 artikel berita lengkap |
| `news.upsert` | `news.write` | `title` (string), `slug` (string) | `content`, `category_id`, `status`, `tags` (array) | Membuat baru atau memperbarui artikel berita |
| `news.delete` | `news.delete` | `slug` (string) | - | Menghapus artikel berita berdasarkan slug |

---

## 6. Contoh Pemanggilan Manual via cURL

```bash
# Inisialisasi
curl -X POST http://localhost:8000/api/mcp \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: act_your_api_key_here" \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05"}}'

# List Tools
curl -X POST http://localhost:8000/api/mcp \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: act_your_api_key_here" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}'

# Panggil Tool product.list
curl -X POST http://localhost:8000/api/mcp \
  -H "Content-Type: application/json" \
  -H "X-API-KEY: act_your_api_key_here" \
  -d '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"product.list","arguments":{"limit":3}}}'
```

---

## 7. Kode Error & Troubleshooting

| Kode HTTP / RPC | Pesan Error | Penyebab & Solusi |
|---|---|---|
| **HTTP 401** | `API Key is missing` | Header `X-API-KEY` belum disertakan di request. |
| **HTTP 401** | `Invalid or inactive API Key` | API Key tidak ditemukan atau status `is_active` bernilai `false` di database. |
| **RPC -32700** | `Parse error` | Body JSON tidak valid atau kosong. |
| **RPC -32600** | `Invalid Request` | Format request JSON-RPC tidak menyertakan `method`. |
| **RPC -32601** | `Method not found` / `Unknown tool` | Nama method atau nama tool yang dipanggil tidak terdaftar. |
| **RPC -32602** | `Missing required argument: {field}` | Parameter wajib tidak disertakan dalam `arguments`. |
| **RPC -32003** | `Unauthorized capability: {cap}` | API Key tidak memiliki izin yang dibutuhkan untuk mengeksekusi tool tersebut. Perbarui capabilities di Filament. |
| **RPC -32000** | Pesan error aplikasi | Kesalahan saat eksekusi tool (misal data tidak ditemukan). |