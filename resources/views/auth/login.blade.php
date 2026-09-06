<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Admin - HalimiNews</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    {{ \Illuminate\Support\Facades\Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-900 font-sans antialiased">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        <!-- Left Panel: Login Form -->
        <div class="flex flex-col min-h-screen px-6 sm:px-12 lg:px-20 xl:px-28 py-10 sm:py-14">
            <!-- Brand Logo -->
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center space-x-2.5 group">
                    <span class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center font-black text-lg leading-none group-hover:bg-gray-800 transition">H</span>
                    <span class="font-extrabold tracking-[0.12em] text-xl uppercase text-black">HalimiNews</span>
                </a>
            </div>

            <!-- Form Area (centered vertically) -->
            <div class="flex-1 flex items-center">
                <div class="w-full max-w-sm mx-auto lg:mx-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                        Login Admin
                    </h1>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Selamat datang kembali. Masuk untuk mengelola berita dan kategori HalimiNews.
                    </p>

                    @if (session('success'))
                        <div class="mt-6 p-3.5 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-6 p-3.5 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="mt-8 space-y-5">
                        @csrf

                        <!-- Email Field (label di dalam, ala referensi) -->
                        <div>
                            <div class="rounded-xl border @error('email') border-red-400 ring-1 ring-red-400 @else border-gray-300 @enderror bg-white px-4 pt-2.5 pb-2 focus-within:border-black focus-within:ring-1 focus-within:ring-black transition">
                                <label for="email" class="block text-[11px] font-medium text-gray-400">
                                    Alamat Email
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                       placeholder="nama@email.com"
                                       class="block w-full border-0 p-0 pt-0.5 text-sm font-semibold text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-0 bg-transparent">
                            </div>
                        </div>

                        <!-- Password Field (label di dalam + toggle lihat) -->
                        <div>
                            <div class="rounded-xl border @error('password') border-red-400 ring-1 ring-red-400 @else border-gray-300 @enderror bg-white px-4 pt-2.5 pb-2 focus-within:border-black focus-within:ring-1 focus-within:ring-black transition flex items-center">
                                <div class="flex-1 min-w-0">
                                    <label for="password" class="block text-[11px] font-medium text-gray-400">
                                        Password
                                    </label>
                                    <input type="password" name="password" id="password" required
                                           placeholder="Masukkan password"
                                           class="block w-full border-0 p-0 pt-0.5 text-sm font-semibold text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-0 bg-transparent">
                                </div>
                                <button type="button" id="password-toggle"
                                        class="ml-3 p-1.5 text-gray-400 hover:text-black rounded-lg transition shrink-0"
                                        aria-label="Tampilkan password">
                                    <!-- Eye (lihat) -->
                                    <svg id="icon-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <!-- Eye off (sembunyikan) -->
                                    <svg id="icon-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 text-black border-gray-300 rounded focus:ring-black">
                                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                                class="w-full py-3.5 px-4 rounded-xl bg-black text-white text-sm font-bold tracking-wide hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition shadow-sm">
                            Masuk ke Dashboard
                        </button>
                    </form>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-400 pt-8">
                <a href="{{ url('/') }}" class="inline-flex items-center space-x-1.5 hover:text-black transition font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <p>&copy; {{ date('Y') }} HalimiNews</p>
            </div>
        </div>

        <!-- Right Panel: Visual (hidden di mobile) -->
        <div class="hidden lg:flex relative overflow-hidden bg-gradient-to-br from-neutral-100 via-gray-200 to-neutral-300 items-center justify-center">
            <!-- Pola lengkung arsitektur ala referensi (pure CSS) -->
            <div class="absolute inset-0 flex items-center justify-center" aria-hidden="true">
                <div class="relative flex items-end justify-center">
                    <!-- Arch terluar -->
                    <div class="w-[26rem] xl:w-[30rem] aspect-[3/4] rounded-t-full bg-white/25 backdrop-blur-sm shadow-inner flex items-end justify-center">
                        <!-- Arch tengah -->
                        <div class="w-[19rem] xl:w-[22rem] aspect-[3/4] rounded-t-full bg-white/30 shadow-inner flex items-end justify-center -mb-[8%]">
                            <!-- Arch terdalam -->
                            <div class="w-[12rem] xl:w-[14rem] aspect-[3/4] rounded-t-full bg-gradient-to-b from-neutral-500/40 to-neutral-700/50 -mb-[14%]"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Podium melingkar -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center" aria-hidden="true">
                <div class="w-72 h-10 rounded-[50%] bg-white/40 shadow-md"></div>
                <div class="w-96 h-10 rounded-[50%] bg-white/30 shadow-sm -mt-2"></div>
            </div>

            <!-- Caption brand di atas visual -->
            <div class="relative z-10 text-center px-12">
                <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-neutral-500">HalimiNews Editorial</p>
                <p class="mt-3 text-2xl xl:text-3xl font-black text-neutral-700 tracking-tight leading-snug">
                    Berita Terkini,<br>Terpercaya &amp; Aktual.
                </p>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('password-toggle');
            const input = document.getElementById('password');
            const eye = document.getElementById('icon-eye');
            const eyeOff = document.getElementById('icon-eye-off');

            toggle?.addEventListener('click', function () {
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                eye.classList.toggle('hidden', isHidden);
                eyeOff.classList.toggle('hidden', !isHidden);
                toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    </script>
</body>
</html>
