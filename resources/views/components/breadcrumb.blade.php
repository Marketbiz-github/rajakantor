@props(['items' => []])

<nav class="flex text-gray-700 text-sm mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        <li class="inline-flex items-center">
            <a href="/dashboard" class="inline-flex items-center text-gray-700 hover:text-teal-600">
                <i class="fas fa-home w-4 h-4 mr-2"></i>
                Dashboard
            </a>
        </li>

        @foreach($items as $item)
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="ml-1 text-gray-700 hover:text-teal-600">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="ml-1 text-gray-500">{{ $item['label'] }}</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>