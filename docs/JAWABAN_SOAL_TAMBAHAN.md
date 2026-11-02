# Jawaban soal tambahan Final Project API TimeDoor Pro

Sumber soal: materi 1804, slide 4, yang sudah dibaca dari gambar slide offline asli. Jawaban ini menjelaskan implementasi di `C:\laragon\www\finalproject`.

## 1. Validasi input melalui API

Form atau Postman mengirim body JSON ke endpoint, misalnya POST /api/products. Controller memanggil `$request->validate(...)` sebelum menyimpan data. Jika tidak valid, API mengembalikan 422 beserta `errors` per field. Frontend dapat menampilkan pesan itu di samping input terkait; di Postman pesan yang sama terlihat pada respons JSON. Jika valid, hanya data hasil validasi yang diteruskan ke Eloquent.

Contoh jenis validasi yang diterapkan:

- Kelengkapan, tipe, dan panjang: name memakai required|string|max:100. Nama tidak boleh kosong, harus teks dan tidak melebihi batas.
- Angka dan rentang: price memakai required|numeric|min:0|max:99999999.99, stock memakai required|integer|min:0|max:2147483647. Harga/stok negatif dan angka di luar kapasitas kolom MySQL ditolak.
- Relasi: category_id dan brand_id memakai required|integer|exists pada tabel masing-masing. Produk tidak dapat disimpan dengan ID kategori/merek yang tidak tersedia.
- Registrasi: email harus berbentuk email dan unik di users; password minimal delapan karakter serta confirmed untuk kecocokan password_confirmation.
- Gambar dari mockup: image_url boleh kosong/null, tetapi bila dikirim harus URL HTTP/HTTPS dan maksimal 2048 karakter.

Aturan produk ada di ProductController::rules(); aturan registrasi ada di AuthController::register(). PUT meminta semua field wajib produk, sedangkan PATCH memakai sometimes agar hanya field yang dikirim diperiksa. Pengujian mencakup input kosong, harga/stok negatif, email duplikat, ID relasi invalid, URL invalid, dan query sorting/pagination invalid.

## 2. Autentikasi akun dan keamanannya

POST /api/register memvalidasi data lalu menyimpan akun ke tabel users. Informasi akun adalah id, name, email unik, hash password, email_verified_at yang boleh null, remember_token yang boleh null, serta created_at/updated_at. Password disimpan melalui Hash::make, bukan teks asli. Model User menyembunyikan password dan remember_token dari serialisasi JSON.

POST /api/login mencari pengguna berdasarkan email dan memeriksa password melalui Hash::check. Bila tidak cocok, API mengembalikan 401 tanpa menerbitkan token. Bila cocok, trait HasApiTokens pada User menyediakan createToken dari Laravel Sanctum. Respons memberikan token pada data.token sesuai format data yang diajarkan dalam materi 1798. Nilai token polos hanya diberikan saat penerbitan; Sanctum menyimpan hash token pada personal_access_tokens, bersama identitas pemilik, nama token, abilities dan informasi waktunya.

Client menyertakan `Authorization: Bearer <token>` pada request berikutnya. Route GET /api/me, GET /api/user dan seluruh mutasi katalog berada dalam grup auth:sanctum. Token tidak ada/tidak valid/sudah dicabut menghasilkan 401. Logout menghapus currentAccessToken. Setiap register/login dapat menghasilkan token berbeda, sehingga logout satu token tidak otomatis menghapus token perangkat atau sesi lain.

Keamanan yang ada: hashing password, validasi tipe dan email unik, konfirmasi password, pembatasan request register/login, pembatasan mass assignment, serta pengujian bahwa password tidak bocor dan token benar-benar ditolak setelah logout. HTTPS dan konfigurasi produksi diperlukan saat deployment; sesi ini hanya menggunakan localhost dan tidak memublikasikan aplikasi. Authentication membuktikan identitas pengguna, sedangkan authorization membatasi tindakan; tugas ini melindungi mutasi dengan token dan belum menerapkan pemisahan role admin/pelanggan.

## 3. Algoritme searching, sorting dan CRUD

Endpoint list produk menerima query, misalnya `/api/products?keyword=Mouse&sort=price_desc&per_page=5&page=2`. Langkah di ProductController::index():

1. Validasi keyword, category_id/brand_id, sort, per_page dan page. Sort harus berasal dari whitelist; per_page dibatasi 1–100 dan page minimal 1.
2. Bangun Product::query() dan gunakan with(['category','brand']) untuk mengambil relasi secara efisien.
3. Bila keyword bukan null atau string kosong setelah trim, tambahkan where name LIKE pola keyword. Nilai teks 0 tetap dianggap pencarian. Eloquent mengikat nilainya sebagai parameter query.
4. Tambahkan filter kategori/merek jika diberikan.
5. Terapkan orderBy/orderByDesc berdasarkan opsi whitelist. Untuk harga/nama yang sama, ID menjadi pembeda agar pagination stabil.
6. Panggil paginate setelah filter dan urutan diterapkan. Secara konsep offset adalah (page - 1) × per_page.
7. Kembalikan JSON data dan metadata paginator seperti total, current_page, last_page serta tautan halaman. Client memakai informasi itu untuk berpindah halaman.

CRUD menggunakan method controller yang diajarkan: index/show untuk Read, store untuk Create, update untuk Update dan destroy untuk Delete. findOrFail menghasilkan 404 untuk ID yang tidak tersedia. Create mengembalikan 201, Read/Update 200, dan Delete 204 dengan body kosong. Mutasi wajib token dan memproses data tervalidasi saja. Model fillable membatasi field yang dapat disimpan dan foreign key menjaga referensi tabel. Penghapusan kategori/merek yang masih dipakai produk ditolak 409.

Agar operasi lancar, pengujian memeriksa respons sekaligus hasilnya: data tersimpan/diperbarui/dihapus, urutan harga benar, filter mengecualikan produk lain, halaman kedua berisi item yang sesuai, validasi gagal tidak mengubah data, serta token setelah logout ditolak. Seeder menyediakan data contoh yang dapat diulang. Tes dilakukan pada SQLite memori dan MySQL QA terpisah agar data proyek asli tetap aman. Bukti pengujian serta batas verifikasinya dicatat di VERIFIKASI_TUGAS_AKHIR.md.
