<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">{{ $category->name }}</h2></x-slot>
    <div class="py-8"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-flash />
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
                <h3 class="font-semibold mb-2">Кілт сөздер (классификация сөздігі)</h3>
                <p class="text-xs text-gray-500 mb-2">Сөз түбірі токеннің басталуымен салыстырылады: «отоплен» → «отопление», «отоплением».</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($category->keywords->sortByDesc('weight') as $k)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 rounded">
                            {{ $k->word }} <b class="text-xs text-indigo-700">×{{ $k->weight }}</b>
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('keywords.destroy', $k) }}">@csrf @method('DELETE')<button class="text-red-600 ml-1">✕</button></form>
                            @endif
                        </span>
                    @endforeach
                </div>
                @if (auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('keywords.store', $category) }}" class="mt-3 flex gap-2">
                        @csrf
                        <input name="word" placeholder="Жаңа сөз түбірі" class="rounded border-gray-300 text-sm flex-1" required>
                        <select name="weight" class="rounded border-gray-300 text-sm">
                            <option value="1">салмақ 1</option><option value="2">салмақ 2</option><option value="3">салмақ 3</option>
                        </select>
                        <button class="px-3 py-1 bg-gray-800 text-white rounded">+</button>
                    </form>
                @endif
            </div>

            <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
                <h3 class="font-semibold mb-2">Сезім бойынша бөлінісі</h3>
                @foreach (config('socialmonitor.sentiment') as $k => $v)
                    <div class="flex justify-between border-t py-1"><x-badge type="sentiment" :value="$k" /> <b>{{ $sentiments[$k] ?? 0 }}</b></div>
                @endforeach
                <a class="text-indigo-700 text-xs mt-2 inline-block" href="{{ route('posts.index', ['category_id' => $category->id]) }}">Осы санаттағы барлық мәтін →</a>
            </div>
        </div>

        <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
            <h3 class="font-semibold mb-2">Соңғы мәтіндер</h3>
            @forelse ($posts as $p)
                <div class="border-t py-2 flex justify-between gap-3">
                    <a class="text-indigo-700 hover:underline" href="{{ route('posts.show', $p) }}">{{ $p->title }}</a>
                    <span class="whitespace-nowrap text-gray-500">{{ $p->published_at->format('d.m H:i') }} <x-badge type="sentiment" :value="$p->sentiment" /></span>
                </div>
            @empty
                <div class="text-gray-500">Мәтін жоқ</div>
            @endforelse
        </div>
    </div></div>
</x-app-layout>
