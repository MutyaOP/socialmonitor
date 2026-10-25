<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Дереккөздер</h2>
            @if (auth()->user()->isAdmin())<a href="{{ route('sources.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">+ Қосу</a>@endif
        </div>
    </x-slot>
    <div class="py-8"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <x-flash />
        <form method="GET" class="bg-white p-4 shadow sm:rounded-lg mb-4 flex gap-3 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Атауы" class="rounded border-gray-300 text-sm">
            <select name="type" class="rounded border-gray-300 text-sm">
                <option value="">Барлық түр</option>
                @foreach (config('socialmonitor.source_types') as $k => $v)<option value="{{ $k }}" @selected(request('type') === $k)>{{ $v }}</option>@endforeach
            </select>
            <button class="px-4 py-2 bg-gray-800 text-white rounded">Іздеу</button>
        </form>
        <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left"><tr><th class="p-2">Атауы</th><th class="p-2">Түрі</th><th class="p-2">Мәтін саны</th><th class="p-2">Белсенді</th><th class="p-2"></th></tr></thead>
                <tbody>
                @forelse ($items as $s)
                    <tr class="border-t">
                        <td class="p-2">{{ $s->name }}@if ($s->url)<div class="text-xs text-gray-500">{{ $s->url }}</div>@endif</td>
                        <td class="p-2">{{ config('socialmonitor.source_types.' . $s->type) }}</td>
                        <td class="p-2">{{ $s->posts_count }}</td>
                        <td class="p-2">{{ $s->is_active ? 'Иә' : 'Жоқ' }}</td>
                        <td class="p-2 text-right space-x-2">
                            @if (auth()->user()->isAdmin())
                                <a class="text-yellow-700" href="{{ route('sources.edit', $s) }}">Өңдеу</a>
                                <form class="inline" method="POST" action="{{ route('sources.destroy', $s) }}" onsubmit="return confirm('Дереккөзбен бірге оның мәтіндері де жойылады. Жалғастыру?')">@csrf @method('DELETE')<button class="text-red-600">Жою</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-4 text-center text-gray-500">Деректер жоқ</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
