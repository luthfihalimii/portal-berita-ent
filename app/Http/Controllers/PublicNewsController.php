<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
            ->take(6)
            ->get();

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

        $categories = Category::orderBy('name')->get();

        return view('articles.public-index', compact('articles', 'categories'));
    }

    /**
     * Display the specified published article.
     */
    public function show(string $slug): View
    {
        $article = Article::with('category')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $relatedArticles = Article::with('category')
            ->where('status', 'published')
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        // SEO meta untuk halaman detail artikel
        $title = $article->title . ' - HalimiNews';
        $metaDescription = $article->excerpt;
        $ogType = 'article';
        $ogImage = $article->thumbnail ? asset('storage/' . $article->thumbnail) : null;

        return view('articles.public-show', compact('article', 'relatedArticles', 'title', 'metaDescription', 'ogType', 'ogImage'));
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

        $categories = Category::orderBy('name')->get();

        return view('articles.public-category', compact('category', 'articles', 'categories'));
    }

    /**
     * Search published articles by keyword.
     */
    public function search(Request $request): View
    {
        $keyword = $request->input('q', '');

        $articles = Article::with('category')
            ->where('status', 'published')
            ->where(function ($query) use ($keyword) {
                $query->where('title', 'like', '%'.$keyword.'%')
                    ->orWhere('excerpt', 'like', '%'.$keyword.'%');
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('articles.public-search', compact('articles', 'keyword', 'categories'));
    }
}
