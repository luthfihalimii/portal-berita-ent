# PRD --- CRUDBerita

## 1. Ringkasan Project

**Nama Project:** CRUDBerita\
**Jenis:** Website Portal Berita dengan Dashboard Admin\
**Framework:** Laravel\
**Frontend:** Blade + Tailwind CSS\
**Database:** SQLite untuk development, dapat menggunakan
MySQL/PostgreSQL\
**Build Tool:** Vite

CRUDBerita adalah website portal berita sederhana yang memungkinkan
pengguna melihat berita yang telah dipublikasikan dan memungkinkan admin
mengelola kategori serta berita melalui operasi CRUD (Create, Read,
Update, Delete).

Project dibuat dari awal dan seluruh source code harus ditulis sendiri.
Jangan menggunakan CMS seperti WordPress dan jangan mengambil source
code dari project atau repository orang lain.

------------------------------------------------------------------------

## 2. Tujuan Project

Tujuan utama project:

1.  Membuat portal berita yang dapat digunakan melalui browser.
2.  Mengimplementasikan CRUD menggunakan Laravel.
3.  Mengimplementasikan database relational antara kategori dan berita.
4.  Membuat dashboard admin untuk mengelola konten.
5.  Menerapkan validasi input dan autentikasi admin.
6.  Membuat kode yang mudah dipahami dan dijelaskan saat proses
    validasi.
7.  Menghasilkan repository GitHub public yang memiliki dokumentasi
    lengkap.

------------------------------------------------------------------------

## 3. Aturan dan Batasan

Project wajib mengikuti aturan berikut:

-   Project harus dibuat dari awal.
-   Tidak boleh menggunakan project lama.
-   Tidak boleh mengambil source code orang lain dari sumber mana pun.
-   Tidak boleh menggunakan CMS.
-   Framework dan tools AI/agentic coding diperbolehkan.
-   Developer wajib memahami seluruh kode yang dibuat.
-   Kode harus sederhana, readable, dan mudah dijelaskan.
-   Jangan menambahkan fitur kompleks yang tidak diperlukan untuk
    kebutuhan tugas.

------------------------------------------------------------------------

## 4. Target User

### 4.1 Pengunjung

Pengunjung adalah pengguna umum yang hanya membutuhkan informasi berita.

Pengunjung dapat:

-   Melihat daftar berita.
-   Melihat detail berita.
-   Melihat berita berdasarkan kategori.
-   Mencari berita.

### 4.2 Admin

Admin adalah pengguna yang bertanggung jawab mengelola konten portal
berita.

Admin dapat:

-   Login.
-   Melihat dashboard.
-   Membuat berita.
-   Melihat berita.
-   Mengubah berita.
-   Menghapus berita.
-   Membuat kategori.
-   Mengubah kategori.
-   Menghapus kategori.
-   Mengatur status berita.

------------------------------------------------------------------------

# 5. Fitur Utama

## 5.1 Halaman Beranda

Menampilkan:

-   Nama/logo portal berita.
-   Navigasi kategori.
-   Berita terbaru.
-   Berita yang telah dipublikasikan.
-   Thumbnail berita.
-   Judul berita.
-   Ringkasan berita.
-   Tanggal publikasi.

------------------------------------------------------------------------

## 5.2 Daftar Berita

Halaman untuk menampilkan seluruh berita yang berstatus `published`.

Fitur:

-   Pagination.
-   Thumbnail.
-   Judul.
-   Kategori.
-   Tanggal publikasi.
-   Link menuju detail berita.

Berita dengan status `draft` tidak boleh ditampilkan kepada pengunjung
umum.

------------------------------------------------------------------------

## 5.3 Detail Berita

Menampilkan:

-   Judul.
-   Thumbnail.
-   Kategori.
-   Isi berita.
-   Tanggal publikasi.

URL menggunakan slug agar lebih mudah dibaca.

Contoh:

`/berita/perkembangan-teknologi-ai`

------------------------------------------------------------------------

