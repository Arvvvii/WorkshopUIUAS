# Dokumentasi Coding: Penjelasan Fungsi

Dokumen ini menjelaskan alur fungsi utama yang berjalan di Frontend (JavaScript) maupun Backend (PHP).

## 1. Frontend (JavaScript / UI Logics)
Fungsi-fungsi utama terletak pada `js/script.js` dan inline script dalam halaman HTML.

### `updateCartBadge()`
- **Tujuan**: Memperbarui angka *badge* keranjang belanja di pojok kanan atas layar secara *real-time*.
- **Cara Kerja**: Membaca data `blinkco_cart` dari `localStorage`. Menghitung jumlah (qty) seluruh item yang ada di keranjang, lalu menginjeksinya ke elemen `#cart-badge`. Jika jumlah `0`, badge akan disembunyikan menggunakan kelas utilitas CSS `hidden`.

### `tambahKeKeranjangDB(id, name, price, image)`
- **Tujuan**: Menambahkan produk dari halaman Katalog ke keranjang belanja lokal.
- **Cara Kerja**:
  1. Melakukan validasi sesi. Jika *user* tidak login (`currentUser` tidak ada), sistem mencegah aksi dan memunculkan *toast* error.
  2. Jika sudah login, sistem membaca keranjang di `localStorage`.
  3. Mencari apakah produk (ID) sudah ada. Jika ada, `qty` ditambahkan 1. Jika belum, data *object* baru akan di-*push*.
  4. Data kembali di-simpan ke `localStorage` dan `updateCartBadge()` dipanggil.
  5. Menampilkan notifikasi sukses menggunakan `showCleanToast()`.

### `showLocalToast(title, subtitle, isError)`
- **Tujuan**: Menampilkan notifikasi *popup* (Toast) di pojok kanan bawah.
- **Cara Kerja**: Membuat elemen HTML *div* secara dinamis, mengisinya dengan ikon (ceklis hijau atau silang merah tergantung argumen `isError`), menyisipkan animasi *translate* Tailwind, dan otomatis menghapus elemen tersebut dari DOM setelah 3.5 detik dengan fungsi `setTimeout`.

### Algoritma Routing & Auth Global
- Terdapat blok IIFE (Immediately Invoked Function Expression) di awal file rahasia (seperti `profile.html` dan `history-transaksi.html`) yang mengecek `localStorage.getItem('currentUser')`. Jika tidak ditemukan, pengguna langsung dilempar ke `login.html` menggunakan `window.location.href`.

---

## 2. Backend (PHP / API)

### `api/login.php`
- **Metode**: POST JSON
- **Tujuan**: Autentikasi Pengguna
- **Cara Kerja**:
  1. Membaca *payload* JSON (email dan password).
  2. Mencari data dari tabel `users` berdasarkan email (prepared statement PDO).
  3. Menggunakan `password_verify()` bawaan PHP untuk mencocokkan hash password di database dengan password *plaintext* yang dikirimkan.
  4. Jika lolos, API merespons dengan atribut *user object* untuk disimpan di `localStorage` Frontend.

### `api/process_checkout.php`
- **Metode**: POST JSON
- **Tujuan**: Merekam transaksi pengguna ke dalam database.
- **Cara Kerja**:
  1. Memulai transaksi database menggunakan `$pdo->beginTransaction()`.
  2. Melakukan INSERT ke tabel induk `orders` untuk mendapatkan `$order_id` menggunakan `lastInsertId()`.
  3. Melakukan iterasi (perulangan) pada *array* `items` yang dikirim dari klien.
  4. Setiap iterasi melakukan 2 hal:
     - INSERT ke tabel detail `order_items`.
     - UPDATE tabel `product_variants` untuk mengurangi (decrement) nilai kolom `stock` sesuai jumlah pesanan (`quantity`).
  5. Jika seluruh proses berhasil, melakukan `$pdo->commit()`. Jika terjadi *Exception*, database dikembalikan ke posisi semula dengan `$pdo->rollBack()`.

### `api/get_dashboard_stats.php` (Admin)
- **Tujuan**: Mengembalikan data ringkasan analitik.
- **Cara Kerja**: Menjalankan beberapa *query* agregasi (`SUM`, `COUNT`) dan secara spesifik mengumpulkan tren penjualan 7 hari terakhir menggunakan fungsi tanggal database MySQL (`DATE_SUB(CURDATE(), INTERVAL 7 DAY)`) dengan klausa `GROUP BY DATE(order_date)`.
