<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Admin - CRUDBerita</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4 font-sans antialiased">
    <div class="max-w-md w-full">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="inline-block text-3xl font-extrabold tracking-tight text-blue-600 mb-2">
                CRUD<span class="text-gray-900">Berita</span>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Login Admin</h1>
            <p class="text-sm text-gray-600 mt-1">Masuk untuk mengelola berita dan kategori</p>
        </div>

        <!-- Card Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            @if (session('success'))
                <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-3 py-2 border @error('email') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-3 py-2 border @error('password') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                </div>

                <!-- Cloudflare Turnstile Captcha -->
                <div class="cf-turnstile flex justify-center py-1" 
                     data-sitekey="{{ config('services.turnstile.key') }}" 
                     data-theme="light">
                </div>

                <button type="submit" 
                        class="w-full py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    Masuk ke Dashboard
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-blue-600 transition flex items-center justify-center space-x-1">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Halaman Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
