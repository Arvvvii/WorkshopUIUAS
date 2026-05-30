# Dokumentasi Sistem: BLINKCO E-Commerce

## 1. Arsitektur Sistem
Sistem BLINKCO dibangun menggunakan arsitektur **Client-Server** dengan pendekatan **Single Page Application (SPA) feel**. 
- **Frontend (Client)**: HTML5, TailwindCSS (via CDN), Vanilla JavaScript. Menggunakan `localStorage` untuk manajemen *state* keranjang belanja dan sesi *login* pengguna (`currentUser`).
- **Backend (API)**: PHP 8+. Bertindak sebagai RESTful API provider yang mengolah *request* JSON dan merespons dalam format JSON.
- **Database**: MySQL. Terhubung menggunakan PDO (PHP Data Objects) untuk keamanan dari SQL Injection.

## 2. Analisa Entity Relationship Diagram (ERD)
Sistem menggunakan relasi database relasional untuk mengelola e-commerce. Berikut adalah analisis tabel dan korelasinya:

### Tabel `users`
Menyimpan data pengguna (baik admin maupun user).
- `id` (PK)
- `name`, `email`, `password` (Hashed)
- `role` (ENUM: 'admin', 'user')
- `created_at`

### Tabel `product_categories`
Menyimpan kategori utama untuk filter katalog (contoh: Apparel, Albums).
- `id` (PK)
- `name`, `slug`

### Tabel `products`
Menyimpan informasi utama dari sebuah produk (tanpa mendetailkan ukuran/warna).
- `id` (PK)
- `category_id` (FK ke `product_categories`)
- `name`, `description`, `price`, `image_url`

### Tabel `product_variants`
Menyimpan varian dari produk (misalnya Baju Ukuran M, Album Versi Pink). Memisahkan stok di level varian.
- `id` (PK)
- `product_id` (FK ke `products`)
- `variant_name` (Contoh: "Size M", "Pink Ver")
- `stock` (Integer)
- `sku`, `price_adjustment`

### Tabel `orders`
Menyimpan data induk dari sebuah pesanan (transaksi).
- `id` (PK)
- `user_id` (FK ke `users`)
- `total_amount` (Total harga)
- `status` (ENUM: 'pending', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled')
- `shipping_address`, `payment_method`
- `order_date`

### Tabel `order_items`
Menyimpan baris item yang dibeli dalam satu `order`.
- `id` (PK)
- `order_id` (FK ke `orders`)
- `product_id` (FK ke `products`)
- `variant_id` (FK ke `product_variants`)
- `quantity`, `price` (Harga saat dibeli)

### Relasi Utama:
1. **User - Order** (One-to-Many): Satu pengguna dapat memiliki banyak pesanan.
2. **Category - Product** (One-to-Many): Satu kategori memiliki banyak produk.
3. **Product - Variant** (One-to-Many): Satu produk memiliki banyak pilihan varian (stok dikelola di varian).
4. **Order - Order Items** (One-to-Many): Satu pesanan terdiri dari berbagai macam item belanja.
5. **Variant - Order Items** (One-to-Many): Item belanja mengambil data (dan mengurangi stok) dari varian tertentu.
