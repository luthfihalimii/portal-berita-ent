@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Dashboard</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan data dan aktivitas portal berita HalimiNews</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ url('/admin/categories/create') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:border-black hover:text-black transition">
                + Tambah Kategori
            </a>
            <a href="{{ url('/admin/articles/create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-black hover:bg-gray-800 transition shadow-sm">
                + Buat Berita
            </a>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Total Berita -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Total Berita</p>
                    <p class="text-3xl font-black text-gray-900 mt-1.5">{{ $totalArticles }}</p>
                </div>
                <div class="w-11 h-11 bg-gray-100 text-gray-700 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Semua artikel terdaftar</span>
                <a href="{{ url('/admin/articles') }}" class="font-bold text-black hover:underline underline-offset-4">Kelola &rarr;</a>
            </div>
        </div>

        <!-- Berita Published -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Published</p>
                    <p class="text-3xl font-black text-green-600 mt-1.5">{{ $publishedArticles }}</p>
                </div>
                <div class="w-11 h-11 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Tayang di portal publik</span>
                <span class="inline-flex items-center space-x-1 font-semibold text-green-600">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    <span>Aktif</span>
                </span>
            </div>
        </div>

        <!-- Berita Draft -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Draft</p>
                    <p class="text-3xl font-black text-amber-500 mt-1.5">{{ $draftArticles }}</p>
                </div>
                <div class="w-11 h-11 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Belum dipublikasikan</span>
                <span class="inline-flex items-center space-x-1 font-semibold text-amber-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>Privat</span>
                </span>
            </div>
        </div>

        <!-- Total Kategori -->
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400">Kategori</p>
                    <p class="text-3xl font-black text-gray-900 mt-1.5">{{ $totalCategories }}</p>
                </div>
                <div class="w-11 h-11 bg-gray-100 text-gray-700 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span>Topik pengelompokan</span>
                <a href="{{ url('/admin/categories') }}" class="font-bold text-black hover:underline underline-offset-4">Kelola &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Berita Terbaru -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-bold text-gray-900">Berita Terakhir Dibuat</h2>
            <a href="{{ url('/admin/articles') }}" class="text-xs font-bold text-black hover:underline underline-offset-4">
                Lihat Semua Berita &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-widest text-left">
                    <tr>
                        <th class="px-6 py-3 font-bold">Judul</th>
                        <th class="px-6 py-3 font-bold">Kategori</th>
                        <th class="px-6 py-3 font-bold">Status</th>
                        <th class="px-6 py-3 font-bold">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($recentArticles as $article)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900 max-w-xs truncate">
                                {{ $article->title }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                    {{ $article->category->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($article->status === 'published')
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        <span>Published</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                        <span>Draft</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs font-medium whitespace-nowrap">
                                {{ $article->created_at->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                Belum ada berita yang dibuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
