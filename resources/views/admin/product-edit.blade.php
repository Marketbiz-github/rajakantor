@extends('layouts.app-admin')

@section('title', 'Edit Produk')

@section('content')
<div class="max-w-6xl mx-auto mt-8 px-4">
    <x-breadcrumb :items="[['label' => 'Produk', 'url' => route('product.index')], ['label' => 'Edit Produk']]" />

    <div class="bg-white rounded-lg shadow-sm border p-8">
        <h2 class="text-xl font-bold mb-6">Edit Produk</h2>
        <form method="POST" action="{{ route('product.update', [$product->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 text-red-700 px-4 py-3 rounded-md mb-6 border border-red-200">
                    @foreach($errors->all() as $err)
                        <div>{{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <div class="mb-3">
                <label class="block mb-2 text-sm">Nama Produk <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('name', $product->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Slug <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="slug" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('slug', $product->slug) }}" required>
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Kategori</label>
                <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id_category }}" {{ (old('category') ?? $productCategory?->id_category) == $category->id_category ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="block mb-2 text-sm">Deskripsi Singkat</label>
                <textarea name="description_short" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('description_short', $product->description_short) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Deskripsi</label>
                <textarea name="description" id="description" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Meta Title</label>
                <input type="text" name="meta_title" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('meta_title', $product->meta_title) }}">
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Meta Keyword</label>
                <input type="text" name="meta_keywords" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('meta_keywords', $product->meta_keywords) }}">
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Meta Description</label>
                <input type="text" name="meta_description" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('meta_description', $product->meta_description) }}">
            </div>

            <!-- Current Images Display -->
            @if($images && count($images) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Gambar Saat Ini</h3>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach($images as $img)
                            @php
                                $publicPath = 'images/product/' . $product->id_product . '-' . $img->id_image . '.jpg';
                                $storagePath = 'storage/product/' . $product->id_product . '-' . $img->id_image . '.jpg';

                                if (file_exists(storage_path('app/public/product/' . $product->id_product . '-' . $img->id_image . '.jpg'))) {
                                    $imageUrl = asset($storagePath);
                                } elseif (file_exists(public_path($publicPath))) {
                                    $imageUrl = asset($publicPath);
                                } else {
                                    $imageUrl = asset('images/product/en.jpg');
                                }
                            @endphp

                            <div class="border-gray-300 rounded-lg p-4">
                                <img src="{{ $imageUrl }}" 
                                    alt="Gambar {{ $img->position }}" 
                                    class="w-full object-cover rounded mb-2">
                            </div>
                        @endforeach

                    </div>
                </div>
            @endif

            <!-- Image Upload Fields -->
            <div class="mb-3">
                <label class="block mb-2 text-sm">Gambar 1</label>
                <input type="file" name="image1" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="mt-2 text-sm text-gray-500">
                    Format: jpg. Maks: 2MB.
                </p>
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Gambar 2</label>
                <input type="file" name="image2" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="mt-2 text-sm text-gray-500">
                    Format: jpg. Maks: 2MB.
                </p>
            </div>
            <div class="mb-3">
                <label class="block mb-2 text-sm">Gambar 3</label>
                <input type="file" name="image3" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="mt-2 text-sm text-gray-500">
                    Format: jpg. Maks: 2MB.
                </p>
            </div>

            <div class="mb-3">
                <label class="block mb-2 text-sm">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ old('status', $product->status) == 2 ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>

            <div class="flex justify-end mt-4">
                <a href="{{ route('product.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Batal</a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    tinymce.init({
        selector: '#description',
        menubar: false,
        plugins: 'link lists code',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
        height: 250
    });
});
</script>
@endpush