# Laporan Analisis Lanjutan BLINKCO: Desain & Pengujian Sistem

## 2.2 Design System

### 1. Palet Warna
| Nama Warna | Hex Code | Konteks Penggunaan |
|---|---|---|
| Pink (Primer) | `#F72585` | Tombol CTA utama (Shop Now, Tambah ke Keranjang), status fokus pada input, tombol varian aktif, ikon wishlist saat dipilih. |
| Merah | Beragam | Lencana (badge) notifikasi angka pada keranjang belanja, status pesanan "Cancelled", ikon error (Toast), peringatan stok darurat. |
| Hijau | Beragam | Ikon sukses (Toast), lencana status pesanan "Paid", indikator status stok aman (Healthy). |
| Biru | Beragam | Lencana peran (Role Badge) khusus Admin, lencana status pesanan "Shipped". |
| Kuning | Beragam | Status pesanan tertentu, indikator notifikasi peringatan stok menipis (Action Required). |
| Abu-abu | Beragam | Latar belakang tabel, lencana peran User, garis pembatas, *ring* input pasif, elemen *inactive*. |
| Putih | `#FFFFFF` | Latar belakang bersih untuk komponen kartu (Card), kontainer formulir, dan antarmuka utama halaman. |

*(Catatan: Kode warna turunan disesuaikan secara dinamis menggunakan skema utilitas class TailwindCSS)*

### 2. Tipografi
| Font Family | Font Weight | Konteks Penggunaan |
|---|---|---|
| Poppins | Reguler, Bold, Extrabold | Digunakan untuk teks yang membutuhkan hierarki penekanan visual seperti Heading, Judul Artikel, Promo Banner, dan Label Tombol CTA. |
| Montserrat | Reguler, Medium | Digunakan untuk teks berparagraf, deskripsi detail produk, konten panjang artikel, untuk memaksimalkan *readability* (keterbacaan). |

### 3. Komponen UI
| Nama Komponen | Spesifikasi Visual & Interaksi |
|---|---|
| Navbar | Navigasi dengan efek *glassmorphism* (blur tembus pandang) yang bersifat *sticky* saat halaman digulir. Mendukung *Hamburger Menu* beranimasi di layar seluler. |
| Button (Tombol) | Memiliki efek transisi warna dan *drop-shadow* saat disentuh (*hover*). Model *Pill* (kapsul) digunakan spesifik untuk pemilihan varian dengan garis tepi berwarna saat aktif. |
| Badge (Lencana) | Bentuk label mungil dengan variasi warna untuk menandakan status (*Status Badge*), peran pengguna (*Role Badge*), atau notifikasi hitungan dinamis pada ujung ikon keranjang. |
| Toast (Notifikasi) | *Popup* dinamis yang muncul melayang di sudut kanan bawah. Berisi ikon validasi silang merah atau ceklis hijau, akan menghilang secara otomatis setelah 3.5 detik. |
| Text Field (Input) | Formulir masukan teks dengan garis tepi (*ring* warna primer) ketika sedang diketik/fokus. Bidang kolom pencarian dilengkapi ikon *clear* (silang) untuk hapus cepat. |
| Card (Kartu) | Latar putih dengan bayangan *soft shadow*. Digunakan di katalog produk/artikel dengan efek khusus saat disentuh (gambar mengalami *scale-up* atau teks judul berubah warna). |
| Data Table | Tabel administrator mengusung garis pembatas tipis elegan dan mendukung baris berisikan gambar miniatur (*thumbnail*) untuk produk. |
| Sidebar / Menu | Panel samping kiri vertikal untuk dashboard admin (standar SaaS), serta menu navigasi melayang untuk filter kategori katalog tanpa merusak *layout* utama. |
| File Upload | Komponen unggah gambar khusus berbingkai putus-putus tebal (*dashed border*) dilengkapi langsung dengan *Image Preview* otomatis saat memuat foto. |

