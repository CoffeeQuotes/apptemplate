<div class="w-20 h-20 flex items-center justify-center bg-gray-100 rounded-lg overflow-hidden">
    @if($getRecord()->type === 'image')
        <img 
            src="{{ Storage::url($getRecord()->file_name) }}" 
            alt="{{ $getRecord()->alt_text }}"
            class="w-full h-full object-cover"
        >
    @elseif($getRecord()->type === 'video')
        <div class="flex flex-col items-center justify-center w-full h-full bg-blue-50">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs text-blue-600 mt-1">Video</span>
        </div>
    @else
        <div class="flex flex-col items-center justify-center w-full h-full bg-amber-50">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
            <span class="text-xs text-amber-600 mt-1">Document</span>
        </div>
    @endif
</div> 