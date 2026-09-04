@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Kategori</h1>
            <p class="text-sm text-gray-600">Perbarui informasi kategori</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required autofocus
                       class="w-full px-3 py-2 border @error('name') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                @error('name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" required
                       class="w-full px-3 py-2 border @error('slug') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono text-xs">
                @error('slug')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