## 5.4 Pencarian Berita

Pengunjung dapat mencari berita berdasarkan judul.

Contoh:

``` text
/search?q=teknologi
```

Hasil pencarian hanya menampilkan berita yang telah dipublikasikan.

------------------------------------------------------------------------

## 5.5 Filter Kategori

Pengunjung dapat memilih kategori tertentu untuk melihat berita yang
sesuai.

Contoh:

``` text
/berita/kategori/teknologi
```

------------------------------------------------------------------------

# 6. Dashboard Admin

Dashboard hanya dapat diakses oleh pengguna yang telah login.

Dashboard menampilkan informasi sederhana seperti:

-   Jumlah berita.
-   Jumlah berita published.
-   Jumlah berita draft.
-   Jumlah kategori.

Tidak perlu membuat sistem analytics yang kompleks.

------------------------------------------------------------------------

# 7. CRUD Kategori

Admin dapat melakukan:

### Create

Membuat kategori baru.

Field:

-   `name`
-   `slug`

### Read

Melihat daftar kategori.

### Update

Mengubah nama atau slug kategori.

### Delete

Menghapus kategori.

Sistem harus menangani hubungan kategori dengan berita secara konsisten.

------------------------------------------------------------------------

# 8. CRUD Berita

Admin dapat melakukan:

### Create

Membuat berita baru.

Field:

-   `category_id`
-   `title`
-   `slug`
-   `excerpt`
-   `content`
-   `thumbnail`
-   `status`
-   `published_at`

### Read

Melihat seluruh berita di dashboard admin.

### Update

Mengubah data berita.

### Delete

Menghapus berita.

------------------------------------------------------------------------

# 9. Status Berita

Berita memiliki dua status:

``` text
draft
published
```

Default status:

``` text
draft
```

Aturan:

-   `draft` tidak muncul di halaman publik.
-   `published` dapat muncul di halaman publik.
-   `published_at` digunakan untuk menyimpan waktu publikasi.
-   Berita yang belum dipublikasikan dapat memiliki
    `published_at = NULL`.

------------------------------------------------------------------------

# 10. Struktur Database

## 10.1 Users

Digunakan untuk autentikasi admin.

Field minimal:

``` text
id
name
email
password
created_at
updated_at
```

------------------------------------------------------------------------

## 10.2 Categories

Field:

``` text
id
name
slug
created_at
updated_at
```

Constraint:

-   `slug` harus unique.

------------------------------------------------------------------------

## 10.3 Articles

Field:

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

Constraint:

-   `category_id` merupakan foreign key menuju `categories.id`.
-   `slug` harus unique.
-   `status` hanya boleh `draft` atau `published`.
-   `thumbnail` boleh kosong.
-   `published_at` boleh kosong.

------------------------------------------------------------------------

# 11. Relasi Database

Relasi utama:

``` text
Category
   |
   | 1
   |
   | N
   v
Article
```

Implementasi Laravel:

``` text
Category hasMany Articles
Article belongsTo Category
```

Contoh:

Satu kategori `Teknologi` dapat memiliki banyak artikel.

Satu artikel hanya memiliki satu kategori.

------------------------------------------------------------------------

# 12. Struktur Route

Gunakan route yang sederhana dan RESTful.

Contoh:

``` text
GET    /                         Homepage
GET    /berita                   Daftar berita
GET    /berita/{slug}            Detail berita
GET    /berita/kategori/{slug}   Berita berdasarkan kategori
GET    /search                   Pencarian
```

Admin:

``` text
GET       /admin
GET       /admin/articles
GET       /admin/articles/create
POST      /admin/articles
GET       /admin/articles/{article}/edit
PUT/PATCH /admin/articles/{article}
DELETE    /admin/articles/{article}

GET       /admin/categories
GET       /admin/categories/create
POST      /admin/categories
GET       /admin/categories/{category}/edit
PUT/PATCH /admin/categories/{category}
DELETE    /admin/categories/{category}
```

