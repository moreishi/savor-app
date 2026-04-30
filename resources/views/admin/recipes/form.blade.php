<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $recipe->exists ? __('Edit Recipe') : __('Create Recipe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ $recipe->exists ? route('admin.recipes.update', $recipe) : route('admin.recipes.store') }}" class="space-y-6">
                        @csrf
                        @if($recipe->exists)
                            @method('PUT')
                        @endif

                        <!-- Title -->
                        <div>
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $recipe->title)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <!-- Category & Difficulty Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="category_id" :value="__('Category')" />
                                <select id="category_id" name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $recipe->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                            </div>
                            <div>
                                <x-input-label for="difficulty" :value="__('Difficulty')" />
                                <select id="difficulty" name="difficulty" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="easy" {{ old('difficulty', $recipe->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
                                    <option value="medium" {{ old('difficulty', $recipe->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="hard" {{ old('difficulty', $recipe->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('difficulty')" />
                            </div>
                        </div>

                        <!-- Prep Time, Cook Time, Servings Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="prep_time" :value="__('Prep Time (min)')" />
                                <x-text-input id="prep_time" name="prep_time" type="number" class="mt-1 block w-full" :value="old('prep_time', $recipe->prep_time)" min="0" />
                                <x-input-error class="mt-2" :messages="$errors->get('prep_time')" />
                            </div>
                            <div>
                                <x-input-label for="cook_time" :value="__('Cook Time (min)')" />
                                <x-text-input id="cook_time" name="cook_time" type="number" class="mt-1 block w-full" :value="old('cook_time', $recipe->cook_time)" min="0" />
                                <x-input-error class="mt-2" :messages="$errors->get('cook_time')" />
                            </div>
                            <div>
                                <x-input-label for="servings" :value="__('Servings')" />
                                <x-text-input id="servings" name="servings" type="number" class="mt-1 block w-full" :value="old('servings', $recipe->servings)" min="1" required />
                                <x-input-error class="mt-2" :messages="$errors->get('servings')" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $recipe->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <!-- Instructions -->
                        <div>
                            <x-input-label for="instructions" :value="__('Instructions')" />
                            <p class="text-xs text-gray-500 mb-1">Write each step on a new line. They will be numbered automatically.</p>
                            <textarea id="instructions" name="instructions" rows="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('instructions', $recipe->instructions) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('instructions')" />
                        </div>

                        <!-- Checkboxes Row -->
                        <div class="flex items-center gap-6">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_featured" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_featured', $recipe->is_featured) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">Featured</span>
                            </label>
                        </div>

                        <!-- Tags -->
                        <div>
                            <x-input-label :value="__('Tags')" />
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach ($tags as $tag)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            {{ (old('tags') && in_array($tag->id, old('tags'))) || (!$recipe->exists && !old('tags')) ? '' : (optional($recipe->tags)->contains($tag->id) ? 'checked' : '') }}>
                                        <span class="ms-2 text-sm text-gray-600">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('tags')" />
                        </div>

                        <!-- Ingredients (Dynamic Rows with Alpine.js) -->
                        <div x-data="ingredientRows()" x-init="init()">
                            <div class="flex items-center justify-between">
                                <x-input-label :value="__('Ingredients')" />
                                <button type="button" @click="addRow()" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">+ Add Ingredient</button>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('ingredients')" />
                            <x-input-error class="mt-2" :messages="$errors->get('ingredients.*.ingredient_id')" />
                            <x-input-error class="mt-2" :messages="$errors->get('ingredients.*.quantity')" />
                            <x-input-error class="mt-2" :messages="$errors->get('ingredients.*.unit')" />

                            <template x-for="(row, index) in rows" :key="index">
                                <div class="mt-3 p-3 border border-gray-200 rounded-md relative">
                                    <button type="button" @click="removeRow(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 text-sm" x-show="rows.length > 1">&times;</button>
                                    <div class="grid grid-cols-12 gap-2 items-start">
                                        <!-- Ingredient Select -->
                                        <div class="col-span-4">
                                            <select :name="'ingredients['+index+'][ingredient_id]'" x-model="row.ingredient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                                <option value="">Select ingredient...</option>
                                                @foreach ($ingredients as $ingredient)
                                                    <option value="{{ $ingredient->id }}">{{ $ingredient->name }} ({{ $ingredient->category?->name ?? 'N/A' }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Quantity -->
                                        <div class="col-span-2">
                                            <input type="number" step="0.01" min="0" :name="'ingredients['+index+'][quantity]'" x-model="row.quantity" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Qty" required>
                                        </div>
                                        <!-- Unit -->
                                        <div class="col-span-2">
                                            <input type="text" :name="'ingredients['+index+'][unit]'" x-model="row.unit" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Unit" required>
                                        </div>
                                        <!-- Notes -->
                                        <div class="col-span-3">
                                            <input type="text" :name="'ingredients['+index+'][notes]'" x-model="row.notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Notes (optional)">
                                        </div>
                                        <!-- Optional checkbox -->
                                        <div class="col-span-1 flex items-center pt-3">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" :name="'ingredients['+index+'][is_optional]'" value="1" x-model="row.is_optional" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 text-xs">
                                                <span class="ms-1 text-xs text-gray-500">Opt?</span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Sort Order (hidden, auto-incrementing) -->
                                    <input type="hidden" :name="'ingredients['+index+'][sort_order]'" :value="index">
                                </div>
                            </template>

                            <p class="mt-2 text-xs text-gray-500" x-show="rows.length === 0">No ingredients added yet. Click "+ Add Ingredient" to add one.</p>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.recipes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <x-primary-button>
                                {{ $recipe->exists ? __('Update Recipe') : __('Create Recipe') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function ingredientRows() {
            return {
                rows: [],
                init() {
                    @if($recipe->exists && $recipe->ingredients->count() > 0)
                        this.rows = [
                            @foreach($recipe->ingredients as $ingredient)
                                {
                                    ingredient_id: '{{ $ingredient->id }}',
                                    quantity: '{{ $ingredient->pivot->quantity }}',
                                    unit: '{{ $ingredient->pivot->unit }}',
                                    notes: '{{ $ingredient->pivot->notes ?? '' }}',
                                    is_optional: {{ $ingredient->pivot->is_optional ? 'true' : 'false' }},
                                },
                            @endforeach
                        ];
                    @elseif(old('ingredients'))
                        this.rows = [
                            @foreach(old('ingredients') as $item)
                                {
                                    ingredient_id: '{{ $item['ingredient_id'] ?? '' }}',
                                    quantity: '{{ $item['quantity'] ?? '' }}',
                                    unit: '{{ $item['unit'] ?? '' }}',
                                    notes: '{{ $item['notes'] ?? '' }}',
                                    is_optional: {{ isset($item['is_optional']) ? 'true' : 'false' }},
                                },
                            @endforeach
                        ];
                    @else
                        this.rows = [{ ingredient_id: '', quantity: '', unit: '', notes: '', is_optional: false }];
                    @endif
                },
                addRow() {
                    this.rows.push({ ingredient_id: '', quantity: '', unit: '', notes: '', is_optional: false });
                },
                removeRow(index) {
                    this.rows.splice(index, 1);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