### 4. Prinsip Desain
- **Imersif & Dinamis (*Immersive & Dynamic*)**: Memaksimalkan visual seperti penempatan latar belakang video pada pendaratan pertama (Hero Section) serta mode *Split Screen* untuk formulir login agar fokus terpusat.
- **Responsivitas Tinggi (*Responsive Design*)**: Sistem grid disusun responsif 100%, dari struktur tabel dan kolom hingga bertumpuk rapi secara vertikal di ukuran layar ponsel.
- **Umpan Balik Instan (*Visual Feedback*)**: Setiap aksi krusial selalu diikuti reaksi sistem (*real-time*). Seperti: perubahan angka di keranjang instan, perubahan tombol saat proses API (dari tulisan normal ke "Menyimpan..."), dan animasi kedip (*pulse*) untuk peringatan stok.
- **Transisi Tanpa Jeda (*Seamless Transition*)**: Peralihan tab pengaturan keamanan profil, manipulasi *thumbnail* detail produk, serta penyaringan filter katalog dikembangkan meminimalisir layar putih ter-muat ulang (*smooth reload*).
- **Keterbacaan (*Readability*)**: Layout konten panjang seperti artikel dibatasi lebarnya secara tersentralisasi (*centered constraint*) demi mencegah pergerakan mata pembaca yang lelah.
- **Focal Point & Kontras Visual**: Warna dan *shadow* (bayangan pelindung) diaplikasikan strategis untuk memisahkan pengalaman berbelanja *Storefront* biasa dengan area Dashboard Manajemen yang profesional.

---

## 2.4 Pengujian Sistem

### Pengujian Fitur Pengguna (User/Customer)
| ID Test | Skenario Pengujian | Langkah Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-U01 | Melakukan registrasi akun | Klik daftar, isi data Nama, Email, Password, dan *submit* form | Sistem memproses pendaftaran dan mengarahkan klien ke proses otentikasi (login) selanjutnya | Sesuai ekspektasi | Berhasil |
| TC-U02 | Melakukan otentikasi login | Memasukkan kredensial email dan password yang tepat, lalu klik 'Log In' | Sistem memvalidasi, menyimpan *session*, mengubah rute halaman, dan tombol navbar berubah menyapa pengguna (Hi, [Nama]) | Sesuai ekspektasi | Berhasil |
| TC-U03 | Melihat Katalog Produk | Mengakses menu *Shop* | Menampilkan grid kartu produk secara interaktif beserta gambar dan harga | Sesuai ekspektasi | Berhasil |
| TC-U04 | Menjalankan *Filter* Katalog | Mengeklik salah satu opsi kategori produk di menu *Sidebar* Katalog | Daftar kartu produk langsung tersaring secara halus menyesuaikan kategori yang ditunjuk | Sesuai ekspektasi | Berhasil |
| TC-U05 | Mencari (Search) Produk | Mengetikkan kata kunci pada bilah pencarian produk | Hasil produk relevan tertampil dan ikon hapus ketikan (*Clear*) fungsional | Sesuai ekspektasi | Berhasil |
| TC-U06 | Melihat detail dan memilih Varian | Mengklik satu produk spesifik lalu memilih opsi varian berbentuk *pill* | Galeri produk muncul, klik varian mengubah garis keliling varian menjadi *highlight* primer aktif | Sesuai ekspektasi | Berhasil |
| TC-U07 | Menambah Keranjang Belanja | Menekan tombol "Tambah ke Keranjang" pada produk | *Cart Badge* di navbar langsung bertambah angkanya (real-time), dan *Toast* sukses tampil melayang | Sesuai ekspektasi | Berhasil |
| TC-U08 | Kelola pesanan di dalam Keranjang | Menekan tombol (+) atau (-) pada daftar item di halaman Keranjang | Total hitungan kuantitas menyesuaikan secara logis dengan ringkasan kalkulasi nominal Harga Total | Sesuai ekspektasi | Berhasil |
| TC-U09 | Melakukan *Checkout* / Pembayaran | Mengisi formulir alamat, memilih kurir, lalu menekan Konfirmasi | API menyimpan riwayat ke DB, mengurangi kuantitas stok varian bersangkutan, dan dialihkan ke histori | Sesuai ekspektasi | Berhasil |
| TC-U10 | Melihat Riwayat Transaksi | Membuka menu panel *Orders* (Histori Transaksi) | Menampilkan baris riwayat *order* secara urut waktu dilengkapi *Status Badge* pesanan spesifik | Sesuai ekspektasi | Berhasil |
| TC-U11 | Melihat Detail Pesanan Rinci | Menekan salah satu transaksi di menu Riwayat Transaksi | Halaman beralih membedah informasi item yang dipesan beserta riwayat pelacakan detail *(timeline)* | Sesuai ekspektasi | Berhasil |
| TC-U12 | Memperbarui Profil Pribadi | Mengakses pengaturan, merubah input (Nama/Email), lalu Simpan | Form meredup melindungi dari *double click*, *Toast* konfirmasi muncul, dan data profil tertulis diubah | Sesuai ekspektasi | Berhasil |
| TC-U13 | Mengganti Kata Sandi (Password) | Berpindah tabulasi ke Keamanan Akun, merubah *password*, Simpan | Transisi perpindahan form cepat (via tab DOM), lalu sandi akun divalidasi tersimpan baru | Sesuai ekspektasi | Berhasil |

