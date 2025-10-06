# Tabungan App

Aplikasi tabungan sederhana berbasis Laravel 12 dengan fokus pada kemudahan pengelolaan setoran dan penarikan untuk satu rekening tabungan per pengguna. Seluruh antarmuka menggunakan TailwindCSS berbasis komponen Blade dan mendukung pencetakan struk transaksi.

## Fitur Utama

- Registrasi & login pengguna dengan pembuatan rekening otomatis pada login pertama.
- Satu rekening tabungan per pengguna dengan nomor rekening unik.
- Transaksi setoran dan penarikan dengan validasi saldo dan nominal minimal Rp1.000.
- Riwayat transaksi lengkap dengan filter tipe dan rentang tanggal.
- Dashboard saldo, ringkasan transaksi bulanan, dan daftar transaksi terbaru.
- Halaman detail transaksi dan struk siap cetak (PDF/HTML fallback) lengkap dengan QR visual.
- Kebijakan akses berbasis policy: pengguna hanya dapat melihat data miliknya.
- Seeder demo dengan dua akun pengguna dan transaksi contoh.

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru dengan ekstensi `bcmath` dan `intl`.
- SQLite (default) atau MySQL untuk basis data.
- Composer untuk manajemen dependensi PHP.
- Node.js (opsional bila ingin menyalin aset statis sendiri, meskipun proyek ini menggunakan CDN Tailwind untuk kemudahan demo).

## Instalasi & Setup

1. **Clone repositori** dan masuk ke direktori proyek.
2. **Pasang dependensi PHP** dan paket tambahan:
   ```bash
   composer install
   composer require barryvdh/laravel-dompdf simplesoftwareio/simple-qrcode pestphp/pest --dev pestphp/pest-plugin-laravel --dev
   ```
   > Catatan: pada lingkungan tanpa akses ke GitHub, siapkan token OAuth GitHub agar proses install berhasil.
3. **Salin berkas environment** dan buat key aplikasi:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Konfigurasi basis data** di `.env`. Secara default, aplikasi menggunakan SQLite. Untuk SQLite, cukup buat file kosong:
   ```bash
   touch database/database.sqlite
   ```
5. **Jalankan migrasi dan seeder demo**:
   ```bash
   php artisan migrate --seed
   ```
6. **(Opsional) Build aset front-end** bila ingin menghapus ketergantungan CDN:
   ```bash
   npm install
   npm run build
   ```

## Menjalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi melalui `http://localhost:8000`. Gunakan kredensial demo dari seeder:

- Email: `demo@tabungan.test` — Password: `password`
- Email: `demo2@tabungan.test` — Password: `password`

## Pengujian

Proyek ini menggunakan Pest. Setelah memasang dependensi dev, jalankan:

```bash
php artisan test
```

Pengujian mencakup skenario utama: pembuatan rekening otomatis, setoran & penarikan, pembatasan akses data, serta endpoint struk.

## Asumsi & Catatan

- TailwindCSS dimuat via CDN untuk menyederhanakan setup demo. Gunakan Vite bila membutuhkan optimasi produksi.
- Struk transaksi menggunakan DomPDF. Jika DomPDF belum terpasang, aksi pencetakan akan merender halaman HTML sebagai fallback.
- QR code pada struk merupakan representasi visual berbasis hash nomor struk. Pasang `simple-qrcode` bila ingin menggunakan QR code standar.
- Rate limit sederhana (`throttle:5,1`) diterapkan pada endpoint transaksi untuk mencegah spam.
- Zona waktu aplikasi disetel ke `Asia/Jakarta` dan bahasa default `id`.

## Struktur Penting

- `app/Actions`: logika bisnis terpisah untuk pembuatan rekening dan transaksi.
- `app/Http/Controllers`: controller untuk dashboard, transaksi, dan autentikasi.
- `app/Http/Requests`: validasi form setoran dan penarikan.
- `app/Policies`: policy akses rekening dan transaksi.
- `resources/views/components`: komponen Blade untuk card, button, badge, input uang, tabel, modal, dan layout.
- `resources/views/transactions/receipt.blade.php`: template struk PDF/HTML.
- `tests/`: pengujian fitur menggunakan Pest (tambahkan setelah memasang dependensi dev).

## Lisensi

Proyek ini berlisensi MIT mengikuti lisensi standar Laravel.
