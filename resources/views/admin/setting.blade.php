@extends('layouts.app-admin')

@section('title', 'Site Settings')

@section('content')
<div class="max-w-6xl mx-auto mt-8 px-4">
    <x-breadcrumb :items="[['label' => 'Settings', 'url' => route('settings.edit')], ['label' => 'Edit Settings']]" />

    <div class="bg-white rounded-lg shadow-sm border p-8">
        <h2 class="text-xl font-bold mb-6">Edit Site Settings</h2>

        @if(session('success'))
            <div class="bg-green-50 text-green-700 px-4 py-3 rounded-md mb-6 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-md mb-6 border border-red-200">
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm">Site Name</label>
                    <input type="text" name="site_name" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('site_name', $settings->site_name ?? '') }}">
                </div>

                <div>
                    <label class="block mb-2 text-sm">Meta Title</label>
                    <input type="text" name="meta_title" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('meta_title', $settings->meta_title ?? '') }}">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6 mt-4">
                <div>
                    <label class="block mb-2 text-sm">Logo</label>
                    @if(!empty($settings->logo) && file_exists(public_path($settings->logo)))
                        <div class="mb-2"><img src="{{ asset($settings->logo) }}" alt="logo" class="h-20 object-contain"></div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block mb-2 text-sm">Favicon</label>
                    @if(!empty($settings->favicon) && file_exists(public_path($settings->favicon)))
                        <div class="mb-2"><img src="{{ asset($settings->favicon) }}" alt="favicon" class="h-12 object-contain"></div>
                    @endif
                    <input type="file" name="favicon" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block mb-2 text-sm">WhatsApp</label>
                    <input type="text" name="wa" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('wa', $settings->wa ?? '') }}">
                    <label class="block mt-2 mb-2 text-sm">WA Order</label>
                    <input type="text" name="wa_order" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('wa_order', $settings->wa_order ?? '') }}">
                </div>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">Meta Keywords</label>
                <input type="text" name="meta_keywords" class="w-full border border-gray-300 rounded-lg px-3 py-2" value="{{ old('meta_keywords', $settings->meta_keywords ?? '') }}">
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">Meta Description</label>
                <textarea name="meta_description" class="w-full border border-gray-300 rounded-lg px-3 py-2" rows="3">{{ old('meta_description', $settings->meta_description ?? '') }}</textarea>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">Home Description</label>
                <textarea name="home_description" id="home_description" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('home_description', $settings->home_description ?? '') }}</textarea>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">About</label>
                <textarea name="about" id="about" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('about', $settings->about ?? '') }}</textarea>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">Information</label>
                <textarea name="information" id="information" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('information', $settings->information ?? '') }}</textarea>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">Slider Images (you may upload multiple)</label>
                @php
                    $currentSliders = [];
                    if (!empty($settings->slider)) {
                        $decoded = json_decode($settings->slider, true);
                        if (is_array($decoded)) $currentSliders = $decoded;
                    }
                @endphp
                <div class="flex gap-3 mb-3">
                    @foreach($currentSliders as $s)
                        @if(file_exists(public_path($s)))
                            <img src="{{ asset($s) }}" class="h-20 object-cover rounded" alt="slider">
                        @endif
                    @endforeach
                </div>
                <input type="file" name="slider[]" accept="image/*" multiple class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>

            <div class="grid grid-cols-3 gap-6 mt-4">
                <div>
                    <label class="block mb-2 text-sm">Banner Sidebar</label>
                    @if(!empty($settings->banner_sidebar) && file_exists(public_path($settings->banner_sidebar)))
                        <div class="mb-2"><img src="{{ asset($settings->banner_sidebar) }}" alt="banner" class="h-24 object-contain"></div>
                    @endif
                    <input type="file" name="banner_sidebar" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block mb-2 text-sm">Banner Home Top</label>
                    @if(!empty($settings->banner_home_top) && file_exists(public_path($settings->banner_home_top)))
                        <div class="mb-2"><img src="{{ asset($settings->banner_home_top) }}" alt="banner" class="h-24 object-contain"></div>
                    @endif
                    <input type="file" name="banner_home_top" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block mb-2 text-sm">Banner Home Bottom</label>
                    @if(!empty($settings->banner_home_bottom) && file_exists(public_path($settings->banner_home_bottom)))
                        <div class="mb-2"><img src="{{ asset($settings->banner_home_bottom) }}" alt="banner" class="h-24 object-contain"></div>
                    @endif
                    <input type="file" name="banner_home_bottom" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">Terms</label>
                <textarea name="terms" id="terms" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('terms', $settings->terms ?? '') }}</textarea>
            </div>

            <div class="mt-4">
                <label class="block mb-2 text-sm">Client (HTML allowed)</label>
                <textarea name="client" id="client" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('client', $settings->client ?? '') }}</textarea>
            </div>

            

            <div class="flex justify-end mt-6">
                <a href="{{ route('dashboard') }}" class="bg-gray-400 text-white px-4 py-2 rounded mr-2">Back</a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save Settings</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tinySelectors = ['#about', '#information', '#terms', '#client', '#home_description'];
    tinySelectors.forEach(function (sel) {
        if (document.querySelector(sel)) {
            tinymce.init({
                selector: sel,
                menubar: false,
                plugins: 'link lists code',
                toolbar: 'undo redo | bold italic underline | bullist numlist | link | code',
                height: 250
            });
        }
    });
});
</script>
@endpush