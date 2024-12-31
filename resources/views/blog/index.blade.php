<x-guest-layout>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-gray-100">Blog Posts</h1>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($posts as $post)
                        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                            @if($post->featuredImage)
                                <img src="{{ Storage::url($post->featuredImage->file_name) }}" 
                                     alt="{{ $post->title }}"
                                     class="w-full h-48 object-cover">
                            @endif
                            
                            <div class="p-6">
                                <h2 class="text-xl font-bold mb-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" 
                                       class="text-gray-900 dark:text-gray-100 hover:text-primary-600 dark:hover:text-primary-400">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                    {{ $post->published_at->format('F j, Y') }} | 
                                    By {{ $post->author->name }}
                                </div>
                                
                                <p class="text-gray-700 dark:text-gray-300 mb-4">
                                    {{ $post->excerpt }}
                                </p>
                                
                                <div class="flex flex-wrap gap-2">
                                    @foreach($post->categories as $category)
                                        <a href="{{ route('blog.category', $category->slug) }}"
                                           class="px-3 py-1 bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 rounded-full text-sm hover:bg-primary-200 dark:hover:bg-primary-800 transition">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Categories</h2>
                    <ul class="space-y-2">
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('blog.category', $category->slug) }}" 
                                   class="text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition">
                                    {{ $category->name }} 
                                    <span class="text-gray-500 dark:text-gray-400">({{ $category->posts_count }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout> 