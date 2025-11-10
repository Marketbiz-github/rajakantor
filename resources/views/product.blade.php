@extends('layouts.app-landingpage')

@section('meta_title')
    {{ $product->meta_title ?? ($product->name . '-' . $siteSettings->site_name) }}
@endsection
@section('meta_description')
    {{ $product->meta_description ?? Str::limit(strip_tags($product->description_short), 160) }}
@endsection
@section('meta_keywords')
    {{ $product->meta_keywords ?? $siteSettings->meta_keywords }}
@endsection
@section('og_image')
    @php
        $img = $product->images[0] ?? null;
    @endphp
    {{ $img ? asset($img) : $siteSettings->logo }}
@endsection

@section('content')
{{-- CENTER CONTENT --}}
<main class="order-0 md:order-none">
  <div class="bg-white p-4 text-[13px] text-[#444]">

    <a href="{{ route('home') }}" class="text-[#15314b] hover:underline">Home</a>
    <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
    @if(!empty($categoryTrail) && count($categoryTrail) > 0)
      @foreach($categoryTrail as $i => $cat)
        <a href="{{ url('/category/'.$cat['id_category'].'-'.$cat['slug']) }}" class="text-[#15314b] hover:underline">{{ $cat['name'] }}</a>
        @if($i < count($categoryTrail) - 1)
          <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
        @endif
      @endforeach
      <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
    @else
      <span class="text-[#15314b]">Category</span>
      <i class="fa-solid fa-chevron-right text-xs mx-2"></i>
    @endif
    <span class="font-semibold">{{ $product->name }}</span>

    <div class="mt-4 border-t pt-4 pb-12">
      <!-- Nama produk -->
      <div class="font-bold text-base text-[#333] mb-4">{{ $product->name }}</div>

      <!-- Konten utama -->
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Kolom kiri: Gambar + tombol -->
        <div class="flex flex-col items-center md:items-start gap-4 md:w-1/2">
          @php
            $images = $product->images ?? [];
            $imageUrls = collect($images)->map(fn($i) => asset($i))->toArray();
          @endphp

          <div x-data="{ active: 0, showZoom: false, imgs: {{ Js::from($imageUrls) }} }" class="w-[80%] md:w-full">
            <div class="border border-gray-300 flex items-center justify-center bg-gray-100">
              <template x-if="imgs && imgs.length">
                <img
                  :src="imgs[active]"
                  :alt="'{{ addslashes($product->name) }}'
                  "
                  class="max-w-full max-h-[350px] object-contain cursor-zoom-in"
                  @click="showZoom = true"
                />
              </template>

              <template x-if="!imgs || imgs.length === 0">
                <img src="{{ asset('images/product/en.jpg') }}" alt="{{ $product->name }}" class="max-w-full max-h-[350px] object-contain" />
              </template>
            </div>

            <!-- Thumbnails -->
            <div class="mt-3 flex gap-2 overflow-x-auto">
              <template x-for="(img, idx) in imgs" :key="idx">
                <button type="button" @click="active = idx" class="flex-shrink-0">
                  <img :src="img" :alt="'thumb-'+idx" class="w-16 h-16 object-cover border rounded transition" :class="{'ring-2 ring-[#cf6a00]': active === idx}" />
                </button>
              </template>
            </div>

            <!-- Zoom modal -->
            <div x-show="showZoom" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70">
              <div class="relative max-w-[90%] max-h-[90%]">
                <button @click="showZoom = false" class="absolute top-2 right-2 z-50 bg-white p-2 rounded-full">✕</button>
                <img :src="imgs[active]" class="max-w-full max-h-[90vh] object-contain" />
              </div>
            </div>
          </div>

          <div class="flex gap-2 flex-wrap justify-center md:justify-start mt-2">
            <!-- Facebook share -->
            <a
              href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition"
            >
              <i class="fa-brands fa-facebook-f mr-2"></i> Share
            </a>

            <!-- WhatsApp order -->
            <a
              href="https://wa.me/{{ $siteSettings->wa_order }}?text={{ rawurlencode('Halo, saya tertarik dengan produk: ' . $product->name . ' - ' . url()->current()) }}"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition"
            >
              <i class="fa-brands fa-whatsapp mr-2"></i> Order
            </a>
          </div>
        </div>

        <!-- Kolom kanan: Deskripsi -->
        <div class="flex-1">
          <!-- Deskripsi singkat -->
          <div class="prose text-gray-600 [&_*]:text-xs">
            {!! ($product->description_short) !!}
          </div>

          
        </div>
      </div>

      <!-- More info -->
      <div class="mt-6">
        <div class="flex justify-between items-center bg-[#acb0b8] px-3 py-2">
          <div class="font-bold text-white">More Info</div>
        </div>

        <div class="prose mt-3 text-gray-600 [&_*]:text-xs">
          {!! ($product->description) !!}
        </div>
      </div>
    </div>


    
  </div>
</main>

@endsection
