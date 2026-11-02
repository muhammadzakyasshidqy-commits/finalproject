# Final Project Backend TimeDoor Pro

Laravel 10 REST API untuk online shop, dikerjakan pada source asli `finalproject`.

Repository pengumpulan: [muhammadzakyasshidqy-commits/finalproject](https://github.com/muhammadzakyasshidqy-commits/finalproject).

Fitur: CRUD products/categories/brands; Eloquent relationships; validasi; register/login/profile/logout dengan Sanctum; searching, sorting, filtering dan pagination; migration serta seeder repeatable. Field image_url opsional mengikuti mockup guru.

## Web sederhana

Root `/` menampilkan katalog, login/register, tambah/edit produk dan konfirmasi hapus sesuai alur mockup guru, menggunakan API yang sama. Tidak perlu npm/build. URL sesi lokal saat ini: **http://127.0.0.1:8003**; QA terpisah: **http://127.0.0.1:8004**. [Hasil uji web](docs/WEB_RESULTS.md).

## Postman untuk tugas

Gunakan **finalproj - TimeDoor Pro** dari [collection manual](docs/finalproj-TimeDoor.postman_collection.json): **19 request** dalam folder user/product/category/brand. Pilih request lalu Send. [Petunjuk singkat](docs/POSTMAN_MANUAL.md). Collection 57 request di bawah tetap disimpan sebagai tes lanjutan.

## Setup

Gunakan PHP >=8.1, Composer dan MySQL/MariaDB (misalnya Laragon).

1. Clone repository, lalu jalankan `composer install`.
2. Salin `.env.example` menjadi `.env` pada instalasi baru. Buat database lokal dan isi koneksi MySQL sendiri. `.env` tidak disertakan dalam Git.
3. Jalankan `php artisan key:generate` hanya jika APP_KEY belum tersedia.
4. Jalankan `php artisan migrate`, kemudian `php artisan db:seed`.
5. Jalankan `php artisan serve --host=127.0.0.1 --port=8001`.
6. Buka `http://127.0.0.1:8001` untuk web atau `/api/products` melalui Postman.

Seeder default membuat 25 produk, 5 kategori dan 5 merek. Seeder dapat diulang. Migration tambahan menjaga data lama; jangan menjalankan migrate:fresh pada database yang berisi data penting.

## Pengujian dan Postman

`php artisan test` menggunakan SQLite `:memory:` yang dipaksa oleh phpunit.xml. Hasil verifikasi: **17 tests, 135 assertions lulus**. Route API: **23**.

Collection di `docs/TimeDoor_FinalProject.postman_collection.json` telah diuji dengan Newman dan Postman VS Code: **57 request, 90 assertions, 0 gagal**. Default base_url adalah `http://127.0.0.1:8002` untuk database QA terpisah. Collection membuat data dummy, menghapus fixture katalog miliknya dan mencabut token di akhir; gunakan database pengujian kosong yang terpisah dari data penting.

Untuk server QA, buat database kosong sendiri, isi DB_DATABASE pada environment proses terminal QA, jalankan migration dan seeder, lalu serve pada port 8002. Jalankan collection dari awal sampai akhir agar email/token/ID tersimpan sesuai urutan. Runner CLI alternatif: `npx --yes --package newman newman run docs/TimeDoor_FinalProject.postman_collection.json`.

## Dokumen pengumpulan

- [Endpoint dan spesifikasi API](docs/API.md)
- [Checklist ketentuan tugas](docs/CHECKLIST_PENGUMPULAN.md)
- [Jawaban tiga soal tambahan](docs/JAWABAN_SOAL_TAMBAHAN.md)
- [Penjelasan kode dan alur HTTP](docs/PENJELASAN_TUGAS_TIMEDOOR.md)
- [Pencocokan referensi guru](docs/REFERENSI_GURU.md)
- [Ringkasan verifikasi](docs/VERIFIKASI_TUGAS_AKHIR.md)
- [Hasil HTTP Newman](docs/POSTMAN_RESULTS.json)
- [Hasil Postman VS Code](docs/POSTMAN_VSCODE_FINAL_RESULTS.json)
- [Integritas source yang diuji](docs/SOURCE_INTEGRITY.json)
- [Audit dependency](docs/SECURITY_REVIEW.md)

Migration ada di `database/migrations`, seeder di `database/seeders`, dan tes di `tests/Feature`. Struktur tetap memakai route/controller/model, request validation, Eloquent dan Sanctum sesuai materi TimeDoor Pro.

## Catatan keamanan

Repository public ini berisi source dan bukti tes yang sudah dibersihkan. File environment asli, token aktif, data pribadi/klien, backup database, vendor, log dan cache runtime tidak disertakan. Riwayat audit lokal yang berisi metadata pribadi tidak dipublikasikan; commit publik memakai alamat GitHub noreply. Source aplikasi dicocokkan per file dengan versi yang diuji.

Framework dipertahankan pada Laravel 10 sesuai tugas. Composer audit masih menandai advisori framework yang dicatat dalam SECURITY_REVIEW.md. Project ini menyediakan bahan tugas dan pengujian lokal; sebelum penggunaan produksi, evaluasi upgrade dan kebijakan otorisasi. Semua pengguna terautentikasi saat ini memiliki hak mutasi katalog yang sama, tanpa role admin/customer.
