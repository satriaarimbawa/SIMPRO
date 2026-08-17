<p align="center">
  <img src="public/images/logo_simpro_lockup_wordmark.png" alt="SIMPRO Logo" width="280">
</p>

<h1 align="center">SIMPRO</h1>
<p align="center"><strong>Sistem Informasi & Pengelolaan SPK</strong></p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/TailwindCSS-3-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black" alt="Alpine.js">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
</p>

---

## 📋 Tentang Aplikasi

**SIMPRO** adalah aplikasi manajemen proyek internal berbasis web yang dirancang untuk mengelola:
- **Surat Perintah Kerja (SPK)** secara digital dari awal hingga selesai
- **Termin & Pembayaran** dengan fitur pencocokan uang masuk otomatis
- **Arsip Proyek** yang terstruktur dan mudah dicari
- **Surat Jalan** dengan 3 template perusahaan berbeda yang dapat diunduh dalam format Excel
- **Laporan Realisasi** SPK secara periodik

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 📊 **Dashboard** | Ringkasan status SPK, termin, dan pembayaran secara real-time |
| 📝 **Input SPK** | Form pembuatan SPK baru dengan manajemen termin multi-tahap |
| 🗂️ **Arsip** | Daftar seluruh SPK dengan filter, pencarian, dan modal detail |
| 💰 **Cocokkan Pembayaran** | Input nominal uang masuk, sistem mencari termin yang cocok otomatis |
| 🚚 **Surat Jalan** | Generate & unduh Surat Jalan format Excel untuk 3 perusahaan (WTM, WKB, WAM) |
| 📈 **Laporan** | Laporan realisasi SPK berdasarkan periode |
| 🔐 **Autentikasi** | Sistem login pengguna yang aman |

---

## 🛠️ Tech Stack

- **Backend**: [Laravel 12](https://laravel.com) (PHP 8.2)
- **Frontend**: Blade Template + [Alpine.js](https://alpinejs.dev) + [HTMX](https://htmx.org)
- **Styling**: [Tailwind CSS v3](https://tailwindcss.com) + Custom Design System
- **Database**: MySQL
- **Export Excel**: [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io)
- **Build Tool**: [Vite](https://vitejs.dev)
- **Animasi**: Native CSS Animations + HTMX Boost (SPA-like navigation)

---

## 🚀 Cara Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js >= 18 & NPM
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/username/simpro.git
cd simpro

# 2. Install dependensi PHP
composer install

# 3. Install dependensi JavaScript
npm install

# 4. Salin file environment
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Konfigurasi database di file .env
#    DB_DATABASE=nama_database
#    DB_USERNAME=username
#    DB_PASSWORD=password

# 7. Jalankan migrasi database
php artisan migrate

# 8. Build asset frontend
npm run build

# 9. Jalankan server development
php artisan serve
```

Buka browser dan akses `http://127.0.0.1:8000`

---

## 📁 Struktur Direktori Penting

```
simpro/
├── app/
│   ├── Http/Controllers/     # TerminController, SpkController, dll
│   └── Models/               # Spk, Termin, SuratJalan, Pembayaran, dll
├── database/
│   └── migrations/           # Semua skema tabel database
├── resources/
│   ├── css/app.css           # Custom CSS & animasi
│   ├── js/app.js             # Alpine.js & HTMX setup
│   └── views/                # Blade templates per fitur
│       ├── layouts/          # app.blade.php, auth.blade.php
│       ├── dashboard.blade.php
│       ├── arsip/
│       ├── spk/
│       ├── termin/
│       ├── pembayaran/
│       ├── laporan/
│       └── auth/
├── routes/
│   └── web.php               # Definisi seluruh route
├── public/
│   └── images/               # Logo & aset gambar
├── tailwind.config.js        # Konfigurasi design system
└── vite.config.js
```

---

## 📄 Lisensi

Aplikasi ini bersifat **Proprietary** — untuk penggunaan internal perusahaan.  
Tidak diizinkan untuk didistribusikan atau digunakan secara komersial tanpa izin.
