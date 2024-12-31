<x-guest-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <article class="prose lg:prose-xl dark:prose-invert mx-auto">
            <h1 class="text-gray-900 dark:text-gray-100">{{ $page->title }}</h1>
            
            @if($page->featuredImage)
                <img src="{{ Storage::url($page->featuredImage->file_name) }}" 
                     alt="{{ $page->title }}" 
                     class="w-full h-auto">
            @endif
            
            <div class="mt-6">
                {!! $page->content !!}
            </div>
        </article>
    </div>
</x-guest-layout> 