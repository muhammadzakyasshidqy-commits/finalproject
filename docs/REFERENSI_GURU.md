# Pencocokan referensi guru — 2 November 2026

[Dokumentasi Postman marketplace guru](https://documenter.getpostman.com/view/9722437/2s9YkoehUo) berhasil dibuka dan dibaca melalui browser. Alat web awal gagal memuatnya; browser menampilkan isi collection publik.

| Contoh guru | Proyek finalproject | Catatan |
|---|---|---|
| POST /api/login | Sama | Contoh token top-level; proyek token ada di data.token dan collection telah mengikuti kontrak aktual. |
| POST /api/register | Sama | Contoh 200; proyek 201 sesuai kriteria create pada lampiran. |
| GET /api/products | Sama | Contoh array id/name/price/stock; proyek paginator dengan data dan metadata agar pagination dapat diperiksa. |
| GET detail product | GET /api/products/{id} | Contoh halaman guru menampilkan /api/products tanpa ID walaupun responsnya objek tunggal. Proyek menggunakan ID agar tidak berbenturan dengan list. |
| POST /api/products | Sama | Keduanya 201; proyek mengembalikan data produk beserta relasi, bukan message saja. |
| PUT /api/products/1 | Sama | Contoh name/price/stock; proyek mewajibkan category_id dan brand_id juga karena persyaratan relationship/validasi. |

Halaman yang terbaca berisi kelompok user dan product hingga update product. Tidak ditemukan spesifikasi kategori/merek, logout, searching/sorting/pagination, PATCH atau DELETE pada isi halaman yang ditampilkan. Fitur-fitur itu mengikuti instruksi eksplisit lampiran, bukan diasumsikan berasal dari contoh tersebut.

Referensi contoh bukan kontrak byte-for-byte yang identik. Implementasi mempertahankan struktur source yang sudah ada, dengan status/validasi dan fitur tambahan sesuai lampiran. Tidak menyalin token contoh atau data akun apa pun dari referensi.

## Figma

[Desain Figma guru](https://www.figma.com/design/73xIZPKzFUl1kesRISGGaH/Untitled?node-id=0-1&p=f) berhasil dibaca secara visual melalui browser tanpa masuk atau mengubah desain. Tautan be-final-design mengarah ke file tersebut.

Terlihat enam layar: Home, Login, Register, Create, Update, delete. Home memuat kartu produk, nama/harga/stok, search, sorting dan aksi edit/hapus. Register memuat name/email/password/confirm password. Form create/update memuat nama produk, Link URL Gambar, harga dan stok. Alur backend mendukung layar tersebut; image_url opsional ditambahkan pada migration baru agar URL gambar tersimpan dan tervalidasi. URL hanya disimpan, server tidak mengunduhnya.

Web sederhana kini mengikuti alur enam layar tersebut menggunakan API yang sama. Tidak menambah keranjang/checkout atau dashboard; pencocokan ini berfokus pada alur dan field, bukan kesamaan pixel.

## Slide offline TimeDoor Pro

Materi asli 1804 beserta materi pendukung 1793–1804 dicocokkan dari salinan offline. Salinan materi privat tidak disertakan di repository.

Keempat gambar `all_slide_images/1804_001.jpg` sampai `1804_004.jpg` dibaca secara visual. Slide 2 meminta respons JSON berdasarkan mockup dan ditampilkan melalui Postman. Slide 3 memuat endpoint/spesifikasi, minimal tiga tabel, Eloquent relationship, validasi/auth/sort/search/pagination, CRUD, seeder dan URL GitHub dengan migration/seeder. Slide 4 berisi tiga soal tambahan; jawabannya sudah ditulis dalam JAWABAN_SOAL_TAMBAHAN.md.

Teks materi pendukung 1793–1804 juga dibaca: model/migration/seeder, API controller, routing, Eloquent, validasi, authentication Sanctum, middleware, pagination, searching, sorting, latihan API dan final project.

| Materi | Penerapan pada source asli |
|---|---|
| 1793–1796 | Model Product/Category/Brand, migration, seeder, controller index/show/store/update/destroy, route eksplisit, create/update/delete dan findOrFail |
| 1797 | request->validate dan errors 422; batas/tipenya didokumentasikan pada API.md |
| 1798–1799 | HasApiTokens, createToken, data.token, Bearer token dan grup route auth:sanctum |
| 1800 | paginate dengan page/per_page dan metadata JSON |
| 1801–1802 | keyword melalui query parameter, where LIKE, orderBy/orderByDesc kemudian paginate |
| 1803 | CRUD kategori/merek, relationship, validasi, Sanctum, pagination serta filter kategori, search/sort |
| 1804 | Spesifikasi endpoint, tiga tabel katalog, semua fitur utama, dummy seeder, collection dan jawaban tiga soal |

Materi 1798 menjelaskan format data.token, sehingga format proyek mengikuti materi tersebut meskipun contoh Documenter menampilkan token top-level. Materi 1800 menampilkan format paginator dengan data dan metadata, yang juga dipakai proyek. Beberapa contoh slide memakai delete 200 dengan message; proyek memakai 204 tanpa body sesuai instruksi eksplisit lampiran. Contoh latihan validasi nama pada 1797 memakai max:50; kontrak source yang sudah ada memakai max:100 dan batas itu dipertahankan/didokumentasikan untuk final project, karena slide final tidak menetapkan panjang nama.
