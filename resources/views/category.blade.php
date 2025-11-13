@extends('layouts.app-landingpage')

@section('meta_title')
    {{ $category->meta_title ?? ($category->name . '-' . $siteSettings->site_name) }}
@endsection
@section('meta_description')
    {{ $category->meta_description ?? Str::limit(strip_tags($category->description_short), 160) }}
@endsection
@section('meta_keywords')
    {{ $category->meta_keywords ?? $siteSettings->meta_keywords }}
@endsection
@section('og_image')
     @php
          $publicPath = 'images/category/' . $category->id_category . '.jpg';
          $storagePath = 'storage/category/' . $category->id_category . '.jpg';

          if (file_exists(public_path($storagePath))) {
              $imageUrl = asset($storagePath);
          } elseif (file_exists(public_path($publicPath))) {
              $imageUrl = asset($publicPath);
          }  else {
              $imageUrl = $siteSettings->logo;
          }
      @endphp
    {{ $imageUrl }}
@endsection

@section('content')

{{-- CENTER CONTENT --}}
<main class="order-0 md:order-none">
  <div class="bg-white p-4 text-[13px] text-[#444]">

    <div class="mb-4">
      <a href="{{ route('home') }}" class="text-[#15314b] hover:underline">Home</a>
      <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
      <span class="font-semibold">{{ $category->name }}</span>
    </div>

    <div class="border-t pt-4">
      <div class="font-bold text-lg text-[#333] mb-4">{{ $category->name }}</div>

      @php
          $publicPath = 'images/category/' . $category->id_category . '.jpg';
          $storagePath = 'storage/category/' . $category->id_category . '.jpg';

          if (file_exists(public_path($storagePath))) {
              $imageUrl = asset($storagePath);
          } elseif (file_exists(public_path($publicPath))) {
              $imageUrl = asset($publicPath);
          }  else {
              $imageUrl = asset('images/product/en.jpg');
          }
      @endphp

      <img 
          src="{{ $imageUrl }}" 
          alt="{{ $category->name }}" 
          class="w-full object-contain mb-4 border border-gray-300" 
      />


      {{-- Category description --}}
      @if($category->description)
        <div class="prose text-xs text-gray-600 mb-6 [&_*]:text-xs">
          {!! $category->description !!}
        </div>
      @endif

      {{-- Child categories (if any) --}}
      @if(!empty($children) && count($children) > 0)
        <div class="mb-6 pb-4 border-b">
          <h3 class="font-bold text-[#333] mb-3">Sub Categories</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            @foreach($children as $child)
              <div class="bg-gray-100 text-center p-2 border border-gray-300 text-[12px] hover:shadow-md transition">
                @php
                  $publicPath = 'images/category/' . $child->id_category . '.jpg';
                  $storagePath = 'storage/category/' . $child->id_category . '.jpg';

                  if (file_exists(public_path($storagePath))) {
                      $imageSrc = asset($storagePath);
                  } elseif (file_exists(public_path($publicPath))) {
                      $imageSrc = asset($publicPath);
                  }  else {
                      $imageSrc = asset('images/product/en.jpg');
                  }

                @endphp

                <img 
                  src="{{ $imageSrc }}" 
                  alt="{{ $child->name }}" 
                  class="w-full h-20 object-contain mb-2 border border-gray-300" 
                />

                <a href="{{ url('/category/'.$child->id_category.'-'.$child->slug) }}" class="block">
                  <div class="font-semibold text-xs text-[#15314b] hover:text-[#cf6a00] transition">
                    {{ $child->name }}
                  </div>
                </a>
              </div>
            @endforeach

          </div>
        </div>
      @endif

      {{-- Products section --}}
      @if($productsPaginated->count() > 0)
        <div>
          <div class="flex flex-wrap justify-between items-center bg-[#acb0b8] px-3 py-2 mb-4">
            <div class="font-bold text-white mb-2 md:mb-0">PRODUCTS ({{ $productsPaginated->total() }})</div>
          </div>

          <div class="space-y-3 mb-6">
            @foreach($productsPaginated as $product)
              @php $img = $product->images[0] ?? null; @endphp

              <div class="flex flex-col md:flex-row items-center md:items-stretch border border-gray-300 bg-white p-3 hover:shadow-md transition">
                
                {{-- Kolom 1: Gambar --}}
                <div class="w-full md:w-1/4 flex justify-center items-center bg-gray-100 border border-gray-200 p-2">
                  <img 
                    src="{{ $img ? asset($img) : asset('images/product/en.jpg') }}" 
                    alt="{{ $product->name }}" 
                    class="w-[100px] h-[100px] object-contain"
                  />
                </div>

                {{-- Kolom 2: Nama dan Deskripsi --}}
                <div class="w-full md:w-2/4 px-3 flex flex-col justify-center text-center md:text-left">
                  <div class="font-bold text-[#333] text-sm md:text-base mb-1">{{ $product->name }}</div>
                  <div class="prose text-xs text-gray-500 [&_*]:text-[10px]">
                    {!! $product->description_short !!}
                  </div>
                </div>

                {{-- Kolom 3: Tombol --}}
                <div class="w-full md:w-1/4 flex flex-col justify-center items-center gap-2 mt-3 md:mt-0">
                  <a href="{{ url('/product/'.$product->id_product.'-'.$product->slug) }}"
                    class="px-4 py-2 w-full text-center bg-[#acb0b8] text-white text-xs rounded hover:bg-[#8f949e] transition">
                    <i class="fa-solid fa-eye mr-1"></i> View
                  </a>

                  <a href="https://wa.me/{{ $siteSettings->wa_order }}?text={{ rawurlencode('Halo, saya tertarik dengan produk: ' . $product->name . ' - ' . url('/product/'.$product->id_product.'-'.$product->slug)) }}"
                    target="_blank" rel="noopener noreferrer"
                    class="px-4 py-2 w-full text-center bg-green-600 text-white text-xs rounded hover:bg-green-700 transition">
                    <i class="fa-brands fa-whatsapp mr-1"></i> Order
                  </a>
                </div>
              </div>
            @endforeach
          </div>


          {{-- Pagination --}}
          <div class="flex justify-center mt-6">
            {{ $productsPaginated->links('pagination::simple-bootstrap-4') }}
          </div>
        </div>
      @else
        <div class="text-center py-8">
          <p class="text-gray-500">No products found in this category.</p>
        </div>
      @endif
    </div>
  </div>
</main>

@endsection
