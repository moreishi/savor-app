<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="shrink-0 bg-indigo-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ms-4">
                                <p class="text-sm font-medium text-gray-500">Total Recipes</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['recipes'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.recipes.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Manage Recipes &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="shrink-0 bg-green-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="ms-4">
                                <p class="text-sm font-medium text-gray-500">Total Ingredients</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['ingredients'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.ingredients.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Manage Ingredients &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="shrink-0 bg-yellow-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ms-4">
                                <p class="text-sm font-medium text-gray-500">Total Branches</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['branches'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('admin.branches.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Manage Branches &rarr;</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="shrink-0 bg-purple-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ms-4">
                                <p class="text-sm font-medium text-gray-500">Price Records</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['prices'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('prices.import') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">Import Prices &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Links</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="{{ route('admin.tags.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="text-sm font-medium text-gray-700">Manage Tags</span>
                            <span class="ms-auto text-gray-400">&rarr;</span>
                        </a>
                        <a href="{{ route('recipes.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="text-sm font-medium text-gray-700">View Public Recipes</span>
                            <span class="ms-auto text-gray-400">&rarr;</span>
                        </a>
                        <a href="{{ route('grocery-list.index') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="text-sm font-medium text-gray-700">Grocery List</span>
                            <span class="ms-auto text-gray-400">&rarr;</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <span class="text-sm font-medium text-gray-700">Profile Settings</span>
                            <span class="ms-auto text-gray-400">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
