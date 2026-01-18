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
<main
  x-data="{
      showOrderModal: false,
      waNumber: '',
      order: {
          name: '',
          email: '',
          qty: 1,
          notes: '',
          productName: '',
          productUrl: ''
      },
      openOrder(productName, productUrl) {
          this.order.productName = productName
          this.order.productUrl = productUrl
          this.order.qty = 1
          this.showOrderModal = true
      },
      sendOrder() {
          if (!this.order.name) {
              alert('Masukkan nama terlebih dahulu')
              return
          }
          if (!this.order.email) {
              alert('Masukkan email terlebih dahulu')
              return
          }
          if (this.order.qty < 1) {
              alert('Jumlah pemesanan minimal 1')
              return
          }

          const msg =
              'Halo, saya tertarik dengan produk: ' + this.order.productName + '\n' +
              this.order.productUrl + '\n\n' +
              'Nama: ' + this.order.name + '\n' +
              'Email: ' + this.order.email + '\n' +
              'Qty: ' + this.order.qty + '\n' +
              'Catatan: ' + this.order.notes

          const url = 'https://wa.me/' + this.waNumber + '?text=' + encodeURIComponent(msg)
          window.open(url, '_blank')
          this.showOrderModal = false
      }
  }"
  x-init="waNumber = '{{ $siteSettings->wa_order }}'"
  class="order-0 md:order-none"
  >

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
      <div class="flex flex-row gap-6">
        <!-- Kolom kiri: Gambar + tombol -->
        <div class="flex flex-col items-center gap-4 w-1/2">
          @php
            $images = $product->images ?? [];
            $imageUrls = collect($images)->map(fn($i) => asset($i))->toArray();
          @endphp

          <div x-data="{ active: 0, showZoom: false, imgs: {{ Js::from($imageUrls) }} }" class="w-full">
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

            <!-- WhatsApp order (open modal) -->
            <button
              type="button"
              @click="openOrder('{{ addslashes($product->name) }}','{{ addslashes(url()->current()) }}')"
              class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition"
            >
              <i class="fa-brands fa-whatsapp mr-2"></i> Order
            </button>
          </div>

          <!-- Order Modal -->
          <div x-show="showOrderModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
            <div class="bg-white w-[95%] max-w-lg p-4 rounded">
              <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold">Order: <span x-text="order.productName"></span></h3>
                <button @click="showOrderModal = false" class="text-gray-600">✕</button>
              </div>

              <div class="space-y-3 p-3">
                <div>
                  <label class="text-xs font-semibold">Nama</label>
                  <input x-model="order.name" type="text" class="w-full border p-2 text-sm rounded" required/>
                </div>

                <div>
                  <label class="text-xs font-semibold">Email</label>
                  <input x-model="order.email" type="email" class="w-full border p-2 text-sm rounded" required/>
                </div>

                <div>
                  <label class="text-xs font-semibold">Qty</label>
                  <input x-model.number="order.qty" type="number" min="1" class="w-full border p-2 text-sm rounded" required/>
                </div>

                <div>
                  <label class="text-xs font-semibold">Catatan</label>
                  <textarea x-model="order.notes" class="w-full border p-2 text-sm rounded" rows="3"></textarea>
                </div>

                <div class="flex gap-2 justify-end">
                  <button @click="showOrderModal = false" type="button" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                  <button @click="sendOrder()" type="button" class="px-4 py-2 bg-green-600 text-white rounded">Kirim ke WhatsApp</button>
                </div>
              </div>
            </div>
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
