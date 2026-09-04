# CRUDBerita

Portal berita sederhana berbasis framework Laravel dengan dashboard admin untuk mengelola kategori serta artikel berita melalui operasi CRUD (Create, Read, Update, Delete).

Aplikasi ini memiliki dua area utama:
- **Portal Publik**: Halaman berita untuk pengunjung umum yang menampilkan berita terkini berstatus *published*, pencarian berita, serta filter berdasarkan kategori.
- **Dashboard Admin**: Area terproteksi autentikasi (dilengkapi proteksi captcha Cloudflare Turnstile) untuk mengelola data kategori, artikel berita, upload thumbnail, dan status publikasi (*draft* / *published*).

---

## Tahapan Instalasi

Pastikan sistem Anda telah terpasang **PHP 8.2+**, **Composer**, **MySQL Database Server**, **Bun**, dan **Git**.

### 1. Clone Repository
```bash
git clone <repository-url>
cd portal-berita
```

### 2. Install Dependensi Backend
```bash
composer install
```

### 3. Konfigurasi Environment
Salin template environment:
```bash
cp .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

Buka dan sesuaikan konfigurasi database MySQL pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_berita
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Install Dependensi Frontend
```bash
bun install
```

### 5. Buat Symlink Storage
Hubungkan direktori storage agar file thumbnail berita dapat diakses secara publik:
```bash
php artisan storage:link
```

### 6. Jalankan Migrasi dan Seeder Database
Jalankan migrasi tabel dan data awal (akun admin default, kategori, dan artikel dummy):
```bash
php artisan migrate --seed
```

> **Informasi Akun Admin Development:**
> - **Email:** `admin@portalberita.com`
> - **Password:** `password123`

---

## Tahapan Menjalankan

### 1. Kompilasi Asset Frontend
Build asset Tailwind CSS menggunakan Bun:
```bash
bun run build
```

*(Atau jalankan `bun run dev` jika sedang dalam mode development aktif)*

### 2. Jalankan Web Server Laravel
```bash
php artisan serve
```

### 3. Buka Aplikasi di Browser
- **Portal Berita Publik:** [http://127.0.0.1:8000](http://127.0.0.1:8000)
- **Halaman Login Admin:** [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login)
