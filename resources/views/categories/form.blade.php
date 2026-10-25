<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">{{ $item->exists ? 'Санатты өңдеу' : 'Жаңа санат' }}</h2></x-slot>
    <div class="py-8"><div class="max-w-xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow sm:rounded-lg">
        <x-flash />
        <form method="POST" action="{{ $item->exists ? route('categories.update', $item) : route('categories.store') }}" class="space-y-4">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div><x-input-label for="name" value="Атауы" /><x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $item->name)" required /></div>
            <div><x-input-label for="color" value="Түсі" /><input id="color" type="color" name="color" value="{{ old('color', $item->color) }}" class="mt-1 h-10 w-20 rounded border-gray-300"></div>
            <div><x-input-label for="description" value="Сипаттама" /><textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded border-gray-300">{{ old('description', $item->description) }}</textarea></div>
            <div class="flex gap-3"><x-primary-button>Сақтау</x-primary-button><a href="{{ route('categories.index') }}" class="py-2 text-gray-600">Болдырмау</a></div>
        </form>
    </div></div></div>
</x-app-layout>
