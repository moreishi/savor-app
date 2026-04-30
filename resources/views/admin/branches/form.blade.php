<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $branch->exists ? __('Edit Branch') : __('Create Branch') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ $branch->exists ? route('admin.branches.update', $branch) : route('admin.branches.store') }}" class="space-y-6">
                        @csrf
                        @if($branch->exists)
                            @method('PUT')
                        @endif

                        <div>
                            <x-input-label for="name" :value="__('Branch Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $branch->name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="brand" :value="__('Brand')" />
                            <select id="brand" name="brand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="osave" {{ old('brand', $branch->brand) == 'osave' ? 'selected' : '' }}>OSave</option>
                                <option value="robinsons" {{ old('brand', $branch->brand) == 'robinsons' ? 'selected' : '' }}>Robinsons</option>
                                <option value="easymart" {{ old('brand', $branch->brand) == 'easymart' ? 'selected' : '' }}>Easymart</option>
                                <option value="shopwise" {{ old('brand', $branch->brand) == 'shopwise' ? 'selected' : '' }}>Shopwise</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('brand')" />
                        </div>

                        <div>
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $branch->address) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="city" :value="__('City')" />
                                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $branch->city)" />
                                <x-input-error class="mt-2" :messages="$errors->get('city')" />
                            </div>
                            <div>
                                <x-input-label for="contact_number" :value="__('Contact Number')" />
                                <x-text-input id="contact_number" name="contact_number" type="text" class="mt-1 block w-full" :value="old('contact_number', $branch->contact_number)" />
                                <x-input-error class="mt-2" :messages="$errors->get('contact_number')" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="latitude" :value="__('Latitude')" />
                                <x-text-input id="latitude" name="latitude" type="number" step="any" class="mt-1 block w-full" :value="old('latitude', $branch->latitude)" placeholder="e.g. 14.5995" />
                                <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                            </div>
                            <div>
                                <x-input-label for="longitude" :value="__('Longitude')" />
                                <x-text-input id="longitude" name="longitude" type="number" step="any" class="mt-1 block w-full" :value="old('longitude', $branch->longitude)" placeholder="e.g. 120.9842" />
                                <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                            </div>
                        </div>

                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_active', $branch->is_active ?? true) ? 'checked' : '' }}>
                                <span class="ms-2 text-sm text-gray-600">Active</span>
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.branches.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <x-primary-button>
                                {{ $branch->exists ? __('Update Branch') : __('Create Branch') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
