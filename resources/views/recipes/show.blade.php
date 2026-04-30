<x-app-layout>
    <div class="py-8" x-data="{
        servings: {{ $recipe->servings }},
        baseServings: {{ $recipe->servings }},
        get multiplier() { return this.servings / this.baseServings; },
        adjust(qty) { return (qty * this.multiplier).toFixed(qty % 1 === 0 && (qty * this.multiplier) % 1 === 0 ? 0 : 1); },
        get totalTime() { return {{ $recipe->prep_time ?? 0 }} + {{ $recipe->cook_time ?? 0 }}; }
    }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <a href="{{ route('recipes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 hover:underline">
                    &larr; Back to recipes
                </a>
            </div>

            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-6">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl">{{ $recipe->category?->icon ?? '🍽️' }}</span>
                    <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ $recipe->category?->name ?? 'Uncategorized' }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">{{ $recipe->title }}</h1>

                <p class="text-lg text-gray-600 mb-4">{{ $recipe->description }}</p>

                <!-- Meta bar -->
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-4">
                    <x-recipe-meta :prep="$recipe->prep_time" :cook="$recipe->cook_time" :servings="$recipe->servings" :difficulty="$recipe->difficulty" />
                    <span class="text-gray-300">|</span>
                    <x-difficulty-badge :difficulty="$recipe->difficulty" />
                </div>

                <!-- Tags -->
                @if($recipe->tags->count() > 0)
                <div class="flex flex-wrap gap-1.5">
                    @foreach($recipe->tags as $tag)
                        <span class="inline-block bg-indigo-50 text-indigo-700 rounded-full px-3 py-1 text-xs font-medium">{{ $tag->name }}</span>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sidebar: Ingredients -->
                <div class="lg:col-span-1 order-2 lg:order-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-gray-900">Ingredients</h2>
                            <span class="text-sm text-gray-500">{{ $recipe->ingredients->count() }} items</span>
                        </div>

                        <!-- Serving adjuster -->
                        <div class="mb-5 p-3 bg-gray-50 rounded-lg">
                            <label class="text-sm font-medium text-gray-700 block mb-2">Servings</label>
                            <div class="flex items-center gap-3">
                                <button @click="servings = Math.max(1, servings - 1)"
                                        class="w-8 h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors text-lg font-medium">−</button>
                                <span class="text-xl font-bold text-gray-900 min-w-[2rem] text-center" x-text="servings"></span>
                                <button @click="servings = Math.min(50, servings + 1)"
                                        class="w-8 h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors text-lg font-medium">+</button>
                            </div>
                        </div>

                        <!-- Ingredient list with multiplier -->
                        <ul class="space-y-3">
                            @foreach($recipe->ingredients as $ingredient)
                            <li class="flex items-start gap-2 text-sm">
                                <input type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span>
                                    <span x-text="adjust({{ $ingredient->pivot->quantity }})" class="font-medium"></span>
                                    <span class="text-gray-600">{{ $ingredient->pivot->unit }}</span>
                                    <span class="text-gray-900 font-medium">{{ $ingredient->name }}</span>
                                    @if($ingredient->pivot->notes)
                                        <span class="text-gray-400 italic">({{ $ingredient->pivot->notes }})</span>
                                    @endif
                                </span>
                            </li>
                            @endforeach
                        </ul>

                        <!-- Branch Selector -->
                        <div class="mb-4">
                            <x-branch-selector :branches="$branches" :selected="session('grocery_list.branch_id')" />
                        </div>
                        <!-- Add to Grocery List -->
                        <div class="mt-6">
                            <form method="POST" action="{{ route('grocery-list.add', $recipe) }}">
                                @csrf
                                <input type="hidden" name="branch_id" x-bind:value="Alpine.store('branch')?.id ?? ''">
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors text-sm shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                                    Add to Grocery List
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Main: Instructions -->
                <div class="lg:col-span-2 order-1 lg:order-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Instructions</h2>

                        <ol class="space-y-6">
                            @foreach($steps as $index => $step)
                            <li class="flex gap-4">
                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</span>
                                <div class="pt-1 text-gray-700 leading-relaxed">{{ $step }}</div>
                            </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
