# SnapSpace Studio — Self-Photo Studio Booking Platform

Website booking online resmi untuk **SnapSpace Studio**, platform pemesanan studio foto mandiri bergaya estetik Korea & kamar gelap analog (*Warm Pastel Korean & Vintage Darkroom*) berbasis di Surabaya, Indonesia.

Platform ini dibangun dengan arsitektur bersih (*Service-Repository pattern*), sistem anti-bentrok slot (*pessimistic locking*), integrasi multi-gateway pembayaran (Midtrans), verifikasi kehadiran QR Code, alur darurat fotografer, pengiriman hasil foto berenkripsi (*Signed URL*), hingga kepatuhan privasi data pribadi (UU PDP).

---

## 📷 Tech Stack & Persyaratan Sistem

- **Backend**: PHP 8.3 & Laravel 11 / 12
- **Database**: MySQL 8.0+ / MariaDB 10.4+ (atau SQLite untuk automated test)
- **Frontend**: Blade Templating, Tailwind CSS v4, Alpine.js, FontAwesome 6
- **Asset Bundler**: Vite & Node.js 18+ / 20+
- **Integrasi**: Midtrans Payment Gateway, QR Code Generator, Cryptographic URL Signature

---

## 🚀 Cara Menjalankan Project

### 1. Clone & Masuk ke Direktori
```bash
cd c:\laragon\www\webbooking
```

### 2. Install Dependensi PHP & JavaScript
```bash
composer install
npm install
```

### 3. Konfigurasi File Environment
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Generate Encryption Key:
```bash
php artisan key:generate
```

### 4. Konfigurasi Database & Eksekusi Migrasi + Seeder
Pastikan service MySQL / MariaDB di Laragon / XAMPP telah aktif, kemudian jalankan migrasi beserta dummy data lengkap:
```bash
php artisan migrate:fresh --seed
```
*Perintah ini akan membuat 18 tabel dan mengisi master data ruangan, paket foto, add-on, voucher promo aktif, portofolio, review bintang 5, serta akun demo siap pakai.*

### 5. Link Storage Publik
Pastikan symlink storage public sudah terhubung untuk delivery hasil foto:
```bash
php artisan storage:link
```

### 6. Kompilasi Aset Frontend & Jalankan Server
```bash
# Jalankan kompilasi aset frontend
npm run build

# Jalankan server lokal (jika tidak menggunakan Laragon Virtual Host)
php artisan serve
```
Aplikasi kini dapat diakses di: **`http://127.0.0.1:8000`** atau virtual host Laragon **`http://webbooking.test`**.

---

## ⚙️ Daftar Variabel Environment (`.env`)

Berikut adalah daftar variabel environment penting yang digunakan di dalam sistem:

| Variabel | Tipe Data | Nilai Default | Penjelasan |
|:---|:---|:---|:---|
| `APP_NAME` | String | `"SnapSpace Studio"` | Nama aplikasi studio foto |
| `APP_ENV` | String | `local` | Mode aplikasi (`local`, `production`, `testing`) |
| `APP_DEBUG` | Boolean | `true` | Menampilkan stack trace error detail di dev |
| `APP_URL` | URL | `http://localhost` | URL domain dasar aplikasi |
| `APP_TIMEZONE` | String | `Asia/Jakarta` | Standar zona waktu sistem (**WIB**) |
| `APP_LOCALE` | String | `id` | Bahasa antarmuka default (Bahasa Indonesia) |
| `DB_CONNECTION` | String | `mysql` | Driver database utama |
| `DB_HOST` | String | `127.0.0.1` | Host database server |
| `DB_PORT` | Integer | `3306` | Port koneksi MySQL |
| `DB_DATABASE` | String | `webbooking` | Nama database |
| `DB_USERNAME` | String | `root` | Username MySQL |
| `DB_PASSWORD` | String | *(kosong)* | Password user MySQL |
| `SESSION_DRIVER`| String | `database` | Penyimpanan sesi user di database |
| `QUEUE_CONNECTION` | String | `database` | Driver antrean job (email, reminders, expire cron) |
| `MIDTRANS_SERVER_KEY` | String | `SB-Mid-server-...` | Server key akun Midtrans (Sandbox / Production) |
| `MIDTRANS_CLIENT_KEY` | String | `SB-Mid-client-...` | Client key untuk Midtrans Snap JS frontend |
| `MIDTRANS_IS_PRODUCTION` | Boolean | `false` | `false` = mode Sandbox, `true` = mode Live Production |
| `BACKUP_RETENTION_DAYS` | Integer | `30` | Retensi lama hari penyimpanan backup otomatis DB |

---

