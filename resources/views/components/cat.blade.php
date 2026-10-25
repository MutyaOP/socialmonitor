@props(['category'])
@if ($category)
    <span class="px-2 py-1 rounded text-xs font-semibold" style="background: {{ $category->color }}22; color: {{ $category->color }}">{{ $category->name }}</span>
@endif