Route admin harus dilindungi middleware authentication.

------------------------------------------------------------------------

# 13. Arsitektur Aplikasi

Gunakan pola sederhana Laravel:

``` text
Browser
   ↓
Route
   ↓
Controller
   ↓
Validation
   ↓
Model / Eloquent
   ↓
Database
   ↓
Controller
   ↓
Blade View
   ↓
Browser
```

Contoh alur membuat berita:

``` text
Admin mengisi form
        ↓
POST /admin/articles
        ↓
ArticleController@store
        ↓
Validasi input
        ↓
Simpan menggunakan Eloquent
        ↓
Database
        ↓
Redirect ke daftar berita
```

------------------------------------------------------------------------

# 14. Validasi

Semua input dari user harus divalidasi.

Contoh aturan berita:

``` text
title:
- required
- string
- maksimal panjang yang wajar

slug:
- required
- unique
- format string

excerpt:
- required

content:
- required

category_id:
- required
- harus merupakan category yang tersedia

status:
- required
- hanya draft atau published
```

Validasi harus dilakukan di server-side.

Jangan hanya mengandalkan validasi HTML/JavaScript.

------------------------------------------------------------------------

# 15. Keamanan

Implementasikan keamanan dasar Laravel:

-   Authentication untuk halaman admin.
-   Authorization menggunakan middleware.
-   CSRF protection pada form.
-   Server-side validation.
-   Password disimpan menggunakan hashing Laravel.
-   Jangan menyimpan credential/database password di repository.
-   Gunakan `.env` untuk konfigurasi sensitif.
-   Jangan commit `.env`.
-   Gunakan mass assignment protection pada model.
-   Jangan menampilkan error sensitif pada production.
-   Pastikan draft tidak dapat diakses dari halaman publik.

------------------------------------------------------------------------

# 16. Upload Thumbnail

Admin dapat mengupload thumbnail berita.

Ketentuan:

-   Hanya format gambar yang diperbolehkan.
-   Validasi ukuran file.
-   Nama file tidak boleh digunakan sebagai sumber kepercayaan.
-   File disimpan menggunakan mekanisme storage Laravel.
-   Database hanya menyimpan path/lokasi file.

Contoh alur:

``` text
Upload gambar
     ↓
Validasi
     ↓
Laravel Storage
     ↓
Simpan path ke database
```

------------------------------------------------------------------------

# 17. UI/UX

Desain harus:

-   Responsive.
-   Sederhana.
-   Bersih.
-   Mudah digunakan.
-   Tidak terlalu banyak animasi.
-   Fokus pada konten berita.

Gunakan Tailwind CSS.

### Public

Gaya visual:

``` text
Modern
Clean
News / Editorial
Responsive
```

### Admin

Gaya visual:

``` text
Simple dashboard
Sidebar/navigation
Data table
Form CRUD
```

Tidak perlu membuat desain yang terlalu kompleks.

------------------------------------------------------------------------

# 18. Model Laravel

Minimal model:

``` text
User
Category
Article
```

Relasi:

``` php
Category::class
    -> hasMany(Article::class)

Article::class
    -> belongsTo(Category::class)
```

Gunakan Eloquent ORM untuk interaksi dengan database.

------------------------------------------------------------------------

# 19. Seeder

Buat seeder untuk menyediakan data awal development.

Minimal:

-   1 akun admin.
-   3--5 kategori.
-   5--10 berita contoh.

Data seeder harus berupa data dummy buatan sendiri.

Jangan menggunakan data hasil scraping atau menyalin artikel orang lain.

------------------------------------------------------------------------

# 20. Testing Minimum

Pastikan pengujian manual mencakup:

### Public

-   Homepage dapat dibuka.
-   Daftar berita dapat dibuka.
-   Detail berita dapat dibuka.
-   Search berjalan.
-   Filter kategori berjalan.
-   Draft tidak muncul di halaman publik.

### Admin

