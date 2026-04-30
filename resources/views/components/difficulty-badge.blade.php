@props(['difficulty'])

@php
    $colors = [
        'easy' => 'bg-emerald-100 text-emerald-700',
        'medium' => 'bg-amber-100 text-amber-700',
        'hard' => 'bg-red-100 text-red-700',
    ];
    $color = $colors[$difficulty] ?? 'bg-gray-100 text-gray-700';
@endphp

<span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">
    {{ ucfirst($difficulty) }}
</span>
