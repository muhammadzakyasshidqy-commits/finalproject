# Audit keamanan — 2 November 2026

`composer validate --no-check-publish` lulus. `composer audit --locked --format=json` selesai dengan exit 1: lima record advisori Laravel framework, mencakup empat GHSA unik (email CRLF terdaftar dua kali). Tidak ada paket abandoned. Hasil mentah tersimpan di `COMPOSER_AUDIT.json`.

| Advisori | Temuan dan konteks proyek |
|---|---|
| [TokenGuard bypass, critical](https://github.com/advisories/GHSA-f9x3-7vq9-cph6) | Mengenai guard token bawaan Laravel. Proyek memakai auth:sanctum; config auth tidak mengaktifkan driver token. Ini membatasi relevansi jalur serangan, tetapi tidak membuat dependency audit bersih. |
| [Debug page XSS, low](https://github.com/advisories/GHSA-jh5r-qr3c-85q8) | Berkaitan dengan APP_DEBUG=true. Kedua server sesi ini dijalankan dengan override APP_DEBUG=false, hanya pada 127.0.0.1; .env lokal tidak diubah dan masih mengaktifkan debug untuk development. |
| [Temporary signed URL confusion, medium](https://github.com/advisories/GHSA-crmm-hgp2-wgrp) | Proyek tidak mempunyai route temporary URL upload/download atau penggunaan temporaryUrl. Belum dilakukan exploit test terhadap framework. |
| [Email CRLF, high](https://github.com/laravel/framework/security/advisories/GHSA-5vg9-5847-vvmq) | Relevan untuk aplikasi yang mengirim email ke input pengguna. Register/login saat ini tidak mengirim email dan User tidak menerapkan MustVerifyEmail. Jangan menambahkan flow email dengan asumsi validator versi lama bebas advisori. |

Advisori resmi mencantumkan patch pada Laravel 12/13; versi tersebut di luar ketentuan Laravel 10 pada brief. Framework/vendor tidak dimodifikasi atau di-upgrade lintas major secara diam-diam. Agar audit dependency bersih sebelum deployment publik, sepakati upgrade framework dengan guru/pemilik proyek, update dependencies dan ulangi seluruh tes. Audit ini tidak mengklaim bahwa seluruh kerentanan framework dapat dieksploitasi melalui endpoint proyek.

Pemeriksaan aplikasi: password di-hash, password/remember_token tidak keluar pada profil; input mutasi berasal dari validate dan model fillable; sort memakai whitelist; query memakai parameter Eloquent; foreign key terverifikasi; logout benar-benar menolak token; mutasi katalog butuh token. Semua pengguna yang berhasil register memiliki akses mutasi yang sama: belum ada role admin/customer, sesuai kontrak tugas yang diterapkan. Ini perlu keputusan produk sebelum toko dipublikasikan.

Snapshot publik hanya memuat source, dokumentasi dan bukti tes yang sudah diperiksa. File .env, database, backup, log, vendor dan token aktif tidak disertakan. Metadata pribadi pada riwayat lama disimpan lokal; riwayat publik memakai commit bersih dengan alamat GitHub noreply. Lihat PUBLICATION_REVIEW.md untuk ruang lingkup pemeriksaan.
