# Penjelasan Proyek Backend — TimeDoor

## Alur HTTP
Postman -> routes/api.php -> auth:sanctum middleware (jika perlu) -> controller -> validasi input -> model Eloquent -> database -> JSON response.

## Tabel dan hubungan
Category hasMany Product, Brand hasMany Product, Product belongsTo Category and Brand.
Migration: skema/foreign key. Seeder: data latihan (5 kategori, 5 merek, 25 produk). Model: representasi dan relasi data. Controller: proses request dan response. Route: metode HTTP dan endpoint.

## Method pada ProductController
index(): validasi keyword/category_id/brand_id/sort/per_page/page, Eloquent with() mencegah N+1, where() filter, orderBy() sorting dari whitelist, paginate() membatasi hasil.
show(): findOrFail() -> 404 bila tidak ada.
store(): validasi, Product::create(data tervalidasi) -> 201.
update(): PUT semua field wajib, PATCH hanya field yang diubah -> 200.
destroy(): hapus ID yang ada -> 204.

## Ringkasan jawaban soal tambahan guru

Soal asli sudah dibaca dari materi 1804 slide 4 di folder offline yang pengguna tunjukkan. Jawaban lengkap yang mengikuti ketiga soal tersedia di JAWABAN_SOAL_TAMBAHAN.md; berikut ringkasannya.
1. Validasi input: $request->validate([...]) pada controller. Contoh name required|string|max:100; price required|numeric|min:0; stock required|integer|min:0; category_id/brand_id required dan harus ada pada tabel terkait. Jika gagal, JSON 422 berisi errors. Simpan data dari hasil validate(), bukan request->all().
2. Autentikasi: register menyimpan hash password dengan Hash::make; login memverifikasi Hash::check lalu membuat token Sanctum melalui createToken. Endpoint mutasi memakai middleware auth:sanctum dan Bearer Token, GET /api/me untuk profil; POST /api/logout mencabut token yang sedang dipakai. Proteksi tambahan: validasi email unik dan password minimal delapan karakter, throttle register/login, HTTPS di deployment.
3. Search/sort dan CRUD aman: keyword pada Eloquent where('name','like',...), category_id/brand_id difilter dan tervalidasi. Sorting dipilih hanya dari opsi yang diizinkan. Paginasi dengan paginate() setelah filter dan sort. CRUD semua entity wajib token untuk perubahan, input tervalidasi, cek 401/404/409/422 selain success 200/201/204.

## Sesi demo
1. GET /api/products -> 200, metadata pagination.
2. POST /api/register -> 201; POST /api/login -> 200, token.
3. GET /api/me dengan token -> 200, tanpa token -> 401.
4. GET categories/brands -> 200, tampil relasi.
5. POST products -> 201; harga negatif -> 422.
6. GET products?keyword=Mouse&sort=price_desc&per_page=5 -> 200, terfilter/terurut.
7. PUT/PATCH -> 200; DELETE -> 204; GET setelah delete -> 404.
8. Logout -> 200; token yang dicabut -> 401.

## Diskusi lebih dalam
Mengapa PUT vs PATCH berbeda? Mengapa whitelist sorting penting? Apa beda authentication dan authorization? Bagaimana transaction dan foreign key mencegah data tidak konsisten? Mengapa migration tambahan harus kompatibel dengan data lama? Apa beda unit test, feature test dan end-to-end HTTP? Bagaimana melindungi token dan .env?

## Bedah contoh request

Untuk `PATCH /api/products/26` dengan body `{"stock":3}`: route memilih ProductController@update setelah auth:sanctum memeriksa Bearer token. findOrFail mencari produk atau menghasilkan 404. Karena method PATCH, aturan validasi memakai sometimes, sehingga field lain tidak harus dikirim. stock tetap wajib integer nonnegatif jika dikirim. update menerima hanya hasil validate, Eloquent menjalankan query dengan parameter, lalu controller memuat category/brand dan membungkus hasil dalam data. PUT berbeda: name, price, stock, category_id dan brand_id wajib dikirim.

Product::belongsTo(Category::class) berarti products.category_id menunjuk categories.id. Category::hasMany(Product::class) membaca arah balik. Eager loading with(['category','brand']) mengambil relasi secara berkelompok agar tidak menjalankan query tambahan untuk setiap produk. Foreign key mencegah referensi ke ID yang tidak ada; validasi exists memberi respons 422 yang dapat dipahami sebelum database menolak.

Token berbeda dari password. Password disimpan sebagai hash dan diperiksa dengan Hash::check. Nilai token polos diberikan saat createToken, sedangkan Sanctum menyimpan hash token. Client mengirim token sebagai Bearer untuk request berikutnya. Logout menghapus record token aktif saja, sehingga token lain masih berlaku sampai dicabut. Authentication membuktikan siapa pengguna; authorization menentukan tindakan yang boleh dilakukan. Proyek tugas ini baru menggunakan authentication untuk mutasi katalog, belum membedakan admin dan pelanggan.

## Menjelaskan tiga bug yang ditemukan

1. API tanpa Accept sebelumnya memanggil route login web yang tidak ada: hasilnya 500. Middleware kini melewati redirect web untuk path api dan handler selalu memilih JSON, sehingga hasilnya 401/422/404 sesuai kegagalan.
2. Validasi numeric/min saja masih menerima angka yang tidak muat dalam DECIMAL(10,2) atau INT MySQL. Batas atas sekarang sesuai skema: price 99999999.99 dan stock 2147483647. SQLite tidak menerapkan kapasitas MySQL yang sama, sehingga perlu tes batas dan HTTP MySQL.
3. PHP empty('0') bernilai true. Kondisi lama melewatkan search 0; sekarang hanya string kosong setelah trim yang dilewati. Tes membandingkan jumlah dan nama produk, tidak cukup memeriksa HTTP 200.

## Diagnosis error saat demo

ECONNREFUSED: periksa server dan port; base_url QA 8002 berbeda dari data asli 8001. 401: periksa header Authorization, login kembali jika token dicabut. 422: baca errors lalu cocokkan nama field, batas angka dan ID relasi. 404: periksa method, path dan ID. 409 saat menghapus lookup: hapus/pindahkan produk yang menggunakannya dahulu. 429: beri jeda hingga jendela pembatasan request berakhir. Error impor collection merupakan masalah client yang harus diperiksa terpisah dari HTTP server.

## Kuis singkat dan jawaban

1. Apa peran migration dibanding seeder? Migration membangun skema; seeder mengisi data contoh.
2. Mengapa tidak memakai request->all()? Agar input yang belum divalidasi tidak masuk ke operasi database.
3. Kapan memakai 201 dan 204? 201 setelah resource dibuat; 204 setelah delete berhasil tanpa body.
4. Mengapa sort perlu whitelist? Nama kolom/arah query hanya boleh dipilih dari opsi yang diketahui, bukan input bebas.
5. Mengapa token setelah logout harus diuji ulang? HTTP 200 logout saja belum membuktikan token benar-benar ditolak.

## Field gambar dari mockup

image_url adalah string URL opsional dengan batas 2048 karakter dan protokol HTTP/HTTPS. Migration baru menambah kolom nullable agar produk lama tetap ada. Server hanya menyimpan URL, tidak mengunduh file. Model fillable serta validasi controller harus sama-sama mengizinkan field agar nilai benar-benar tersimpan. Tes membuktikan create/read, penolakan javascript: dan pengosongan lewat PATCH.
