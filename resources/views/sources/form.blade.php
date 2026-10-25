<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">{{ $item->exists ? 'Дереккөзді өңдеу' : 'Жаңа дереккөз' }}</h2></x-slot>
    <div class="py-8"><div class="max-w-xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow sm:rounded-lg">
        <x-flash />
        <form method="POST" action="{{ $item->exists ? route('sources.update', $item) : route('sources.store') }}" class="space-y-4">
            @csrf
            @if ($item->exists) @method('PUT') @endif
            <div><x-input-label for="name" value="Атауы" /><x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $item->name)" required /></div>
            <div>
                <x-input-label for="type" value="Түрі" />
                <select id="type" name="type" class="mt-1 block w-full rounded border-gray-300">
                    @foreach (config('socialmonitor.source_types') as $k => $v)<option value="{{ $k }}" @selected(old('type', $item->type) === $k)>{{ $v }}</option>@endforeach
                </select>
            </div>
            <div><x-input-label for="url" value="Сілтеме (міндетті емес)" /><x-text-input id="url" name="url" class="mt-1 block w-full" :value="old('url', $item->url)" /></div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))> Белсенді</label>
            <div class="flex gap-3"><x-primary-button>Сақтау</x-primary-button><a href="{{ route('sources.index') }}" class="py-2 text-gray-600">Болдырмау</a></div>
        </form>
    </div></div></div>
</x-app-layout>
