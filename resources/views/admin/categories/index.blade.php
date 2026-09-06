@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Manajemen Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar kategori untuk pengelompokan berita</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-black hover:bg-gray-800 transition shadow-sm">
                + Tambah Kategori
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-gray-500 text-[11px] uppercase tracking-widest text-left">
                    <tr>
                        <th class="px-6 py-3 font-bold">Nama Kategori</th>
                        <th class="px-6 py-3 font-bold">Slug</th>
                        <th class="px-6 py-3 font-bold">Jumlah Berita</th>
                        <th class="px-6 py-3 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-400 font-mono text-xs">
                                {{ $category->slug }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                    {{ $category->articles_count ?? $category->articles()->count() }} Berita
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-xs font-bold text-black hover:underline underline-offset-4 transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua artikel terkait juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 transition ml-2">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                Belum ada kategori yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
