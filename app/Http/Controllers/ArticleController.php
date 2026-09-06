<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleSlugRedirect;
use App\Models\Category;
use App\Services\ArticleContentImages;
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
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $validated['content'] = Purifier::clean($validated['content'], 'article');
        $validated['content'] = app(ArticleContentImages::class)->addResponsiveAttributes($validated['content']);

        // Auto-generate excerpt dari konten jika admin mengosongkannya
        $validated['excerpt'] = Article::generateExcerpt($validated['excerpt'], $validated['content']);

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
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status' => ['required', 'in:draft,published'],
        ]);

        $validated['content'] = Purifier::clean($validated['content'], 'article');
        $validated['content'] = app(ArticleContentImages::class)->addResponsiveAttributes($validated['content']);

        // Auto-generate excerpt dari konten jika admin mengosongkannya
        $validated['excerpt'] = Article::generateExcerpt($validated['excerpt'], $validated['content']);

        // Tangkap konten lama SEBELUM update untuk keperluan cleanup gambar inline
        $previousContent = $article->content;
        $previousSlug = $article->slug;

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

        // Catat slug lama agar URL lama bisa di-redirect 301 ke slug baru
        if ($previousSlug !== $article->slug) {
            $this->recordSlugRedirect($article, $previousSlug);
        }

        // Bersihkan gambar inline yang dihapus dari konten dan tidak dipakai artikel lain
        $this->cleanupOrphanedContentImages($article, $previousContent, $validated['content']);

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

        $contentImages = app(ArticleContentImages::class);
        $previousPaths = $contentImages->extractPaths($article->content);

        $article->delete();

        // Hapus gambar inline milik artikel ini yang tidak dipakai artikel lain
        $keepPaths = $contentImages->pathsUsedByOtherArticles($article->id);
        $contentImages->deleteOrphans($previousPaths, $keepPaths);

        return redirect()->route('admin.articles.index')->with('success', 'Berita berhasil dihapus.');
    }

    /**
     * Handle image upload dari Trix rich text editor.
     *
     * Menyimpan gambar ke disk public sekaligus membuat variant WebP
     * responsive, lalu mengembalikan URL-nya untuk disematkan ke konten.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
        ]);

        $path = app(ThumbnailGenerator::class)->storeAs(
            $request->file('image'),
            'article-images'
        );

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Hapus gambar inline yang ada di konten lama tapi sudah tidak ada di
     * konten baru, dan tidak dipakai oleh artikel lain.
     */
    protected function cleanupOrphanedContentImages(Article $article, ?string $previousContent, string $newContent): void
    {
        $contentImages = app(ArticleContentImages::class);

        $previousPaths = $contentImages->extractPaths($previousContent);
        $currentPaths = $contentImages->extractPaths($newContent);

        $removedPaths = array_diff($previousPaths, $currentPaths);

        if (empty($removedPaths)) {
            return;
        }

        $keepPaths = $contentImages->pathsUsedByOtherArticles($article->id);
        $contentImages->deleteOrphans($removedPaths, $keepPaths);
    }

    /**
     * Simpan slug lama ke tabel redirect agar URL lama bisa di-redirect.
     *
     * Jika slug baru saat ini tercatat sebagai slug lama milik artikel lain
     * (reuse slug), catatan tersebut dipindahkan ke artikel ini.
     */
    protected function recordSlugRedirect(Article $article, string $oldSlug): void
    {
        // Hindari duplikasi jika slug lama sudah tercatat
        ArticleSlugRedirect::updateOrCreate(
            ['old_slug' => $oldSlug],
            ['article_id' => $article->id]
        );

        // Jika slug baru sebelumnya tercatat sebagai redirect, hapus agar tidak loop
        ArticleSlugRedirect::where('old_slug', $article->slug)
            ->where('article_id', $article->id)
            ->delete();
    }
}
