# Pemeriksaan publikasi source

Tanggal: 2 November 2026. Source berasal dari proyek finalproject asli. Riwayat lokal lama diperiksa pada publikasi pertama (7 commit, 116 blob); metadata pribadi dan catatan lokalnya tetap lokal. Riwayat public dimulai dari commit root c38537f dengan email GitHub noreply. Pembaruan ini menambahkan web sederhana dan collection Postman manual 19 request pada repository public yang sama.

Seluruh file calon commit diperiksa kembali terhadap nilai rahasia environment lokal, pola token/credential/private key, alamat email pribadi dari riwayat lokal, lokasi home PC, file runtime/database dan metadata commit. File .env asli, database/SQL, data pribadi/klien, backup, log/cache, vendor serta token aktif tidak disertakan. Password fixture hanya untuk akun example.test. Email penulis dependency upstream pada composer.lock merupakan metadata publik paket. Slide privat tidak disertakan.

Perubahan source aplikasi dibatasi pada route root web, Blade, CSS dan JavaScript. Endpoint API, model, controller, migration, seeder dan tes backend sama dengan versi backend yang telah diuji. SOURCE_INTEGRITY.json mencatat hash Git blob dan SHA256 96 file source/konfigurasi/tes, termasuk web. Collection manual tidak memiliki Scripts; token serta ID awal kosong. Collection lengkap 57 request tetap terpisah.

Bukti: Laravel 17 tests/135 assertions; collection manual 19 request diuji HTTP; register/login dan CRUD inti diamati pada Postman VS Code; alur web diuji browser pada database QA. Data dummy katalog dibersihkan dan token QA aktif kembali 0. Ringkasan bukti tidak berisi response body kredensial.

Advisori framework Laravel 10 tetap dicatat pada SECURITY_REVIEW.md. Publikasi source GitHub bukan deployment server aplikasi.
