# Dokumentasi Frontend (UI/UX) - BLINKCO

Dokumen ini merangkum secara rinci pendekatan UI (User Interface) dan UX (User Experience) untuk ke-27 fitur yang ada di dalam platform BLINKCO, tanpa membahas sisi _backend_. Fokus utama adalah pada interaksi pengguna, desain visual, hierarki informasi, dan kemudahan penggunaan.

---

### 1. Landing Page
- **UI/UX**: Menggunakan desain modern dan imersif. Hero section dilengkapi dengan latar belakang video (K-Pop/BLACKPINK) yang memutar otomatis, memberikan kesan dinamis. Dilengkapi dengan navigasi *glassmorphism* (blur tembus pandang) yang *sticky* (menempel di atas saat di-*scroll*). Terdapat tombol CTA (*Call to Action*) ganda ("Shop Now" dan "Explore News") dengan kontras warna primer (#F72585).

### 2. Arsip Artikel
- **UI/UX**: Ditampilkan dalam format *Grid Layout*. Setiap kartu artikel memiliki efek *hover* di mana gambar akan sedikit membesar (*scale-up*) dan judul artikel berubah warna, memberikan umpan balik visual bahwa kartu tersebut bisa diklik. Tipografi menggunakan kombinasi Poppins dan Montserrat untuk *readability* (keterbacaan) maksimal.

### 3. Detail Artikel
- **UI/UX**: Tata letak difokuskan pada kenyamanan membaca. Gambar utama (*Hero Image*) diletakkan di atas dengan lebar penuh, disusul teks artikel yang berada di tengah halaman (*centered constraint*) agar mata tidak terlalu lelah membaca teks yang terlalu lebar. Terdapat rute navigasi (*Breadcrumb*) untuk kembali ke arsip.

### 4. Katalog Produk
- **UI/UX**: Menggunakan sistem *Grid* yang responsif (1 kolom di HP, hingga 4 kolom di layar besar). Kartu produk terlihat bersih dengan latar putih dan bayangan halus (*soft shadow*). Terdapat ikon *Wishlist* berbentuk hati di setiap produk yang akan terisi warna pink bila diklik.

### 5. Detail Produk
- **UI/UX**: Tata letak dua kolom: galeri gambar di sebelah kiri dan informasi (harga, nama, deksripsi) di sebelah kanan. Pengguna tidak perlu berpindah halaman untuk memasukkan barang ke keranjang; klik "Tambah ke Keranjang" memicu notifikasi visual dinamis berupa *Toast popup* di sudut bawah layar.

### 6. Keranjang
- **UI/UX**: Menampilkan daftar item dalam keranjang. Pengguna dapat menambah atau mengurangi jumlah kuantitas lewat tombol interaktif (+ / -). Ringkasan belanja (Total Harga) diletakkan di panel samping (desktop) atau melayang di bawah (mobile) sehingga pengguna selalu tahu total bayar sebelum klik "Checkout".

### 7. Pembayaran (Checkout)
- **UI/UX**: Formulir dipecah secara logis (Alamat Pengiriman, Metode Pembayaran). Input teks (*Text field*) memiliki status fokus yang jelas (*ring* warna primer) agar pengguna tahu bagian mana yang sedang diisi. Ringkasan pesanan disajikan di panel abu-abu yang kontras agar mudah ditinjau.

### 8. History Transaksi
- **UI/UX**: Daftar pesanan disajikan dalam bentuk daftar berbaris (*list*). Setiap pesanan memiliki **Status Badge** berwarna yang langsung menarik perhatian (misal: Hijau untuk "Paid", Biru untuk "Shipped", Merah untuk "Cancelled"). Klik pada kartu pesanan akan diarahkan ke Detail Pesanan.

### 9. Dashboard (Admin)
- **UI/UX**: Mengusung tema *clean-dashboard*. Terdapat 4 kartu metrik utama di bagian atas dengan ikon berwarna khas. Dilengkapi grafik visualisasi interaktif (menggunakan Chart.js) dengan gradien warna yang estetik, membuat admin mudah membaca tren harian/bulanan dalam sekilas pandang.

### 10. Kelola Artikel (Admin)
- **UI/UX**: Berbentuk *Data Table* berdesain modern (garis pembatas abu-abu tipis, teks *bold* pada *header*). Aksi seperti Edit dan Delete disajikan dengan ikon yang intuitif (pensil dan tempat sampah) dengan *tooltip* saat disentuh *mouse*.

### 11. Kelola Produk (Admin)
- **UI/UX**: *Table list* yang menampilkan kuku jempol gambar (*thumbnail*) produk agar admin tidak hanya melihat teks, melainkan visual barangnya langsung. Harga diformat secara otomatis menggunakan Rupiah sehingga rapi dan mudah dibaca.

### 12. Kelola Pengguna (Admin)
- **UI/UX**: Daftar pengguna menyajikan nama, email, dan lencana peran (*Role Badge*). Tampilan lencana untuk Admin (biru) dan User (abu-abu) dibedakan warnanya guna menghindari kesalahan identifikasi.

### 13. Kelola Transaksi (Admin)
- **UI/UX**: Mengelompokkan riwayat order masuk. Tombol "Update Status" menggunakan *dropdown* interaktif. Perubahan status pesanan dikonfirmasi secara *real-time* lewat indikator warna yang berubah di sisi admin dan juga akan berefleksi seketika di sisi klien.

### 14. Login & Registrasi
- **UI/UX**: Halaman menggunakan mode belah layar (*Split Screen*). Sebelah kiri adalah gambar estetis (atau video singkat) yang mencerminkan *brand* BLINKCO, sebelah kanan difokuskan untuk formulir input. Desain minim distraksi untuk memaksimalkan rasio konversi pendaftaran.

### 15. Autentikasi User (Visual Feedback)
- **UI/UX**: Setelah login berhasil, navbar secara mulus (tanpa merusak *layout*) mengganti tombol "Log In" menjadi "Hi, [Nama]". Lencana merah berisi angka barang (*Cart Badge*) yang sebelumnya tersembunyi, kini muncul melayang di ikon keranjang.

### 16. Role Admin & User (Visual Separation)
- **UI/UX**: Pengalaman Admin dan User dipisahkan seratus persen secara visual. User memiliki Navbar khas *Storefront* di atas, sedangkan Admin dipandu dengan *Sidebar Navigation* vertikal di sisi kiri dan area konten di kanan, sesuai standar aplikasi *Software as a Service* (SaaS).

### 17. Product Gallery
- **UI/UX**: Di halaman detail, produk bisa memiliki kumpulan gambar kecil (*thumbnails*) di bawah gambar utama. Mengklik gambar kecil ini akan menukar gambar utama tanpa me-muat ulang (*refresh*) halaman (Transisi yang *seamless*).

### 18. Product Variant
- **UI/UX**: Pemilihan ukuran atau versi album tidak menggunakan *dropdown menu* yang membosankan, melainkan menggunakan tombol berbentuk *pill* (kapsul). Ketika satu varian diklik, akan ada transisi *border* dan *background* berwarna pink (Primer) menandakan tombol tersebut aktif.

### 19. Filter Produk
- **UI/UX**: Filter kategori disematkan pada navigasi mini atau *sidebar* di Katalog. Filter langsung bekerja menyaring kartu-kartu produk tanpa membuat layar pengguna berkedip (*smooth reload*).

### 20. Search Produk
- **UI/UX**: Kolom pencarian memanjang, dilengkapi ikon kaca pembesar. Memberikan teks *placeholder* yang jelas (contoh: "Cari album, baju, dll..."). Saat diketik, tombol silang (Clear) muncul untuk mempermudah penghapusan ketikan.

### 21. Promo Banner
- **UI/UX**: Terletak strategis di tengah Landing Page (atau atas Katalog). Menggunakan tipografi besar nan tebal (*Extrabold*). Dilengkapi bayangan melayang (*drop-shadow*) pada tombol "Claim Offer" untuk menjadikannya pusat perhatian mata pengguna (*Focal Point*).

### 22. Customer Reviews
- **UI/UX**: Ditampilkan di bagian bawah Detail Produk. Menampilkan ikon Bintang emas penuh/kosong yang rapi untuk tingkat kepuasan, diikuti nama pengguna dan teks ulasan. Disusun dalam *grid* yang konsisten.

### 23. Responsive Design
- **UI/UX**: Aplikasi dijamin 100% responsif. Elemen yang awalnya horizontal di laptop akan bertumpuk mulus secara vertikal di *smartphone*. Menu navigasi utama akan disembunyikan dan digantikan oleh *Hamburger Menu* beranimasi saat dibuka.

### 24. Upload Gambar Produk
- **UI/UX**: Di form "Tambah Produk" Admin, kolom *upload file* menggunakan batas garis putus-putus (*dashed border*) yang lebar. Jika admin memilih gambar, kuku jempol pratinjau (*Image Preview*) langsung muncul agar admin yakin gambar yang diunggah tidak salah.

### 25. Stock Management
- **UI/UX**: Diperlihatkan dengan jelas di Dashboard Admin. Produk dengan stok aman (di atas 5) memiliki status *Healthy* (Hijau). Produk di bawah limit memunculkan warna Kuning/Merah (*Action Required*) yang bahkan berkedip (*animate-pulse*) untuk menarik atensi gawat darurat dari admin.

### 26. Profile
- **UI/UX**: Halaman didesain dengan tata letak dua-kolom. Di sebelah kiri, ada *avatar circle* berisi huruf awal nama pengguna yang elegan (misal: "A" untuk Andy). Di bawahnya terdapat label identitas seperti 'Member BLINKCO' beserta tanggal bergabung untuk rasa kepemilikan yang eksklusif.

### 27. Pengaturan
- **UI/UX**: Diintegrasikan di halaman profil menggunakan antarmuka berbasis Tab (*Tabbed Interface*). Terdapat dua tombol tabulator ("Edit Profil" & "Keamanan Akun"). Beralih antartab dilakukan sekejap mata karena dirender menggunakan manipulasi DOM *Javascript*. Saat menekan tombol simpan, tombol akan meredup, tulisan berubah menjadi "Menyimpan...", mencegah pengguna mengeklik berkali-kali secara tidak sengaja (*double-submit prevention*).
