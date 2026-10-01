# Kilat Studio — Self-Photo Studio Booking Platform

Website booking online resmi untuk **Kilat Studio**, studio foto mandiri bergaya analog & kamar gelap (*darkroom & contact sheet aesthetic*) yang beroperasi di Surabaya, Indonesia.

---

## 📷 Tema Visual & Desain
- **Konsep**: Darkroom & Contact Sheet Photography (Kertas cetak foto, framing perforasi klise, aksen safelight #E8451E).
- **Warna Utama**:
  - Background Utama: Kertas (#F3EFE6)
  - Teks Utama: Tinta (#1B1A17)
  - CTA & Highlight: Safelight Red (#E8451E)
  - Section Kontras / Footer: Forest Dark (#1F3A2E)
  - Sorotan Badge: Butter Yellow (#F2C94C)
  - Darkroom Background: (#141311)
- **Tipografi**: Fraunces (Headings), DM Sans (Body Text), DM Mono (Code/Slot/Price).

---

## 🛠️ Tech Stack & Persyaratan

- **PHP**: ^8.2 / 8.3
- **Laravel**: ^11.0
- **Database**: MySQL 8.0+ / MariaDB
- **Frontend**: Blade Templating, Alpine.js, Tailwind CSS v4, Vite
- **Node.js**: ^18.0 / 20.0+

---

## 🚀 Cara Install & menjalankan Project

### 1. Clone Repository & Install Dependensi
```bash
# Update vendor dependencies
composer install

# Install NPM packages
npm install
```

### 2. Konfigurasi Environment
Salin `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database MySQL di `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webbooking
DB_USERNAME=root
DB_PASSWORD=
```
Generate App Key:
```bash
php artisan key:generate
```

### 3. Migrasi Database & Seeding
Jalankan migrasi beserta data awal (Paket, Addon, Jam Operasional, Voucher Promo, & Akun Admin Demo):
```bash
php artisan migrate:fresh --seed
```

### 4. Build Frontend & Jalankan Server
```bash
# Build asset produksi
npm run build

# Atau jalankan dev server
npm run dev

# Jalankan server Laravel (jika tidak memakai Laragon/Valet)
php artisan serve
```

---

## 🔑 Akun Admin Demo

Akses halaman Admin Login di: **`/admin/login`** atau **`http://localhost/admin/login`**

- **URL Admin**: `/admin`
- **Email**: `admin@studio.com`
- **Password**: `password123`

---

## 🧪 Alur Pengujian Manual (Booking Flow)

1. **Beranda (`/`)**: Pilih paket foto atau klik "Book Sekarang".
2. **Pilih Paket & Jadwal (`/booking`)**:
   - **Langkah 1**: Pilih Paket (Quick Snap / Classic / Party Pack).
   - **Langkah 2**: Pilih Tanggal & Slot Jam yang tersedia.
   - **Langkah 3**: Pilih Add-on tambahan (Cetak 4R, Softcopy Drive, dll) & Masukkan data pemesan (Zod validated).
   - **Langkah 4**: Review ringkasan & Masukkan kode promo (`SELFIE10` atau `HESTAG15K`).
3. **Pembayaran (`/booking/payment/{code}`)**: Simulasi metode pembayaran transfer/Midtrans QRIS.
4. **E-Tiket Selesai (`/booking/success/{code}`)**: Tampilan tiket resmi dengan kode booking, nomor frame, & rincian sesi.
5. **Cek Booking (`/check-booking`)**: Masukkan kode booking & nomor WhatsApp untuk melacak status pesanan kapan saja.

