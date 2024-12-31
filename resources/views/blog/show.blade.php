<x-guest-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <article class="max-w-4xl mx-auto">
            @if($post->featuredImage)
                <img src="{{ Storage::url($post->featuredImage->file_name) }}" 
                     alt="{{ $post->title }}"
                     class="w-full h-96 object-cover rounded-lg mb-8">
            @endif
            
            <h1 class="text-4xl font-bold mb-4 text-gray-900 dark:text-gray-100">{{ $post->title }}</h1>
            
            <div class="flex items-center text-gray-500 dark:text-gray-400 mb-8">
                <span>{{ $post->published_at->format('F j, Y') }}</span>
                <span class="mx-2">•</span>
                <span>By {{ $post->author->name }}</span>
            </div>
            
            <div class="prose lg:prose-xl dark:prose-invert max-w-none mb-8">
                {!! $post->content !!}
            </div>
            
            <!-- Tags and Categories -->
            <div class="border-t border-b border-gray-200 dark:border-gray-700 py-6 mb-8">
                @if($post->categories->count())
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Categories:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->categories as $category)
                                <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 rounded-full text-sm">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($post->tags->count())
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Tags:</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-full text-sm">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Related Posts -->
            @if($relatedPosts->count())
                <div>
                    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-gray-100">Related Posts</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $relatedPost)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                                @if($relatedPost->featuredImage)
                                    <img src="{{ Storage::url($relatedPost->featuredImage->file_name) }}" 
                                         alt="{{ $relatedPost->title }}"
                                         class="w-full h-48 object-cover">
                                @endif
                                
                                <div class="p-4">
                                    <h3 class="font-bold mb-2">
                                        <a href="{{ route('blog.show', $relatedPost->slug) }}" 
                                           class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400">
                                            {{ $relatedPost->title }}
                                        </a>
                                    </h3>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $relatedPost->published_at->format('F j, Y') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </article>
    </div>
</x-guest-layout> 