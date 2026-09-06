@extends('layouts.app')

@section('content')
<article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-black transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('news.index') }}" class="hover:text-black transition">Berita</a>
        <span>/</span>
        <a href="{{ route('news.category', $article->category->slug) }}" class="hover:text-black transition font-semibold text-black">
            {{ $article->category->name }}
        </a>
    </nav>

    <!-- Header Artikel -->
    <header class="space-y-5">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-gray-500">
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-black text-white">
                {{ $article->category->name }}
            </span>
            <span>&bull;</span>
            <span>{{ $article->published_for_humans }}</span>
        </div>

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 leading-[1.15] tracking-tight">
            {{ $article->title }}
        </h1>

        <!-- Author Row -->
        <div class="flex items-center text-sm">
            @php
                $aWords = explode(' ', trim($article->author_name));
                $aInitials = count($aWords) >= 2 
                    ? strtoupper(substr($aWords[0], 0, 1) . substr($aWords[1], 0, 1))
                    : strtoupper(substr($article->author_name, 0, 2));
            @endphp
            <div class="w-9 h-9 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-xs shrink-0" title="{{ $article->author_name }}">
                {{ $aInitials }}
            </div>
            <div class="ml-3">
                <p class="font-semibold text-gray-900 text-sm">{{ $article->author_name }}</p>
                <p class="text-xs text-gray-400">Penulis HalimiNews</p>
            </div>
        </div>

        <p class="text-base sm:text-lg text-gray-600 font-medium leading-relaxed border-l-4 border-black pl-4 py-1 bg-gray-50 rounded-r-lg">
            {{ $article->excerpt }}
        </p>
    </header>

    <!-- Thumbnail Utama -->
    @if ($article->thumbnail)
        <div class="rounded-2xl overflow-hidden border border-gray-200 bg-gray-100 max-h-[500px]">
            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}"
                 srcset="{{ $article->thumbnail_srcset }}"
                 sizes="(min-width: 1024px) 896px, 100vw"
                 class="w-full h-full object-cover">
        </div>
    @endif

    <!-- Share Buttons -->
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Bagikan</span>
        <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}" 
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:border-black hover:text-black transition"
           aria-label="Bagikan ke WhatsApp">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <span>WhatsApp</span>
        </a>
        <a href="https://x.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(url()->current()) }}" 
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:border-black hover:text-black transition"
           aria-label="Bagikan ke X">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 24.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
            <span>X</span>
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
           target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:border-black hover:text-black transition"
           aria-label="Bagikan ke Facebook">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook</span>
        </a>
        <button type="button" id="copy-link-btn"
                class="inline-flex items-center space-x-2 px-3.5 py-2 rounded-full border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:border-black hover:text-black transition"
                aria-label="Salin tautan artikel">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <span id="copy-link-text">Salin Link</span>
        </button>
    </div>

    <!-- Konten Berita -->
    <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed space-y-4 pt-4 border-t border-gray-100">
        {!! nl2br(e($article->content)) !!}
    </div>

    <!-- Related Articles -->
    @if ($relatedArticles->isNotEmpty())
        <section class="pt-10 mt-12 border-t border-gray-200 space-y-6">
            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Berita Terkait Lainnya</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                @foreach ($relatedArticles as $related)
                    <a href="{{ route('news.show', $related->slug) }}" class="group block bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
                        <div class="aspect-video bg-gray-100 overflow-hidden">
                            @if ($related->thumbnail)
                                <img src="{{ asset('storage/' . $related->thumbnail) }}" alt="{{ $related->title }}"
                                     loading="lazy"
                                     srcset="{{ $related->thumbnail_srcset }}"
                                     sizes="(min-width: 640px) 33vw, 100vw"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center bg-gray-900 text-white p-4 text-center">
                                    <span class="text-[10px] font-bold tracking-widest uppercase text-gray-400">HalimiNews</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 space-y-1.5">
                            <span class="text-[11px] text-gray-400 font-medium">
                                {{ $related->published_for_humans }}
                            </span>
                            <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 group-hover:text-gray-600 transition">
                                {{ $related->title }}
                            </h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</article>

<!-- Copy Link Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('copy-link-btn');
        const label = document.getElementById('copy-link-text');

        btn?.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(window.location.href);
                label.textContent = 'Tersalin!';
                setTimeout(() => { label.textContent = 'Salin Link'; }, 2000);
            } catch (e) {
                // Fallback untuk browser lama
                const tmp = document.createElement('textarea');
                tmp.value = window.location.href;
                document.body.appendChild(tmp);
                tmp.select();
                document.execCommand('copy');
                document.body.removeChild(tmp);
                label.textContent = 'Tersalin!';
                setTimeout(() => { label.textContent = 'Salin Link'; }, 2000);
            }
        });
    });
</script>
@endsection
