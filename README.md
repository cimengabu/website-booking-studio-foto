# 📸 SnapSpace Studio — Self-Photo Studio Booking Platform

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Version"></a>
  <a href="https://tailwindcss.com"><img src="https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS"></a>
  <a href="https://midtrans.com"><img src="https://img.shields.io/badge/Midtrans-Payment_Gateway-002855?style=for-the-badge" alt="Midtrans"></a>
  <a href="#"><img src="https://img.shields.io/badge/Tests-43%20Passed%20(153%20assertions)-2E9E6B?style=for-the-badge&logo=checkmarx&logoColor=white" alt="Tests Status"></a>
  <a href="#"><img src="https://img.shields.io/badge/License-MIT-gold?style=for-the-badge" alt="License"></a>
</p>

---

## 🌟 Tentang SnapSpace Studio

**SnapSpace Studio** adalah platform web reservasi studio foto mandiri (*self-photo studio*) modern berbasis di Klaten, Jawa Tengah. Platform ini dirancang khusus untuk memberikan pengalaman pemotretan estetik tanpa canggung (*Warm Pastel Korean & Vintage Darkroom*) dengan sistem otomasi reservasi terpadu:

- 🔒 **Zero-Conflict Scheduling**: Kunci slot anti-bentrok (*pessimistic locking* `lockForUpdate()`).
- 💳 **Otomasi Pembayaran**: Integrasi Midtrans Payment Gateway (QRIS, VA Bank, E-Wallet) lengkap dengan mode Simulator 1-Klik untuk testing.
- 🎟️ **E-Tiket & Check-in QR**: Tiket digital resmi langsung terbit dengan verifikasi kedatangan di resepsionis via scanner kamera staf.
- 🎁 **SnapPoints Loyalty System**: Akumulasi poin reward per transaksi dan diskon pemotongan poin transparan dengan riwayat audit trail.
- 📸 **Digital Darkroom Delivery**: Pengiriman foto resolusi tinggi berenkripsi (*Cryptographic Signed URL* & bulk download ZIP).
- 🛡️ **Kepatuhan Privasi (UU PDP)**: Hak akses ekspor data (JSON) dan hak penghapusan akun (*Right to be Forgotten*).

---

## 🏗️ Arsitektur & Tech Stack

```
webbooking/
├── app/
│   ├── Http/Controllers/       # Web & API Controllers (Admin, Customer, Staff, Auth)
│   ├── Models/                 # 13 Eloquent Models & Relasi Database
│   ├── Services/               # Domain-Driven Core Services:
│   │   ├── BookingService.php      # Orkestrasi reservasi & proteksi concurrency
│   │   ├── PricingService.php      # Perhitungan tarif, add-on, voucher, & loyalti
│   │   ├── AvailabilityService.php # Validasi slot, buffer time, & hari libur
│   │   ├── PaymentService.php      # Integrasi Midtrans & verifikasi signature
│   │   └── NotificationService.php # Notifikasi email & WhatsApp pengingat
│   ├── Jobs/                   # Scheduled automation & asynchronous workers
│   └── Console/Commands/       # Cron task (auto-expire, reminder, DB backup)
├── database/
│   ├── migrations/             # 18 tabel terstruktur dengan foreign keys & indexes
│   └── seeders/                # Seeder realistis ruangan, paket, add-on, & akun demo
├── resources/views/            # Antarmuka Blade responsif (Public, Dashboard, Admin)
└── tests/Feature/              # Automated Test Suite (43 tests, 153 assertions)
```

### Komponen Teknologi:
- **Backend Framework**: Laravel 12 (PHP 8.3)
- **Database Engine**: MySQL 8.0 / MariaDB 10.4+ (SQLite in-memory untuk automated testing)
- **Frontend & Styling**: Blade Templating Engine, Tailwind CSS v4, Alpine.js, FontAwesome 6, Leaflet.js
- **Payment Gateway**: Midtrans Snap Engine (Sandbox / Production) & Local Payment Simulation
- **QR Code Engine**: Pure SVG / HTML5 QR Code Generator & Scanner Camera
- **Asset Bundler**: Vite 6 & Node.js 20+

