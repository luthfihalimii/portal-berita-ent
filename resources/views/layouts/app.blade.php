<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'CRUDBerita - Portal Berita Terkini & Terpercaya' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen font-sans antialiased">
    <!-- Navbar Publik -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2">
                        <span class="text-2xl font-bold tracking-tight text-blue-600">CRUD<span class="text-gray-900">Berita</span></span>
                    </a>
                    <nav class="hidden md:flex space-x-6">
                        <a href="{{ url('/') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Beranda</a>
                        <a href="{{ url('/berita') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 transition">Semua Berita</a>
                    </nav>
                </div>
                
                <div class="flex items-center space-x-4">
                    <form action="{{ url('/search') }}" method="GET" class="relative hidden sm:block">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari berita..." 
                               class="w-48 lg:w-64 pl-9 pr-3 py-1.5 text-sm bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </form>

                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition">
                            Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-3.5 py-1.5 text-xs font-semibold text-blue-600 border border-blue-600 rounded-lg hover:bg-blue-50 transition">
                            Login Admin
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Publik -->
    <footer class="bg-white border-t border-gray-200 py-8 mt-12 text-center text-sm text-gray-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} CRUDBerita. Seluruh hak cipta dilindungi.</p>
            <p class="text-xs text-gray-400 mt-1">Dibangun dengan Laravel & Tailwind CSS</p>
        </div>
    </footer>
</body>
</html>
