<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">{{ $post->title }}</h2>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('posts.reanalyze', $post) }}">@csrf<button class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">Қайта талдау</button></form>
                <a href="{{ route('posts.edit', $post) }}" class="px-4 py-2 bg-yellow-500 text-white rounded text-sm">Өңдеу</a>
            </div>
        </div>
    </x-slot>
    <div class="py-8"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-flash />

        <div class="bg-white shadow sm:rounded-lg p-6">
            <div class="text-xs text-gray-500 mb-3">
                {{ $post->source->name }} · {{ $post->region }} · {{ $post->published_at->format('d.m.Y H:i') }}
                @if ($post->user) · енгізген: {{ $post->user->name }} @endif
            </div>
            <p class="whitespace-pre-line">{{ $post->body }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
                <h3 class="font-semibold mb-2">Сезім (sentiment)</h3>
                <x-badge type="sentiment" :value="$post->sentiment" />
                <div class="mt-2 text-2xl font-bold">{{ $post->sentiment_score }}</div>
                <div class="text-xs text-gray-500">шкала: -1 (теріс) … +1 (оң)</div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
                <h3 class="font-semibold mb-2">Ауырлық / шұғылдық</h3>
                <x-badge type="severity" :value="$post->severity" />
                <div class="mt-2 text-2xl font-bold">{{ $post->severity_score }}<span class="text-sm text-gray-500"> / 100</span></div>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
                <h3 class="font-semibold mb-2">Күйі</h3>
                <form method="POST" action="{{ route('posts.status', $post) }}" class="flex gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="rounded border-gray-300 text-sm flex-1">
                        @foreach (config('socialmonitor.post_status') as $k => $v)<option value="{{ $k }}" @selected($post->status === $k)>{{ $v }}</option>@endforeach
                    </select>
                    <button class="px-3 py-1 bg-gray-800 text-white rounded">OK</button>
                </form>
                <div class="mt-3 text-xs text-gray-500">Негізгі сөздер:</div>
                <div class="flex flex-wrap gap-1 mt-1">
                    @foreach ($post->top_words ?? [] as $w)<span class="px-2 py-0.5 bg-gray-100 rounded text-xs">{{ $w }}</span>@endforeach
                </div>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
            <h3 class="font-semibold mb-2">Анықталған санаттар (түсіндірме)</h3>
            @forelse ($analysis['categories'] as $c)
                <div class="border-t py-2 flex flex-wrap items-center gap-3">
                    <x-cat :category="$c" />
                    @if ($c->pivot->is_primary)<span class="text-xs text-indigo-700 font-semibold">негізгі</span>@endif
                    <span class="text-gray-500">балл: {{ $c->pivot->score }}</span>
                    <span class="text-gray-600">кілт сөздер: {{ $c->pivot->matched }}</span>
                </div>
            @empty
                <div class="text-gray-500">Ешбір санатқа жатқызылмады (кілт сөздер табылмады).</div>
            @endforelse
        </div>
    </div></div>
</x-app-layout>