---

## ⚡ Fitur Utama

### 1. Portal Pelanggan (Customer Experience)
- **Booking Wizard 4-Langkah**: Pemilihan tema ruangan, tanggal, slot waktu real-time, paket foto, penambahan add-on (cetak foto/properti), dan input catatan khusus (*notes*).
- **Kalkulator Harga Instan**: Perhitungan harga otomatis via AJAX (`/api/calculate-price`), termasuk kode promo aktif dan penukaran saldo SnapPoints.
- **Pembayaran Fleksibel**: Pilihan bayar Lunas (Full) atau DP 50%, dengan timer pembayaran mundur 60 menit.
- **E-Tiket Digital**: Dilengkapi QR Code unik untuk check-in instan di studio dan tautan petunjuk arah Google Maps.
- **Dashboard Pesanan Saya (`/my-bookings`)**:
  - Pelunasan sisa DP secara online.
  - Unduh Nota Pembayaran Resmi (PDF).
  - Reschedule Mandiri (maksimal 1x, minimal H-2 sebelum sesi).
  - Pembatalan & Kalkulasi Refund Otomatis sesuai kebijakan waktu.
  - Review & Rating bintang 1–5 dengan bonus reward +50 SnapPoints.
  - Unduh hasil foto resolusi tinggi & unduh massal format `.ZIP`.

### 2. Panel Admin & Kasir Operasional Studio (`/admin`)
- **Executive Dashboard**: Ringkasan omzet harian/bulanan, tingkat keterisian studio (*occupancy rate*), dan grafik paket terpopuler.
- **Booking Walk-In (Kasir)**: Input pesanan tamu yang datang langsung tanpa reservasi online dengan cetak tiket instan.
- **Kalender Interaktif (`/admin/calendar`)**: Pantau status ruangan dan slot sesi per hari/minggu.
- **Manajemen Penugasan Fotografer**:
  - Penugasan fotografer bebas bentrok jadwal.
  - Alur tanggap darurat: Jika fotografer berhalangan mendadak, sistem otomatis memberikan kompensasi *Free Reschedule* atau *Full Refund* bagi pelanggan.
- **Blokir Slot & Libur Studio (`BlockedSlots`)**: Penutupan operasional sebagian/seluruh studio dengan notifikasi otomatis ke pelanggan yang terdampak.
- **Delivery Foto & Watermark Engine**: Unggah hasil foto studio (hingga 25MB per foto) dengan preview watermark dan enkripsi *Signed URL* (berlaku 30 hari).
- **Laporan Keuangan & Ekspor**: Filter data pendapatan dan ekspor pembukuan format CSV.
- **Audit Trail & Keamanan**: Pencatatan log aktivitas staf dan manajemen pengguna RBAC.

### 3. Portal Resepsionis & Fotografer (`/staff/scan`)
- Scanner QR Code berbasis kamera untuk memverifikasi e-tiket kedatangan pelanggan secara cepat di meja resepsionis.
- Jadwal sesi kerja hari ini dan tombol mulai/selesaikan sesi foto.

---

## 🚀 Panduan Instalasi Lokal

### 1. Prasyarat Sistem
- PHP `>= 8.3` (ekstensi: `pdo_mysql`, `curl`, `gd`, `zip`, `fileinfo`, `mbstring`, `openssl`)
- Composer `>= 2.5`
- Node.js `>= 18.x` & NPM
- MySQL / MariaDB Server

### 2. Langkah Instalasi

```bash
# 1. Masuk ke direktori project
cd c:\laragon\www\webbooking

# 2. Install dependensi PHP & JavaScript
composer install
npm install

# 3. Setup Environment File
copy .env.example .env
php artisan key:generate

# 4. Konfigurasi Database di file .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=webbooking
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Eksekusi Migrasi & Database Seeder Lengkap
php artisan migrate:fresh --seed

# 6. Hubungkan Symlink Storage Publik
php artisan storage:link

# 7. Kompilasi Aset Frontend
npm run build

# 8. Jalankan Local Development Server
php artisan serve
```

Aplikasi siap diakses di browser: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Demo Siap Pakai

