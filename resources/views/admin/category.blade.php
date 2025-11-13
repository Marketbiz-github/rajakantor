@extends('layouts.app-admin')

@section('title', 'Kategori')

@section('content')
<div>
    <x-breadcrumb :items="[['label' => 'Kategori']]" />

    <div class="mt-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-xl font-semibold text-gray-900">Daftar Kategori</h1>
            <a href="{{ route('category.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded shadow flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Tambah Kategori
            </a>
        </div>

        <x-data-table 
            :data="$categories"
            :columns="[
                ['key' => 'index', 'label' => 'No.', 'type' => 'index'],
                ['key' => 'id_category', 'label' => 'ID', 'type' => 'text'],
                ['key' => 'name', 'label' => 'Nama Kategori', 'type' => 'text'],
                ['key' => 'slug', 'label' => 'Slug', 'type' => 'text'],
                ['key' => 'status', 'label' => 'Status', 'type' => 'status'],
                ['key' => 'updated_at', 'label' => 'Terakhir Diperbarui', 'type' => 'date'],
            ]"
            route-prefix="categories"
            :searchable="true"
            :search-fields="['name','id_category','slug']"
            default-sort="name"
            :default-per-page="10"
            :custom-actions="[
                [
                    'label' => 'Edit',
                    'url' => '/dashboard/category/{id}/edit',
                    'target' => '',
                    'class' => 'text-teal-600 hover:underline'
                ],
                [
                    'label' => 'View',
                    'url' => '/category/{id_category}-{slug}',
                    'target' => '_blank',
                    'class' => 'text-blue-600 hover:underline'
                ]
            ]"
        />
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dataTable', ({
        data = [],
        columns = [],
        hasActions = false,
        routePrefix = '',
        searchFields = ['name'],
        defaultPerPage = 10,
        customActions = []
    }) => ({
        data,
        columns,
        hasActions,
        routePrefix,
        searchFields,
        search: '',
        sortColumn: null,
        sortAsc: false,
        currentPage: 1,
        perPage: defaultPerPage,
        customActions,

        get filteredData() {
            let filtered = this.data;
            if (this.search) {
                const searchTerm = this.search.toLowerCase();
                filtered = filtered.filter(item =>
                    this.searchFields.some(field =>
                        (item[field] || '').toString().toLowerCase().includes(searchTerm)
                    )
                );
            }
            filtered = filtered.sort((a, b) => {
                if (!this.sortColumn) return 0;
                let modifier = this.sortAsc ? 1 : -1;
                let aVal = a[this.sortColumn];
                let bVal = b[this.sortColumn];
                if (!isNaN(aVal) && !isNaN(bVal)) {
                    return (Number(aVal) - Number(bVal)) * modifier;
                }
                aVal = aVal?.toString().toLowerCase() ?? '';
                bVal = bVal?.toString().toLowerCase() ?? '';
                return aVal < bVal ? -1 * modifier : aVal > bVal ? 1 * modifier : 0;
            });
            return filtered;
        },

        get totalPages() {
            return Math.ceil(this.filteredData.length / this.perPage);
        },

        get startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },

        get endIndex() {
            return this.startIndex + this.perPage;
        },

        get paginatedData() {
            return this.filteredData.slice(this.startIndex, this.endIndex).map((item, index) => ({
                ...item,
                index: this.startIndex + index + 1
            }));
        },

        previousPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        nextPage() {
            if (this.currentPage < this.totalPages) this.currentPage++;
        },

        sort(column) {
            if (this.sortColumn === column) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortColumn = column;
                this.sortAsc = true;
            }
        },

        formatColumnValue(item, column) {
            const value = item[column.key];
            switch (column.type) {
                case 'index':
                    return item.index;
                case 'price':
                    return this.formatPrice(value);
                case 'date':
                    return this.formatDate(value);
                case 'status':
                    return this.formatStatus(value);
                default:
                    return value ?? '';
            }
        },

        formatDate(date) {
            return date ? new Date(date).toLocaleDateString('id-ID') : '';
        },

        formatStatus(status) {
            const statusClasses = {
                1: 'text-green-600 bg-green-100',
                2: 'text-red-600 bg-red-100'
            };
            return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClasses[status] || ''}">${status}</span>`;
        },

        displayedPages() {
            const pages = [];
            const maxPages = 7;
            const total = this.totalPages;
            
            if (total <= maxPages) {
                for (let i = 1; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                const start = Math.max(1, this.currentPage - 1);
                const end = Math.min(total, this.currentPage + 1);
                
                for (let i = 1; i <= 3; i++) {
                    pages.push(i);
                }
                
                if (start > 4) {
                    pages.push('...');
                }
                
                for (let i = start; i <= end; i++) {
                    if (i > 3 && i < total - 2) {
                        if (!pages.includes(i)) {
                            pages.push(i);
                        }
                    }
                }
                
                if (end < total - 3) {
                    if (!pages.includes('...')) {
                        pages.push('...');
                    }
                }
                
                for (let i = Math.max(1, total - 2); i <= total; i++) {
                    if (!pages.includes(i)) {
                        pages.push(i);
                    }
                }
            }
            
            return pages;
        },
        
    }));

    window.dataTable = (config) => Alpine.reactive(Alpine.data('dataTable')(config));
});
</script>
@endpush
