@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-10 sm:space-y-12">
    <!-- Category Navigation Bar -->
    <div class="border-b border-gray-200 relative">
        <nav class="flex items-center space-x-6 sm:space-x-8 overflow-x-auto scroll-smooth scrollbar-none [scrollbar-width:none] [&::-webkit-scrollbar]:hidden -mb-px text-xs sm:text-sm font-medium [mask-image:linear-gradient(to_right,black_85%,transparent)] sm:[mask-image:none]" aria-label="Kategori Berita">
            <a href="{{ route('home') }}" 
               @class([
                   'pb-3 border-b-2 whitespace-nowrap transition',
                   'border-black font-semibold text-black' => request()->routeIs('home'),
                   'border-transparent text-gray-500 hover:text-black hover:border-gray-300' => ! request()->routeIs('home'),
               ])
               @if (request()->routeIs('home')) aria-current="page" @endif>
                Home
            </a>
            <a href="{{ route('news.index') }}" 
               @class([
                   'pb-3 border-b-2 whitespace-nowrap transition',
                   'border-black font-semibold text-black' => request()->routeIs('news.index'),
                   'border-transparent text-gray-500 hover:text-black hover:border-gray-300' => ! request()->routeIs('news.index'),
               ])
               @if (request()->routeIs('news.index')) aria-current="page" @endif>
                Semua Berita
            </a>
            @foreach ($categories as $category)
                <a href="{{ route('news.category', $category->slug) }}" 
                   @class([
                       'pb-3 border-b-2 whitespace-nowrap transition',
                       'border-black font-semibold text-black' => request()->routeIs('news.category') && request()->route('slug') === $category->slug,
                       'border-transparent text-gray-500 hover:text-black hover:border-gray-300' => ! (request()->routeIs('news.category') && request()->route('slug') === $category->slug),
                   ])
                   @if (request()->routeIs('news.category') && request()->route('slug') === $category->slug) aria-current="page" @endif>
                    {{ $category->name }}
                </a>
            @endforeach
        </nav>
    </div>

    <!-- News Sort Filter -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">Urutkan Berita</p>
            <p class="mt-1 text-sm text-gray-600">Pilih berita terbaru atau mulai dari berita terlama.</p>
        </div>
        <form method="GET" action="{{ route('home') }}" class="flex items-center gap-2">
            <label for="sort" class="sr-only">Urutan berita</label>
            <select id="sort" name="sort" onchange="this.form.submit()"
                    class="rounded-xl border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-800 shadow-sm focus:border-black focus:ring-black">
                <option value="latest" @selected($sort === 'latest')>Terbaru</option>
                <option value="oldest" @selected($sort === 'oldest')>Terlama</option>
            </select>
            <noscript>
                <button type="submit" class="rounded-xl bg-black px-3 py-2 text-sm font-semibold text-white">Terapkan</button>
            </noscript>
        </form>
    </div>

    <!-- Hero Section (2-Column Magazine Layout) -->
    @if ($featuredArticle)
        <section class="grid grid-cols-1 {{ $secondaryArticles->isNotEmpty() ? 'lg:grid-cols-12' : '' }} gap-8 lg:gap-12 items-start pt-4">
            <!-- Left Column (Featured Article) -->
            <div class="{{ $secondaryArticles->isNotEmpty() ? 'lg:col-span-8' : 'max-w-5xl' }}">
                <article>
                    <!-- Media Image -->
                    <a href="{{ route('news.show', $featuredArticle->slug) }}" 
                       class="block rounded-2xl sm:rounded-3xl overflow-hidden aspect-[16/9] bg-gray-100 relative group shadow-md hover:shadow-xl transition-shadow duration-300">
                        @if ($featuredArticle->thumbnail)
                            <img src="{{ asset('storage/' . $featuredArticle->thumbnail) }}" 
                                 alt="{{ $featuredArticle->title }}" 
                                 loading="eager"
                                 srcset="{{ $featuredArticle->thumbnail_srcset }}"
                                 sizes="(min-width: 1024px) 66vw, 100vw"
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-out">
                            <!-- Gradient overlay untuk kedalaman visual -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent pointer-events-none"></div>
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-gray-900 via-neutral-900 to-black text-white p-6 sm:p-10 text-center">
                                <span class="text-[11px] font-extrabold uppercase tracking-[0.25em] text-gray-400 mb-2">HalimiNews EDITORIAL</span>
                                <span class="text-xl sm:text-2xl lg:text-3xl font-black tracking-tight text-white/90 line-clamp-2">
                                    {{ $featuredArticle->title }}
                                </span>
                            </div>
                        @endif
                    </a>

                    <!-- Meta Row under image -->
                    <div class="flex items-center mt-5 text-sm">
                        @php
                            $fWords = explode(' ', trim($featuredArticle->author_name));
                            $fInitials = count($fWords) >= 2 
                                ? strtoupper(substr($fWords[0], 0, 1) . substr($fWords[1], 0, 1))
                                : strtoupper(substr($featuredArticle->author_name, 0, 2));
                        @endphp
                        <div class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-xs shrink-0" title="{{ $featuredArticle->author_name }}">
                            {{ $fInitials }}
                        </div>
                        <span class="font-semibold text-gray-900 text-sm ml-2.5">{{ $featuredArticle->author_name }}</span>
                        <span class="text-gray-300 mx-3">|</span>
                        <a href="{{ route('news.category', $featuredArticle->category->slug) }}" 
                           class="font-medium text-gray-500 hover:text-black text-sm transition">
                            {{ $featuredArticle->category->name }}
                        </a>
                        <span class="ml-auto text-sm text-gray-400 font-medium shrink-0">
                            {{ $featuredArticle->published_for_humans }}
                        </span>
                    </div>

                    <!-- Big bold headline -->
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-[3.25rem] font-black text-black tracking-tight leading-[1.12] mt-4 hover:text-gray-700 transition">
                        <a href="{{ route('news.show', $featuredArticle->slug) }}">
                            {{ $featuredArticle->title }}
                        </a>
                    </h1>

                    <!-- Excerpt paragraph -->
                    <p class="text-gray-600 text-base sm:text-lg leading-relaxed mt-4 line-clamp-3">
                        {{ $featuredArticle->excerpt }}
                    </p>

                    <!-- read more link -->
                    <a href="{{ route('news.show', $featuredArticle->slug) }}" 
                       class="inline-block text-sm sm:text-base font-bold text-black hover:underline underline-offset-4 mt-3">
                        read more &rarr;
                    </a>
                </article>
            </div>

            <!-- Right Column (Secondary Articles Stack) -->
            @if ($secondaryArticles->isNotEmpty())
                <div class="lg:col-span-4 space-y-8">
                    @foreach ($secondaryArticles as $secondary)
                        <article class="border-b border-gray-200 pb-7 last:border-b-0 last:pb-0 space-y-4">
                            <!-- Top: Flex layout with thumbnail on the left and title on the right -->
                            <div class="flex items-start gap-4">
                                <a href="{{ route('news.show', $secondary->slug) }}" 
                                   class="w-32 sm:w-40 aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 shrink-0 group relative block shadow-sm hover:shadow-md transition-shadow">
                                    @if ($secondary->thumbnail)
                                        <img src="{{ asset('storage/' . $secondary->thumbnail) }}" 
                                             alt="{{ $secondary->title }}" 
                                             loading="lazy"
                                             srcset="{{ $secondary->thumbnail_srcset }}"
                                             sizes="(min-width: 640px) 160px, 128px"
                                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center bg-gray-900 text-white p-2 text-center">
                                            <span class="text-[10px] font-bold tracking-widest uppercase text-gray-400">HALIMI</span>
                                        </div>
                                    @endif
                                </a>

                                <div class="flex-1 min-w-0 pt-0.5">
                                    <h3 class="font-bold text-black text-base sm:text-lg leading-snug line-clamp-3 hover:text-gray-700 transition">
                                        <a href="{{ route('news.show', $secondary->slug) }}">
                                            {{ $secondary->title }}
                                        </a>
                                    </h3>
                                </div>
                            </div>

                            <!-- Meta Row below: Author avatar circle, author name, divider |, category link, and right-aligned time -->
                            <div class="flex items-center text-sm">
                                @php
                                    $sWords = explode(' ', trim($secondary->author_name));
                                    $sInitials = count($sWords) >= 2 
                                        ? strtoupper(substr($sWords[0], 0, 1) . substr($sWords[1], 0, 1))
                                        : strtoupper(substr($secondary->author_name, 0, 2));
                                @endphp
                                <div class="w-6 h-6 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-[10px] shrink-0" title="{{ $secondary->author_name }}">
                                    {{ $sInitials }}
                                </div>
                                <span class="font-semibold text-gray-900 text-sm ml-2 truncate max-w-[100px] sm:max-w-[140px]">{{ $secondary->author_name }}</span>
                                <span class="text-gray-300 mx-2.5">|</span>
                                <a href="{{ route('news.category', $secondary->category->slug) }}" 
                                   class="font-medium text-gray-500 hover:text-black text-sm transition truncate max-w-[90px] sm:max-w-[120px]">
                                    {{ $secondary->category->name }}
                                </a>
                                <span class="ml-auto text-sm text-gray-400 font-medium shrink-0">
                                    {{ $secondary->published_for_humans }}
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    <!-- Additional News Section ($moreArticles) -->
    @if ($moreArticles->isNotEmpty())
        <section class="pt-8 border-t border-gray-200 space-y-6" id="more-articles">
            <div class="flex items-center justify-between">
                <h2 class="text-2xl sm:text-3xl font-black text-black tracking-tight">
                    Berita Terkini Lainnya
                </h2>
                <a href="{{ route('news.index') }}" class="text-sm sm:text-base font-bold text-black hover:underline underline-offset-4 flex items-center space-x-1.5">
                    <span>Lihat Semua Berita</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-4">
                @foreach ($moreArticles as $article)
                    <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col group">
                        <!-- Card Media -->
                        <a href="{{ route('news.show', $article->slug) }}" class="block aspect-video bg-gray-100 overflow-hidden relative">
                            @if ($article->thumbnail)
                                <img src="{{ asset('storage/' . $article->thumbnail) }}" 
                                     alt="{{ $article->title }}" 
                                     loading="lazy"
                                     srcset="{{ $article->thumbnail_srcset }}"
                                     sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
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

                        <!-- Card Body -->
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2.5">
                                <!-- Meta: Author, divider, date -->
                                <div class="flex items-center text-xs">
                                    @php
                                        $mWords = explode(' ', trim($article->author_name));
                                        $mInitials = count($mWords) >= 2 
                                            ? strtoupper(substr($mWords[0], 0, 1) . substr($mWords[1], 0, 1))
                                            : strtoupper(substr($article->author_name, 0, 2));
                                    @endphp
                                    <div class="w-5 h-5 rounded-full bg-gray-900 text-white flex items-center justify-center font-bold text-[9px] shrink-0" title="{{ $article->author_name }}">
                                        {{ $mInitials }}
                                    </div>
                                    <span class="font-semibold text-gray-900 text-xs ml-2 truncate max-w-[120px]">{{ $article->author_name }}</span>
                                    <span class="text-gray-300 mx-2">|</span>
                                    <span class="text-gray-400 font-medium text-xs ml-auto shrink-0">
                                        {{ $article->published_for_humans }}
                                    </span>
                                </div>

                                <h3 class="font-bold text-black text-base sm:text-lg leading-snug line-clamp-2 group-hover:text-gray-700 transition">
                                    <a href="{{ route('news.show', $article->slug) }}">
                                        {{ $article->title }}
                                    </a>
                                </h3>

                                <p class="text-xs sm:text-sm text-gray-600 line-clamp-2 leading-relaxed">
                                    {{ $article->excerpt }}
                                </p>
                            </div>

                            <div class="pt-2 border-t border-gray-100">
                                <a href="{{ route('news.show', $article->slug) }}" class="inline-block text-xs font-bold text-black hover:underline">
                                    read more &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination More Articles -->
            @if ($moreArticles->hasPages())
                <div class="pt-8 border-t border-gray-100">
                    {{ $moreArticles->links() }}
                </div>
            @endif
        </section>
    @endif

    <!-- Empty State -->
    @if (! $featuredArticle)
        <div class="bg-white rounded-3xl border border-gray-200 p-8 sm:p-14 text-center max-w-xl mx-auto my-12 space-y-4 shadow-sm">
            <div class="w-16 h-16 bg-gray-100 text-gray-800 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Belum Ada Berita yang Diterbitkan</h3>
            <p class="text-sm text-gray-500 leading-relaxed">
                Portal berita saat ini belum memiliki artikel yang dipublikasikan. Silakan kembali lagi nanti atau login sebagai admin untuk menambahkan berita baru.
            </p>
            <div class="pt-2">
                @auth
                    <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center px-4 py-2 bg-black text-white rounded-xl text-xs font-semibold hover:bg-gray-800 transition shadow-sm">
                        + Buat Berita Sekarang
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-black text-white rounded-xl text-xs font-semibold hover:bg-gray-800 transition shadow-sm">
                        Login Admin untuk Menambah Berita
                    </a>
                @endauth
            </div>
        </div>
    @endif
</div>
@endsection
