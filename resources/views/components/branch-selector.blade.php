@props(['branches' => [], 'selected' => null, 'form' => false])

@php
    $brandBadge = [
        'osave' => ['label' => 'O!Save', 'class' => 'bg-orange-100 text-orange-800'],
        'robinsons' => ['label' => 'Robinsons', 'class' => 'bg-blue-100 text-blue-800'],
        'easymart' => ['label' => 'Easymart', 'class' => 'bg-green-100 text-green-800'],
        'shopwise' => ['label' => 'Shopwise', 'class' => 'bg-purple-100 text-purple-800'],
    ];
@endphp

<div x-data="{
    branchId: @js($selected ?? ($branches->first()->id ?? null)),
    init() {
        this.$watch('branchId', (val) => {
            Alpine.store('branch', { id: val });
        });
        Alpine.store('branch', { id: this.branchId });
    }
}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Select your store</label>
            <p class="text-xs text-gray-400 mt-0.5">Prices vary by store location</p>
        </div>
    </div>

    @if ($form)
        <form method="POST" action="{{ route('grocery-list.branch') }}">
            @csrf
            <select name="branch_id" x-model="branchId"
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $selected == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <div class="mt-3">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    Update Store
                </button>
            </div>
        </form>
    @else
        <select x-model="branchId"
                @change="Alpine.store('branch', { id: branchId })"
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ $selected == $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>

        <!-- Brand badges -->
        <div class="mt-3 flex flex-wrap items-center gap-2">
            @foreach($branches->groupBy('brand') as $brand => $group)
                @php $badge = $brandBadge[$brand] ?? ['label' => $brand, 'class' => 'bg-gray-100 text-gray-800']; @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $badge['class'] }}">
                    {{ $badge['label'] }}
                    <span class="opacity-60">·</span>
                    <span class="opacity-75">{{ $group->count() }} branch{{ $group->count() !== 1 ? 'es' : '' }}</span>
                </span>
            @endforeach
        </div>
    @endif
</div>
