<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Promo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.promos.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="branch_id" :value="__('Branch')" />
                            <select id="branch_id" name="branch_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('branch_id')" />
                        </div>

                        <div>
                            <x-input-label for="ingredient_id" :value="__('Ingredient')" />
                            <select id="ingredient_id" name="ingredient_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select Ingredient</option>
                                @foreach ($ingredients as $ingredient)
                                    <option value="{{ $ingredient->id }}" {{ old('ingredient_id') == $ingredient->id ? 'selected' : '' }}>{{ $ingredient->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('ingredient_id')" />
                        </div>

                        <div>
                            <x-input-label for="recipe_id" :value="__('Recipe (optional)')" />
                            <select id="recipe_id" name="recipe_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">— No specific recipe —</option>
                                @foreach ($recipes as $recipe)
                                    <option value="{{ $recipe->id }}" {{ old('recipe_id') == $recipe->id ? 'selected' : '' }}>{{ $recipe->title }}</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('recipe_id')" />
                        </div>

                        <div>
                            <x-input-label for="promo_text" :value="__('Promo Text / Label')" />
                            <x-text-input id="promo_text" name="promo_text" type="text" class="mt-1 block w-full" :value="old('promo_text')" placeholder="e.g. Fiesta Offer, Holy Week Saver" required />
                            <x-input-error class="mt-2" :messages="$errors->get('promo_text')" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="valid_from" :value="__('Valid From')" />
                                <x-text-input id="valid_from" name="valid_from" type="date" class="mt-1 block w-full" :value="old('valid_from', date('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('valid_from')" />
                            </div>
                            <div>
                                <x-input-label for="valid_until" :value="__('Valid Until')" />
                                <x-text-input id="valid_until" name="valid_until" type="date" class="mt-1 block w-full" :value="old('valid_until', date('Y-m-d', strtotime('+30 days')))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('valid_until')" />
                            </div>
                        </div>

                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">Active</span>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.promos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <x-primary-button>
                                {{ __('Create Promo') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
