<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Services\ThumbnailGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Mews\Purifier\Facades\Purifier;

class ArticleController extends Controller
{
    /**
     * Display a listing of the articles.
     */
    public function index(Request $request): View
    {
        $query = Article::with('category')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        $articles = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.articles.index', compact('articles', 'categories'));
    }

    /**
     * Show the form for creating a new article.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.articles.create', compact('categories'));
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->filled('slug') && $request->filled('title')) {
            $request->merge(['slug' => Str::slug($request->title)]);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:articles,slug'],
            'author_name' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $validated['content'] = Purifier::clean($validated['content'], 'article');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = app(ThumbnailGenerator::class)->store($request->file('thumbnail'));
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        } else {
            $validated['published_at'] = null;
        }

        Article::create($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Berita berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        if (! $request->filled('slug') && $request->filled('title')) {
            $request->merge(['slug' => Str::slug($request->title)]);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($article->id)],
            'author_name' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['required', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $validated['content'] = Purifier::clean($validated['content'], 'article');

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) {
                app(ThumbnailGenerator::class)->delete($article->thumbnail);
            }

            $validated['thumbnail'] = app(ThumbnailGenerator::class)->store($request->file('thumbnail'));
        }

        if ($validated['status'] === 'published') {
            if (! $article->published_at) {
                $validated['published_at'] = now();
            }
        } else {
            $validated['published_at'] = null;
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Berita berhasil diperbarui.');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(Article $article): RedirectResponse
    {
        if ($article->thumbnail) {
            app(ThumbnailGenerator::class)->delete($article->thumbnail);
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Berita berhasil dihapus.');
    }

    /**
     * Handle image upload dari Trix rich text editor.
     *
     * Menyimpan gambar ke disk public dan mengembalikan URL-nya
     * agar bisa disematkan ke dalam konten artikel.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

        $path = $request->file('image')->store('article-images', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
