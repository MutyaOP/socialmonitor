<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Санаттар (әлеуметтік мәселелер)</h2>
            @if (auth()->user()->isAdmin())
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('categories.reanalyze-all') }}" onsubmit="return confirm('Барлық мәтін қайта талданады. Жалғастыру?')">@csrf
                        <button class="px-4 py-2 bg-gray-700 text-white rounded text-sm">Барлығын қайта талдау</button>
                    </form>
                    <a href="{{ route('categories.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">+ Қосу</a>
                </div>
            @endif
        </div>
    </x-slot>
    <div class="py-8"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <x-flash />
        <form method="GET" class="bg-white p-4 shadow sm:rounded-lg mb-4 flex gap-3 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Атауы" class="rounded border-gray-300 text-sm">
            <button class="px-4 py-2 bg-gray-800 text-white rounded">Іздеу</button>
        </form>
        <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left"><tr><th class="p-2">Санат</th><th class="p-2">Кілт сөздер</th><th class="p-2">Мәтіндер</th><th class="p-2"></th></tr></thead>
                <tbody>
                @foreach ($items as $c)
                    <tr class="border-t">
                        <td class="p-2"><x-cat :category="$c" /></td>
                        <td class="p-2">{{ $c->keywords_count }}</td>
                        <td class="p-2">{{ $c->posts_count }}</td>
                        <td class="p-2 text-right space-x-2">
                            <a class="text-indigo-700" href="{{ route('categories.show', $c) }}">Ашу</a>
                            @if (auth()->user()->isAdmin())
                                <a class="text-yellow-700" href="{{ route('categories.edit', $c) }}">Өңдеу</a>
                                <form class="inline" method="POST" action="{{ route('categories.destroy', $c) }}" onsubmit="return confirm('Жою?')">@csrf @method('DELETE')<button class="text-red-600">Жою</button></form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div></div>
</x-app-layout>
