<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero / Search -->
            <div class="mb-10 text-center">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">
                    What's for dinner?
                </h1>
                <p class="text-gray-600 mb-6 text-lg">
                    Browse recipes and build your grocery list from Robinsons Retail
                </p>

                <form method="GET" action="{{ route('recipes.index') }}" class="max-w-xl mx-auto">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search recipes..."
                               class="w-full rounded-full border-gray-300 shadow-sm pl-10 pr-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Category Grid -->
            <div class="mb-12">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Browse by Category</h2>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-9 gap-3">
                    @foreach($categories as $cat)
                        <a href="{{ route('recipes.index', ['search' => $cat->name]) }}"
                           class="flex flex-col items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md hover:bg-indigo-50 transition-all duration-200 border border-gray-100">
                            <span class="text-3xl mb-1">{{ $cat->icon }}</span>
                            <span class="text-xs font-medium text-gray-700 text-center">{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Featured Recipes -->
            @if(!request()->filled('search') && $featured->count() > 0)
            <div class="mb-12">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🌟 Featured Recipes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($featured as $recipe)
                        <a href="{{ route('recipes.show', $recipe->slug) }}" class="block">
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 overflow-hidden h-full">
                                <div class="p-5">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-lg">{{ $recipe->category?->icon ?? '🍽️' }}</span>
                                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $recipe->category?->name ?? 'Uncategorized' }}</span>
                                    </div>
                                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $recipe->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $recipe->description }}</p>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500">
                                        <x-recipe-meta :prep="$recipe->prep_time" :cook="$recipe->cook_time" :servings="$recipe->servings" :difficulty="$recipe->difficulty" />
                                    </div>
                                    <div class="mt-3">
                                        <x-difficulty-badge :difficulty="$recipe->difficulty" />
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- All Recipes -->
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    @if(request('search'))
                        Search results for "{{ request('search') }}"
                        <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-700">
                            {{ request('search') }}
                            <a href="{{ route('recipes.index') }}" class="ml-1.5 hover:text-indigo-900">&times;</a>
                        </span>
                    @else
                        All Recipes
                    @endif
                </h2>

                @if($recipes->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($recipes as $recipe)
                            <a href="{{ route('recipes.show', $recipe->slug) }}" class="block">
                                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 overflow-hidden h-full">
                                    <div class="p-5">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-lg">{{ $recipe->category?->icon ?? '🍽️' }}</span>
                                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $recipe->category?->name ?? 'Uncategorized' }}</span>
                                        </div>
                                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $recipe->title }}</h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $recipe->description }}</p>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                            <x-recipe-meta :prep="$recipe->prep_time" :cook="$recipe->cook_time" :servings="$recipe->servings" :difficulty="$recipe->difficulty" />
                                        </div>
                                        <div class="mt-3">
                                            <x-difficulty-badge :difficulty="$recipe->difficulty" />
                                            @foreach($recipe->tags as $tag)
                                                <span class="inline-block bg-gray-100 rounded-full px-2.5 py-0.5 text-xs font-medium text-gray-600 mr-1 mb-1">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $recipes->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                        <p class="text-gray-500 text-lg mb-2">No recipes found</p>
                        @if(request('search'))
                            <a href="{{ route('recipes.index') }}" class="text-indigo-600 hover:underline">Clear search</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
