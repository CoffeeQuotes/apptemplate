<div class="grid grid-cols-1 md:grid-cols-{{ $columns ?? 3 }} gap-4">
    @foreach($media as $item)
        <div class="relative group">
            @include('galleries.partials.media-item', ['item' => $item])
        </div>
    @endforeach
</div> 