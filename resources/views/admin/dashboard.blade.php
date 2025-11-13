@extends('layouts.app-admin')

@section('title', 'Dashboard')

@section('content')
<div class="">
    <x-breadcrumb />

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Product -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-teal-50">
                        <i class="fas fa-home w-6 h-6 text-teal-600"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="font-medium text-gray-900">Total Active Products</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $totalProducts }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Categorties --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-50">
                        <i class="fas fa-tags w-6 h-6 text-blue-600"></i>
                    </div>
                    <div class="ml-5">
                        <h3 class="font-medium text-gray-900">Total Active Categories</h3>
                        <p class="mt-1 text-3xl font-semibold text-gray-700">{{ $totalCategories }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection