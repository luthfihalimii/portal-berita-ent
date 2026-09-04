@extends('layouts.app')

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a>
        <span>/</span>
        <a href="{{ route('news.index') }}" class="hover:text-blue-600">Berita</a>
        <span>/</span>
        <a href="{{ route('news.category', $article->category->slug) }}" class="hover:text-blue-600 font-medium text-blue-600">
            {{ $article->category->name }}
        </a>
    </nav>

    <!-- Header Artikel -->
    <header class="space-y-4">
        <div class="flex items-center space-x-3 text-xs text-gray-500">
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">
                {{ $article->category->name }}
            </span>
            <span>&bull;</span>
            <span>Dipublikasikan pada {{ $article->published_at ? $article->published_at->format('d F Y, H:i') : '' }} WIB</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight">
            {{ $article->title }}
        </h1>

        <p class="text-base sm:text-lg text-gray-600 font-medium leading-relaxed border-l-4 border-blue-600 pl-4 py-1 italic bg-gray-50 rounded-r-lg">
            {{ $article->excerpt }}
        </p>
    </header>

    <!-- Thumbnail Utama -->
    @if ($article->thumbnail)
        <div class="rounded-2xl overflow-hidden border border-gray-200 bg-gray-100 max-h-[500px]">
            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}"
                 class="w-full h-full object-cover">
        </div>
    @endif

    <!-- Konten Berita -->
    <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed space-y-4 pt-4 border-t border-gray-100">
        {!! nl2br(e($article->content)) !!}
    </div>

    <!-- Related Articles -->
    @if ($relatedArticles->isNotEmpty())
        <section class="pt-10 mt-12 border-t border-gray-200 space-y-6">
            <h2 class="text-xl font-bold text-gray-900">Berita Terkait Lainnya</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                @foreach ($relatedArticles as $related)
                    <a href="{{ route('news.show', $related->slug) }}" class="group block bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                        <div class="aspect-video bg-gray-100 overflow-hidden">
                            @if ($related->thumbnail)
                                <img src="{{ asset('storage/' . $related->thumbnail) }}" alt="{{ $related->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs font-semibold">
                                    CRUDBerita
                                </div>
                            @endif
                        </div>
                        <div class="p-4 space-y-1">
                            <span class="text-[10px] text-gray-400">
                                {{ $related->published_at ? $related->published_at->format('d M Y') : '' }}
                            </span>
                            <h3 class="text-xs font-bold text-gray-900 line-clamp-2 group-hover:text-blue-600 transition">
                                {{ $related->title }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</article>
@endsection
