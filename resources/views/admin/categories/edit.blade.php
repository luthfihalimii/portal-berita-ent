@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Edit Kategori</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi kategori</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-gray-500 hover:text-black transition">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required autofocus
                       class="w-full px-3.5 py-2.5 border @error('name') border-red-400 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm transition">
                @error('name')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-semibold text-gray-700 mb-1.5">Slug <span class="text-red-500">*</span></label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $category->slug) }}" required
                       class="w-full px-3.5 py-2.5 border @error('slug') border-red-400 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm font-mono transition">
                @error('slug')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:border-black hover:text-black transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white rounded-xl text-sm font-bold shadow-sm transition">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
