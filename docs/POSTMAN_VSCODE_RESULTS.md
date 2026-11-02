# Postman VS Code — verifikasi final 2 November 2026

Status terbaru: **90 assertions lulus, 0 gagal, 0 skipped** pada collection final termasuk image_url. Bukti terstruktur: POSTMAN_VSCODE_FINAL_RESULTS.json. Bagian awal berikut menyimpan riwayat run sebelumnya.

Jendela: `finalproject - Visual Studio Code`. Extension: Postman 1.19.2.
Collection: `TimeDoor FinalProject — Verified API`.
Target: `http://127.0.0.1:8002`, source asli `C:\laragon\www\finalproject`, database MySQL terisolasi `finalproject_qa_20261102`.

Hasil yang terlihat langsung pada UI runner:

- Iterations: 1
- Duration: 32s 208ms
- All tests: 87
- Passed: 87
- Failed: 0
- Skipped: 0
- Average response time: 515 ms

Seluruh 57 request dipilih berurutan dari collection yang diimpor. Bagian akhir UI memperlihatkan token setelah logout -> 401, token register yang independen -> 200, revoke token register -> 200, lalu semua token yang diterbitkan telah dicabut -> 401. Bagian tengah memperlihatkan validasi overflow -> 422 serta create tanpa token -> 401. Respons tidak dipersistenkan oleh runner.

Sebelum run, GET `/api/products?keyword=Mouse&sort=price_desc&per_page=5` dikirim manual di extension dan menghasilkan 200 OK (911 ms), berisi Mouse Wireless dengan relasi kategori/merek.

Impor JSON awal gagal dengan `Could not import collection. Please try again.` Collection kemudian diekspor melalui `postman-collection` SDK resmi (termasuk ID serta struktur URL/event kanonis), lalu berhasil diimpor dan dijalankan. File collection pada folder docs adalah format yang berhasil diimpor ini. Tidak ada perubahan source extension, autentikasi akun Postman, atau pengaturan keamanan.

Dokumen ini adalah transkripsi hasil yang diamati pada UI, bukan hasil export respons. Bukti Newman terpisah ada di `POSTMAN_RESULTS.json`; tidak memuat body kredensial, token, atau password.

## Perubahan setelah run UI ini

Figma kemudian berhasil dibaca dan menunjukkan Link URL Gambar. Field image_url opsional ditambahkan dengan migration dan validasi HTTP/HTTPS. Collection final menambah tiga assertions penyimpanan URL gambar (menjadi 90 assertions), tetap 57 request. Versi final diuji dengan PHPUnit dan Newman. Saat hendak memperbarui impor/rerun UI, pengguna menghentikan Computer Use dengan tombol Escape. Tidak ada tindakan UI sesudah penghentian. Run UI 87 assertions di atas berlaku untuk alur sebelum penambahan image_url; tiga assertions gambar terbaru belum diuji ulang melalui UI VS Code.


## Rerun final setelah izin lanjutan

Pengguna memberi izin melanjutkan Computer Use. File docs/TimeDoor_FinalProject.postman_collection.json final diimpor melalui perintah resmi Postman: Import pada VS Code. Karena collection lama sudah tersedia dan extension menawarkan Import as Copy, versi final tersimpan sebagai “TimeDoor FinalProject — Verified API Copy”. Collection lama tidak dihapus.

Seluruh 57 request dijalankan pada 127.0.0.1:8002 dengan database QA terpisah. UI runner memperlihatkan 1 iterasi, All tests 90, Passed (90), Failed (0), Skipped (0), duration 33s 220ms, average response time 541 ms. Ketiga assertions “Image URL stored” pada create produk A/B/C termasuk dalam 90 assertions yang lulus. Persist responses for a session tidak diaktifkan.

SHA-256 file collection yang diuji: 1D69A95F749FA571C57EBD7D5BCFA07CBE04C860043393EBD8F2368DE84AED16. Commit source saat run: f7ad3a4. Pemeriksaan setelah run menunjukkan katalog QA kembali 25/5/5 dan 0 token aktif. Semua row lama pada database asli tetap identik; hanya penambahan schema nullable dan log migration dari sesi sebelumnya yang ada. Catatan ini merekam pengujian yang berlangsung sebelum publikasi source.