Database seeder (`DatabaseSeeder.php`) menyediakan akun uji coba lengkap:

| Role | Email | Password | Hak Akses Utama |
|:---|:---|:---|:---|
| **Admin / Superuser** | `admin@snapspace.id` | `password` | Akses penuh seluruh dashboard & setting |
| **Admin Studio** | `ind4hwijaya06@gmail.com` | `12345678` | Akses admin studio & kelola pesanan |
| **Staff / Resepsionis** | `staff@snapspace.id` | `password` | Scan tiket QR & operasional harian |
| **Fotografer** | `alim@gmail.com` | `12345678` | Akses sesi foto & upload galeri |
| **Pelanggan (Demo)** | `stefano@gmail.com` | `password` | Dashboard pelanggan (saldo 49 pts) |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Project ini dilengkapi dengan automated feature tests yang memvalidasi integritas logika bisnis dan edge cases:

```bash
# Menjalankan seluruh test suite
php -d memory_limit=512M vendor/bin/phpunit
```

### Hasil Pengujian:
```text
PHPUnit 12.5.36 by Sebastian Bergmann and contributors.

...........................................                       43 / 43 (100%)

Time: 00:15.823, Memory: 64.00 MB

OK (43 tests, 153 assertions)
```

### Area Uji yang Tercakup:
1. `Concurrency & Pessimistic Locking`: Pencegahan tabrakan reservasi slot ganda.
2. `Voucher Limit & Expiry`: Proteksi kuota promo habis secara bersamaan.
3. `Price Freezing`: Snapshot harga tidak berubah saat tarif master diedit di masa depan.
4. `Midtrans Webhook & Signature`: Validasi SHA-512 dan perlindungan idempotence.
5. `Photographer Conflict & Emergency Protocol`: Proteksi double-assignment fotografer.
6. `UU PDP & Security Compliance`: Verifikasi rate limiting, sanitasi input, dan penghapusan data pribadi.

---

## ⚙️ Variabel Konfigurasi Penting (`.env`)

| Kunci Konfigurasi | Deskripsi | Default / Rekomendasi |
|:---|:---|:---|
| `APP_TIMEZONE` | Standar zona waktu aplikasi | `Asia/Jakarta` (WIB) |
| `APP_LOCALE` | Bahasa sistem | `id` |
| `MIDTRANS_SERVER_KEY` | Server Key dari Midtrans Dashboard | `SB-Mid-server-...` |
| `MIDTRANS_CLIENT_KEY` | Client Key untuk Snap UI | `SB-Mid-client-...` |
| `MIDTRANS_IS_PRODUCTION`| Status environment Midtrans | `false` (Sandbox) / `true` (Live) |
| `SESSION_DRIVER` | Driver penyimpanan sesi user | `database` |
| `QUEUE_CONNECTION` | Driver antrean latar belakang | `database` |
| `BACKUP_RETENTION_DAYS`| Retensi backup database otomatis | `30` hari |

---

## ⏰ Otomasi Jadwal (Cron Tasks)

Aplikasi memiliki jadwal rutin otomatis yang terdaftar di `routes/console.php`:

```bash
# Menjalankan scheduler di server (tambahkan ke crontab)
* * * * * cd /var/www/webbooking && php artisan schedule:run >> /dev/null 2>&1
```

- **Setiap 5 Menit**: `ExpireBookingJob` melepaskan slot pesanan *pending* yang melewati batas 60 menit.
- **Setiap Pagi (07:00 WIB)**: `SendReminderHMinus1Job` mengirim pengingat jadwal ke pelanggan (H-1).
- **Setiap 30 Menit**: `SendReminder2HoursJob` mengirim notifikasi persiapan kedatangan (H-2 Jam).
- **Setiap Hari (02:00 WIB)**: `BackupDatabaseCommand` mencadangkan database otomatis.
- **Setiap Hari (03:00 WIB)**: `CleanupExpiredDataJob` membersihkan file sementara dan tautan unduh kedaluwarsa.

---

## 📄 Lisensi

Project ini dilisensikan di bawah lisensi [MIT License](LICENSE).  
Dikembangkan untuk **SnapSpace Studio Indonesia**.