### Pengujian Fitur Administrator
| ID Test | Skenario Pengujian | Langkah Uji | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-A01 | Otentikasi hak akses Admin | Login memakai akun dengan hak `Role: Admin` | Sistem dapat mengenali status peran, mengarahkan tampilan khusus ke *Dashboard SaaS* admin ber-sidebar vertikal | Sesuai ekspektasi | Berhasil |
| TC-A02 | Pemantauan Widget Dashboard | Membuka halaman utama Admin Dashboard | Tabel dan kotak informasi menyajikan kalkulasi Total Pesanan, Pelanggan, dan Nominal Pemasukan (*Real-Time*) | Sesuai ekspektasi | Berhasil |
| TC-A03 | Memeriksa grafik visual Chart.js | Mencocokkan tren penjualan menggunakan opsi rentang harian atau bulanan | Titik poin kurva grafik merender dan mengubah visual data dinamis saat opsi waktu dirubah | Sesuai ekspektasi | Berhasil |
| TC-A04 | Pemantauan *Stock Alert* | Menurunkan stok salah satu produk via proses DB hingga ≤ 5, lalu muat Dashboard | Kotak peringatan *Stock Alert* memunculkan status darurat, berwarna merah/kuning, serta animasi berdenyut (*pulse*) | Sesuai ekspektasi | Berhasil |
| TC-A05 | Unggah dan pratinjau (*Upload*) Produk | Di menu Tambah Produk, tekan area upload *dashed border* lalu masukan file | Gambar kecil pra-tampil (*Image Preview*) muncul di layar sesaat setelah pemilihan aset file | Sesuai ekspektasi | Berhasil |
| TC-A06 | Manajemen Katalog (CRUD) Produk | Mengakses tabel antarmuka 'Kelola Produk' | Baris-baris produk merender *thumbnail* gambar barang, nominal format rupiah, dan deret tombol ubah/hapus | Sesuai ekspektasi | Berhasil |
| TC-A07 | Merubah (*Update*) Status Pesanan | Mengakses 'Kelola Transaksi', berinteraksi pada *dropdown* baris order tertentu | Memilih status pesanan baru (cth: *Shipped*) memicu pergantian data *real-time* tanpa mengacaukan baris lain | Sesuai ekspektasi | Berhasil |
| TC-A08 | Pengelolaan Artikel Web | Membuka 'Kelola Artikel', lalu memeriksa ikon tombol interaktif manajemen | Fungsi tombol edit/hapus diaktifkan lewat ikon interaktif pensil dan sampah berserta umpan-balik *tooltip* pembantu | Sesuai ekspektasi | Berhasil |
| TC-A09 | Pembersihan dan Manajemen User | Membuka 'Kelola Pengguna' di bilah panel sisi kiri | Tabel menyajikan senarai nama akun diiringi lencana (*badge*) warna abu/biru yang menandakan pemisahan peran | Sesuai ekspektasi | Berhasil |

### Rekapitulasi Hasil Pengujian
| Kelompok Pengujian | Total Test Case | Berhasil | Gagal |
|---|:---:|:---:|:---:|
| Pengujian Fitur Pengguna (User/Customer) | 13 | 13 | 0 |
| Pengujian Fitur Administrator | 9 | 9 | 0 |
| **Total Keseluruhan** | **22** | **22** | **0** |
