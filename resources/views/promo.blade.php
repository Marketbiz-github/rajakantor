@extends('layouts.app-landingpage')

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


    <div class="border-t pt-4">
      
      {{-- Products section --}}
      @if($productsPaginated->count() > 0)
        <div>
          <div class="flex justify-between items-center bg-[#acb0b8] px-3 py-2 mb-4">
            <div class="font-bold text-white">PRODUCTS ({{ $productsPaginated->total() }})</div>
          </div>

          <div class="space-y-3 mb-6">
            @foreach($productsPaginated as $product)
              @php $img = $product->images[0] ?? null; @endphp

              <div class="flex flex-row items-stretch border border-gray-300 bg-white p-3 hover:shadow-md transition">
                
                {{-- Kolom 1: Gambar --}}
                <div class="w-1/4 flex justify-center items-center bg-gray-100 border border-gray-200 p-2">
                  <img 
                    src="{{ $img ? asset($img) : asset('images/product/en.jpg') }}" 
                    alt="{{ $product->name }}" 
                    class="w-[100px] h-[100px] object-contain"
                  />
                </div>

                {{-- Kolom 2: Nama dan Deskripsi --}}
                <div class="w-2/4 px-3 flex flex-col justify-center text-left">
                  <div class="font-bold text-[#333] text-base mb-1">{{ $product->name }}</div>
                  <div class="prose text-xs text-gray-500 [&_*]:text-[10px]">
                    {!! $product->description_short !!}
                  </div>
                </div>

                {{-- Kolom 3: Tombol --}}
                <div class="w-1/4 flex flex-col justify-center items-center gap-2">
                  <a href="{{ url('/product/'.$product->id_product.'-'.$product->slug) }}"
                    class="px-4 py-2 w-full text-center bg-[#acb0b8] text-white text-xs rounded hover:bg-[#8f949e] transition">
                    <i class="fa-solid fa-eye mr-1"></i> View
                  </a>

                  <button
                    type="button"
                    @click="openOrder('{{ addslashes($product->name) }}','{{ addslashes(url('/product/'.$product->id_product.'-'.$product->slug)) }}')"
                    class="px-4 py-2 w-full text-center bg-green-600 text-white text-xs rounded hover:bg-green-700 transition">
                    <i class="fa-brands fa-whatsapp mr-1"></i> Order
                  </button>
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
            <input x-model="order.name" type="text" class="w-full border p-2 text-sm rounded" />
          </div>

          <div>
            <label class="text-xs font-semibold">Email</label>
            <input x-model="order.email" type="email" class="w-full border p-2 text-sm rounded" />
          </div>

          <div>
            <label class="text-xs font-semibold">Qty</label>
            <input x-model.number="order.qty" type="number" min="1" class="w-full border p-2 text-sm rounded" />
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
  </div>
</main>

@endsection
