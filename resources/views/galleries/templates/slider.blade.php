<div class="relative" x-data="{ currentSlide: 0 }" x-init="
    setInterval(() => {
        if (@json($settings['autoplay'] ?? false)) {
            currentSlide = (currentSlide + 1) % {{ count($media) }};
        }
    }, 5000)
">
    <div class="overflow-hidden relative">
        @foreach($media as $index => $item)
            <div 
                x-show="currentSlide === {{ $index }}"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-full"
                class="relative"
            >
                @include('galleries.partials.media-item', ['item' => $item])
            </div>
        @endforeach
    </div>

    @if($settings['arrows'] ?? true)
        <button 
            @click="currentSlide = (currentSlide - 1 + {{ count($media) }}) % {{ count($media) }}"
            class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-r"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button 
            @click="currentSlide = (currentSlide + 1) % {{ count($media) }}"
            class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-l"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    @endif

    @if($settings['dots'] ?? true)
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            @foreach($media as $index => $item)
                <button 
                    @click="currentSlide = {{ $index }}"
                    :class="{'bg-white': currentSlide === {{ $index }}, 'bg-gray-400': currentSlide !== {{ $index }}}"
                    class="w-2 h-2 rounded-full transition-colors"
                ></button>
            @endforeach
        </div>
    @endif
</div> 