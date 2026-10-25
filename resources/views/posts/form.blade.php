<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">{{ $item->exists ? 'Мәтінді өңдеу' : 'Жаңа мәтін' }}</h2></x-slot>
    <div class="py-8"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white p-6 shadow sm:rounded-lg">
        <x-flash />
        <form method="POST" action="{{ $item->exists ? route('posts.update', $item) : route('posts.store') }}" class="space-y-4">
            @csrf
            @if ($item->exists) @method('PUT') @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="source_id" value="Дереккөз" />
                    <select id="source_id" name="source_id" class="mt-1 block w-full rounded border-gray-300" required>
                        @foreach ($sources as $s)<option value="{{ $s->id }}" @selected(old('source_id', $item->source_id) == $s->id)>{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="region" value="Өңір" />
                    <select id="region" name="region" class="mt-1 block w-full rounded border-gray-300">
                        @foreach (config('socialmonitor.regions') as $r)<option value="{{ $r }}" @selected(old('region', $item->region) === $r)>{{ $r }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="published_at" value="Жарияланған уақыты" />
                    <x-text-input id="published_at" type="datetime-local" name="published_at" class="mt-1 block w-full"
                        :value="old('published_at', ($item->published_at ?? now())->format('Y-m-d\TH:i'))" required />
                </div>
            </div>
            <div><x-input-label for="title" value="Тақырып" /><x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title', $item->title)" required /></div>
            <div>
                <x-input-label for="body" value="Мәтін (қазақша немесе орысша)" />
                <textarea id="body" name="body" rows="8" class="mt-1 block w-full rounded border-gray-300" required>{{ old('body', $item->body) }}</textarea>
            </div>
            <div>
                <x-input-label for="status" value="Күйі" />
                <select id="status" name="status" class="mt-1 block w-full rounded border-gray-300">
                    @foreach (config('socialmonitor.post_status') as $k => $v)<option value="{{ $k }}" @selected(old('status', $item->status) === $k)>{{ $v }}</option>@endforeach
                </select>
            </div>
            <p class="text-xs text-gray-500">Санат, сезім және ауырлық балы сақтау кезінде автоматты түрде есептеледі.</p>
            <div class="flex gap-3"><x-primary-button>Сақтау және талдау</x-primary-button><a href="{{ route('posts.index') }}" class="py-2 text-gray-600">Болдырмау</a></div>
        </form>
    </div></div></div>
</x-app-layout>
