@extends('layouts.app-admin')

@section('title', 'Produk')

@section('content')
<div>
    <x-breadcrumb :items="[['label' => 'Produk']]" />

    <div class="mt-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-xl font-semibold text-gray-900">Daftar Produk</h1>
                <a href="{{ route('product.create') }}"
                   class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded shadow flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Tambah Produk
                </a>
            </div>

        <form method="GET" action="{{ route('product.index') }}" class="mb-4 flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ID atau Nama Produk..." class="border border-gray-300 rounded px-3 py-2 w-full md:w-64">
            
            <select name="status" class="border border-gray-300 rounded px-3 py-2 w-full md:w-48">
                <option value="">Semua Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('product.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded flex items-center">Reset</a>
            @endif
        </form>

        <form method="POST" action="{{ route('product.bulk-destroy') }}" id="bulk-delete-form">
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
                        <th class="p-4 text-sm font-semibold">Nama Produk</th>
                        <th class="p-4 text-sm font-semibold">Kategori</th>
                        <th class="p-4 text-sm font-semibold">Status</th>
                        <th class="p-4 text-sm font-semibold">Terakhir Diperbarui</th>
                        <th class="p-4 text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $index => $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm text-gray-700">
                            <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="product-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </td>
                        <td class="p-4 text-sm text-gray-700">{{ $products->firstItem() + $index }}</td>
                        <td class="p-4 text-sm text-gray-700">{{ $item->id_product }}</td>
                        <td class="p-4 text-sm text-gray-900 font-medium whitespace-normal min-w-[200px]">{{ $item->name }}</td>
                        <td class="p-4 text-sm text-gray-700">{{ $item->category_name }}</td>
                        <td class="p-4 text-sm">
                            @if($item->status == 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-green-700 bg-green-100 border border-green-200">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-red-700 bg-red-100 border border-red-200">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="p-4 text-sm text-gray-700">{{ $item->updated_at ? date('d/m/Y H:i', strtotime($item->updated_at)) : '-' }}</td>
                        <td class="p-4 text-sm flex gap-3">
                            <a href="{{ url('/dashboard/product/'.$item->id.'/edit') }}" class="text-teal-600 hover:text-teal-800 font-medium"><i class="fas fa-edit mr-1"></i>Edit</a>
                            <a href="{{ url('/product/'.$item->id_product.'-'.$item->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium"><i class="fas fa-external-link-alt mr-1"></i>View</a>
                            <button type="button" onclick="deleteSingle({{ $item->id }})" class="text-red-600 hover:text-red-800 font-medium"><i class="fas fa-trash-alt mr-1"></i>Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-sm text-gray-500">
                            <i class="fas fa-box-open text-3xl mb-3 text-gray-300 block"></i>
                            Tidak ada data produk ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </form>
        
        <div class="mt-6">
            {{ $products->appends(['search' => request('search')])->links('components.pagination') }}
        </div>
    </div>
</div>

<form id="single-delete-form" action="" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- Modal Konfirmasi Hapus -->
<div id="deleteModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-[90%] md:w-1/3 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Konfirmasi Hapus</h3>
        <p id="deleteModalText" class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus produk ini beserta seluruh datanya? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end">
            <button type="button" id="cancelDelete" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded mr-2" onclick="document.getElementById('deleteModal').classList.add('hidden')">Batal</button>
            <button type="button" id="confirmDelete" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Ya, Hapus Produk</button>
        </div>
    </div>
</div>

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

<script>
    let actionToConfirm = null;
    let singleDeleteId = null;

    document.getElementById('select-all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    document.getElementById('btn-bulk-delete').addEventListener('click', function(e) {
        e.preventDefault();
        let checked = document.querySelectorAll('.product-checkbox:checked').length;
        if(checked === 0) {
            document.getElementById('alertModalText').innerText = 'Silakan pilih produk terlebih dahulu!';
            document.getElementById('alertModal').classList.remove('hidden');
            return;
        }
        actionToConfirm = 'bulk';
        document.getElementById('deleteModalText').innerText = 'Apakah Anda yakin ingin menghapus produk yang dipilih beserta datanya? Tindakan ini tidak dapat dibatalkan.';
        document.getElementById('deleteModal').classList.remove('hidden');
    });

    function deleteSingle(id) {
        actionToConfirm = 'single';
        singleDeleteId = id;
        document.getElementById('deleteModalText').innerText = 'Apakah Anda yakin ingin menghapus produk ini beserta seluruh datanya? Tindakan ini tidak dapat dibatalkan.';
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if(actionToConfirm === 'bulk') {
            document.getElementById('bulk-delete-form').submit();
        } else if(actionToConfirm === 'single') {
            let form = document.getElementById('single-delete-form');
            form.action = '{{ url("/dashboard/product") }}/' + singleDeleteId;
            form.submit();
        }
    });
</script>
@endsection
