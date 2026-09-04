# CRUDBerita

Portal berita sederhana berbasis Laravel yang menyediakan halaman publik
untuk membaca berita dan dashboard admin untuk mengelola kategori serta
artikel menggunakan operasi CRUD.

Project ini dibuat sebagai implementasi pembelajaran framework Laravel
dengan fokus pada struktur kode yang sederhana, readable, aman, dan
mudah dijelaskan saat proses validasi.

## Fitur

### Public

-   Beranda portal berita
-   Daftar berita
-   Detail berita
-   Pencarian berita
-   Filter berdasarkan kategori
-   Pagination
-   Hanya berita `published` yang ditampilkan kepada publik

### Admin

-   Login admin
-   Dashboard
-   Statistik sederhana
-   CRUD kategori
-   CRUD berita
-   Upload thumbnail berita
-   Status berita `draft` / `published`
-   Validasi form

## Tech Stack

  Teknologi      Kegunaan
  -------------- -----------------------------------------
  PHP            Bahasa pemrograman backend
  Laravel        Framework aplikasi web
  Blade          Template engine
  Tailwind CSS   Styling dan responsive UI
  Vite           Asset bundling dan frontend development
  Eloquent ORM   Interaksi dengan database
  MySQL          Database utama / development
  Git            Version control

## Requirement

Pastikan environment sudah memiliki:

-   PHP 8.3+
-   Composer
-   MySQL Database Server
-   Bun
-   Git

Untuk memastikan requirement tersedia:

``` bash
php -v
composer -V
mysql --version
bun -v
git --version
```

## Instalasi

### 1. Clone repository

``` bash
git clone <repository-url>
cd portal-berita
```

Ganti `<repository-url>` dengan URL repository GitHub project.

### 2. Install dependency PHP

``` bash
composer install
```

### 3. Buat file environment

Linux/macOS:

``` bash
cp .env.example .env
```

Windows:

``` powershell
copy .env.example .env
```

### 4. Generate application key

``` bash
php artisan key:generate
```

### 5. Konfigurasi database

Pastikan database MySQL sudah dibuat, kemudian sesuaikan konfigurasi pada file `.env`:

``` env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_berita
DB_USERNAME=root
DB_PASSWORD=
```

**Jangan commit file `.env` ke repository.**

### 6. Jalankan migration dan seeder

``` bash
php artisan migrate --seed
```

Seeder menyediakan data dummy untuk development, termasuk akun admin,
kategori, dan berita contoh.

### 7. Install dependency frontend

``` bash
bun install
```

### 8. Build asset

``` bash
bun run build
```

### 9. Jalankan aplikasi

``` bash
php artisan serve
```

Kemudian buka:

``` text
http://127.0.0.1:8000
```

## Development Mode

Untuk development, jalankan:

``` bash
composer run dev
```

Jika menjalankan proses frontend secara terpisah menggunakan Bun:

``` bash
bun run dev
```

## Akun Admin Development

Seeder menyediakan akun admin untuk kebutuhan development.

Gunakan credential yang ditampilkan atau ditentukan di file seeder
project.

**Catatan:** credential development hanya untuk local/testing. Jangan
menggunakan password tersebut untuk production.

## Struktur Fitur

``` text
Public
├── Home
├── Daftar Berita
├── Detail Berita
├── Search
└── Filter Kategori

Admin
├── Login
├── Dashboard
├── Kategori
│   ├── Create
│   ├── Read
│   ├── Update
│   └── Delete
└── Berita
    ├── Create
    ├── Read
    ├── Update
    └── Delete
```

## Struktur Database

Relasi utama:

``` text
Category
    │
    │ 1
    │
    │ N
    ▼
Article
```

### Categories

``` text
id
name
slug
created_at
updated_at
```

### Articles

``` text
id
category_id
title
slug
excerpt
content
thumbnail
status
published_at
created_at
updated_at
```

`articles.category_id` merupakan foreign key yang mengarah ke
`categories.id`.

Satu kategori dapat memiliki banyak artikel, sedangkan satu artikel
memiliki satu kategori.

## Alur Aplikasi

Secara sederhana, request diproses dengan alur:

``` text
Browser
   ↓
Route
   ↓
Middleware
   ↓
Controller
   ↓
Validation
   ↓
Eloquent Model
   ↓
Database
   ↓
Blade View
   ↓
Browser
```

Contoh ketika admin membuat berita:

