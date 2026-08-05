@extends('layouts.app-admin')

@section('title', 'Edit Kategori')

@section('content')
<div>
    <x-breadcrumb :items="[
        ['label' => 'Kategori', 'url' => route('category.index')],
        ['label' => 'Edit Kategori']
    ]" />

    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6 text-gray-900">Edit Kategori</h1>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('category.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori <span class="text-red-500 font-bold">*</span></label>
                <input type="text" id="name" name="name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="{{ old('name', $category->name) }}" placeholder="Masukkan nama kategori">
                @error('name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="block mb-2 text-sm">Slug <span class="text-red-500 font-bold">*</span></label>
                <input type="text" name="slug" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('slug', $category->slug) }}" required>
            </div>

            <!-- Parent Category -->
            <div>
                <label for="category_parent" class="block text-sm font-medium text-gray-700 mb-2">Parent Kategori</label>
                <select id="category_parent" name="category_parent"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Pilih Parent Kategori --</option>
                    @foreach ($categories as $categoryAll)
                        <option value="{{ $categoryAll->id_category }}" 
                            {{ old('category_parent', $parentCategory->id_parent) == $categoryAll->id_category ? 'selected' : '' }}>
                            {{ $categoryAll->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_parent')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="5"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Masukkan deskripsi kategori">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Meta Title -->
            <div>
                <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                <input type="text" id="meta_title" name="meta_title"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="{{ old('meta_title', $category->meta_title) }}" placeholder="Masukkan meta title">
                @error('meta_title')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Meta Keywords -->
            <div>
                <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">Meta Keywords</label>
                <input type="text" id="meta_keywords" name="meta_keywords"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       value="{{ old('meta_keywords', $category->meta_keywords) }}" placeholder="Masukkan meta keywords">
                @error('meta_keywords')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Meta Description -->
            <div>
                <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Masukkan meta description">{{ old('meta_description', $category->meta_description) }}</textarea>
                @error('meta_description')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Current Image Display -->
            @php
                $publicPath = 'images/category/' . $category->id_category . '.jpg';
                $storagePath = 'storage/category/' . $category->id_category . '.jpg';

                if (file_exists(storage_path('app/public/category/' . $category->id_category . '.jpg'))) {
                    $imageUrl = asset($storagePath);
                }
                elseif (file_exists(public_path($publicPath))) {
                    $imageUrl = asset($publicPath);
                } else {
                    $imageUrl = asset('images/product/en.jpg');
                }
            @endphp

            @if($imageUrl)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-4">Gambar Saat Ini</h3>
                    <div class="max-w-xs">
                        <img src="{{ $imageUrl }}" 
                            alt="Gambar Kategori {{ $category->name }}" 
                            class="w-full object-cover rounded border border-gray-300">
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <label class="block mb-2 text-sm">Gambar</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="mt-2 text-sm text-gray-500">
                    Format: jpg. Maks: 2MB.
                </p>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500 font-bold">*</span></label>
                <select id="status" name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="1" {{ old('status', $category->status) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ old('status', $category->status) == '2' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="hapus">Hapus</option>
                </select>
                @error('status')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <input type="hidden" name="delete_products" id="single-delete-products" value="0">

            <div class="flex justify-end mt-4">
                <a href="{{ route('category.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Batal</a>
                <button type="submit" id="btnSave" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center gap-2">
                    <span id="btnSaveText">Simpan</span>
                    <span id="btnSaveIcon" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
                </button>
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

    const form = document.querySelector('form.space-y-6');
    const statusSelect = document.getElementById('status');
    const deleteModal = document.getElementById('deleteModal');
    const alertModal = document.getElementById('alertModal');
    const childCount = {{ $childCount ?? 0 }};
    const productCount = {{ $productCount ?? 0 }};
    const childrenNames = '{!! addslashes($childrenNames ?? '') !!}';

    form.addEventListener('submit', function (e) {
        if (statusSelect.value === 'hapus') {
            e.preventDefault();
            
            if (childCount > 0) {
                let childNamesStr = childrenNames ? ' <strong class="text-red-600">(' + childrenNames + ')</strong>' : '';
                document.getElementById('alertModalText').innerHTML = 'Kategori ini memiliki subkategori' + childNamesStr + '. Silakan hapus subkategori di dalamnya terlebih dahulu!';
                alertModal.classList.remove('hidden');
                return;
            }

            let opts = document.getElementById('deleteOptions');
            if(productCount > 0) {
                opts.classList.remove('hidden');
            } else {
                opts.classList.add('hidden');
            }
            
            
            deleteModal.classList.remove('hidden');
        } else {
            // Normal save loading
            let btn = document.getElementById('btnSave');
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            document.getElementById('btnSaveText').innerText = 'Menyimpan...';
            document.getElementById('btnSaveIcon').classList.remove('hidden');
        }
    });

    document.getElementById('confirmDelete').addEventListener('click', function () {
        let btn = this;
        let btnText = document.getElementById('confirmDeleteText');
        let btnIcon = document.getElementById('confirmDeleteIcon');
        
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btnText.innerText = 'Memproses...';
        btnIcon.classList.remove('hidden');

        let deleteProducts = document.querySelector('input[name="product_action"]:checked') ? document.querySelector('input[name="product_action"]:checked').value : '0';
        document.getElementById('single-delete-products').value = deleteProducts;
        deleteModal.classList.add('hidden');
        form.submit();
    });
});
</script>

<!-- Modal Peringatan -->
<div id="alertModal" class="fixed inset-0 lg:left-72 z-[9999] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] md:w-1/3 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Peringatan</h3>
        <p id="alertModalText" class="text-gray-600 mb-6">Pesan peringatan.</p>
        <div class="flex justify-end">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded" onclick="document.getElementById('alertModal').classList.add('hidden')">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Lanjutan -->
<div id="deleteModal" class="fixed inset-0 lg:left-72 z-[9999] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] md:w-1/3 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Hapus</h3>
        <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus kategori ini?</p>
        
        <div id="deleteOptions" class="hidden mb-6 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
            <p class="text-sm text-yellow-800 mb-3 font-medium">Kategori ini masih berisi produk. Pilih tindakan untuk produk tersebut:</p>
            <label class="flex items-start gap-2 mb-2 cursor-pointer">
                <input type="radio" name="product_action" value="0" class="mt-1" checked>
                <span class="text-sm text-gray-700">Hanya hapus kategori (produk tetap ada, relasi kategori dilepas)</span>
            </label>
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="radio" name="product_action" value="1" class="mt-1 text-red-600">
                <span class="text-sm text-red-700 font-medium">Hapus kategori BESERTA semua produk di dalamnya</span>
            </label>
        </div>

        <div class="flex justify-end">
            <button type="button" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded mr-2" onclick="document.getElementById('deleteModal').classList.add('hidden')">Batal</button>
            <button type="button" id="confirmDelete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center gap-2">
                <span id="confirmDeleteText">Ya, Lanjutkan</span>
                <span id="confirmDeleteIcon" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
        </div>
    </div>
</div>
@endpush
