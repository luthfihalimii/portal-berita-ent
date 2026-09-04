@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    <!-- Header Pencarian -->
    <div class="border-b border-gray-200 pb-5">
        <h1 class="text-3xl font-extrabold text-gray-900">Hasil Pencarian</h1>
        <p class="text-sm text-gray-600 mt-1">
            Menampilkan hasil untuk kata kunci: <span class="font-bold text-blue-600">"{{ $keyword }}"</span> 
            ({{ $articles->total() }} berita ditemukan)
        </p>
    </div>

    <!-- Search input bar in page -->
    <div class="max-w-xl">
        <form action="{{ route('news.search') }}" method="GET" class="flex items-center space-x-2">
            <input type="text" name="q" value="{{ $keyword }}" placeholder="Ketik kata kunci pencarian..." 
                   class="flex-grow px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition">
                Cari
            </button>
        </form>
    </div>

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
                        <span class="absolute top-2 left-2 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-white/90 text-gray-800 backdrop-blur-sm shadow-sm">
                            {{ $article->category->name }}
                        </span>
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
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center space-y-3">
            <h3 class="text-base font-bold text-gray-900">Tidak ada berita yang cocok</h3>
            <p class="text-sm text-gray-500">Coba gunakan kata kunci lain atau lihat seluruh berita yang tersedia.</p>
            <a href="{{ route('news.index') }}" class="inline-block text-sm font-semibold text-blue-600 hover:underline">
                Lihat Semua Berita &rarr;
            </a>
        </div>
    @endif
</div>
@endsection
