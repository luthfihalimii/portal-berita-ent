@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Berita Baru</h1>
            <p class="text-sm text-gray-600">Tulis berita dan publikasikan ke portal pembaca</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Berita <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus
                           placeholder="Masukkan judul berita utama..."
                           class="w-full px-3 py-2 border @error('title') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    @error('title')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="md:col-span-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (Opsional)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                           placeholder="otomatis dibuat jika kosong"
                           class="w-full px-3 py-2 border @error('slug') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono text-xs">
                    @error('slug')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required
                            class="w-full px-3 py-2 border @error('category_id') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Penulis (Author) -->
                <div>
                    <label for="author_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Penulis (Author)</label>
                    <input type="text" name="author_name" id="author_name" value="{{ old('author_name', $article->author_name ?? '') }}" 
                           placeholder="Contoh: Adam Strong, Mary Frost"
                           class="w-full px-3 py-2 border @error('author_name') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Opsional. Jika dikosongkan, otomatis menggunakan "Redaksi VERTONEWS".</p>
                    @error('author_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ringkasan / Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-1">Ringkasan Berita (Excerpt) <span class="text-red-500">*</span></label>
                <textarea name="excerpt" id="excerpt" rows="3" required
                          placeholder="Ringkasan singkat yang menarik untuk tampilan kartu berita..."
                          class="w-full px-3 py-2 border @error('excerpt') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Isi Berita / Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Isi Berita Lengkap <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="10" required
                          placeholder="Tulis artikel berita secara detail di sini..."
                          class="w-full px-3 py-2 border @error('content') border-red-500 @else border-gray-300 @enderror rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                <!-- Thumbnail -->
                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Thumbnail Gambar (Opsional)</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                    @error('thumbnail')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Publikasi <span class="text-red-500">*</span></label>
                    <div class="flex items-center space-x-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="status" value="draft" {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Simpan sebagai Draft</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="status" value="published" {{ old('status') === 'published' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm text-gray-700 font-medium text-green-700">Publikasikan</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Simpan Berita
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
