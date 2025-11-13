<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />

  <title>@yield('meta_title', $siteSettings->meta_title ?? 'Raja Kantor')</title>
  <meta name="description" content="@yield('meta_description', $siteSettings->meta_description ?? '')">
  <meta name="keywords" content="@yield('meta_keywords', $siteSettings->meta_keywords ?? '')">

  <!-- Open Graph -->
  <meta property="og:title" content="@yield('meta_title', $siteSettings->meta_title ?? 'Raja Kantor')">
  <meta property="og:description" content="@yield('meta_description', $siteSettings->meta_description ?? '')">
  <meta property="og:image" content="@yield('og_image', asset($siteSettings->logo))">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:site_name" content="{{ env('APP_NAME', 'Raja Kantor') }}">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="@yield('meta_title', $siteSettings->meta_title ?? 'Raja Kantor')">
  <meta name="twitter:description" content="@yield('meta_description', $siteSettings->meta_description ?? '')">
  <meta name="twitter:image" content="@yield('og_image', asset($siteSettings->logo))">

  <link rel="icon" type="image/png" href="{{ asset($siteSettings->favicon) }}">
  
  @vite('resources/css/app.css')
  <script src="//unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/all.min.css"/>

  @yield('style')
</head>
<body class="min-h-screen bg-[#31302c] text-[#222] font-[Verdana,Arial,Helvetica,sans-serif] antialiased">

  <div class="max-w-[965px] mx-auto bg-[#f5f5f5]">

  {{-- NAVBAR --}}
  <div class="bg-white text-[12px] p-[6px_12px] flex justify-end gap-3">
    <a href="{{ route('home') }}" class="text-[#555] no-underline">
      <h1>Harga kursi kantor - Harga meja kantor - Peralatan Kantor</h1>
    </a>
  </div>

  <nav class="bg-white">
    <ul class="flex gap-2 p-[8px_12px] list-none m-0">
      <li>
        <a href="{{ route('home') }}" class="block md:px-3 px-1 py-2 text-[#222] font-bold md:text-[12px] text-[10px] rounded">
          HOME
        </a>
      </li>
      <li>
        <a href="{{ route('about') }}" class="block md:px-3 px-1 py-2 text-[#222] font-bold md:text-[12px] text-[10px] rounded">
          ABOUT US
        </a>
      </li>
      <li>
        <a href="{{ route('blog') }}" class="block md:px-3 px-1 py-2 text-[#222] font-bold md:text-[12px] text-[10px] rounded">
          BLOG
        </a>
      </li>
      <li>
        <a href="{{ route('product.promo') }}" class="block md:px-3 px-1 py-2 text-[#222] font-bold md:text-[12px] text-[10px] rounded">
          PROMO
        </a>
      </li>
      <li>
        <a href="https://www.youtube.com/watch?v=n7XvnXJ70co" target="blank" class="block md:px-3 px-1 py-2 text-[#222] font-bold md:text-[12px] text-[10px] rounded">
          YOUTUBE
        </a>
      </li>
      <li>
        <a href="https://katalog.inaproc.id/raja-batavia-perkasa" target="blank" class="block md:px-3 px-1 py-2 text-[#222] font-bold md:text-[12px] text-[10px] rounded">
          E KATALOG
        </a>
      </li>
    </ul>
  </nav>


  {{-- Banner --}}
  <div class="flex flex-col md:flex-row bg-white items-start">
    {{-- Left banner --}}
    <div class="w-full md:w-[400px] h-[200px] md:h-[252px] bg-cover bg-no-repeat text-white flex flex-col gap-2 items-start"
         style="background-image: url('{{ asset('images/rajlef.gif') }}')">
      <img src="{{ asset($siteSettings->logo) }}" alt="logo" class="h-[50px] md:min-h-[66px]" />
      <div class="text-[20px] md:text-[26px] italic opacity-95 leading-tight p-6">“Your Office Equipment And Furniture”</div>
    </div>

    {{-- Right banner --}}
    <div class="flex-1 w-full">
    @php
        $slides = [];
        if (isset($siteSettings) && $siteSettings->slider) {
            foreach ($siteSettings->slider as $s) {
                $slides[] = asset($s);
            }
        }
    @endphp

    <div
      x-data="{ active: 0, slides: [] }"
      x-init="
        slides = {{ Js::from($slides) }};
        setInterval(() => active = (active + 1) % slides.length, 3000);
      "
      class="relative w-full h-[200px] md:h-[252px] rounded-sm overflow-hidden bg-gray-300"
    >
      <template x-for="(slide, index) in slides" :key="index">
        <img
          :src="slide"
          alt="slider"
          class="absolute inset-0 w-full h-full object-cover transition-opacity duration-700"
          :class="active === index ? 'opacity-100' : 'opacity-0'"
        />
      </template>
    </div>
  </div>


  </div>

  {{-- 3 COLUMN LAYOUT --}}
  <div class="grid grid-cols-[220px_1fr_260px] gap-[5px] p-2
              max-[1040px]:grid-cols-1 max-[1040px]:gap-4">

    {{-- LEFT SIDEBAR --}}
    <aside class="order-1 md:order-none">
      <div class="bg-white p-3">
        <h4 class="mb-2 text-[15px] text-[#333] border-b-4 border-gray-200 pb-2">CATEGORIES</h4>
        <div class="max-h-[560px] overflow-auto pr-1" x-data="{ openCategories: {} }">
          <ul class="text-[13px] leading-[1.7] text-[#15314b] space-y-1">
            @if(isset($categories[0]['children']))
              @foreach($categories[0]['children'] as $category)
                <li>
                  <div class="flex items-center gap-1">
                    @if(!empty($category['children']))
                      <button 
                        @click="openCategories['{{ $category['id_category'] }}'] = !openCategories['{{ $category['id_category'] }}']"
                        class="w-4 h-4 flex items-center justify-center text-xs text-gray-600"
                      >
                        <span x-show="!openCategories['{{ $category['id_category'] }}']">+</span>
                        <span x-show="openCategories['{{ $category['id_category'] }}']">-</span>
                      </button>
                    @else
                      <span class="w-4"></span>
                    @endif
                    <a href="{{ url('/category/'.$category['id_category'].'-'.$category['slug']) }}" class="text-[#15314b] hover:underline">{{ $category['name'] }}</a>
                  </div>
                  
                  @if(!empty($category['children']))
                    <ul class="pl-6 mt-1 space-y-1"
                        x-show="openCategories['{{ $category['id_category'] }}']"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                    >
                      @foreach($category['children'] as $subCategory)
                        <li>
                          <div class="flex items-center gap-1">
                            @if(!empty($subCategory['children']))
                              <button 
                                @click="openCategories['{{ $subCategory['id_category'] }}'] = !openCategories['{{ $subCategory['id_category'] }}']"
                                class="w-4 h-4 flex items-center justify-center text-xs text-gray-600"
                              >
                                <span x-show="!openCategories['{{ $subCategory['id_category'] }}']">+</span>
                                <span x-show="openCategories['{{ $subCategory['id_category'] }}']">-</span>
                              </button>
                            @else
                              <span class="w-4"></span>
                            @endif
                            <a href="{{ url('/category/'.$subCategory['id_category'].'-'.$subCategory['slug']) }}" class="text-[#15314b] hover:underline">{{ $subCategory['name'] }}</a>
                          </div>

                          @if(!empty($subCategory['children']))
                            <ul class="pl-6 mt-1 space-y-1"
                                x-show="openCategories['{{ $subCategory['id_category'] }}']"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 transform translate-y-0"
                                x-transition:leave-end="opacity-0 transform -translate-y-2"
                            >
                              @foreach($subCategory['children'] as $subSubCategory)
                                <li>
                                  <a href="{{ url('/category/'.$subSubCategory['id_category'].'-'.$subSubCategory['slug']) }}" class="text-[#15314b] hover:underline">{{ $subSubCategory['name'] }}</a>
                                </li>
                              @endforeach
                            </ul>
                          @endif
                        </li>
                      @endforeach
                    </ul>
                  @endif
                </li>
              @endforeach
            @endif
          </ul>
        </div>
      </div>

      {{-- <div class="h-3"></div>

      <div class="bg-white p-3 text-center">
        <img src="https://www.rajakantor.com/img/logo-old.jpg" alt="mini logo"
             class="max-w-[140px] mx-auto my-2 block" />
        <div class="text-[12px] text-gray-500 mt-1">All manufacturers</div>
        <select class="w-full mt-2 p-1.5 text-sm">
          <option>All manufacturers</option>
          <option>Alba</option>
          <option>Bossini</option>
          <option>Donati</option>
        </select>
      </div> --}}

      <img src="{{ asset($siteSettings->banner_sidebar) }}" alt="Advertisement" class="w-full mt-4" />

      <div class="p-4 md:p-6 bg-white mt-3">
        <h5 class="text-sm font-semibold text-gray-700 mb-3 tracking-wide">SHARE ON</h5>

        <div class="grid grid-cols-2 gap-2 justify-items-center">
          <a href="https://twitter.com/intent/tweet?text=Jual Meja Kantor - Jual Kursi Kantor - Distributor Meja Kursi Kantor&amp;url=http://www.rajakantor.com&amp;via=mbz"
            target="_blank"
            title="Twitter"
            class="w-20 h-20 flex items-center justify-center bg-blue-400 text-white hover:bg-blue-500 transition rounded">
            <i class="fa-brands fa-twitter text-3xl"></i>
          </a>

          <a href="https://www.facebook.com/dedi.s.lopez"
            target="_blank"
            title="Facebook"
            class="w-20 h-20 flex items-center justify-center bg-blue-600 text-white hover:bg-blue-700 transition rounded">
            <i class="fa-brands fa-facebook-f text-3xl"></i>
          </a>

          <a href="https://plus.google.com/share?url=http://www.rajakantor.com"
            target="_blank"
            title="Google+"
            class="w-20 h-20 flex items-center justify-center bg-gray-900 text-white hover:bg-gray-900 transition rounded">
            <i class="fa-brands fa-google-plus-g text-3xl"></i>
          </a>

          <a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=http://www.rajakantor.com&amp;title=Jual Meja Kantor - Jual Kursi Kantor - Distributor Meja Kursi Kantor"
            target="_blank"
            title="LinkedIn"
            class="w-20 h-20 flex items-center justify-center bg-blue-700 text-white hover:bg-blue-800 transition rounded">
            <i class="fa-brands fa-linkedin-in text-3xl"></i>
          </a>

          <a href="https://www.instagram.com/rajakantor_jakarta/?hl=id"
            target="_blank"
            title="Instagram"
            class="w-20 h-20 flex items-center justify-center bg-gradient-to-tr from-pink-500 via-red-500 to-yellow-500 text-white hover:opacity-90 transition rounded">
            <i class="fa-brands fa-instagram text-3xl"></i>
          </a>
        </div>
      </div>


    </aside>

    @yield('content')

  
    {{-- RIGHT SIDEBAR --}}
    <aside class="order-2 md:order-none">
      <div class="bg-white p-3">
        <h5 class="mb-2 text-[14px] text-[#333] border-b-4 border-gray-200 pb-1">NEW PRODUCTS</h5>
        @if(!empty($latestProducts) && count($latestProducts) > 0)
          <ul class="space-y-3">
            @foreach($latestProducts as $p)
              <li class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gray-100 rounded overflow-hidden flex-shrink-0">
                  <img src="{{ asset($p['image'] ?? 'images/no-image.png') }}" alt="{{ $p['name'] }}" class="w-full h-full object-cover" />
                </div>
                <div class="text-xs">
                  <a href="{{ url('/product/'.$p['id_product'].'-'.$p['slug']) }}" class="text-[#15314b] hover:underline">{{ $p['name'] }}</a>
                </div>
              </li>
            @endforeach
          </ul>
        @else
          <div class="text-[13px] text-gray-500 py-2">No new product at this time</div>
        @endif
      </div>

      <div class="h-3"></div>

      <div class="bg-white p-3">
        <h5 class="mb-2 text-[14px] text-[#333] border-b-4 border-gray-200 pb-1">SEARCH</h5>

        <form action="{{ route('product.search') }}" method="GET" class="flex items-center gap-2 py-1.5">
          <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Enter a product name"
            class="flex-1 p-2 text-sm rounded-sm border border-gray-300 w-[50px]"
            required
          />
          <button
            type="submit"
            class="py-2 px-4 bg-[#cf6a00] text-white text-sm font-semibold hover:bg-[#b94f00] transition rounded-sm whitespace-nowrap">
            GO
          </button>
        </form>
      </div>



      <div class="h-3"></div>

      {{-- INFORMATION --}}
      <div class="bg-white p-3 text-[13px] leading-relaxed">
        <h4 class="text-[15px] font-semibold mb-2 border-b-4 border-gray-200 pb-2">Information</h4>

        <div class="prose text-gray-700 [&_*]:text-xs">
          {!! $siteSettings->information ?? '<p class="ml-3">No information available.</p>' !!}
        </div>
        
      </div>
    </aside>
  </div>

  {{-- FOOTER --}}
  <footer class="bg-white border-t-2 border-gray-300 px-4 py-3 text-[11px] text-gray-600">
    <div class="space-x-1 mb-1 font-bold">
      <a href="{{ route('home') }}" class="hover:text-gray-800">Home</a> |
      <a href="{{ route('contact') }}" class="hover:text-gray-800">Contact us</a> |
      <a href="{{ route('terms') }}" class="hover:text-gray-800">Terms and conditions of use</a> |
      <a href="{{ route('about') }}" class="hover:text-gray-800">About Us</a> |
      <a href="{{ route('client') }}" class="hover:text-gray-800">Our Clients</a> |
      <span>Copyright © rajakantor.com</span>
    </div>

    <div>
      Distributor Alat Kantor — Meja Gambar — Mesin Absensi
    </div>
  </footer>

  {{-- Floating WhatsApp Button --}}
  <a 
    href="https://wa.me/{{ $siteSettings->wa ?? '' }}?text={{ urlencode('Selamat datang di Raja Kantor' . PHP_EOL . 'Silahkan chat jika ada yang ingin ditanyakan') }}"
    target="_blank"
    rel="noopener noreferrer"
    class="fixed bottom-6 right-6 z-40 flex items-center justify-center w-16 h-16 bg-green-500 text-white rounded-full shadow-lg hover:bg-green-600 transition hover:scale-110 duration-200"
    title="Chat dengan kami di WhatsApp"
  >
    <i class="fa-brands fa-whatsapp text-3xl"></i>
  </a>

</div>
</body>
</html>