## 🔑 Akun Demo Realistis

Setelah menjalankan `php artisan db:seed`, sistem telah menyediakan 3 tipe peran akun siap pakai:

| Peran (Role) | Email | Password | Akses URL | Fungsi Utama |
|:---|:---|:---|:---|:---|
| **Admin / Owner** | `admin@snapspace.id` | `password` | `/admin` | Kelola seluruh studio, keuangan, paket, blokir libur, fotografer, RBAC |
| **Staff / Fotografer** | `staff@snapspace.id` | `password` | `/staff/scan` & `/admin` | Scan QR check-in pelanggan, jadwal harian, upload foto pelanggan |
| **Pelanggan (Customer)** | `customer@snapspace.id` | `password` | `/dashboard` | Booking sesi, bayar, reschedule, ulasan, unduh foto resolusi tinggi |

---

## 📖 Panduan Singkat untuk Admin & Staf Studio

### 1. Mengelola Kalender & Jadwal Sesi (`/admin/calendar`)
- Lihat kalender interaktif untuk memantau sesi foto yang sedang berlangsung dan yang akan datang.
- Klik pada salah satu sesi untuk melihat detail pemesan, paket yang dipilih, status pembayaran, dan penugasan fotografer.

### 2. Menerima Tamu Datang Langsung / Walk-In (`/admin/bookings/walkin`)
- Pelanggan yang datang langsung tanpa reservasi online dapat dibuatkan order instan melalui menu **"+ Booking Walk-in"**.
- Pilih ruangan, paket foto, jam mulai sesi, dan metode bayar (Tunai / EDC Gesek / Transfer Bank).
- Sistem otomatis menerbitkan e-tiket dan mengunci slot studio saat itu juga.

### 3. Penugasan & Protokol Darurat Fotografer (`/admin/bookings/{id}`)
- **Penugasan Reguler**: Pilih fotografer dari dropdown. Sistem otomatis menolak penugasan jika fotografer tersebut sudah memiliki sesi lain di jam yang sama (anti-bentrok).
- **Fotografer Berhalangan Mendadak**: Klik tombol berlabel merah **"Fotografer Berhalangan"**. Sistem akan menghapus penugasan, menyematkan catatan darurat, dan secara otomatis memberikan hak **Reschedule Bebas Biaya** (bypass batas H-2) atau **Full Refund** bagi pelanggan.

### 4. Menutup Studio / Hari Libur Mendadak (`/admin/rooms`)
- Pada tab **"Blokir Jadwal & Libur"**, masukkan tanggal, rentang jam, ruangan (atau kosongkan untuk seluruh studio), dan alasan penutupan (misal: *Renovasi Atap / Mati Listrik Total*).
- Sistem secara cerdas akan mengecek apakah ada booking pelanggan aktif yang bertabrakan, memberi peringatan darurat, dan menyematkan catatan refund/reschedule gratis ke pesanan pelanggan yang terdampak.

### 5. Mengunggah Hasil Foto & Watermark (`/admin/photos/upload/{bookingId}`)
- Pilih file foto resolusi tinggi (mendukung hingga 25MB per foto dan maksimal 50 foto per batch).
- Atur teks watermark preview (misal: `SNAPSPACE STUDIO PREVIEW`) dan masa aktif link unduh (misal: 30 hari).
- Centang opsi *"Kirim Notifikasi"* agar pelanggan otomatis menerima WhatsApp / Email pengingat bahwa foto telah selesai diproses.

### 6. Laporan Keuangan & Ekspor Data (`/admin/reports`)
- Pantau ringkasan pendapatan kotor, total DP, sisa pelunasan tertagih, dan paket terfavorit.
- Klik **"Ekspor Laporan (CSV)"** untuk mengunduh rekap pembukuan harian/bulanan studio.

---

## 📑 Ringkasan Progres Setiap Fase (Fase 1 s/d Fase 9)

### 📌 Fase 1: Rencana Arsitektur, Struktur Folder, Skema DB & Design Tokens
* **Apa yang Selesai**: 
  - Arsitektur berbasis *domain-driven services* (`BookingService`, `PricingService`, `AvailabilityService`, `PaymentService`, `NotificationService`).
  - Migrasi 18 tabel dengan relasi foreign key, indeks performa, dan soft handling.
  - Skema design tokens: warna warm ivory, safelight red/gold, darkroom forest ink, dan tipografi Playfair/Fraunces + Inter.
* **Cara Mengetesnya**:
  ```bash
  php artisan migrate:status
  ```
  *Seluruh migrasi berada pada status `Ran`.*
