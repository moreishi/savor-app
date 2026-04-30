<x-app-layout>
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-4">
        <div class="text-center max-w-md">
            <!-- Offline icon -->
            <svg class="w-24 h-24 mx-auto text-indigo-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636a9 9 0 010 12.728m-2.829-2.829a5 5 0 010-7.07m-4.243 4.243a1 1 0 010-1.414m-4.243 4.243a9 9 0 010-12.728M3.515 3.515l18.384 18.384" />
            </svg>

            <h1 class="text-3xl font-bold text-gray-900 mb-3">You're Offline</h1>

            <p class="text-lg text-gray-600 mb-8">
                It looks like you've lost your internet connection. 
                Don't worry — your saved recipes and grocery list will be here when you're back online.
            </p>

            <button onclick="window.location.reload()" 
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Try Again
            </button>
        </div>
    </div>
</x-app-layout>
