<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Hasilkan sitemap.xml dinamis untuk mesin pencari.
     *
     * Mencakup halaman utama, daftar berita, kategori, dan setiap
     * artikel yang sudah dipublikasikan.
     */
    public function index(): Response
    {
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->get(['slug', 'published_at', 'updated_at']);

        $categories = Category::has('articles', '>', 0)->get(['slug', 'updated_at']);

        $xml = view('sitemap', compact('articles', 'categories'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Hasilkan robots.txt dinamis agar URL sitemap mengikuti APP_URL.
     */
    public function robots(): Response
    {
        $sitemapUrl = route('sitemap');

        $content = "User-agent: *\n".
            "Disallow: /admin\n".
            "Disallow: /login\n\n".
            "Sitemap: {$sitemapUrl}\n";

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * Hasilkan RSS feed (RSS 2.0) berisi artikel terbaru yang dipublikasikan.
     */
    public function feed(): Response
    {
        $articles = Article::with('category')
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(20)
            ->get();

        $xml = view('feed', compact('articles'))->render();

        return response($xml, 200)->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
