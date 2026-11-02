# Pemeriksaan publikasi source

Tanggal: 2 November 2026. Pemeriksaan mencakup tujuh commit lokal dan 116 blob historis, nama file, pesan commit, metadata penulis, template environment, source aplikasi, migration/seeder, tes serta seluruh dokumentasi. Pola credential/token/private key dan referensi data privat diperiksa.

Riwayat lama memuat metadata pribadi dan catatan lokal, sehingga tetap disimpan lokal. Repository public dibuat dari snapshot terverifikasi dengan satu commit root baru memakai alamat GitHub noreply. File aplikasi, konfigurasi template, migration, seeder dan tes dicocokkan hash per file dengan versi yang diuji; hanya dokumentasi publik yang disesuaikan. Tidak ada proyek pengganti atau perubahan perilaku aplikasi.

File environment asli, database/SQL, data pribadi atau klien, backup, log/cache runtime, vendor, token aktif, dan alamat pribadi pada metadata commit tidak dipublikasikan. Bukti tes berisi ringkasan status dan assertions tanpa request/response body kredensial. Password contoh pada test dan collection adalah fixture untuk akun example.test, bukan credential aplikasi asli. Metadata penulis paket upstream dalam composer.lock merupakan metadata publik dependency. Materi slide privat tidak disertakan.

Pemeriksaan ini tidak menggantikan audit keamanan independen. Advisori framework Laravel 10 tetap dicatat secara terbuka pada SECURITY_REVIEW.md. Source GitHub bukan deployment server aplikasi.
