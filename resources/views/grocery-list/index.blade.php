<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Grocery List</h1>
                    <p class="text-gray-500 text-sm mt-1">Build your shopping list by selecting recipes</p>
                </div>

                @if ($list && $list['recipe_count'] > 0)
                    <form method="POST" action="{{ route('grocery-list.clear') }}" class="inline"
                          onsubmit="return confirm('Clear your entire grocery list?')">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-2 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Clear All
                        </button>
                    </form>
                @endif
            </div>

            <!-- Branch Selector -->
            <x-branch-selector :branches="$branches" :selected="$selectedBranch?->id ?? null" />

            @if ($list && $list['recipe_count'] > 0 && $list['items']->isNotEmpty())
                <!-- Recipe List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Recipes in List</h2>
                    <div class="space-y-2">
                        @foreach($cart['recipes'] as $recipeId)
                            @php $recipe = $cartRecipes[$recipeId] ?? null; @endphp
                            @if ($recipe)
                                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-gray-600">{{ $recipe->category?->icon ?? '🍽️' }}</span>
                                        <a href="{{ route('recipes.show', $recipe->slug) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600 transition-colors">
                                            {{ $recipe->title }}
                                        </a>
                                    </div>
                                    <form method="POST" action="{{ route('grocery-list.remove', $recipe) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                    <div class="p-4 sm:p-6 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                            Ingredients
                            <span class="ml-2 text-gray-300">·</span>
                            <span class="text-gray-700 normal-case">{{ $list['items']->count() }} item{{ $list['items']->count() !== 1 ? 's' : '' }}</span>
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left px-4 sm:px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wide">Ingredient</th>
                                    <th class="text-right px-4 sm:px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wide">Quantity</th>
                                    <th class="text-right px-4 sm:px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wide hidden sm:table-cell">Price/Unit</th>
                                    <th class="text-right px-4 sm:px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wide">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($list['items'] as $item)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 sm:px-6 py-3">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900">{{ $item['name'] }}</span>
                                                @if($item['variant_label'])
                                                    <span class="text-xs text-gray-400">({{ $item['variant_label'] }})</span>
                                                @endif
                                                @if($item['is_on_promo'])
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">
                                                        PROMO
                                                    </span>
                                                @endif
                                            </div>
                                            @if($item['promo_label'])
                                                <p class="text-xs text-red-600 mt-0.5">{{ $item['promo_label'] }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 text-right">
                                            <span class="font-medium text-gray-900">{{ number_format($item['quantity'], $item['quantity'] == floor($item['quantity']) ? 0 : 1) }}</span>
                                            <span class="text-gray-500 ml-1">{{ $item['unit'] }}</span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 text-right hidden sm:table-cell">
                                            @if($item['has_price'] && $item['price_per_unit'] !== null)
                                                <span class="text-gray-600">₱{{ number_format($item['price_per_unit'], 2) }}/{{ $item['price_unit'] }}</span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 text-right">
                                            @if($item['has_price'] && $item['total_cost'] !== null)
                                                @if($item['is_on_promo'] && $item['promo_price'] !== null)
                                                    <span class="text-sm text-gray-400 line-through mr-1">₱{{ number_format($item['total_cost'], 2) }}</span>
                                                    <span class="font-semibold text-red-600">₱{{ number_format($item['promo_price'], 2) }}</span>
                                                @else
                                                    <span class="font-semibold text-gray-900">₱{{ number_format($item['total_cost'], 2) }}</span>
                                                @endif
                                            @elseif($item['has_price'] === false)
                                                <span class="text-xs text-amber-600">No price</span>
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Grand Total -->
                    <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Estimated Total</span>
                            <span class="text-xl font-bold text-gray-900">
                                @if($list['total_cost'] !== null)
                                    ₱{{ number_format($list['total_cost'], 2) }}
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Prices are estimates and may vary at the store.</p>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="text-5xl mb-4">🛒</div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Your list is empty</h2>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        @if($selectedBranch)
                            Select a recipe to build your grocery list. You'll be able to see estimated costs based on {{ $selectedBranch->name }}'s prices.
                        @else
                            Select a store above, then browse recipes and add items to your grocery list.
                        @endif
                    </p>
                    <a href="{{ route('recipes.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                        Browse Recipes
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            @endif

            <!-- Print-friendly note -->
            <div class="mt-8">
                <button onclick="window.print()" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print List
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
