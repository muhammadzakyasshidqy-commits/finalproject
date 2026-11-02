<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Shop · TimeDoor</title>
    <link rel="stylesheet" href="{{ asset('shop.css') }}">
    <script src="{{ asset('shop.js') }}" defer></script>
</head>
<body>
    <header class="navbar"><nav class="container" aria-label="Navigasi utama">
        <a class="brand" href="#home">Online Shop</a>
        <div id="account-nav"></div>
    </nav></header>
    <main class="container" id="app" tabindex="-1"><p role="status">Memuat produk…</p></main>
    <div class="container"><p id="notice" role="status" aria-live="polite"></p></div>
    <dialog id="delete-dialog" aria-labelledby="delete-title">
        <h2 id="delete-title">Hapus produk?</h2>
        <p id="delete-name"></p><p>Produk yang dihapus tidak akan tampil lagi.</p>
        <p class="form-error" id="delete-error" role="alert"></p>
        <div class="actions"><button type="button" id="cancel-delete" class="secondary">Batal</button><button type="button" id="confirm-delete" class="danger">Hapus produk</button></div>
    </dialog>
    <noscript>Aktifkan JavaScript untuk menampilkan katalog dan menggunakan form.</noscript>
</body>
</html>
