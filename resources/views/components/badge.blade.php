@props(['type', 'value'])
@php
    $colors = [
        'negative' => 'bg-red-100 text-red-800', 'high' => 'bg-red-100 text-red-800',
        'escalated' => 'bg-red-100 text-red-800', 'open' => 'bg-red-100 text-red-800',
        'medium' => 'bg-yellow-100 text-yellow-800', 'neutral' => 'bg-gray-100 text-gray-700',
        'low' => 'bg-green-100 text-green-800', 'positive' => 'bg-green-100 text-green-800',
        'reviewed' => 'bg-green-100 text-green-800', 'new' => 'bg-blue-100 text-blue-800',
        'closed' => 'bg-gray-100 text-gray-700',
    ];
@endphp
<span class="px-2 py-1 rounded text-xs font-semibold {{ $colors[$value] ?? 'bg-gray-100 text-gray-700' }}">{{ config("socialmonitor.$type.$value", $value) }}</span>
