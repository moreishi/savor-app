<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Import Branch Prices
        </h2>
    </x-slot>

    <div x-data="priceImport()" class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <!-- Step 1: Upload -->
            <div x-show="step === 'upload'" class="bg-white dark:bg-gray-800 p-6 rounded shadow">
                <h3 class="text-lg font-medium mb-4">Upload CSV File</h3>

                <label class="block mb-2 font-medium text-sm">Select Branch</label>
                <select x-model="branchId" class="w-full border dark:border-gray-600 rounded p-2 mb-4 bg-white dark:bg-gray-700">
                    <option value="">-- Choose Branch --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>

                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 p-8 text-center rounded cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event)">
                    <p x-show="!file" class="text-gray-500 dark:text-gray-400">Drop CSV here or click to browse</p>
                    <p x-show="file" class="text-green-600" x-text="file.name"></p>
                    <input type="file" accept=".csv" @change="file = $event.target.files[0]" class="hidden"
                           :class="{ 'hidden': false }" id="file-input">
                </div>

                <div class="flex justify-between items-center mt-4">
                    <a href="{{ route('prices.template') }}" class="text-sm text-blue-600 hover:underline">
                        Download CSV Template
                    </a>
                    <button @click="upload()"
                            :disabled="!branchId || !file"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed transition">
                        Upload & Validate
                    </button>
                </div>
            </div>

            <!-- Step 2: Validating -->
            <div x-show="step === 'validating'" class="bg-white dark:bg-gray-800 p-6 rounded shadow text-center">
                <h3 class="text-lg font-medium mb-4">Validating CSV...</h3>
                <div class="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full mx-auto"></div>
                <p class="text-sm text-gray-500 mt-4" x-text="statusMessage"></p>
            </div>

            <!-- Step 3: Preview -->
            <div x-show="step === 'preview'" class="bg-white dark:bg-gray-800 p-6 rounded shadow">
                <h3 class="text-lg font-medium mb-4">Validation Results</h3>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="bg-green-50 dark:bg-green-900/30 p-3 rounded text-center">
                        <p class="text-2xl font-bold text-green-600" x-text="result.valid_rows"></p>
                        <p class="text-sm text-gray-500">Valid Rows</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/30 p-3 rounded text-center" x-show="result.error_rows > 0">
                        <p class="text-2xl font-bold text-red-600" x-text="result.error_rows"></p>
                        <p class="text-sm text-gray-500">Errors</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded text-center">
                        <p class="text-2xl font-bold text-blue-600" x-text="result.total_rows"></p>
                        <p class="text-sm text-gray-500">Total Rows</p>
                    </div>
                </div>

                <!-- Error list -->
                <template x-if="result.errors && result.errors.length > 0">
                    <div class="mb-4">
                        <h4 class="font-medium text-red-600 mb-2">Row Errors:</h4>
                        <template x-for="err in result.errors" :key="err.line">
                            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 my-2 rounded text-sm">
                                <strong>Line <span x-text="err.line"></span>:</strong>
                                <span x-text="err.errors.join(', ')"></span>
                                <code class="block text-xs text-gray-500 mt-1" x-text="JSON.stringify(err.data)"></code>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="flex gap-3 mt-4">
                    <button @click="step = 'upload'" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">
                        Back
                    </button>
                    <button @click="confirm()"
                            :disabled="result.valid_rows === 0"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded disabled:opacity-50 transition">
                        Confirm Import (<span x-text="result.valid_rows"></span> rows)
                    </button>
                </div>
            </div>

            <!-- Step 4: Done -->
            <div x-show="step === 'done'" class="bg-white dark:bg-gray-800 p-6 rounded shadow text-center">
                <div class="text-4xl mb-4">✅</div>
                <h3 class="text-xl font-medium text-green-600">Import Completed!</h3>
                <p class="text-gray-500 mt-2">Imported <strong x-text="result.valid_rows"></strong> prices successfully.</p>
                <a href="{{ route('prices.import') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded transition">
                    Import Another File
                </a>
            </div>

            <!-- Error state -->
            <div x-show="step === 'error'" class="bg-white dark:bg-gray-800 p-6 rounded shadow text-center">
                <div class="text-4xl mb-4">❌</div>
                <h3 class="text-xl font-medium text-red-600">Something went wrong</h3>
                <p class="text-gray-500 mt-2" x-text="errorMessage"></p>
                <button @click="step = 'upload'" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded transition">
                    Try Again
                </button>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function priceImport() {
            return {
                step: 'upload',
                branchId: '',
                file: null,
                importId: null,
                result: {},
                pollTimer: null,
                pollAttempts: 0,
                statusMessage: '',
                errorMessage: '',

                async upload() {
                    if (!this.branchId || !this.file) return;

                    const form = new FormData();
                    form.append('file', this.file);
                    form.append('branch_id', this.branchId);

                    this.step = 'validating';
                    this.pollAttempts = 0;
                    this.statusMessage = 'Uploading...';

                    try {
                        const res = await fetch('{{ route("prices.import") }}', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: form,
                        });

                        if (!res.ok) {
                            const err = await res.json();
                            this.errorMessage = err.message || 'Upload failed';
                            this.step = 'error';
                            return;
                        }

                        const data = await res.json();
                        this.importId = data.id;
                        this.statusMessage = 'Validating...';
                        this.pollStatus();
                    } catch (e) {
                        this.errorMessage = 'Network error: ' + e.message;
                        this.step = 'error';
                    }
                },

                pollStatus() {
                    this.pollTimer = setInterval(async () => {
                        this.pollAttempts++;

                        try {
                            const res = await fetch(`/prices/import/${this.importId}/status`);
                            const data = await res.json();

                            if (data.status === 'validated') {
                                clearInterval(this.pollTimer);
                                this.result = data;
                                this.step = 'preview';
                            } else if (data.status === 'failed') {
                                clearInterval(this.pollTimer);
                                this.errorMessage = 'Validation failed';
                                this.result = data;
                                this.step = 'error';
                            } else {
                                this.statusMessage = `Validating... (${this.pollAttempts}s)`;
                            }
                        } catch (e) {
                            // Exponential backoff on network error
                            if (this.pollAttempts > 30) {
                                clearInterval(this.pollTimer);
                                this.errorMessage = 'Status check timed out';
                                this.step = 'error';
                            }
                        }
                    }, 2000);
                },

                async confirm() {
                    this.step = 'validating';
                    this.statusMessage = 'Importing prices...';

                    try {
                        const res = await fetch(`/prices/import/${this.importId}/confirm`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        });

                        if (!res.ok) {
                            const err = await res.json();
                            this.errorMessage = err.message || 'Confirm failed';
                            this.step = 'error';
                            return;
                        }

                        // Poll for completion
                        this.pollConfirmStatus();
                    } catch (e) {
                        this.errorMessage = 'Network error: ' + e.message;
                        this.step = 'error';
                    }
                },

                pollConfirmStatus() {
                    this.pollTimer = setInterval(async () => {
                        try {
                            const res = await fetch(`/prices/import/${this.importId}/status`);
                            const data = await res.json();

                            if (data.status === 'completed') {
                                clearInterval(this.pollTimer);
                                this.result = data;
                                this.step = 'done';
                            } else if (data.status === 'failed') {
                                clearInterval(this.pollTimer);
                                this.errorMessage = 'Import failed';
                                this.result = data;
                                this.step = 'error';
                            }
                        } catch (e) {
                            // keep polling
                        }
                    }, 2000);
                },

                handleDrop(event) {
                    const files = event.dataTransfer.files;
                    if (files.length > 0 && files[0].name.endsWith('.csv')) {
                        this.file = files[0];
                    }
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
