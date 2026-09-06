<?php

namespace App\Console\Commands;

use App\Services\ThumbnailGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateThumbnailVariants extends Command
{
    protected $signature = 'thumbnails:generate';

    protected $description = 'Generate responsive WebP variants untuk semua thumbnail artikel yang sudah tersimpan';

    public function handle(ThumbnailGenerator $generator): int
    {
        $files = collect(Storage::disk('public')->files('thumbnails'))
            ->reject(fn ($file) => str_ends_with($file, '.webp')); // skip variant yang sudah ada

        if ($files->isEmpty()) {
            $this->info('Tidak ada thumbnail yang perlu diproses.');

            return self::SUCCESS;
        }

        $this->withProgressBar($files, function ($file) use ($generator) {
            $generator->generateVariants($file);
        });

        $this->newLine();
        $this->info("Selesai. {$files->count()} thumbnail diproses.");

        return self::SUCCESS;
    }
}
