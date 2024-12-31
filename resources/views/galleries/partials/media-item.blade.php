@if($item->type === 'image')
    <img 
        src="{{ Storage::url($item->file_name) }}" 
        alt="{{ $item->alt_text }}"
        class="w-full object-cover rounded-lg"
        @if($gallery->template !== 'masonry')
        style="height: 300px"
        @endif
    >
@elseif($item->type === 'video')
    <video 
        src="{{ Storage::url($item->file_name) }}"
        class="w-full object-cover rounded-lg"
        @if($gallery->template !== 'masonry')
        style="height: 300px"
        @endif
        controls
    ></video>
@else
    <div class="w-full flex items-center justify-center bg-gray-100 rounded-lg" style="height: 300px">
        <div class="flex flex-col items-center justify-center">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <span class="mt-2 text-sm text-gray-600">{{ $item->name }}</span>
        </div>
    </div>
@endif

<div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity duration-200 rounded-lg"></div>

<div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
    <span class="text-white text-sm font-medium">{{ $item->name }}</span>
</div> 