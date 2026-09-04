@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Kategori</h1>
            <p class="text-sm text-gray-600">Daftar kategori untuk pengelompokan berita</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                + Tambah Kategori
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider text-left">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Nama Kategori</th>
                        <th class="px-6 py-3 font-semibold">Slug</th>
                        <th class="px-6 py-3 font-semibold">Jumlah Berita</th>
                        <th class="px-6 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $category->name }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                                {{ $category->slug }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                    {{ $category->articles_count ?? $category->articles()->count() }} Berita
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                    Edit
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini? Semua artikel terkait juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800 transition ml-2">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Belum ada kategori yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
