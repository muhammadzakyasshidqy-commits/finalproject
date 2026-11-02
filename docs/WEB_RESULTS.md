# Hasil web lokal - 2 November 2026

Sebelumnya root Laravel masih menampilkan welcome. Root kini memuat katalog sederhana berdasarkan mockup Home/Login/Register/Create/Update/delete guru. Tidak ada keranjang, checkout, dashboard atau role tambahan. Web memakai endpoint API yang sama dengan Postman, tanpa mengubah kontrak API atau migration.

URL lokal proyek asli: `http://127.0.0.1:8003` (27 produk). URL QA: `http://127.0.0.1:8004` (25 produk seeder).

Pengujian browser nyata pada QA berhasil untuk:

- Katalog dari API, pencarian `kaos` menghasilkan Kaos Katun, sorting harga menaik, dan pindah ke halaman 2 dari 3.
- Register akun dummy, logout, login dengan password salah menampilkan pesan, login berhasil, lalu logout kembali.
- Create produk dummy ID 39, edit nama/harga, URL javascript ditolak, batal hapus tetap mempertahankan produk, dan konfirmasi hapus menghilangkan produk dummy.
- Viewport 390px menampilkan dua kolom, documentWidth 375px tanpa overflow horizontal; viewport dikembalikan ke ukuran awal.
- Tes Laravel setelah pemasangan: 17 tests / 135 assertions lulus. JavaScript syntax check lulus.

Semua perubahan data pengujian berada di database QA. Produk dummy browser telah dihapus dan token register/login telah dicabut melalui logout. Tidak menjalankan reset database atau mematikan server/proses lama.

Server lama port 8001/8002 menerima TCP tetapi pemeriksaan API timeout; server tambahan 8003/8004 merespons HTTP 200. Penyebab timeout lama belum dipastikan, sehingga proses lama dibiarkan utuh.

Web dan collection ringkas melengkapi source pengumpulan. Bukti GUI collection manual ada pada POSTMAN_GUI_RESULTS.json; bukti collection lanjutan 57 request tetap dicatat terpisah. GitHub menyediakan source, sedangkan halaman web dijalankan lokal setelah setup.