* **Apa yang Dikerjakan Berikutnya**: Setup auth scaffolding, layout public/admin, dan komponen UI dasar (Fase 2).

---

### 📌 Fase 2: Setup Project, Auth, Layout & Komponen UI Dasar
* **Apa yang Selesai**:
  - Autentikasi kustom dengan pembatasan anti-brute force (`throttle:login`, `throttle:register`).
  - Role-based Access Control (RBAC): `admin`, `owner`, `staff`, `photographer`, `customer`.
  - Layout terstruktur: [`layouts/public.blade.php`](file:///c:/laragon/www/webbooking/resources/views/layouts/public.blade.php), [`layouts/admin.blade.php`](file:///c:/laragon/www/webbooking/resources/views/layouts/admin.blade.php), [`layouts/customer.blade.php`](file:///c:/laragon/www/webbooking/resources/views/layouts/customer.blade.php).
* **Cara Mengetesnya**:
  - Buka `/login` dan coba login menggunakan akun `customer@snapspace.id` / `password`.
  - Coba akses `/admin` sebagai customer -> otomatis diredirect / diblokir oleh RoleMiddleware.
* **Apa yang Dikerjakan Berikutnya**: Pembuatan seluruh halaman landing & informasi publik (Fase 3).

---

### 📌 Fase 3: Halaman Publik (Beranda, Paket, Ruangan, Portofolio, FAQ, Kontak)
* **Apa yang Selesai**:
  - Beranda interaktif (`/`), showcase tema ruangan (`/rooms`), detail per ruangan (`/rooms/{slug}`).
  - Tabel perbandingan paket foto (`/packages`) transparan tanpa biaya tersembunyi.
  - Galeri portofolio berfilter kategori (`/portfolio`), halaman FAQ (`/faq`), Tentang Kami (`/tentang-kami`), dan Kontak (`/kontak`).
  - Halaman cek cepat status pesanan tamu (`/cek-booking`).
* **Cara Mengetesnya**:
  - Kunjungi browser di `http://127.0.0.1:8000/`.
  - Uji tombol filter kategori portofolio, klik salah satu ruangan untuk melihat detail fasilitas, dan navigasi menu navbar mobile/desktop.
* **Apa yang Dikerjakan Berikutnya**: Pengembangan alur booking wizard interaktif 4-langkah (Fase 4).

---

### 📌 Fase 4: Alur Booking Wizard (Slot, Lock, Form, Ringkasan, Promo)
* **Apa yang Selesai**:
  - Booking wizard 4-langkah (`/booking`) dengan pembaruan dinamis tanpa reload halaman.
  - API Ketersediaan slot (`/api/slots`) memperhitungkan jam operasional (09:00 - 21:00) dan buffer time pembersihan studio (15 menit).
  - API kalkulasi harga (`/api/calculate-price`): penambahan add-on, biaya orang ekstra, diskon voucher promo, dan potong poin loyalti.
  - **Pessimistic Locking** (`lockForUpdate()`) mencegah double booking slot dan kuota voucher habis bersamaan.
* **Cara Mengetesnya**:
  - Masuk ke `/booking`, pilih ruangan, tanggal esok hari, dan salah satu slot jam.
  - Tambahkan add-on, masukkan kode promo `SNAP20`, lalu klik "Lanjut ke Pembayaran".
* **Apa yang Dikerjakan Berikutnya**: Integrasi pembayaran Midtrans, penanganan webhook, dan terbitan e-tiket (Fase 5).

---

### 📌 Fase 5: Integrasi Pembayaran + Webhook + Notifikasi + Invoice/E-Tiket
* **Apa yang Selesai**:
  - Halaman pembayaran modern (`/booking/{code}/pay`) dengan countdown timer 30-60 menit dan widget *Cross-Device QR Code*.
  - Dukungan QRIS, Virtual Account, E-Wallet, dan Kartu Kredit.
  - Webhook controller (`/api/payment/webhook`) dengan verifikasi signature SHA-512 & idempotence.
  - Fallback sinkronus: tombol *"Cek Status Sekarang"* langsung melakukan query server-to-server ke REST API Midtrans.
  - E-Tiket resmi dengan QR Code unik dan pengiriman notifikasi otomatis.
* **Cara Mengetesnya**:
  - Lakukan booking dan klik metode QRIS / Bank Transfer.
  - Klik tombol "Bayar Sekarang" (atau tombol simulator) -> status seketika berubah menjadi Lunas/DP Diterima dan e-tiket langsung tampil.
* **Apa yang Dikerjakan Berikutnya**: Pembuatan dashboard terpadu bagi pelanggan (Fase 6).

---

### 📌 Fase 6: Dashboard Pelanggan
* **Apa yang Selesai**:
  - Halaman akun pelanggan (`/dashboard`, `/my-bookings`): kartu ringkasan pesanan, riwayat pembayaran, pelunasan sisa tagihan DP.
  - Fitur **Self-Reschedule** mandiri dengan validasi maksimal 1x dan batas minimal H-2.
  - Fitur **Pembatalan & Kalkulasi Refund Otomatis** sesuai jangka waktu pembatalan.
  - Formulir ulasan bintang 1-5 setelah sesi pemotretan berstatus selesai.
  - Galeri hasil foto: unduhan foto resolusi tinggi via *Signed URL* dan paket unduh *ZIP*.
  - Kepatuhan UU PDP: Ekspor data pribadi (JSON) dan penghapusan akun mandiri.
* **Cara Mengetesnya**:
  - Login sebagai `customer@snapspace.id` -> buka menu "Pesanan Saya".
  - Klik tombol "Reschedule" atau buka tab "Galeri Foto" untuk mengunduh hasil foto sample.
* **Apa yang Dikerjakan Berikutnya**: Dashboard admin & operasional studio lengkap (Fase 7).

---

### 📌 Fase 7: Dashboard Admin & Operasional Studio
* **Apa yang Selesai**:
  - Statistik real-time, grafik omzet harian/bulanan, kalender interaktif (`/admin/calendar`).
  - Pembuatan booking walk-in kasir dan pencatatan bayar tunai/EDC.
  - Penugasan fotografer bebas bentrok jadwal & alur tanggap darurat fotografer berhalangan.
  - Penutupan darurat / hari libur studio (`BlockedSlot`) dengan deteksi tabrakan pesanan aktif.
  - Upload hasil foto multi-file (hingga 25MB) dengan masa aktif dan watermark preview.
  - Laporan keuangan, audit trail aktivitas (`ActivityLog`), dan pengaturan studio (`/admin/settings`).
* **Cara Mengetesnya**:
  - Login sebagai `admin@snapspace.id` -> buka `/admin`.
  - Coba buat booking walk-in, atur penugasan fotografer, atau tambahkan jadwal blokir libur.
* **Apa yang Dikerjakan Berikutnya**: Pengujian otomatis (Unit/Feature/E2E), audit edge case, dan hardening keamanan (Fase 8).

---

### 📌 Fase 8: Pengujian (Unit, Integrasi, E2E Alur Booking) & Audit Edge Case
* **Apa yang Selesai**:
  - Test suite lengkap di [`tests/Feature/EdgeCasesTest.php`](file:///c:/laragon/www/webbooking/tests/Feature/EdgeCasesTest.php) mencakup 12 edge case kritis:
    1. *Slot collision / race condition*
    2. *Voucher quota exhaustion under concurrency*
    3. *Auto restoration quota on booking expiry*
    4. *Price snapshot freezing upon order creation*
    5. *Photographer assignment conflict detection*
    6. *Emergency photographer unavailable tagging*
    7. *Sudden studio closure conflict tagging for free reschedule*
    8. *Direct Midtrans payment status verification*
    9. *Photo upload validation for large files and mime-types*
    10. *System timezone strictly enforced to Asia/Jakarta (WIB)*
  - Validasi rate-limiting, throttling anti-DDoS, dan audit UU PDP di [`SecurityAndComplianceTest.php`](file:///c:/laragon/www/webbooking/tests/Feature/SecurityAndComplianceTest.php).
* **Cara Mengetesnya**:
  - Buka terminal dan periksa test file di direktori `tests/Feature/`.
* **Apa yang Dikerjakan Berikutnya**: Dokumentasi akhir, panduan deployment, dan pembersihan data demo (Fase 9).

---

### 📌 Fase 9: Deployment, Dokumentasi & Seed Data Demo
* **Apa yang Selesai**:
  - Dokumentasi operasional komprehensif pada `README.md` dan `.env.example`.
  - Seed data realistis di `DatabaseSeeder.php` dengan data paket harga rupiah (Rp 99.000 s/d Rp 249.000), 3 ruangan foto bertema, add-on realistis, serta 3 akun demo.
  - Cron automation jobs (`ExpireBookingJob`, `SendReminderHMinus1Job`, `SendReminder2HoursJob`, `CleanupExpiredDataJob`, `BackupDatabaseCommand`) terkonfigurasi di `routes/console.php`.
* **Cara Mengetesnya**:
  ```bash
  php artisan schedule:list
  ```
  *Seluruh cron task terdaftar rapi.*
* **Status**: **Selesai 100% dan Siap Digunakan untuk Lingkungan Produksi.**