``` text
Admin mengisi form
        ↓
POST /admin/articles
        ↓
ArticleController
        ↓
Validasi input
        ↓
Article Model / Eloquent
        ↓
Database
        ↓
Redirect
        ↓
Daftar berita
```

## Validasi

Input pengguna divalidasi di server menggunakan Laravel Validation.

Data berita minimal divalidasi untuk:

-   Kategori wajib tersedia.
-   Judul wajib diisi.
-   Slug wajib dan unik.
-   Excerpt wajib diisi.
-   Content wajib diisi.
-   Thumbnail harus berupa gambar jika diupload.
-   Status hanya `draft` atau `published`.

## Keamanan

Project menerapkan keamanan dasar Laravel:

-   Authentication untuk halaman admin.
-   Middleware untuk melindungi route admin.
-   CSRF protection pada form.
-   Server-side validation.
-   Password hashing.
-   Mass assignment protection.
-   Eloquent/query builder untuk mencegah SQL injection.
-   Blade escaping untuk mencegah XSS pada output normal.
-   Validasi upload thumbnail.
-   Secret dan credential disimpan melalui `.env`.
-   File `.env` tidak boleh dimasukkan ke repository.
-   Artikel draft tidak ditampilkan pada halaman publik.

## Testing

Jalankan automated test dengan:

``` bash
php artisan test
```

Selain automated test, lakukan pengujian manual terhadap:

### Public

-   [ ] Homepage dapat dibuka.
-   [ ] Daftar berita dapat dibuka.
-   [ ] Detail berita dapat dibuka.
-   [ ] Search bekerja.
-   [ ] Filter kategori bekerja.
-   [ ] Draft tidak muncul di halaman publik.

### Admin

-   [ ] Login berhasil.
-   [ ] Guest tidak dapat membuka dashboard.
-   [ ] Admin dapat membuat kategori.
-   [ ] Admin dapat mengubah kategori.
-   [ ] Admin dapat menghapus kategori.
-   [ ] Admin dapat membuat berita.
-   [ ] Admin dapat mengubah berita.
-   [ ] Admin dapat menghapus berita.
-   [ ] Validasi form bekerja.
-   [ ] Upload thumbnail bekerja.

## Git Workflow

Gunakan commit message yang jelas.

Contoh:

``` text
chore: initialize laravel project
feat: add category migration and model
feat: implement category crud
feat: implement article crud
feat: add admin authentication
feat: add public article pages
feat: add article search
fix: prevent draft articles from public pages
docs: add project documentation
```

Jangan commit file sensitif seperti:

``` text
.env
```

## Project Structure

Struktur utama project mengikuti konvensi Laravel:

``` text
portal-berita/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
│
├── database/
│   ├── migrations/
│   └── seeders/
│
├── resources/
│   └── views/
│       ├── layouts/
│       ├── articles/
│       ├── categories/
│       └── admin/
│
├── routes/
│   └── web.php
│
├── public/
├── storage/
├── tests/
├── .env.example
├── AGENTS.md
├── PRD.md
├── README.md
├── composer.json
└── package.json
```

## Development Principle

Project ini mengutamakan:

``` text
Simple
   +
Readable
   +
Secure
   +
Maintainable
```

Fitur tidak dibuat terlalu kompleks karena project akan melalui proses
validasi di mana developer harus dapat menjelaskan alur dan kode yang
digunakan.

AI coding assistant dapat digunakan untuk membantu development, tetapi
seluruh kode yang dihasilkan tetap harus dipahami, diperiksa, dan diuji
oleh developer.

## Scope

### Wajib

-   Laravel setup
-   Database
-   Migration
-   Model dan relationship
-   Authentication admin
-   Dashboard
-   CRUD kategori
-   CRUD berita
-   Public news page
-   Detail berita
-   Search
-   Filter kategori
-   Thumbnail upload
-   Validation
-   Basic security
-   Seeder
-   README
-   GitHub repository

### Di luar scope utama

Fitur berikut tidak diperlukan kecuali diminta secara khusus:

-   Komentar
-   Like
-   Bookmark
-   Newsletter
-   Notification
-   Real-time feature
-   Advanced analytics
-   Recommendation system
-   Microservices
-   GraphQL
-   WebSocket
-   Payment system

## License

Project ini dibuat untuk kebutuhan pembelajaran dan tugas pengembangan
aplikasi web.
