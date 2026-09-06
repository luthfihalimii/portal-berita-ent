<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleSlugRedirect;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PublicNewsController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function home(Request $request): View
    {
        $sort = $request->validate([
            'sort' => ['nullable', 'in:latest,oldest'],
        ])['sort'] ?? 'latest';
        $sortDirection = $sort === 'oldest' ? 'asc' : 'desc';

        $categories = Category::has('articles')->withCount(['articles' => function ($query) {
            $query->where('status', 'published');
        }])->orderBy('name')->get();

        $featuredArticle = Article::with('category')
            ->where('status', 'published')
            ->orderBy('published_at', $sortDirection)
            ->orderBy('id', $sortDirection)
            ->first();

        $secondaryArticles = Article::with('category')
            ->where('status', 'published')
            ->when($featuredArticle, function ($query) use ($featuredArticle) {
                $query->where('id', '!=', $featuredArticle->id);
            })
            ->orderBy('published_at', $sortDirection)
            ->orderBy('id', $sortDirection)
            ->take(4)
            ->get();

        $excludedIds = collect([$featuredArticle?->id])
            ->merge($secondaryArticles->pluck('id'))
            ->filter()
            ->all();

        $moreArticles = Article::with('category')
            ->where('status', 'published')
            ->whereNotIn('id', $excludedIds)
            ->orderBy('published_at', $sortDirection)
            ->orderBy('id', $sortDirection)
            ->paginate(6)
            ->withQueryString();

        // Kept for backward compatibility with current home.blade.php until Task 6
        $latestArticles = $secondaryArticles;

        return view('home', compact('categories', 'featuredArticle', 'secondaryArticles', 'moreArticles', 'latestArticles', 'sort'));
    }

    /**
     * Display all published articles with pagination.
     */
    public function index(): View
    {
        $articles = Article::with('category')
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::cached();

        return view('articles.public-index', compact('articles', 'categories'));
    }

    /**
     * Display the specified published article.
     *
     * Jika slug tidak ditemukan, cek apakah ini slug lama yang sudah
     * diganti — jika ya, redirect 301 (permanent) ke URL baru agar
     * ranking SEO dan tautan eksternal tidak rusak.
     */
    public function show(string $slug): Response
    {
        $article = Article::with('category')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->first();

        if (! $article) {
            $redirect = ArticleSlugRedirect::where('old_slug', $slug)->first();

            if ($redirect) {
                $target = Article::where('id', $redirect->article_id)
                    ->where('status', 'published')
                    ->first();

                if ($target) {
                    return redirect()->route('news.show', $target->slug, 301);
                }
            }

            abort(404);
        }

        $relatedArticles = Article::with('category')
            ->where('status', 'published')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $categories = Category::cached();

        // Meta SEO dibangun terpusat di model (Article::seo_meta)
        $view = view('articles.public-show', array_merge(
            compact('article', 'relatedArticles', 'categories'),
            $article->seo_meta
        ));

        // ETag stabil berbasis updated_at + id artikel agar cache browser
        // hanya invalid ketika artikel benar-benar berubah.
        $etag = '"article-'.$article->id.'-'.$article->updated_at->timestamp.'"';

        return response($view)->header('ETag', $etag);
    }

    /**
     * Display articles filtered by category.
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $articles = Article::with('category')
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        $categories = Category::cached();

        return view('articles.public-category', compact('category', 'articles', 'categories'));
    }

    /**
     * Search published articles by keyword.
     *
     * Mencari di judul, excerpt, dan konten, lalu mengurutkan berdasarkan
     * relevansi: kecocokan di judul lebih diutamakan daripada excerpt,
     * lalu konten. Hasil dengan relevansi sama diurutkan dari terbaru.
     */
    public function search(Request $request): View
    {
        $keyword = trim($request->input('q', ''));

        $articles = Article::with('category')
            ->where('status', 'published')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $like = '%'.$keyword.'%';

                $query->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('content', 'like', $like);
                })
                // Skor relevansi: judul=3, excerpt=2, konten=1
                    ->orderByRaw(
                        '(CASE
                        WHEN title LIKE ? THEN 3
                        WHEN excerpt LIKE ? THEN 2
                        ELSE 1
                    END) DESC',
                        [$like, $like]
                    );
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::cached();

        return view('articles.public-search', compact('articles', 'keyword', 'categories'));
    }
}
