# Panduan Integrasi Platform Platform Luar - Act RDW

Dokumen ini menjelaskan langkah demi langkah cara menghubungkan platform eksternal (seperti n8n, Zapier, Make, atau script custom) ke API **Act RDW**.

## Konsep Dasar Koneksi

Untuk menghubungkan platform luar, Anda memerlukan tiga komponen utama:
1.  **URL Endpoint**: Alamat API tujuan (misal: `https://domain.com/api/automation/leads`).
2.  **Method**: Jenis request (`GET` untuk mengambil data, `POST` untuk mengirim data).
3.  **Authentication**: Header `X-API-KEY` yang berisi token rahasia Anda.

---

## 1. Integrasi menggunakan n8n (Rekomendasi)

n8n adalah platform yang sangat fleksibel untuk menghubungkan Act RDW dengan ribuan layanan lainnya.

### Langkah-langkah:
1.  Tambahkan node **HTTP Request**.
2.  Atur **Method**: Pilih `GET` atau `POST` (sesuai endpoint).
3.  Masukkan **URL**: Contoh `https://domain.com/api/automation/leads`.
4.  Buka tab **Authentication**:
    *   Pilih **Header Auth**.
    *   **Name**: `X-API-KEY`.
    *   **Value**: Masukkan API Key Anda (misal: `act_abcd123...`).
5.  (Jika POST) Tambahkan **Body Parameters**:
    *   Pilih **Specify Body** -> `JSON`.
    *   Masukkan JSON yang sesuai (lihat `api_guide.md`).

---

## 2. Integrasi menggunakan Zapier

Zapier sangat populer untuk otomasi yang lebih sederhana.

### Langkah-langkah:
1.  Pilih App **Webhooks by Zapier**.
2.  Pilih **Event**: `POST` atau `GET`.
3.  Atur **URL**: Masukkan alamat API Anda.
4.  Pada bagian **Headers**:
    *   Di kolom kiri, masukkan: `X-API-KEY`.
    *   Di kolom kanan, masukkan: API Key Anda.
5.  Atur **Payload Type**: Pilih `Json`.
6.  Masukkan data pada bagian **Data** sesuai kebutuhan endpoint.

---

## 3. Integrasi menggunakan Python (Script Custom)

Jika Anda ingin membuat aplikasi custom, berikut contoh sederhananya:

```python
import requests

url = "https://domain.com/api/automation/leads"
headers = {
    "X-API-KEY": "act_YOUR_SECRET_KEY",
    "Accept": "application/json"
}

# Contoh Mengambil Data (GET)
response = requests.get(url, headers=headers)
print(response.json())

# Contoh Mengirim Data (POST)
payload = {
    "form_key": "landing_page",
    "payload": {
        "name": "Budi",
        "email": "budi@mail.com"
    }
}
response = requests.post(url, headers=headers, json=payload)
print(response.status_code, response.json())
```

---

## Troubleshooting (Penyelesaian Masalah)

Jika platform Anda gagal terhubung, periksa hal-hal berikut:

### Error 401 (Unauthorized)
*   **Penyebab**: API Key salah, tidak dikirim, atau tidak aktif.
*   **Solusi**: Pastikan header `X-API-KEY` tertulis dengan benar (bukan `API-KEY` atau `Authorization`). Pastikan statusnya **Active** di dashboard.

### Error 422 (Unprocessable Content)
*   **Penyebab**: Format JSON Anda tidak sesuai atau ada field wajib yang kurang.
*   **Solusi**: Periksa kembali `api_guide.md`. Contoh: `pushLead` mewajibkan field `form_key` dan `payload` (harus berupa array/object).

### Payload Masuk Kosong
*   **Penyebab**: Platform luar tidak mengirim dalam format JSON yang benar.
*   **Solusi**: Pastikan header `Content-Type: application/json` disertakan (biasanya otomatis di n8n/Zapier jika mode JSON dipilih).

---

> [!TIP]
> Aktifkan **Debug Mode** pada API Key Anda selama proses integrasi awal. Dengan begitu, Anda bisa melihat persis data apa yang diterima oleh server Act RDW di tabel logs (database).
