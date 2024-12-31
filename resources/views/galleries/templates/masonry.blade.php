<div class="columns-1 md:columns-{{ $columns ?? 3 }} gap-4 space-y-4">
    @foreach($media as $item)
        <div class="relative group break-inside-avoid">
            @include('galleries.partials.media-item', ['item' => $item])
        </div>
    @endforeach
</div> 