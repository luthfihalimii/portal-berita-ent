<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Admin Dashboard - CRUDBerita' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased min-h-screen flex flex-col">
    <!-- Navbar Admin -->
    <header class="bg-gray-900 text-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-6">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight text-white flex items-center space-x-2">
                        <span class="text-blue-400">CRUD</span><span>Berita Admin</span>
                    </a>
                    <nav class="hidden md:flex space-x-4">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ url('/admin/categories') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->is('admin/categories*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            Kategori
                        </a>
                        <a href="{{ url('/admin/articles') }}" 
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->is('admin/articles*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                            Berita
                        </a>
                    </nav>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ url('/') }}" target="_blank" class="text-xs text-gray-400 hover:text-white transition flex items-center space-x-1">
                        <span>Lihat Portal</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>

                    <div class="flex items-center space-x-3 border-l border-gray-700 pl-4">
                        <span class="text-sm font-medium text-gray-300">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded font-medium transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alert -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Admin -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        <p>&copy; {{ date('Y') }} CRUDBerita Dashboard Admin. Semua aksi tercatat aman.</p>
    </footer>
</body>
</html>