-   Login berhasil.
-   User yang belum login tidak dapat mengakses dashboard.
-   Admin dapat membuat kategori.
-   Admin dapat mengubah kategori.
-   Admin dapat menghapus kategori.
-   Admin dapat membuat berita.
-   Admin dapat mengubah berita.
-   Admin dapat menghapus berita.
-   Validasi form bekerja.
-   Upload thumbnail bekerja.

------------------------------------------------------------------------

# 21. Struktur Folder yang Diharapkan

Gunakan struktur standar Laravel.

Contoh bagian penting:

``` text
app/
├── Http/
│   └── Controllers/
│       ├── ArticleController.php
│       ├── CategoryController.php
│       └── DashboardController.php
│
└── Models/
    ├── Article.php
    └── Category.php

database/
├── migrations/
└── seeders/

resources/
└── views/
    ├── layouts/
    ├── articles/
    ├── categories/
    ├── admin/
    └── home.blade.php

routes/
└── web.php

public/
└── ...

README.md
```

Jangan membuat arsitektur yang terlalu kompleks untuk kebutuhan project.

------------------------------------------------------------------------

# 22. Konvensi Coding

Gunakan kode yang:

-   Readable.
-   Konsisten.
-   Mengikuti konvensi Laravel.
-   Menggunakan nama variable yang jelas.
-   Tidak membuat fungsi terlalu panjang.
-   Tidak menggunakan logic yang tidak diperlukan.
-   Tidak menggunakan hardcoded credential.
-   Memisahkan logic sesuai tanggung jawabnya.

Prioritas:

``` text
Understandable > Complex
Simple > Over-engineered
Secure > Fast to build
```

------------------------------------------------------------------------

# 23. Git Workflow

Gunakan Git sejak awal.

Contoh commit:

``` text
chore: initialize laravel project
feat: add category migration and model
feat: add article migration and model
feat: add category crud
feat: add article crud
feat: add admin authentication
feat: add public news pages
feat: add article search
fix: validate article input
docs: add project documentation
```

Hindari commit seperti:

``` text
update
fix
asdf
final
final2
final-banget
final-banget-revisi
```

Karena itu adalah jalan menuju neraka Git.

------------------------------------------------------------------------

# 24. README.md

README wajib menjelaskan:

1.  Nama project.
2.  Deskripsi project.
3.  Fitur.
4.  Tech stack.
5.  Requirement.
6.  Tahapan instalasi.
7.  Konfigurasi environment.
8.  Konfigurasi database.
9.  Cara menjalankan migration.
10. Cara menjalankan seeder.
11. Cara menjalankan aplikasi.
12. Akun admin dummy untuk development.
13. Struktur fitur utama.

Contoh instalasi:

``` bash
git clone <repository-url>
cd portal-berita

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate --seed

npm install
npm run build

php artisan serve
```

Sesuaikan command dengan konfigurasi project yang sebenarnya.

------------------------------------------------------------------------

# 25. Definition of Done

Project dianggap selesai apabila:

-   [ ] Laravel berhasil dijalankan.
-   [ ] Database berhasil dibuat.
-   [ ] Migration berhasil.
-   [ ] Model dan relasi berhasil.
-   [ ] Authentication admin berjalan.
-   [ ] Dashboard admin berjalan.
-   [ ] CRUD kategori berjalan.
-   [ ] CRUD berita berjalan.
-   [ ] Upload thumbnail berjalan.
-   [ ] Public news page berjalan.
-   [ ] Search berjalan.
-   [ ] Filter kategori berjalan.
-   [ ] Draft tidak muncul ke publik.
-   [ ] Validasi input berjalan.
-   [ ] Basic security diterapkan.
-   [ ] Seeder tersedia.
-   [ ] Project dapat dijalankan dari repository baru.
-   [ ] README.md lengkap.
-   [ ] Repository GitHub bersifat public.
-   [ ] Developer memahami alur dan kode project.

------------------------------------------------------------------------

# 26. Prioritas Implementasi

Implementasikan fitur berdasarkan prioritas berikut:

