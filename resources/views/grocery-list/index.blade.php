<x-app-layout>
    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Grocery List</h1>
            <p class="text-gray-600 mb-8">Build your shopping list by selecting recipes</p>

            <!-- Branch Selector -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">Select your store</label>
                <select id="branch" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }} — @php
                            $brandMap = ['osave' => 'O!Save', 'robinsons' => 'Robinsons Supermarket', 'easymart' => 'Easymart', 'shopwise' => 'Shopwise'];
                        @endphp{{ $brandMap[$branch->brand] ?? $branch->brand }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="text-5xl mb-4">🛒</div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Your list is empty</h2>
                <p class="text-gray-500 mb-6">Select a recipe to build your grocery list. You'll be able to see estimated costs based on your chosen store's prices.</p>
                <a href="{{ route('recipes.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Browse Recipes
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>

            <!-- Placeholder for Sprint 3 -->
            <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                <p class="text-sm text-amber-800">
                    <strong>Coming in Sprint 3:</strong> Add recipes to your grocery list, view estimated costs per store, check promo highlights, and export your list.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
