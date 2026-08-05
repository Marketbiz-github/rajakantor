@extends('layouts.app-admin')

@section('title', 'Kategori')

@section('content')
<div>
    <x-breadcrumb :items="[['label' => 'Kategori']]" />

    <div class="mt-8">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-semibold text-gray-900">Daftar Kategori</h1>
                <button type="button" onclick="document.getElementById('helpModal').classList.remove('hidden')" class="text-gray-400 hover:text-blue-600 transition-colors" title="Informasi Penghapusan Kategori">
                    <i class="fas fa-question-circle text-lg"></i>
                </button>
            </div>
            <a href="{{ route('category.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded shadow flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Kategori
            </a>
        </div>

        <form method="GET" action="{{ route('category.index') }}" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID atau Nama Kategori..." class="border border-gray-300 rounded px-3 py-2 w-full md:w-64">
            
            <select name="status" class="border border-gray-300 rounded px-3 py-2 w-full md:w-48">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('category.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded flex items-center">Reset</a>
            @endif
        </form>

        <form method="POST" action="{{ route('category.bulk-destroy') }}" id="bulk-delete-form">
            @csrf
            <div class="mb-4">
                <button type="button" id="btn-bulk-delete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center gap-2">
                    <i class="fas fa-trash"></i> Hapus Terpilih
                </button>
            </div>
            
        <div class="bg-white rounded-lg shadow-sm border overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600">
                        <th class="p-4 text-sm font-semibold w-10">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </th>
                        <th class="p-4 text-sm font-semibold">No</th>
                        <th class="p-4 text-sm font-semibold">ID</th>
                        <th class="p-4 text-sm font-semibold">Nama Kategori</th>
                        <th class="p-4 text-sm font-semibold">Kategori Induk</th>
                        <th class="p-4 text-sm font-semibold">Slug</th>
                        <th class="p-4 text-sm font-semibold">Status</th>
                        <th class="p-4 text-sm font-semibold">Terakhir Diperbarui</th>
                        <th class="p-4 text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($categories as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm text-gray-700">
                            <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="category-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" data-child="{{ $item->child_count }}" data-product="{{ $item->product_count }}" data-children="{{ $item->children_names }}">
                        </td>
                        <td class="p-4 text-sm text-gray-700">{{ $categories->firstItem() + $index }}</td>
                        <td class="p-4 text-sm text-gray-700">{{ $item->id_category }}</td>
                        <td class="p-4 text-sm text-gray-900 font-medium whitespace-normal min-w-[200px]">{{ $item->name }}</td>
                        <td class="p-4 text-sm text-gray-500 italic">{{ $item->parent_name ? $item->parent_name : '-' }}</td>
                        <td class="p-4 text-sm text-gray-700">{{ $item->slug }}</td>
                        <td class="p-4 text-sm">
                            @if($item->status == 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-green-700 bg-green-100 border border-green-200">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-red-700 bg-red-100 border border-red-200">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-gray-700">{{ $item->updated_at ? date('d/m/Y H:i', strtotime($item->updated_at)) : '-' }}</td>
                        <td class="p-4 text-sm flex gap-3">
                            <a href="{{ url('/dashboard/category/'.$item->id.'/edit') }}" class="text-teal-600 hover:text-teal-800 font-medium"><i class="fas fa-edit mr-1"></i>Edit</a>
                            <a href="{{ url('/category/'.$item->id_category.'-'.$item->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-external-link-alt mr-1"></i>View</a>
                            <button type="button" onclick="deleteCategory({{ $item->id }}, {{ $item->child_count }}, {{ $item->product_count }}, '{{ htmlspecialchars($item->children_names ?? '', ENT_QUOTES) }}')" class="text-red-600 hover:text-red-800 font-medium"><i class="fas fa-trash-alt mr-1"></i>Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-sm text-gray-500">
                            <i class="fas fa-box-open text-3xl mb-3 text-gray-300 block"></i>
                            Tidak ada data kategori ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </form>
        
        <div class="mt-6">
            {{ $categories->appends(['search' => request('search'), 'status' => request('status')])->links('components.pagination') }}
        </div>
    </div>
</div>

<form id="single-delete-form" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
    <input type="hidden" name="delete_products" id="single-delete-products" value="0">
</form>

<!-- Modal Peringatan -->
<div id="alertModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] md:w-1/3 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Peringatan</h3>
        <p id="alertModalText" class="text-gray-600 mb-6">Pesan peringatan.</p>
        <div class="flex justify-end">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded" onclick="document.getElementById('alertModal').classList.add('hidden')">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Bantuan Hapus Kategori -->
<div id="helpModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] md:w-1/2 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-600"></i>
            Informasi Penghapusan Kategori
        </h3>
        <div class="text-gray-700 space-y-3 mb-6 text-sm">
            <p>Menghapus kategori memiliki aturan khusus untuk menjaga agar data produk Anda tidak hilang secara tidak sengaja:</p>
            <ul class="list-disc pl-5 space-y-2">
                <li><strong class="text-red-600">Proteksi Subkategori:</strong> Anda tidak bisa menghapus kategori yang masih memiliki subkategori (kategori turunan). Hapus subkategorinya terlebih dahulu.</li>
                <li><strong class="text-yellow-600">Opsi Hapus Produk:</strong> Jika kategori yang akan dihapus memiliki produk, Anda akan diberikan 2 pilihan:
                    <ul class="list-circle pl-5 mt-1 space-y-1">
                        <li><strong>Hanya hapus kategori:</strong> Kategori akan terhapus, tetapi produk akan tetap ada di database (hanya dilepas dari kategori ini).</li>
                        <li><strong class="text-red-600">Hapus beserta produk:</strong> Kategori beserta <u>seluruh produk</u> di dalamnya akan dihapus secara permanen, termasuk seluruh gambarnya. <b>Gunakan opsi ini dengan sangat hati-hati!</b></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="flex justify-end">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded" onclick="document.getElementById('helpModal').classList.add('hidden')">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Lanjutan -->
<div id="deleteModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] md:w-1/3 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Hapus</h3>
        <p id="deleteModalText" class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus kategori ini?</p>
        
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
            <button type="button" id="confirmDelete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Ya, Lanjutkan</button>
        </div>
    </div>
</div>

<script>
    let actionToConfirm = null;
    let singleDeleteId = null;

    document.getElementById('select-all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.category-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.getElementById('btn-bulk-delete').addEventListener('click', function(e) {
        e.preventDefault();
        let checkboxes = document.querySelectorAll('.category-checkbox:checked');
        if(checkboxes.length === 0) {
            document.getElementById('alertModalText').innerText = 'Silakan pilih kategori terlebih dahulu!';
            document.getElementById('alertModal').classList.remove('hidden');
            return;
        }
        
        let hasChildren = false;
        let hasProducts = false;
        let childrenList = [];
        checkboxes.forEach(cb => {
            if(parseInt(cb.getAttribute('data-child')) > 0) {
                hasChildren = true;
                if(cb.getAttribute('data-children')) {
                    childrenList.push(cb.getAttribute('data-children'));
                }
            }
            if(parseInt(cb.getAttribute('data-product')) > 0) hasProducts = true;
        });

        if(hasChildren) {
            let childNamesStr = childrenList.length > 0 ? ' (' + childrenList.join(', ') + ')' : '';
            document.getElementById('alertModalText').innerHTML = 'Kategori yang dipilih memiliki subkategori <strong class="text-red-600">' + childNamesStr + '</strong>. Silakan hapus subkategori di dalamnya terlebih dahulu!';
            document.getElementById('alertModal').classList.remove('hidden');
            return;
        }

        actionToConfirm = 'bulk';
        document.getElementById('deleteModalText').innerText = 'Apakah Anda yakin ingin menghapus kategori yang dipilih?';
        
        let opts = document.getElementById('deleteOptions');
        if(hasProducts) {
            opts.classList.remove('hidden');
        } else {
            opts.classList.add('hidden');
        }
        
        document.getElementById('deleteModal').classList.remove('hidden');
    });

    function deleteCategory(id, childCount, productCount, childrenNames) {
        if(childCount > 0) {
            let childNamesStr = childrenNames ? ' <strong class="text-red-600">(' + childrenNames + ')</strong>' : '';
            document.getElementById('alertModalText').innerHTML = 'Kategori ini memiliki subkategori' + childNamesStr + '. Silakan hapus subkategori di dalamnya terlebih dahulu!';
            document.getElementById('alertModal').classList.remove('hidden');
            return;
        }

        actionToConfirm = 'single';
        singleDeleteId = id;
        document.getElementById('deleteModalText').innerText = 'Apakah Anda yakin ingin menghapus kategori ini?';
        
        let opts = document.getElementById('deleteOptions');
        if(productCount > 0) {
            opts.classList.remove('hidden');
        } else {
            opts.classList.add('hidden');
        }

        document.getElementById('deleteModal').classList.remove('hidden');
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        let deleteProducts = document.querySelector('input[name="product_action"]:checked').value;
        
        if(actionToConfirm === 'bulk') {
            let bulkForm = document.getElementById('bulk-delete-form');
            let hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'delete_products';
            hiddenInput.value = deleteProducts;
            bulkForm.appendChild(hiddenInput);
            bulkForm.submit();
        } else if(actionToConfirm === 'single') {
            let form = document.getElementById('single-delete-form');
            document.getElementById('single-delete-products').value = deleteProducts;
            form.action = '{{ url("/dashboard/category") }}/' + singleDeleteId;
            form.submit();
        }
    });
</script>
@endsection
