@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Buat Berita Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Tulis berita dan publikasikan ke portal pembaca</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="text-sm font-semibold text-gray-500 hover:text-black transition">
            &larr; Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" data-autosave-form>
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Judul Berita <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required autofocus
                           placeholder="Masukkan judul berita utama..."
                           class="w-full px-3.5 py-2.5 border @error('title') border-red-400 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm transition">
                    @error('title')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div class="md:col-span-2">
                    <label for="slug" class="block text-sm font-semibold text-gray-700 mb-1.5">Slug (Opsional)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                           placeholder="otomatis dibuat jika kosong"
                           class="w-full px-3.5 py-2.5 border @error('slug') border-red-400 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm font-mono transition">
                    @error('slug')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" id="category_id" required
                            class="w-full px-3.5 py-2.5 border @error('category_id') border-red-400 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm bg-white transition">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Penulis (Author) -->
                <div>
                    <label for="author_name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Penulis (Author)</label>
                    <input type="text" name="author_name" id="author_name" value="{{ old('author_name', $article->author_name ?? '') }}" 
                           placeholder="Contoh: Adam Strong, Mary Frost"
                           class="w-full px-3.5 py-2.5 border @error('author_name') border-red-400 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm transition">
                    <p class="text-xs text-gray-400 mt-1.5">Opsional. Jika dikosongkan, otomatis menggunakan "Redaksi HalimiNews".</p>
                    @error('author_name')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ringkasan / Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-semibold text-gray-700 mb-1.5">Ringkasan Berita (Excerpt)</label>
                <textarea name="excerpt" id="excerpt" rows="3"
                          placeholder="Ringkasan singkat yang menarik untuk tampilan kartu berita..."
                          class="w-full px-3.5 py-2.5 border @error('excerpt') border-red-400 @else border-gray-300 @enderror rounded-xl focus:outline-none focus:ring-2 focus:ring-black focus:border-black text-sm transition">{{ old('excerpt') }}</textarea>
                <p class="text-xs text-gray-400 mt-1.5">Opsional. Jika dikosongkan, otomatis dibuat dari awal isi berita.</p>
                @error('excerpt')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Isi Berita / Content -->
            <div>
                <label for="content" class="block text-sm font-semibold text-gray-700 mb-1.5">Isi Berita Lengkap <span class="text-red-500">*</span></label>
                <input id="content" type="hidden" name="content" value="{{ old('content') }}">
                <trix-editor input="content"
                             data-upload-url="{{ route('admin.articles.upload-image') }}"
                             placeholder="Tulis artikel berita secara detail di sini..."
                             class="trix-content block w-full border @error('content') border-red-400 @else border-gray-300 @enderror rounded-xl text-sm transition focus:outline-none focus:ring-2 focus:ring-black focus:border-black bg-white"></trix-editor>
                @error('content')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-5 border-t border-gray-100">
                <!-- Thumbnail -->
                <div>
                    <label for="thumbnail" class="block text-sm font-semibold text-gray-700 mb-1.5">Thumbnail Gambar (Opsional)</label>
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200 file:transition">
                    <p class="text-xs text-gray-400 mt-1.5">Format: JPG, PNG, WEBP. Maksimal 2MB. Variant WebP dibuat otomatis.</p>
                    @error('thumbnail')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Publikasi <span class="text-red-500">*</span></label>
                    <div class="flex items-center space-x-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="status" value="draft" {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}
                                   class="text-black focus:ring-black">
                            <span class="ml-2 text-sm text-gray-700">Simpan sebagai Draft</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="status" value="published" {{ old('status') === 'published' ? 'checked' : '' }}
                                   class="text-green-600 focus:ring-green-500">
                            <span class="ml-2 text-sm font-semibold text-green-700">Publikasikan</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.articles.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:border-black hover:text-black transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-black hover:bg-gray-800 text-white rounded-xl text-sm font-bold shadow-sm transition">
                    Simpan Berita
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
