<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Жылдам талдау (сақтамай)</h2></x-slot>
    <div class="py-8"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-flash />
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <form method="POST" action="{{ route('analyze.run') }}" class="space-y-4">
                @csrf
                <div><x-input-label for="title" value="Тақырып (міндетті емес)" /><x-text-input id="title" name="title" class="mt-1 block w-full" :value="old('title')" /></div>
                <div>
                    <x-input-label for="body" value="Талданатын мәтін" />
                    <textarea id="body" name="body" rows="7" class="mt-1 block w-full rounded border-gray-300" required>{{ old('body') }}</textarea>
                </div>
                <x-primary-button>Талдау</x-primary-button>
            </form>
        </div>

        @if ($result)
            <div class="bg-white p-6 shadow sm:rounded-lg text-sm space-y-3">
                <h3 class="font-semibold text-base">Нәтиже</h3>
                <div class="flex flex-wrap gap-6">
                    <div>Сезім: <x-badge type="sentiment" :value="$result['sentiment']" /> <b>{{ $result['sentiment_score'] }}</b></div>
                    <div>Ауырлық: <x-badge type="severity" :value="$result['severity']" /> <b>{{ $result['severity_score'] }}/100</b></div>
                    <div class="text-gray-500">Токен саны: {{ $result['token_count'] }}</div>
                </div>
                <div>
                    <div class="font-semibold mb-1">Санаттар</div>
                    @forelse ($result['categories'] as $c)
                        <div class="py-1"><x-cat :category="$c['category']" /> балл {{ $c['score'] }} — {{ implode(', ', $c['matched']) }}</div>
                    @empty
                        <div class="text-gray-500">Санат анықталмады</div>
                    @endforelse
                </div>
                <div><span class="font-semibold">Теріс белгілер:</span> {{ implode(', ', $result['negative_hits']) ?: '—' }}</div>
                <div><span class="font-semibold">Оң белгілер:</span> {{ implode(', ', $result['positive_hits']) ?: '—' }}</div>
                <div><span class="font-semibold">Шұғылдық белгілері:</span> {{ implode(', ', $result['urgent_hits']) ?: '—' }}</div>
                <div><span class="font-semibold">Негізгі сөздер:</span> {{ implode(', ', $result['top_words']) ?: '—' }}</div>
            </div>
        @endif
    </div></div>
</x-app-layout>
