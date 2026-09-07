# CampusHub - Portal Belajar & Manajemen Tugas Mahasiswa

**CampusHub** adalah website portal berbasis PHP + MySQL/MariaDB untuk mahasiswa sebagai platform pembelajaran dan manajemen tugas akademik.

## 📋 Tentang Project

Website ini dibangun dengan arsitektur PHP modular, menyediakan fitur lengkap untuk:
- Sistem autentikasi (login/register/lupa password)
- Manajemen mata kuliah
- Sistem tugas dan pembagian prioritas
- Library materi belajar
- Sistem catatan mahasiswa
- Kalender akademik
- Profil dan settings pengguna
- Panel admin untuk manajemen data

## 🚀 Fitur Utama

### Untuk Mahasiswa
- **Auth System**: Login/Register dengan email/NIM, lupa password
- **Dashboard**: Widget ringkasan kursus, tugas, materi, progress
- **Mata Kuliah**: Lihat daftar mata kuliah dengan detail
- **Tugas**: Tracking deadline, prioritas, status (pending/in_progress/completed/late)
- **Materi**: Download file PDF, video, atau akses link external
- **Catatan**: Buat dan kelola catatan per mata kuliah, favoritkan
- **Kalender**: Lihat acara akademik dan deadline
- **Profile**: Lihat dan edit informasi akun, ubah password
- **Settings**: Switch tema light/dark mode, preferensi akun

### Untuk Admin
- **Kelola Pengguna**: Lihat, tambah, edit, hapus user
- **Kelola Mata Kuliah**: CRUD mata kuliah beserta detail
- **Kelola Tugas**: CRUD tugas dengan filter course/prioritas/status
- **Kelola Materi**: CRUD materi dengan upload file
- **Kelola Catatan**: CRUD catatan mahasiswa
- **Kelola Kalender**: CRUD acara akademik

### Fitur Teknis
- 🔒 **Keamanan**: PDO Prepared Statements, password_hash (bcrypt), validasi input
- 🌙 **Tema**: Light/Dark mode dengan localStorage persistence
- 📱 **Responsive**: Mendukung lebar 320px - 1440px+
- ⚡ **Performance**: Query optimized dengan indexing
- 📦 **Modular**: Struktur kode terpisah, tidak satu file besar
- 🚫 **CSRF Protection**: Basic form protection

## 🛠️ Teknologi yang Digunakan

- **PHP 8.x** - Bahasa pemrograman
- **MySQL / MariaDB** - Database
- **PDO** - Database abstraction layer
- **CSS Custom** - Gaya visual tanpa Bootstrap
- **Google Fonts** - Font Inter untuk typography
- **Boxicons** - Ikon bibliotek
- **localStorage** - Simpan preferensi user

## 📁 Struktur Project

```
campushub/
├── config/
│   ├── database.php     # Konfigurasi koneksi PDO
│
├── includes/
│   ├── auth.php         # Fungsi auth (login, logout, session)
│   ├── header.php       # Bagian atas halaman (navbar, CSS)
│   ├── sidebar.php      # Sidebar navigasi (desktop)
│   ├── toast.php        # Sistem notifikasi toast
│   ├── theme.php        # Light/Dark mode toggle
│   └── functions.php    # Fungsi bantuan
│
├── dashboard/           # Halaman utama setelah login
├── courses/             # Manajemen mata kuliah
├── assignments/         # Sistem tugas
├── materials/           # Library materi belajar
├── notes/               # Sistem catatan mahasiswa
├── calendar/            # Kalender akademik
├── profile/             # Profil pengguna
├── settings/            # Pengaturan akun
├── admin/               # Panel administrasi
│   ├── users/           # Manajemen pengguna
│   ├── courses/         # Manajemen mata kuliah
│   ├── assignments/     # Manajemen tugas
│   ├── materials/       # Manajemen materi
│   ├── notes/           # Manajemen catatan
│   └── calendar/        # Manajemen kalender
├── assets/
│   ├── css/             # File gaya kustom
│   ├── js/              # File JavaScript
│   └── uploads/         # Upload folder materi
├── database/
│   ├── schema.sql       # Skema database tabel
│   └── seed.php         # Script seeding data sample
└── README.md            # Documentation ini
```

## 🏃‍♂️ Cara Menjalankan

### Prasyarat
- PHP 8.x atau versi baru
- MySQL/MariaDB server
- Web server (Apache, Nginx, atau built-in PHP server)

### Instalasi

1. **Clone atau ekstrak project**:
   ```bash
   git clone [repository-url]
   cd campushub
   ```

2. **Setup database**:
   - Buat database bernama `campushub`
   - Import file `database/schema.sql` ke MySQL/MariaDB
   - Atau jalankan `database/seed.php` untuk mengisi data sample

3. **Konfigurasi koneksi**:
   - Buka `config/database.php`
   - Pastikan setting socket/path MySQL sesuai environment Anda
   - Default socket: `/home/rrsec/mysql-data/mysql.sock`

4. **Jalankan aplikasi**:
   - Menggunakan built-in PHP server:
     ```bash
     php -S localhost:8000 -t /path/to/campushub
     ```
   - Atau konfigurasikan dengan Apache/Nginx

5. **Akses aplikasi**:
   - Buka `http://localhost:8000` atau sesuai konfigurasi web server
   - Akun default: Admin (email: admin@campushub.id, password: admin123)
   - Atau registrasi akun mahasiswa baru

## 👤 Akun Default

| Role | Email | Password | NIM |
|------|-------|----------|-----|
| Admin | admin@campushub.id | admin123 | 0000001 |
| Mahasiswa | budi@university.id | password123 | 210101 |

## 📦 Sample Data

Setelah menjalankan `seed.php`, database akan terisi dengan:
- 1 Admin + 5 Mahasiswa
- 5 Mata Kuliah
- 5 Tugas dengan deadline berbeda
- 5 Materi (PDF + Video)
- 3 Catatan mahasiswa
- 3 Acara Kalender
- 3 Notifikasi

Data sample dapat digunakan untuk development, testing, atau demonstrasi fitur.

## 🔒 Keamanan

- Password tidak pernah disimpan dalam format plaintext
- Semua query gunakan PDO Prepared Statements
- Validasi input di sisi server (PHP)
- htmlspecialchars() untuk output ke HTML
- Session fixation protection dengan session_regenerate_id()
- File upload dengan validasi tipe MIME dan ukuran

## 🛠️ Development

### Menambah Fitur Baru
1. Buat file PHP di direktori yang sesuai (contoh: `courses-add.php`)
2. Tambahkan tabel ke database jika butuh data baru (lihat `database/schema.sql`)
3. Update navigasi di `includes/sidebar.php` jika perlu
4. Tambahkan CSS di `assets/css/global.css` jikabutuh gaya baru

### Menambah Mata Kuliah Baru (via Admin)
1. Login sebagai admin
2. Menuju `/admin/courses.php`
3. Klik "Tambah Mata Kuliah"
4. Isi form dan simpan

## 📜 Lisensi

Proyek ini dibuat untuk tujuan edukasi dan dapat digunakan secara gratis dengan mencantumkan credit ke "Created by Risky Manuel Tamba" di footer.

## 👨‍💻 Author

**Risky Manuel Tamba** - Developer & Desainer
- Email: risky@campushub.id (fiktif)
- GitHub: [@ManuelKy08](https://github.com/ManuelKy08)

---

*CampusHub - Dibuat dengan ❤️ menggunakan PHP dan MySQL*