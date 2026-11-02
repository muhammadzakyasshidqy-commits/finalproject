# Verifikasi Final Project API

Tanggal pengujian: 2 November 2026. Source: project asli `finalproject`, Laravel 10.50.4, PHP 8.3.16, MySQL Laragon.

| Pemeriksaan | Hasil |
|---|---|
| Route API | 23 |
| Laravel tests | 17 tests, 135 assertions, seluruhnya lulus; SQLite :memory: terisolasi |
| PHP lint | 66 file, 0 error sintaks |
| Newman | 57 request, 90 assertions, 0 failure |
| Postman VS Code 1.19.2 | 1 iterasi, 90 lulus, 0 gagal, 0 skipped; 33s 220ms |
| Migration MySQL QA | 8 migration diterapkan |
| Seeder QA | 25 produk, 5 kategori, 5 merek; pengulangan tidak menggandakan data |
| Keutuhan data lama | Seluruh row lama tetap identik; hanya penambahan kolom image_url nullable dan satu log migration |
| Composer validate | Valid |
| Composer audit | 5 record advisori Laravel (4 GHSA unik), 0 abandoned; lihat SECURITY_REVIEW.md |

Pengujian HTTP meliputi register/login/profile/logout, kedua token yang independen, seluruh CRUD, relasi, validasi gagal, autentikasi gagal, not found, konflik lookup terpakai, urutan harga, pencarian, filter gabungan dan pagination. Tiga pemeriksaan image_url ikut lulus di runner VS Code. Respons berisi kredensial/token tidak dipersistenkan.

Perbaikan yang dibuktikan melalui tes yang sebelumnya gagal: JSON error API tanpa Accept, batas angka sesuai kapasitas MySQL, pencarian keyword 0, serta penyimpanan image_url dari mockup. Test suite juga memeriksa mass assignment, kebocoran password dan credential non-string.

Figma, contoh Documenter dan slide asli materi 1804 sudah dicocokkan. Implementasi mengikuti format data.token pada materi Sanctum dan metadata paginator pada materi pagination. Jawaban tiga soal tambahan disertakan. Tidak ada penambahan fitur bisnis di luar tugas online-shop REST API.

Repository pengumpulan public: https://github.com/muhammadzakyasshidqy-commits/finalproject. Source aplikasi/migration/seeder/tes identik dengan versi lokal yang diuji; dokumentasi publik sudah dibersihkan dari metadata pribadi. Lihat SOURCE_INTEGRITY.json untuk hash tiap file dan PUBLICATION_REVIEW.md untuk ruang lingkup pemeriksaan.
