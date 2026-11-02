# Postman untuk tugas TimeDoor Pro

Gunakan collection **finalproj - TimeDoor Pro** dari `finalproj-TimeDoor.postman_collection.json`. Isinya 19 request, dibagi menjadi user (3), product (6), category (5), dan brand (5). Nama dan susunan folder mengikuti contoh guru; kategori/merek melengkapi ketentuan tiga tabel dan CRUD.

Collection ini untuk memilih dan mengirim request secara manual. Tidak perlu menjalankan semua request dengan Runner. Uji negatif, PATCH, FK, batas angka, dan revokasi lengkap tetap tersedia terpisah dalam `TimeDoor_FinalProject.postman_collection.json` (57 request).

1. `user/register` membuat akun dummy. Jika email sudah terdaftar (422), gunakan `user/login` atau ubah variabel email. Salin nilai `data.token` dari respons ke kolom **Current value** variabel **token** pada collection Variables. Authorization request terlindungi mewarisi Bearer token ini.
2. Buka `category/get categories` dan `brand/get brands`. Untuk mencoba mutasi tanpa mengubah data yang sudah ada, jalankan `create category` dan `create brand` terlebih dahulu. Isi **categoryId** dan **brandId** di Current value pada collection Variables dari ID respons masing-masing.
3. `product/create product` menggunakan categoryId dan brandId tersebut, lalu isi **productId** dari ID respons. Kirim get detail, update, dan delete untuk produk dummy itu. Alternatifnya isi ID dari daftar pada Variables sebelum request detail.
4. Request `search, sort, pagination` memperlihatkan satu contoh query gabungan. Ubah keyword, sort, per_page dan page melalui tab Params.
5. Hapus produk dummy dahulu, kemudian kategori/merek dummy. Respons delete **204 tanpa body** berarti berhasil. Logout dilakukan paling akhir.

ID awal dibiarkan kosong supaya request update/delete tidak langsung menunjuk data yang sudah ada. Register dan login masing-masing menerbitkan token; logout mencabut token yang sedang dipakai. Jangan mengunggah variabel token yang telah terisi ke GitHub.

Server QA lokal sesi ini: `http://127.0.0.1:8004` menggunakan database pengujian terpisah. Web proyek asli: `http://127.0.0.1:8003`. Saat menjalankan ulang proyek, gunakan port yang tersedia dan sesuaikan baseUrl pada Variables. File .env asli tidak diubah.

Collection manual ini tidak menggunakan Scripts. Pada pengujian extension, nilai yang ditetapkan skrip tidak terbawa ke request manual berikutnya; token dan ID karena itu sengaja diisi melalui Variables. Setelah logout, kosongkan Current value token. Jangan mengubah Initial value menjadi token aktif.

Pengisian Variables: klik dua kali sel pada kolom **Current value** yang benar, isi nilainya, tekan Tab, lalu klik tombol **Save** collection. Pengujian GUI final membuktikan create/update/delete produk menghasilkan 201/200/204. Token dan ID fixture dikosongkan kembali setelah selesai; data dummy katalog dihapus dan token QA aktif kembali 0. Bukti: POSTMAN_GUI_RESULTS.json.
