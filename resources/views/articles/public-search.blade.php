@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header Pencarian -->
    <div class="border-b border-gray-200 pb-5">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Hasil Pencarian</h1>
        <p class="text-sm text-gray-500 mt-1">
            Menampilkan hasil untuk kata kunci: <span class="font-bold text-black">"{{ $keyword }}"</span> 
            ({{ $articles->total() }} berita ditemukan)
        </p>
    </div>

    <!-- Search input bar in page -->
    <div class="max-w-xl">
        <form action="{{ route('news.search') }}" method="GET" class="flex items-center space-x-2">
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Ketik kata kunci pencarian..." 
                   class="flex-grow px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition">
            <button type="submit" class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white rounded-xl text-sm font-bold transition">
                Cari
            </button>
        </form>
    </div>

    <!-- Cards Grid -->
    @if ($articles->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($articles as $article)
                <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col group">
                    <a href="{{ route('news.show', $article->slug) }}" class="block aspect-video bg-gray-100 overflow-hidden relative">
                        @if ($article->thumbnail)
                            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}"
                                 loading="lazy"
                                 srcset="{{ $article->thumbnail_srcset }}"
                                 sizes="(min-width: 1024px) 33vw, (min-width: 768px) 50vw, 100vw"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gray-900 text-white p-4 text-center">
                                <span class="text-[10px] font-bold tracking-widest uppercase text-gray-400">HalimiNews</span>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[11px] font-bold bg-white/95 text-black shadow-sm">
                            {{ $article->category->name }}
                        </span>
                    </a>

                    <div class="p-5 flex-grow flex flex-col justify-between space-y-3">
                        <div>
                            <p class="text-xs text-gray-400 font-medium mb-1.5">
                                {{ $article->published_for_humans }}
                            </p>
                            <h2 class="text-lg font-bold text-gray-900 leading-snug group-hover:text-gray-600 transition line-clamp-2">
                                <a href="{{ route('news.show', $article->slug) }}">
                                    {{ $article->title }}
                                </a>
                            </h2>
                            <p class="text-xs text-gray-500 line-clamp-3 mt-2 leading-relaxed">
                                {{ $article->excerpt }}
                            </p>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <a href="{{ route('news.show', $article->slug) }}" class="text-xs font-bold text-black hover:underline underline-offset-4">
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
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center space-y-3">
            <div class="w-14 h-14 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Tidak ada berita yang cocok</h3>
            <p class="text-sm text-gray-500">Coba gunakan kata kunci lain atau lihat seluruh berita yang tersedia.</p>
            <a href="{{ route('news.index') }}" class="inline-block text-sm font-bold text-black hover:underline underline-offset-4">
                Lihat Semua Berita &rarr;
            </a>
        </div>
    @endif
</div>
@endsection
