@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header Kategori -->
    <div class="border-b border-gray-200 pb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a>
                <span>/</span>
                <span>Kategori</span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center space-x-3">
                <span>Kategori:</span>
                <span class="text-blue-600">{{ $category->name }}</span>
            </h1>
        </div>
        <div>
            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                {{ $articles->total() }} Berita Ditemukan
            </span>
        </div>
    </div>

    <!-- Category Filter Bar -->
    @if ($categories->isNotEmpty())
        <div class="flex items-center space-x-2 overflow-x-auto pb-2 scrollbar-none">
            <a href="{{ route('news.index') }}" class="px-3.5 py-1.5 rounded-full text-xs font-medium bg-white border border-gray-200 text-gray-700 hover:border-blue-500 hover:text-blue-600 shrink-0 transition">
                Semua
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('news.category', $cat->slug) }}" 
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold shrink-0 transition {{ $cat->id === $category->id ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:border-blue-500 hover:text-blue-600' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    @endif

    <!-- Cards Grid -->
    @if ($articles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($articles as $article)
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
                    </a>

                    <div class="p-5 flex-grow flex flex-col justify-between space-y-3">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">
                                {{ $article->published_at ? $article->published_at->format('d M Y, H:i') : '' }}
                            </p>
                            <h2 class="text-lg font-bold text-gray-900 leading-snug hover:text-blue-600 transition">
                                <a href="{{ route('news.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            <p class="text-xs text-gray-500 line-clamp-3 mt-2 leading-relaxed">
                                {{ $article->excerpt }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <a href="{{ route('news.show', $article->slug) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                                Baca Selengkapnya &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $articles->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-500">
            Belum ada berita pada kategori ini.
        </div>
    @endif
</div>
@endsection
