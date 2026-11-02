# Spesifikasi REST API finalproject

Source: `C:\laragon\www\finalproject`, Laravel 10, Sanctum, MySQL.
Server lokal data asli: `http://127.0.0.1:8001`. Server QA: `http://127.0.0.1:8002`.
Semua route berawalan `/api`; kirim `Accept: application/json` dan `Content-Type: application/json` untuk body JSON. Error API tetap JSON meskipun Accept tidak dikirim.

## Autentikasi

| Method | Endpoint | Body / hasil | Status berhasil |
|---|---|---|---|
| POST | /register | name (1–100), email unik (maks. 255), password (min. 8), password_confirmation sama; menghasilkan data.user dan data.token | 201 |
| POST | /login | email dan password; menghasilkan data.user dan data.token | 200 |
| GET | /me | Bearer token; profil dalam data | 200 |
| GET | /user | Bearer token; profil langsung tanpa pembungkus data | 200 |
| POST | /logout | Bearer token; mencabut token yang digunakan pada request ini | 200 |

Header terproteksi: `Authorization: Bearer <token>`. Register dan login masing-masing membuat token baru. Logout satu token tidak mencabut token dari login/perangkat lain. Password dan remember_token tidak diserialisasi ke JSON. Register/login dibatasi 10 request/menit; API 60 request/menit per pengguna atau IP (429 jika melebihi).

## CRUD

| Resource | GET list | GET detail | POST | PUT | PATCH | DELETE |
|---|---|---|---|---|---|---|
| products | /products | /products/{id} | /products | /products/{id} | /products/{id} | /products/{id} |
| categories | /categories | /categories/{id} | /categories | /categories/{id} | /categories/{id} | /categories/{id} |
| brands | /brands | /brands/{id} | /brands | /brands/{id} | /brands/{id} | /brands/{id} |

GET katalog publik. Seluruh POST/PUT/PATCH/DELETE resource memerlukan token. ID harus numerik. GET/PUT/PATCH berhasil 200, POST 201, DELETE 204 dengan body kosong. Detail/mutasi ID tidak ada menghasilkan 404. Category/brand yang masih dipakai product tidak dapat dihapus (409).

Product POST/PUT: name wajib string maks. 100; price wajib numerik 0..99999999.99 (kolom DECIMAL(10,2)); stock wajib integer 0..2147483647 (kolom INT); category_id dan brand_id wajib integer yang ada. description opsional, boleh null, maks. 5000 karakter. image_url opsional, boleh null, string URL HTTP/HTTPS maks. 2048 sesuai Link URL Gambar pada Figma; server hanya menyimpan URL. PATCH hanya memvalidasi field yang dikirim; field wajib tidak boleh null jika dikirim. Category/brand POST/PUT/PATCH: name wajib string maks. 100 dan unik pada tabelnya. Data yang tidak tervalidasi seperti id atau created_at diabaikan.

Respons create/detail/update: `{"data": {"id": 1, ...}}`. Product memuat category dan brand; detail lookup memuat array products, sedangkan list lookup memuat products_count. Dua produk lama tetap dipertahankan dan dapat memiliki relasi null dari migration tambahan; produk baru wajib memiliki kedua relasi.

## Query list produk

| Parameter | Validasi / perilaku |
|---|---|
| keyword | string opsional, boleh null, maks. 100; substring nama, termasuk teks `0`; pola LIKE mendukung `%` dan `_` |
| category_id | integer, kategori harus tersedia |
| brand_id | integer, merek harus tersedia |
| sort | newest (default), oldest, name_asc, name_desc, price_asc, price_desc |
| per_page | integer 1..100, default 10 |
| page | integer minimal 1 |

Seluruh parameter dapat digabung. `newest/oldest` memakai ID; sort nama/harga memakai ID sebagai pembeda hasil bernilai sama. Urutan diterapkan sebelum paginate. Contoh: `/products?keyword=Mouse&category_id=1&brand_id=1&sort=price_desc&per_page=5&page=2`.

Respons list produk berupa paginator Laravel: data, current_page, per_page, total, last_page, from, to, links, next_page_url, prev_page_url. Halaman di luar hasil mengembalikan data kosong 200. Query tidak valid menghasilkan 422, bukan diam-diam diabaikan.

## Error

| Status | Arti |
|---|---|
| 401 | kredensial salah, token tidak ada/tidak valid/sudah dicabut |
| 404 | route atau ID tidak ditemukan |
| 409 | kategori/merek masih digunakan |
| 422 | validasi gagal, JSON berisi message dan errors per field |
| 429 | batas request per menit terlampaui |

Untuk tugas ini semua pengguna terautentikasi dapat mengelola katalog; belum ada pemisahan role admin/customer. Sebelum penggunaan toko publik, tentukan dan implementasikan kebijakan otorisasi sesuai kebutuhan bisnis. Source pengumpulan tersedia pada repository GitHub public; API dijalankan di lingkungan lokal.

## Menjalankan collection

`TimeDoor_FinalProject.postman_collection.json` berisi 57 request berurutan dan 90 assertions. Default base_url 8002 untuk QA. Jalankan dari awal sampai akhir; prerequest register menghasilkan email unik, test login menyimpan token, create menyimpan ID, bagian akhir menghapus fixture miliknya dan mencabut kedua token. Hasil pengujian tersimpan di `POSTMAN_RESULTS.json` tanpa body kredensial/token.

Dengan server QA aktif: `npx --yes --package newman newman run docs/TimeDoor_FinalProject.postman_collection.json`. Jangan mengekspor environment yang sudah berisi token. Collection meninggalkan akun dummy QA, namun tidak menyisakan token aktif setelah berhasil selesai.
