<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'HalimiNews - Portal Berita Terkini & Terpercaya' }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

    <!-- SEO Meta -->
    <meta name="description" content="{{ $metaDescription ?? 'HalimiNews - Portal berita terkini, terpercaya, dan aktual. Baca berita terbaru seputar politik, ekonomi, teknologi, olahraga, dan lainnya.' }}">

    <!-- RSS Feed -->
    <link rel="alternate" type="application/rss+xml" title="HalimiNews RSS Feed" href="{{ route('feed') }}">

    <!-- Canonical URL -->
    @if (isset($canonicalUrl))
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endif

    <!-- Open Graph -->
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:site_name" content="HalimiNews">
    <meta property="og:title" content="{{ $title ?? 'HalimiNews - Portal Berita Terkini & Terpercaya' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Portal berita terkini, terpercaya, dan aktual.' }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('og-image.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'HalimiNews - Portal Berita Terkini & Terpercaya' }}">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Portal berita terkini, terpercaya, dan aktual.' }}">
    <meta name="twitter:image" content="{{ $ogImage ?? asset('og-image.png') }}">

    {{ \Illuminate\Support\Facades\Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen font-sans antialiased">
    <!-- Header Bar -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Left Section: Search Toggle -->
                <div class="flex items-center">
                    <button type="button" 
                            id="search-toggle-btn" 
                            class="p-2 text-gray-700 hover:text-black hover:bg-gray-100 rounded-full transition focus:outline-none focus:ring-2 focus:ring-gray-300" 
                            aria-label="Buka Pencarian" 
                            title="Cari Berita">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Center Section: Brand Logo -->
                <div class="flex-1 text-center px-2">
                    <a href="{{ route('home') }}" 
                       class="font-extrabold tracking-[0.18em] text-xl sm:text-2xl uppercase text-black hover:opacity-80 transition inline-block">
                        HalimiNews
                    </a>
                </div>

                <!-- Right Section: Social Icons & User Avatar -->
                <div class="flex items-center">
                    <div class="flex items-center space-x-1 sm:space-x-2">
                        <a href="https://x.com" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="p-1.5 sm:p-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-full transition" 
                           aria-label="X (Twitter)" 
                           title="X (Twitter)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 24.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="https://facebook.com" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="p-1.5 sm:p-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-full transition" 
                           aria-label="Facebook" 
                           title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>

                    <div class="h-4 w-px bg-gray-300 mx-2 sm:mx-3" aria-hidden="true"></div>

                    @auth
                        <a href="{{ route('admin.dashboard') }}" 
                           title="Dashboard Admin ({{ Auth::user()->name }})" 
                           aria-label="Dashboard Admin ({{ Auth::user()->name }})" 
                           class="w-8 h-8 rounded-full bg-black text-white flex items-center justify-center hover:bg-gray-800 transition ring-2 ring-transparent hover:ring-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           title="Login Admin" 
                           aria-label="Login Admin" 
                           class="w-8 h-8 rounded-full border border-gray-300 text-gray-700 flex items-center justify-center hover:border-black hover:text-black transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Search Overlay / Slide-down -->
        <div id="search-slide-down" class="grid grid-rows-[0fr] opacity-0 -translate-y-1 transition-[grid-template-rows,opacity,transform] duration-300 ease-out overflow-hidden border-t border-transparent">
            <div class="min-h-0">
                <div id="search-slide-inner" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4 bg-white border-t border-gray-200 shadow-sm -mt-px">
                <form action="{{ route('news.search') }}" method="GET" class="relative flex items-center">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="q" 
                           id="search-input" 
                           value="{{ request('q') }}" 
                           placeholder="Cari berita berdasarkan kata kunci..." 
                           class="w-full pl-11 pr-24 py-2.5 sm:py-3 text-sm sm:text-base bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:bg-white transition"
                           autocomplete="off">
                    <div class="absolute right-2 flex items-center space-x-1">
                        <button type="submit" 
                                class="px-3.5 py-1.5 text-xs font-semibold bg-black text-white rounded-lg hover:bg-gray-800 transition">
                            Cari
                        </button>
                        <button type="button" 
                                id="search-close-btn" 
                                class="p-1.5 text-gray-400 hover:text-black rounded-lg transition" 
                                aria-label="Tutup Pencarian">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>

    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Publik -->
    <footer class="bg-black text-white mt-16 sm:mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-8">
                <!-- Brand Column -->
                <div class="lg:col-span-5 space-y-4">
                    <a href="{{ route('home') }}" class="inline-flex items-center space-x-2.5 group">
                        <span class="w-9 h-9 rounded-xl bg-white text-black flex items-center justify-center font-black text-base leading-none group-hover:bg-gray-200 transition">H</span>
                        <span class="font-extrabold tracking-[0.12em] text-xl uppercase">HalimiNews</span>
                    </a>
                    <p class="text-sm text-gray-400 leading-relaxed max-w-sm">
                        Portal berita terkini, terpercaya, dan aktual. Menyajikan informasi mendalam dari berbagai topik untuk pembaca setia.
                    </p>
                    <div class="flex items-center space-x-2 pt-1">
                        <a href="https://x.com" target="_blank" rel="noopener noreferrer" 
                           class="w-9 h-9 flex items-center justify-center rounded-full border border-white/20 text-gray-300 hover:bg-white hover:text-black hover:border-white transition" 
                           aria-label="X (Twitter)">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 24.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" 
                           class="w-9 h-9 flex items-center justify-center rounded-full border border-white/20 text-gray-300 hover:bg-white hover:text-black hover:border-white transition" 
                           aria-label="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Categories Column -->
                <div class="lg:col-span-3">
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500 mb-4">Kategori</h3>
                    <ul class="space-y-2.5">
                        @foreach (($categories ?? collect())->take(6) as $category)
                            <li>
                                <a href="{{ route('news.category', $category->slug) }}" class="text-sm text-gray-400 hover:text-white transition">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Navigation Column -->
                <div class="lg:col-span-2">
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500 mb-4">Navigasi</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('home') }}" class="text-sm text-gray-400 hover:text-white transition">Beranda</a></li>
                        <li><a href="{{ route('news.index') }}" class="text-sm text-gray-400 hover:text-white transition">Semua Berita</a></li>
                        <li><a href="{{ route('news.search', ['q' => 'terkini']) }}" class="text-sm text-gray-400 hover:text-white transition">Pencarian</a></li>
                    </ul>
                </div>

                <!-- Account Column -->
                <div class="lg:col-span-2">
                    <h3 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500 mb-4">Akun</h3>
                    <ul class="space-y-2.5">
                        @auth
                            <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-400 hover:text-white transition">Dashboard Admin</a></li>
                            <li><a href="{{ route('admin.articles.create') }}" class="text-sm text-gray-400 hover:text-white transition">Tulis Berita</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition">Login Admin</a></li>
                        @endauth
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="mt-12 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} HalimiNews. All rights reserved.</p>
                <p>Built with Laravel &amp; Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button type="button" id="back-to-top"
            class="fixed bottom-6 right-6 z-40 w-11 h-11 rounded-full bg-black text-white shadow-lg hover:bg-gray-800 transition-all duration-300 flex items-center justify-center opacity-0 translate-y-4 pointer-events-none"
            aria-label="Kembali ke atas">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"></path>
        </svg>
    </button>

    <!-- Interactive Navigation Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchToggleBtn = document.getElementById('search-toggle-btn');
            const searchCloseBtn = document.getElementById('search-close-btn');
            const searchSlideDown = document.getElementById('search-slide-down');
            const searchInput = document.getElementById('search-input');

            const OPEN_CLASSES = ['grid-rows-[1fr]', 'opacity-100', 'translate-y-0', 'border-gray-200'];
            const CLOSED_CLASSES = ['grid-rows-[0fr]', 'opacity-0', '-translate-y-1', 'border-transparent'];

            function openSearch() {
                searchSlideDown.classList.remove(...CLOSED_CLASSES);
                searchSlideDown.classList.add(...OPEN_CLASSES);
                setTimeout(() => searchInput?.focus(), 250);
            }

            function closeSearch() {
                searchSlideDown.classList.remove(...OPEN_CLASSES);
                searchSlideDown.classList.add(...CLOSED_CLASSES);
            }

            function isSearchOpen() {
                return searchSlideDown.classList.contains('opacity-100');
            }

            function toggleSearch() {
                isSearchOpen() ? closeSearch() : openSearch();
            }

            searchToggleBtn?.addEventListener('click', toggleSearch);
            searchCloseBtn?.addEventListener('click', closeSearch);

            // Close on Escape key press
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && isSearchOpen()) {
                    closeSearch();
                }
            });

            // Back to Top
            const backToTop = document.getElementById('back-to-top');
            const VISIBLE_CLASSES = ['opacity-100', 'translate-y-0', 'pointer-events-auto'];
            const HIDDEN_CLASSES = ['opacity-0', 'translate-y-4', 'pointer-events-none'];

            function updateBackToTop() {
                if (window.scrollY > 600) {
                    backToTop.classList.remove(...HIDDEN_CLASSES);
                    backToTop.classList.add(...VISIBLE_CLASSES);
                } else {
                    backToTop.classList.remove(...VISIBLE_CLASSES);
                    backToTop.classList.add(...HIDDEN_CLASSES);
                }
            }

            window.addEventListener('scroll', updateBackToTop, { passive: true });
            updateBackToTop();

            backToTop?.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
