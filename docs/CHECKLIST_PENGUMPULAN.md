# Checklist pengumpulan TimeDoor Pro

| Ketentuan | Implementasi dan bukti |
|---|---|
| List endpoint dan spesifikasi API | API.md, 23 route API |
| Minimal tiga tabel | products, categories, brands; ditambah tabel auth |
| Eloquent relationship | Product belongsTo Category/Brand; Category/Brand hasMany Product; foreign key |
| Validasi | Input wajib/tipe/rentang, email unik, konfirmasi password, exists FK dan validasi URL; 422 saat gagal |
| Autentikasi | Sanctum register/login/profile/logout; token terproteksi dan revokasi teruji |
| Sorting | Whitelist; urutan harga naik/turun dibandingkan dengan nilai fixture |
| Searching | Query keyword dengan where LIKE; termasuk regresi keyword 0 |
| Pagination | page/per_page dan metadata; isi halaman kedua diuji |
| Seluruh CRUD | Products/categories/brands dengan GET/POST/PUT/PATCH/DELETE; 401/404/409/422 juga diuji |
| Dummy data seeder | 25 produk, 5 kategori, 5 merek; default seeder repeatable |
| JSON dan Postman | Collection 57 request/90 assertions lulus pada Newman dan VS Code |
| Soal tambahan | Tiga jawaban di JAWABAN_SOAL_TAMBAHAN.md, berdasarkan materi 1804 slide 4 |
| URL GitHub, migration, seeder | [Repository public finalproject](https://github.com/muhammadzakyasshidqy-commits/finalproject); folder database/migrations dan database/seeders |

Source aplikasi yang dipublikasikan dicocokkan dengan versi teruji melalui SOURCE_INTEGRITY.json. Detail hasil tes, perbedaan format dari contoh Documenter serta advisori dependency tersedia pada dokumen terkait.
