<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Мәтіндер</h2>
            <a href="{{ route('posts.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">+ Мәтін қосу</a>
        </div>
    </x-slot>
    <div class="py-8"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <x-flash />
        <form method="GET" class="bg-white p-4 shadow sm:rounded-lg mb-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Тақырып / мәтін бойынша іздеу" class="rounded border-gray-300 text-sm col-span-2">
            <select name="category_id" class="rounded border-gray-300 text-sm">
                <option value="">Барлық санат</option>
                @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach
            </select>
            <select name="source_id" class="rounded border-gray-300 text-sm">
                <option value="">Барлық дереккөз</option>
                @foreach ($sources as $s)<option value="{{ $s->id }}" @selected(request('source_id') == $s->id)>{{ $s->name }}</option>@endforeach
            </select>
            <select name="sentiment" class="rounded border-gray-300 text-sm">
                <option value="">Барлық сезім</option>
                @foreach (config('socialmonitor.sentiment') as $k => $v)<option value="{{ $k }}" @selected(request('sentiment') === $k)>{{ $v }}</option>@endforeach
            </select>
            <select name="severity" class="rounded border-gray-300 text-sm">
                <option value="">Барлық ауырлық</option>
                @foreach (config('socialmonitor.severity') as $k => $v)<option value="{{ $k }}" @selected(request('severity') === $k)>{{ $v }}</option>@endforeach
            </select>
            <select name="region" class="rounded border-gray-300 text-sm">
                <option value="">Барлық өңір</option>
                @foreach (config('socialmonitor.regions') as $r)<option value="{{ $r }}" @selected(request('region') === $r)>{{ $r }}</option>@endforeach
            </select>
            <select name="status" class="rounded border-gray-300 text-sm">
                <option value="">Барлық күй</option>
                @foreach (config('socialmonitor.post_status') as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach
            </select>
            <label>Бастап <input type="date" name="from" value="{{ request('from') }}" class="rounded border-gray-300 text-sm w-full"></label>
            <label>Дейін <input type="date" name="to" value="{{ request('to') }}" class="rounded border-gray-300 text-sm w-full"></label>
            <div class="flex items-end gap-3 col-span-2">
                <button class="px-4 py-2 bg-gray-800 text-white rounded">Сүзу</button>
                <a href="{{ route('posts.index') }}" class="py-2 text-gray-500">Тазалау</a>
            </div>
        </form>

        <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left"><tr><th class="p-2">Уақыты</th><th class="p-2">Тақырып</th><th class="p-2">Санат</th><th class="p-2">Өңір</th><th class="p-2">Сезім</th><th class="p-2">Ауырлық</th><th class="p-2">Күйі</th><th class="p-2"></th></tr></thead>
                <tbody>
                @forelse ($items as $p)
                    <tr class="border-t">
                        <td class="p-2 whitespace-nowrap">{{ $p->published_at->format('d.m.Y H:i') }}</td>
                        <td class="p-2 max-w-xs"><a class="text-indigo-700 hover:underline" href="{{ route('posts.show', $p) }}">{{ $p->title }}</a><div class="text-xs text-gray-500">{{ $p->source->name }}</div></td>
                        <td class="p-2"><x-cat :category="$p->primary()" /></td>
                        <td class="p-2">{{ $p->region }}</td>
                        <td class="p-2"><x-badge type="sentiment" :value="$p->sentiment" /></td>
                        <td class="p-2"><x-badge type="severity" :value="$p->severity" /> <span class="text-xs text-gray-500">{{ $p->severity_score }}</span></td>
                        <td class="p-2"><x-badge type="post_status" :value="$p->status" /></td>
                        <td class="p-2 text-right space-x-2 whitespace-nowrap">
                            <a class="text-yellow-700" href="{{ route('posts.edit', $p) }}">Өңдеу</a>
                            @if (auth()->user()->isAdmin())
                                <form class="inline" method="POST" action="{{ route('posts.destroy', $p) }}" onsubmit="return confirm('Жою?')">@csrf @method('DELETE')<button class="text-red-600">Жою</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-4 text-center text-gray-500">Мәтін табылмады</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
