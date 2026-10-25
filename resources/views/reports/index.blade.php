<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Есептер</h2>
            <a href="{{ route('reports.export', ['from' => $from, 'to' => $to]) }}" class="px-4 py-2 bg-green-600 text-white rounded text-sm">CSV жүктеу</a>
        </div>
    </x-slot>
    <div class="py-8"><div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-flash />
        <form method="GET" class="bg-white p-4 shadow sm:rounded-lg flex flex-wrap gap-3 items-end text-sm">
            <label>Бастап <input type="date" name="from" value="{{ $from }}" class="rounded border-gray-300 text-sm"></label>
            <label>Дейін <input type="date" name="to" value="{{ $to }}" class="rounded border-gray-300 text-sm"></label>
            <button class="px-4 py-2 bg-gray-800 text-white rounded">Көрсету</button>
        </form>

        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white shadow sm:rounded-lg p-4"><div class="text-xs text-gray-500">Мәтін саны</div><div class="text-3xl font-bold">{{ $summary['total'] }}</div></div>
            <div class="bg-white shadow sm:rounded-lg p-4"><div class="text-xs text-gray-500">Орташа ауырлық балы</div><div class="text-3xl font-bold">{{ $summary['avg_severity'] }}</div></div>
            <div class="bg-white shadow sm:rounded-lg p-4"><div class="text-xs text-gray-500">Орташа сезім (-1…1)</div><div class="text-3xl font-bold">{{ $summary['avg_sentiment'] }}</div></div>
        </div>

        <div class="bg-white shadow sm:rounded-lg p-4">
            <h3 class="font-semibold mb-2">Санаттар бойынша сезім</h3>
            <canvas id="catChart" height="90"></canvas>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 text-sm">
            <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">
                <h3 class="font-semibold mb-2">Санат × сезім</h3>
                <table class="min-w-full">
                    <thead class="bg-gray-100 text-left"><tr><th class="p-2">Санат</th><th class="p-2">Теріс</th><th class="p-2">Бейтарап</th><th class="p-2">Оң</th><th class="p-2">Барлығы</th><th class="p-2">Теріс, %</th></tr></thead>
                    <tbody>
                    @forelse ($matrix as $row)
                        <tr class="border-t"><td class="p-2">{{ $row['name'] }}</td><td class="p-2 text-red-600">{{ $row['negative'] }}</td><td class="p-2">{{ $row['neutral'] }}</td><td class="p-2 text-green-700">{{ $row['positive'] }}</td><td class="p-2 font-semibold">{{ $row['total'] }}</td><td class="p-2">{{ $row['neg_share'] }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="p-4 text-center text-gray-500">Деректер жоқ</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">
                <h3 class="font-semibold mb-2">Өңірлер</h3>
                <table class="min-w-full">
                    <thead class="bg-gray-100 text-left"><tr><th class="p-2">Өңір</th><th class="p-2">Барлығы</th><th class="p-2">Теріс</th></tr></thead>
                    <tbody>
                    @foreach ($regions as $r)
                        <tr class="border-t"><td class="p-2">{{ $r->region }}</td><td class="p-2">{{ $r->total }}</td><td class="p-2 text-red-600">{{ $r->negative }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const c = @json($chart);
        new Chart(document.getElementById('catChart'), {
            type: 'bar',
            data: { labels: c.labels, datasets: [
                { label: 'Теріс', data: c.negative, backgroundColor: '#ef4444' },
                { label: 'Бейтарап', data: c.neutral, backgroundColor: '#9ca3af' },
                { label: 'Оң', data: c.positive, backgroundColor: '#22c55e' },
            ] },
            options: { scales: { x: { stacked: true }, y: { stacked: true } } },
        });
    </script>
</x-app-layout>
