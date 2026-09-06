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
        $kategoriSains = Category::firstOrCreate(['slug' => 'sains'], ['name' => 'Sains & Riset']);
        $kategoriKesehatan = Category::firstOrCreate(['slug' => 'kesehatan'], ['name' => 'Kesehatan']);

        // 3. Contoh Berita Published & Draft
        $articles = [
            [
                'category_id' => $kategoriOlahraga->id,
                'title' => 'Will he retire? One more loss and Fury is finished!',
                'slug' => 'will-he-retire-one-more-loss-and-fury-is-finished',
                'author_name' => 'Adam Strong',
                'excerpt' => 'The Usyk vs. Fury fight is on the horizon, but will it be the last for the "Gypsy King"? Tyson Fury, who recently narrowly escaped defeat in his last fights, is now facing the toughest challenge of his career - a confrontation with the undefeated Oleksandr Usyk.',
                'content' => "Pertarungan unifikasi gelar kelas berat dunia antara Tyson Fury dan Oleksandr Usyk menjadi sorotan utama penggemar olahraga tinju di seluruh dunia. Laga ini diprediksi menjadi penentu warisan karier sang Gypsy King.\n\nBanyak pengamat menilai bahwa kekalahan dalam duel ini dapat menandai akhir dari era dominasi Fury di ring tinju profesional.",
                'status' => 'published',
                'published_at' => now()->subMinutes(30),
            ],
            [
                'category_id' => $kategoriSains->id,
                'title' => 'Astronomers discover new exoplanet in habitable zone',
                'slug' => 'astronomers-discover-new-exoplanet-in-habitable-zone',
                'author_name' => 'Mary Frost',
                'excerpt' => 'A team of international researchers has confirmed the detection of an Earth-sized exoplanet orbiting within the circumstellar habitable zone of a neighboring star.',
                'content' => 'Penemuan eksoplanet baru ini membuka wawasan segar bagi para astrofisikawan dalam pencarian jejak biosfer di luar tata surya. Instrumen teleskop luar angkasa canggih berhasil mendeteksi komposisi atmosfer yang menjanjikan.',
                'status' => 'published',
                'published_at' => now()->subHours(2),
            ],
            [
                'category_id' => $kategoriBisnis->id,
                'title' => 'Scientists have developed a new method of storing renewable energy',
                'slug' => 'scientists-have-developed-a-new-method-of-storing-renewable-energy',
                'author_name' => 'Lucas Ray',
                'excerpt' => 'Groundbreaking solid-electrolyte flow batteries offer high density energy storage with minimal degradation over thousands of cycles.',
                'content' => 'Solusi penyimpanan energi hijau terus mencatat terobosan baru. Pendekatan material ramah lingkungan ini berpotensi menekan biaya penyimpanan daya pada pembangkit listrik tenaga surya dan angin secara massal.',
                'status' => 'published',
                'published_at' => now()->subHours(4),
            ],
            [
                'category_id' => $kategoriKesehatan->id,
                'title' => 'New vaccine against a rare disease has been successfully tested',
                'slug' => 'new-vaccine-against-a-rare-disease-has-been-successfully-tested',
                'author_name' => 'Adam Strong',
                'excerpt' => 'Phase 3 clinical trials yield overwhelmingly positive immune response markers with no critical adverse events observed in trial cohorts.',
                'content' => 'Uji klinis tahap akhir menunjukkan efikasi tinggi vaksin generasi baru ini dalam melawan patogen langka yang selama ini minim opsi pengobatan. Persetujuan edar global ditargetkan rampung akhir tahun ini.',
                'status' => 'published',
                'published_at' => now()->subHours(6),
            ],
            [
                'category_id' => $kategoriTeknologi->id,
                'title' => 'Perkembangan Kecerdasan Buatan (AI) Mengubah Lanskap Industri Modern',
                'slug' => 'perkembangan-kecerdasan-buatan-ai-mengubah-lanskap-industri-modern',
                'author_name' => 'Samantha Hayes',
                'excerpt' => 'Teknologi AI kian terintegrasi di berbagai sektor mulai dari perbankan hingga layanan kesehatan untuk efisiensi tinggi.',
                'content' => "Kecerdasan Buatan (Artificial Intelligence) terus berkembang pesat dalam beberapa tahun terakhir. Inovasi model bahasa besar dan otomatisasi cerdas memungkinkan perusahaan mengoptimalkan proses operasional sehari-hari.\n\nPara pakar memprediksi bahwa adopsi AI di masa depan akan semakin fokus pada kolaborasi manusia dengan mesin.",
                'status' => 'published',
                'published_at' => now()->subHours(8),
            ],
            [
                'category_id' => $kategoriHiburan->id,
                'title' => 'Festival Film Karya Sineas Muda Kembali Digelar Secara Luring',
                'slug' => 'festival-film-karya-sineas-muda-kembali-digelar-secara-luring',
                'author_name' => 'Samantha Hayes',
                'excerpt' => 'Ratusan film pendek independen siap diputar menyuguhkan cerita orisinal dari berbagai pelosok negeri.',
                'content' => "Ajang tahunan perfilman independen kembali menghadirkan ruang apresiasi bagi talenta-talenta muda tanah air.\n\nSelain pemutaran film, rangkaian acara juga mencakup lokakarya penyutradaraan dan penulisan skenario bersama praktisi perfilman terkemuka.",
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
            [
                'category_id' => $kategoriTeknologi->id,
                'title' => 'Draft Rencana Peluncuran Satelit Komunikasi Generasi Ketiga',
                'slug' => 'draft-rencana-peluncuran-satelit-komunikasi-generasi-ketiga',
                'author_name' => 'Redaksi HalimiNews',
                'excerpt' => 'Artikel ini masih berstatus draft dan berisi data awal perencanaan peluncuran satelit.',
                'content' => 'Dokumen ini masih dalam penyusunan internal redaksi dan tidak boleh tampil di halaman pengunjung umum.',
                'status' => 'draft',
                'published_at' => null,
            ],
        ];

        foreach ($articles as $articleData) {
            Article::updateOrCreate(
                ['slug' => $articleData['slug']],
                $articleData
            );
        }
    }
}
