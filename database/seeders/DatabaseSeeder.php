<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with development dummy data.
     */
    public function run(): void
    {
        // 1. Akun Admin Default
        $admin = User::firstOrCreate(
            ['email' => 'admin@portalberita.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Kategori Berita
        $kategoriTeknologi = Category::firstOrCreate(['slug' => 'teknologi'], ['name' => 'Teknologi']);
        $kategoriOlahraga = Category::firstOrCreate(['slug' => 'olahraga'], ['name' => 'Olahraga']);
        $kategoriBisnis = Category::firstOrCreate(['slug' => 'bisnis'], ['name' => 'Bisnis & Ekonomi']);
        $kategoriHiburan = Category::firstOrCreate(['slug' => 'hiburan'], ['name' => 'Hiburan & Budaya']);

        // 3. Contoh Berita Published & Draft
        $articles = [
            [
                'category_id' => $kategoriTeknologi->id,
                'title' => 'Perkembangan Kecerdasan Buatan (AI) Mengubah Lanskap Industri Modern',
                'slug' => 'perkembangan-kecerdasan-buatan-ai-mengubah-lanskap-industri-modern',
                'excerpt' => 'Teknologi AI kian terintegrasi di berbagai sektor mulai dari perbankan hingga layanan kesehatan untuk efisiensi tinggi.',
                'content' => "Kecerdasan Buatan (Artificial Intelligence) terus berkembang pesat dalam beberapa tahun terakhir. Inovasi model bahasa besar dan otomatisasi cerdas memungkinkan perusahaan mengoptimalkan proses operasional sehari-hari.\n\nPara pakar memprediksi bahwa adopsi AI di masa depan akan semakin fokus pada kolaborasi manusia dengan mesin untuk mendorong inovasi dan penciptaan lapangan kerja baru di bidang analitik data dan rekayasa prompt.",
                'status' => 'published',
                'published_at' => now()->subHours(2),
            ],
            [
                'category_id' => $kategoriBisnis->id,
                'title' => 'Pertumbuhan Ekonomi Digital Nasional Menunjukkan Tren Positif Tahun Ini',
                'slug' => 'pertumbuhan-ekonomi-digital-nasional-menunjukkan-tren-positif-tahun-ini',
                'excerpt' => 'Sektor e-commerce dan fintech menjadi pendorong utama pertumbuhan ekosistem bisnis digital lokal.',
                'content' => "Aktivitas transaksi digital di berbagai daerah tercatat mengalami peningkatan signifikan. Pelaku UMKM yang beralih ke platform online berkontribusi besar terhadap peningkatan perputaran modal.\n\nPemerintah dan otoritas terkait terus memperluas jaringan infrastruktur internet serta mempermudah akses pembiayaan digital bagi wirausahawan pemula.",
                'status' => 'published',
                'published_at' => now()->subHours(5),
            ],
            [
                'category_id' => $kategoriOlahraga->id,
                'title' => 'Timnas Raih Kemenangan Dramatis di Laga Persahabatan Internasional',
                'slug' => 'timnas-raih-kemenangan-dramatis-di-laga-persahabatan-internasional',
                'excerpt' => 'Gol di menit-menit akhir pertandingan memastikan kemenangan tim kebanggaan dengan skor 2-1.',
                'content' => "Pertandingan sengit tersaji di hadapan puluhan ribu suporter setia. Sejak peluit awal dibunyikan, kedua kubu saling melancarkan serangan terbuka.\n\nGol kemenangan di menit ke-89 memicu sorak-sorai penonton dan menjadi modal penting dalam persiapan turnamen tingkat benua mendatang.",
                'status' => 'published',
                'published_at' => now()->subDay(),
            ],
            [
                'category_id' => $kategoriTeknologi->id,
                'title' => 'Inovasi Baterai Generasi Baru Siap Gandakan Jarak Tempuh Kendaraan Listrik',
                'slug' => 'inovasi-baterai-generasi-baru-siap-gandakan-jarak-tempuh-kendaraan-listrik',
                'excerpt' => 'Riset teknologi solid-state battery menjanjikan pengisian daya lebih cepat dan kapasitas daya lebih aman.',
                'content' => "Kendaraan listrik di masa depan diprediksi akan semakin efisien seiring penemuan material kimia baru untuk sel baterai.\n\nDengan waktu pengisian daya di bawah 15 menit dan jangkauan tempuh hingga 800 km, hambatan utama adopsi kendaraan ramah lingkungan kini perlahan teratasi.",
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'category_id' => $kategoriHiburan->id,
                'title' => 'Festival Film Karya Sineas Muda Kembali Digelar Secara Luring',
                'slug' => 'festival-film-karya-sineas-muda-kembali-digelar-secara-luring',
                'excerpt' => 'Ratusan film pendek independen siap diputar menyuguhkan cerita orisinal dari berbagai pelosok negeri.',
                'content' => "Ajang tahunan perfilman independen kembali menghadirkan ruang apresiasi bagi talenta-talenta muda tanah air.\n\nSelain pemutaran film, rangkaian acara juga mencakup lokakarya penyutradaraan dan penulisan skenario bersama praktisi perfilman terkemuka.",
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'category_id' => $kategoriTeknologi->id,
                'title' => 'Draft Rencana Peluncuran Satelit Komunikasi Generasi Ketiga',
                'slug' => 'draft-rencana-peluncuran-satelit-komunikasi-generasi-ketiga',
                'excerpt' => 'Artikel ini masih berstatus draft dan berisi data awal perencanaan peluncuran satelit.',
                'content' => 'Dokumen ini masih dalam penyusunan internal redaksi dan tidak boleh tampil di halaman pengunjung umum.',
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($articles as $articleData) {
            Article::firstOrCreate(
                ['slug' => $articleData['slug']],
                $articleData
            );
        }
    }
}
