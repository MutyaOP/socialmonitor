<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Мониторинг ескертулері</h2>
            <form method="POST" action="{{ route('alerts.scan') }}">@csrf<button class="px-4 py-2 bg-indigo-600 text-white rounded text-sm">Мониторингті іске қосу</button></form>
        </div>
    </x-slot>
    <div class="py-8"><div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <x-flash />
        <form method="GET" class="bg-white p-4 shadow sm:rounded-lg mb-4 flex gap-3 text-sm">
            <select name="status" class="rounded border-gray-300 text-sm">
                <option value="">Барлық күй</option>
                @foreach (config('socialmonitor.alert_status') as $k => $v)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>@endforeach
            </select>
            <select name="level" class="rounded border-gray-300 text-sm">
                <option value="">Барлық деңгей</option>
                @foreach (config('socialmonitor.severity') as $k => $v)<option value="{{ $k }}" @selected(request('level') === $k)>{{ $v }}</option>@endforeach
            </select>
            <button class="px-4 py-2 bg-gray-800 text-white rounded">Сүзу</button>
        </form>
        <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left"><tr><th class="p-2">Уақыты</th><th class="p-2">Санат</th><th class="p-2">Деңгей</th><th class="p-2">Хабарлама</th><th class="p-2">Күйі</th><th class="p-2"></th></tr></thead>
                <tbody>
                @forelse ($items as $a)
                    <tr class="border-t">
                        <td class="p-2 whitespace-nowrap">{{ $a->updated_at->format('d.m.Y H:i') }}</td>
                        <td class="p-2"><x-cat :category="$a->category" /></td>
                        <td class="p-2"><x-badge type="severity" :value="$a->level" /></td>
                        <td class="p-2">{{ $a->message }}<div><a class="text-indigo-700 text-xs" href="{{ route('posts.index', ['category_id' => $a->category_id, 'sentiment' => 'negative']) }}">Мәтіндерді көру →</a></div></td>
                        <td class="p-2"><x-badge type="alert_status" :value="$a->status" /></td>
                        <td class="p-2 text-right">
                            @if ($a->status === 'open')
                                <form method="POST" action="{{ route('alerts.close', $a) }}">@csrf @method('PATCH')<button class="text-green-700">Жабу</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-500">Ескерту жоқ</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
