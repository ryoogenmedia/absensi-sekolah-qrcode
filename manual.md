# Panduan Instalasi Sistem Absensi Sekolah QR Code & WhatsApp Broadcast

Dokumen ini berisi panduan langkah-demi-langkah untuk melakukan instalasi dan konfigurasi aplikasi **Absensi Sekolah berbasis QR Code** yang terintegrasi dengan **WhatsApp Broadcast Server**.

---

## 📌 Prasyarat Sistem (System Requirements)

Sebelum memulai instalasi, pastikan server atau komputer Anda memenuhi spesifikasi berikut:

*   **Sistem Operasi**: Windows (disarankan menggunakan Laragon/XAMPP), Linux, atau macOS.
*   **PHP**: Versi `8.1` atau lebih tinggi.
*   **Database**: MySQL atau MariaDB.
*   **Composer**: Dependency manager untuk PHP.
*   **Node.js**: Versi LTS terbaru (untuk Vite dan WhatsApp Broadcast Server).
*   **Browser/Chromium**: Dibutuhkan oleh library `whatsapp-web.js` untuk menjalankan browser headless.

---

## ⚙️ Langkah 1: Instalasi & Konfigurasi Laravel (Aplikasi Utama)

Ikuti langkah-langkah berikut di direktori utama proyek (`absensi-sekolah-qrcode`):

### 1. Salin File Environment (`.env`)
Salin file konfigurasi `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*(Jika di Windows CMD/PowerShell, gunakan `copy .env.example .env`)*

### 2. Konfigurasi Database & APP_URL
Buka file `.env` yang baru saja dibuat, lalu sesuaikan konfigurasi database dan URL aplikasi Anda:
```env
APP_NAME="Absensi Sekolah QR Code"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://absensi-sekolah-qrcode.test # Sesuaikan dengan domain Laragon/Localhost Anda

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_sekolah # Nama database yang Anda buat di MySQL
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Install Dependensi PHP
Jalankan perintah composer untuk mendownload dan menginstal pustaka yang diperlukan:
```bash
composer install
```

### 4. Generate Application Key
Buat kunci aplikasi Laravel yang unik:
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Seeder Database
Buat tabel database dan isi data awal (seperti akun pengguna default, kelas, guru, dll.):
```bash
php artisan migrate --seed
```

### 6. Hubungkan Folder Storage
Buat symbolic link dari folder storage agar foto siswa/avatar dapat diakses secara publik:
```bash
php artisan storage:link
```

### 7. Install Dependensi Frontend & Compile Assets
Untuk mengompilasi CSS dan JS menggunakan Vite:
```bash
npm install
npm run build
```
Jika Anda dalam mode pengembangan (development), Anda bisa menjalankan:
```bash
npm run dev
```

---

## 💬 Langkah 2: Instalasi & Menjalankan WhatsApp Broadcast Server

Aplikasi ini menggunakan Node.js server terpisah untuk menangani fungsi WhatsApp Web.

### 1. Masuk ke Folder Server WhatsApp
```bash
cd whatsapp-server
```

### 2. Install Dependensi Node.js
```bash
npm install
```

### 3. Jalankan Server WhatsApp
```bash
node index.js
```
Secara default, server ini akan berjalan pada `http://localhost:3000`.

> [!NOTE]
> Pastikan port `3000` tidak sedang digunakan oleh aplikasi lain. Saat pertama kali dijalankan, server WhatsApp akan menunggu koneksi dari aplikasi Laravel dan proses autentikasi (scan QR code).

---

## 🔗 Langkah 3: Integrasi Sistem & Autentikasi WhatsApp

Setelah kedua server berjalan (Laravel dan Node.js WhatsApp Server), lakukan integrasi melalui dashboard admin:

1.  Buka aplikasi di browser (misalnya: `http://localhost:8000` atau URL Laragon Anda).
2.  Login menggunakan akun superadmin/admin (detail akun ada di bawah).
3.  Buka menu **Whatsapp Broadcast** (biasanya terdapat di sidebar sebelah kiri).
4.  Pada formulir **Konfigurasi Whatsapp Broadcast**, isi data berikut:
    *   **Nomor Whatsapp**: Nomor WA yang akan digunakan sebagai pengirim (dengan kode negara, contoh: `628xxxxxxxxxx`).
    *   **Whatsapp URL Konfigurasi**: `http://localhost` (sesuai host WhatsApp Server Anda).
    *   **Whatsapp PORT Konfigurasi**: `3000` (sesuai port WhatsApp Server Anda).
5.  Klik tombol **Simpan**.
6.  Jika status koneksi menunjukkan **Menunggu Scan**, sebuah **QR Code** akan muncul di halaman dashboard atau halaman WhatsApp Broadcast.
7.  Buka WhatsApp di ponsel Anda, pilih **Perangkat Tertaut (Linked Devices)**, lalu scan QR Code tersebut.
8.  Setelah berhasil terscan, status akan berubah menjadi **Connected** dan siap digunakan untuk mengirim notifikasi absensi.

---

## 🔑 Akun Akses Default (Default Credentials)

Gunakan akun berikut untuk login pertama kali setelah melakukan seeding database:

| Peran (Role) | Email | Password |
| :--- | :--- | :--- |
| **Superadmin** | `akunsuperadmin@gmail.com` | `superadmin123` |
| **Admin** | `akunadmin@gmail.com` | `admin123` |

---

## 🛠️ Pemecahan Masalah (Troubleshooting)

### 1. Eror Puppeteer di OS Linux (VPS)
Jika Anda men-deploy WhatsApp server di Linux (VPS) dan mendapati eror terkait Chromium/Puppeteer, instal paket-paket dependensi Chromium berikut:
```bash
sudo apt update
sudo apt install -y gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev
```

### 2. QR Code Tidak Muncul atau Gagal Memuat
*   Pastikan servis WhatsApp server (`node index.js`) di folder `whatsapp-server` sedang aktif.
*   Periksa apakah port `3000` diblokir oleh firewall atau digunakan oleh aplikasi lain.
*   Periksa log di terminal tempat Anda menjalankan `node index.js` untuk melihat detail eror koneksi.

### 3. Gambar/Foto Siswa Tidak Tampil
*   Pastikan Anda sudah menjalankan perintah `php artisan storage:link`.
*   Periksa kembali pengaturan `APP_URL` di file `.env` apakah sudah sesuai dengan domain/URL yang Anda akses.
