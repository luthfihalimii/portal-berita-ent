<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard Admin - HalimiNews' }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    {{ \Illuminate\Support\Facades\Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[16rem_1fr]">

        <!-- Mobile Topbar -->
        <header class="lg:hidden sticky top-0 z-40 bg-black text-white flex items-center justify-between h-14 px-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-lg bg-white text-black flex items-center justify-center font-black text-sm leading-none">H</span>
                <span class="font-extrabold tracking-[0.1em] text-sm uppercase">HalimiNews</span>
            </a>
            <button type="button" id="sidebar-toggle" class="p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition" aria-label="Buka menu navigasi">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </header>

        <!-- Sidebar Overlay (mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

        <!-- Sidebar -->
        <aside id="admin-sidebar" class="fixed lg:sticky top-0 z-50 lg:z-0 h-screen w-64 lg:w-auto bg-black text-white flex flex-col -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out">
            <!-- Brand -->
            <div class="h-16 flex items-center px-6 border-b border-white/10 shrink-0">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2.5 group">
                    <span class="w-9 h-9 rounded-xl bg-white text-black flex items-center justify-center font-black text-base leading-none group-hover:bg-gray-200 transition">H</span>
                    <div class="leading-tight">
                        <span class="block font-extrabold tracking-[0.1em] text-sm uppercase">HalimiNews</span>
                        <span class="block text-[10px] font-medium text-gray-400 uppercase tracking-widest">Admin Panel</span>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <p class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500">Menu</p>

                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-black' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ url('/admin/articles') }}" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/articles*') ? 'bg-white text-black' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                    <span>Berita</span>
                </a>

                <a href="{{ url('/admin/categories') }}" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->is('admin/categories*') ? 'bg-white text-black' : 'text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>Kategori</span>
                </a>

                <p class="px-3 pt-5 pb-1 text-[10px] font-bold uppercase tracking-[0.2em] text-gray-500">Lainnya</p>

                <a href="{{ url('/') }}" target="_blank" 
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-300 hover:bg-white/10 hover:text-white transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    <span>Lihat Portal</span>
                </a>
            </nav>

            <!-- User & Logout -->
            <div class="p-4 border-t border-white/10 shrink-0">
                <div class="flex items-center space-x-3">
                    @php
                        $adminName = Auth::user()->name ?? 'Admin';
                        $aWords = explode(' ', trim($adminName));
                        $aInitials = count($aWords) >= 2 
                            ? strtoupper(substr($aWords[0], 0, 1) . substr($aWords[1], 0, 1))
                            : strtoupper(substr($adminName, 0, 2));
                    @endphp
                    <div class="w-9 h-9 rounded-full bg-white text-black flex items-center justify-center font-bold text-xs shrink-0">
                        {{ $aInitials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate">{{ $adminName }}</p>
                        <p class="text-[11px] text-gray-400">Administrator</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition" title="Logout" aria-label="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex flex-col min-h-screen min-w-0">
            <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <!-- Flash Alert -->
                @if (session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 flex items-center space-x-2.5">
                        <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 flex items-center space-x-2.5">
                        <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer Admin -->
            <footer class="border-t border-gray-200 py-5 text-center text-xs text-gray-400">
                <p>&copy; {{ date('Y') }} HalimiNews Admin Panel</p>
            </footer>
        </div>
    </div>

    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const toggle = document.getElementById('sidebar-toggle');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            toggle?.addEventListener('click', openSidebar);
            overlay?.addEventListener('click', closeSidebar);

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>
</html>
