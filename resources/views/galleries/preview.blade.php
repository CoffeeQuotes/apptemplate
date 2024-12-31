<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $gallery->name }}</h1>
                            @if($gallery->description)
                                <p class="mt-2 text-gray-600">{{ $gallery->description }}</p>
                            @endif
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <select 
                                class="rounded-md border-gray-300"
                                onchange="window.location.href = '{{ route('gallery.preview', $gallery) }}?template=' + this.value"
                            >
                                @foreach($gallery->templateOptions as $key => $option)
                                    <option value="{{ $key }}" {{ $gallery->template === $key ? 'selected' : '' }}>
                                        {{ $option['name'] }}
                                    </option>
                                @endforeach
                            </select>

                            @if($gallery->template === 'grid' || $gallery->template === 'masonry')
                                <select 
                                    class="rounded-md border-gray-300"
                                    onchange="window.location.href = '{{ route('gallery.preview', $gallery) }}?columns=' + this.value"
                                >
                                    @foreach($templateOptions['columns'] ?? [3] as $cols)
                                        <option value="{{ $cols }}" {{ ($settings['columns'] ?? 3) == $cols ? 'selected' : '' }}>
                                            {{ $cols }} Columns
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    @include("galleries.templates.{$gallery->template}", [
                        'media' => $media,
                        'columns' => $settings['columns'] ?? 3,
                        'settings' => $settings
                    ])
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 