### P0 --- Wajib

-   Laravel setup
-   Database
-   Authentication
-   Category CRUD
-   Article CRUD
-   Public article list
-   Article detail
-   Validation
-   README
-   GitHub repository

### P1 --- Penting

-   Search
-   Category filtering
-   Thumbnail upload
-   Dashboard statistics
-   Seeder

### P2 --- Opsional

-   Pagination yang lebih advanced
-   Rich text editor
-   Dark mode
-   Bookmark
-   Komentar
-   Analytics

Jangan mengimplementasikan P2 apabila mengganggu penyelesaian fitur
wajib.

------------------------------------------------------------------------

# 27. Prinsip Pengembangan dengan AI

AI coding assistant diperbolehkan untuk membantu proses development,
tetapi developer tetap bertanggung jawab memahami kode.

AI tidak boleh digunakan untuk:

-   Mengambil source code project orang lain.
-   Menyalin repository orang lain.
-   Membuat developer tidak memahami hasil kode.

Setiap kode yang dihasilkan AI harus:

1.  Dibaca.
2.  Dipahami.
3.  Diuji.
4.  Dapat dijelaskan kembali oleh developer.

Jika terdapat bug, developer harus memahami penyebab dan solusi bug
tersebut.

------------------------------------------------------------------------

# 28. Fokus Validasi

Project harus mudah dijelaskan saat validasi.

Developer minimal harus memahami:

-   Apa fungsi Laravel?
-   Apa fungsi route?
-   Apa fungsi controller?
-   Apa fungsi model?
-   Apa itu Eloquent?
-   Apa itu migration?
-   Apa itu middleware?
-   Apa itu authentication?
-   Apa itu validation?
-   Apa itu foreign key?
-   Apa itu relationship `hasMany` dan `belongsTo`?
-   Bagaimana data dari form masuk ke database?
-   Bagaimana data database ditampilkan ke Blade?
-   Mengapa draft tidak ditampilkan ke publik?
-   Bagaimana upload thumbnail bekerja?
-   Bagaimana CSRF protection bekerja?
-   Mengapa password harus di-hash?
-   Bagaimana cara menemukan dan memperbaiki bug sederhana?

------------------------------------------------------------------------

# 29. Catatan untuk AI Coding Agent

Saat mengerjakan project ini:

1.  Jangan membuat seluruh project secara asal dalam satu langkah.
2.  Implementasikan fitur secara bertahap.
3.  Setelah setiap fitur selesai, jalankan test/check.
4.  Jangan mengubah file yang tidak diperlukan.
5.  Jangan menambahkan dependency tanpa alasan.
6.  Gunakan fitur bawaan Laravel jika sudah tersedia.
7.  Jangan menggunakan CMS.
8.  Jangan menggunakan source code eksternal.
9.  Jangan memasukkan secret ke source code.
10. Prioritaskan kode sederhana yang mudah dipahami.
11. Berikan penjelasan singkat untuk perubahan penting.
12. Pastikan seluruh fitur dapat dijelaskan oleh developer saat
    validasi.

------------------------------------------------------------------------

# 30. Final Goal

Hasil akhir yang diharapkan:

``` text
                    ┌─────────────────┐
                    │    Pengunjung   │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │  Portal Berita  │
                    └────────┬────────┘
                             │
                ┌────────────┴────────────┐
                ▼                         ▼
        Daftar Berita              Detail Berita
                │
                ▼
           Kategori/Search


                    ┌─────────────────┐
                    │      Admin      │
                    └────────┬────────┘
                             │
                             ▼
                    ┌─────────────────┐
                    │ Admin Dashboard │
                    └────────┬────────┘
                             │
                    ┌────────┴────────┐
                    ▼                 ▼
              CRUD Berita      CRUD Kategori
                    │                 │
                    └────────┬────────┘
                             ▼
                         Database
```

Project harus tetap sederhana, aman, dan mudah
dipresentasikan/dijelaskan saat proses validasi.
