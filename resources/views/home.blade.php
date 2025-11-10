@extends('layouts.app-landingpage')

@section('content')

{{-- CENTER CONTENT --}}
<main class="order-0 md:order-none">
  <div class="bg-white">

    <div class="p-4 text-[13px] text-[#444]">
      <img src="{{ asset($siteSettings->banner_home_top) }}" alt="Banner Top" class="w-full mb-4" />
      <p class="prose text-gray-700 [&_*]:text-xs">{!! ($siteSettings->home_description) !!}</p>

      {{-- PRODUCTS DISPLAY --}}
      <div class="mt-6">
        <div class="flex flex-wrap justify-between items-center bg-[#acb0b8] px-3 py-2">
          <div class="font-bold text-white mb-2 md:mb-0">PRODUCTS DISPLAY</div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1 mt-3">
          @foreach($products as $product)
          <div class="bg-gray-100 text-center p-2 border border-gray-300 text-[12px] hover:shadow-md transition">
            @php $img = $product->images[0] ?? null; @endphp
            <div class="font-bold text-[#333] mt-1">{{ $product->name }}</div>
            <img src="{{ $img ? asset($img) : asset('images/product/en.jpg') }}" alt="{{ $product->name }}"
                  class="w-[72px] h-[72px] object-contain mx-auto mt-2 mb-4" />
            <a href="{{ url('/product/'.$product->id_product.'-'.$product->slug) }}" class="text-xs text-gray-600 hover:underline mt-1 block"><i class="fa-solid fa-eye mr-1"></i> View</a>
          </div>
          @endforeach
        </div>
      </div>

      <img src="{{ asset($siteSettings->banner_home_bottom) }}" alt="Banner Bottom" class="w-full mt-4" />
    </div>
  </div>
</main>

@endsection
