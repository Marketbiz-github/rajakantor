@php
$menuItems = [
    [
        'label' => 'Dashboard',
        'route' => 'dashboard',
        'icon' => '<i class="fas fa-home w-5 h-5"></i>',
    ],
    [
        'label' => 'Produk',
        'route' => 'product.index',
        'icon' => '<i class="fas fa-box-open w-5 h-5"></i>',
    ],
    [
        'label' => 'Kategori',
        'route' => 'category.index',
        'icon' => '<i class="fas fa-tags w-5 h-5"></i>',
    ],
    [
        'label' => 'Pengaturan Situs',
        'route' => 'settings.edit',
        'icon' => '<i class="fas fa-cogs w-5 h-5"></i>',
    ],
    [
        'label' => 'Ubah Password',
        'route' => 'profile.edit',
        'icon' => '<i class="fas fa-user w-5 h-5"></i>',
    ]
];
@endphp

<div x-data="{ mobileMenuOpen: false }">
    <!-- Mobile Header -->
    <div class="sticky top-0 z-40 lg:hidden bg-white shadow-md py-2">
        <div class="flex items-center justify-between px-4 py-2">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-sm font-medium text-gray-600">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </span>
                </div>
                <div class="ml-3">
                    <p class="font-medium">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    type="button" 
                    class="text-gray-500 hover:text-gray-600 focus:outline-none">
                <svg class="h-6 w-6" :class="{'hidden': mobileMenuOpen, 'block': !mobileMenuOpen }"
                    stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg class="h-6 w-6" :class="{'block': mobileMenuOpen, 'hidden': !mobileMenuOpen }"
                    stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="absolute inset-x-0 top-full bg-white shadow-lg border-b">
            

            <!-- Navigation Links - Mobile -->
            <nav class="px-4 py-2 space-y-2">
                @foreach($menuItems as $item)
                    <a href="{{ route($item['route']) }}" 
                       class="flex items-center px-3 py-3 text-base font-medium rounded-lg transition-colors duration-150
                        {{ request()->routeIs($item['route']) ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-200' }}">
                        {!! $item['icon'] !!}
                        <span class="ml-3">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Profile Section - Mobile -->
            <div class="border-t p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault();
                            this.closest('form').submit();"
                            class="text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Log Out
                    </x-dropdown-link>
                </form>
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex lg:flex-col fixed inset-y-0 z-50 w-72 bg-white border-r">
       
        <div class="flex items-center h-16 border-b px-4">
            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-gray-600">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </span>
            </div>
            <div class="ml-3">
                <p class="font-medium">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <!-- Navigation Links - Desktop -->
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            @foreach($menuItems as $item)
                <a href="{{ route($item['route']) }}" 
                   class="flex items-center px-4 py-3 rounded-lg transition-colors duration-150
                    {{ request()->routeIs($item['route']) ? 'bg-gray-200 text-gray-800' : 'text-gray-600 hover:bg-gray-200' }}">
                    {!! $item['icon'] !!}
                    <span class="ml-3 text-sm font-medium">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="border-t p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault();
                        this.closest('form').submit();"
                            class="text-red-600 hover:bg-red-50 hover:text-red-700 rounded-lg">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                    Log Out
                </x-dropdown-link>
            </form>
        </div>
    </aside>
</div>