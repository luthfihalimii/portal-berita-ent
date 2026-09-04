@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">
    <!-- Category Chips / Navigation Bar -->
    @if ($categories->isNotEmpty())
        <div class="flex items-center space-x-2 overflow-x-auto pb-2 scrollbar-none border-b border-gray-200">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 mr-2 shrink-0">Kategori:</span>
            <a href="{{ route('news.index') }}" 
               class="px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-900 text-white shrink-0 hover:bg-gray-800 transition">
                Semua Berita
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('news.category', $category->slug) }}" 
                   class="px-3 py-1.5 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700 hover:border-blue-500 hover:text-blue-600 shrink-0 transition">
                    {{ $category->name }} ({{ $category->articles_count }})
                </a>
            @endforeach
        </div>
    @endif

    <!-- Hero / Headline Article -->
    @if ($featuredArticle)
        <section class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                <div class="lg:col-span-7 h-64 lg:h-auto min-h-[320px] relative bg-gray-100 overflow-hidden">
                    @if ($featuredArticle->thumbnail)
                        <img src="{{ asset('storage/' . $featuredArticle->thumbnail) }}" alt="{{ $featuredArticle->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-600 to-indigo-800 text-white p-8">
                            <span class="text-3xl font-extrabold opacity-75">CRUDBerita Utama</span>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-600 text-white shadow">
                            Headline
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-5 p-6 sm:p-8 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 text-xs text-gray-500">
                            <a href="{{ route('news.category', $featuredArticle->category->slug) }}" 
                               class="font-semibold text-blue-600 hover:underline">
                                {{ $featuredArticle->category->name }}
                            </a>
                            <span>&bull;</span>
                            <span>{{ $featuredArticle->published_at ? $featuredArticle->published_at->format('d M Y, H:i') : '' }}</span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight hover:text-blue-600 transition">
                            <a href="{{ route('news.show', $featuredArticle->slug) }}">
                                {{ $featuredArticle->title }}
                            </a>
                        </h2>

                        <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">
                            {{ $featuredArticle->excerpt }}
                        </p>
                    </div>

                    <div class="pt-6 mt-6 border-t border-gray-100 flex items-center justify-between">
                        <a href="{{ route('news.show', $featuredArticle->slug) }}" 
                           class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition">
                            <span>Baca Selengkapnya</span>
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Berita Terbaru Grid -->
    <section class="space-y-6">
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h2 class="text-xl font-bold text-gray-900 flex items-center space-x-2">
                <span class="w-2.5 h-6 bg-blue-600 rounded-sm inline-block"></span>
                <span>Berita Terbaru</span>
            </h2>
            <a href="{{ route('news.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        @if ($latestArticles->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($latestArticles as $article)
                    <article class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
                        <a href="{{ route('news.show', $article->slug) }}" class="block aspect-video bg-gray-100 overflow-hidden relative group">
                            @if ($article->thumbnail)
                                <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 text-xs font-semibold">
                                    CRUDBerita
                                </div>
                            @endif
                            <span class="absolute top-2 left-2 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-white/90 text-gray-800 backdrop-blur-sm shadow-sm">
                                {{ $article->category->name }}
                            </span>
                        </a>

                        <div class="p-5 flex-grow flex flex-col justify-between space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 mb-1">
                                    {{ $article->published_at ? $article->published_at->format('d M Y') : '' }}
                                </p>
                                <h3 class="font-bold text-gray-900 leading-snug line-clamp-2 hover:text-blue-600 transition">
                                    <a href="{{ route('news.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                <p class="text-xs text-gray-500 line-clamp-2 mt-2 leading-relaxed">
                                    {{ $article->excerpt }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-gray-100">
                                <a href="{{ route('news.show', $article->slug) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                    Baca &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @elseif (! $featuredArticle)
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center space-y-4">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Belum Ada Berita yang Diterbitkan</h3>
                <p class="text-sm text-gray-500 max-w-md mx-auto">
                    Portal berita saat ini masih kosong. Admin dapat login untuk membuat dan mempublikasikan berita baru.
                </p>
                @auth
                    <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        + Buat Berita Sekarang
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition">
                        Login Admin untuk Menambah Berita
                    </a>
                @endauth
            </div>
        @endif
    </section>
</div>
@endsection
