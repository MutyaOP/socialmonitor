<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Әлеуметтік мәселелер мониторингі</h2>
            <form method="POST" action="{{ route('alerts.scan') }}">@csrf
                <button class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">Мониторингті іске қосу</button>
            </form>
        </div>
    </x-slot>

    <div class="py-8"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <x-flash />

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['Барлық мәтін', $stats['total'], 'text-gray-800'],
                ['Теріс мәтіндер үлесі, %', $stats['negative_share'], 'text-red-600'],
                ['Ашық ескертулер', $stats['open_alerts'], 'text-yellow-600'],
                ['Қаралмаған шұғыл мәтін', $stats['critical'], 'text-indigo-600'],
            ] as [$label, $value, $color])
                <div class="bg-white shadow sm:rounded-lg p-4">
                    <div class="text-xs text-gray-500">{{ $label }}</div>
                    <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white shadow sm:rounded-lg p-4 lg:col-span-2">
                <h3 class="font-semibold mb-2">Соңғы 14 күн: барлық және теріс мәтіндер</h3>
                <canvas id="dayChart" height="110"></canvas>
            </div>
            <div class="bg-white shadow sm:rounded-lg p-4">
                <h3 class="font-semibold mb-2">Санаттар бойынша</h3>
                <canvas id="catChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white shadow sm:rounded-lg p-4">
                <h3 class="font-semibold mb-2">Өңірлер бойынша</h3>
                <canvas id="regionChart"></canvas>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
                <h3 class="font-semibold mb-2">Ашық ескертулер</h3>
                @forelse ($alerts as $a)
                    <div class="border-t py-2">
                        <x-badge type="severity" :value="$a->level" /> <x-cat :category="$a->category" />
                        <div class="mt-1 text-gray-700">{{ $a->message }}</div>
                    </div>
                @empty
                    <div class="text-gray-500">Ашық ескерту жоқ</div>
                @endforelse
                <a href="{{ route('alerts.index') }}" class="text-indigo-700 text-xs">Барлық ескертулер →</a>
            </div>

            <div class="bg-white shadow sm:rounded-lg p-4 text-sm">
                <h3 class="font-semibold mb-2">Шұғыл назар аударуды қажет ететін мәтіндер</h3>
                @forelse ($critical as $p)
                    <div class="border-t py-2">
                        <a class="text-indigo-700 hover:underline" href="{{ route('posts.show', $p) }}">{{ $p->title }}</a>
                        <div class="text-xs text-gray-500">{{ $p->region }} · {{ $p->published_at->format('d.m H:i') }} · балл {{ $p->severity_score }}</div>
                        <x-cat :category="$p->primary()" />
                    </div>
                @empty
                    <div class="text-gray-500">Жоқ</div>
                @endforelse
            </div>
        </div>
    </div></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const day = @json($dayChart);
        new Chart(document.getElementById('dayChart'), {
            type: 'line',
            data: { labels: day.labels, datasets: [
                { label: 'Барлығы', data: day.totals, borderColor: '#4f46e5', tension: .3 },
                { label: 'Теріс', data: day.negatives, borderColor: '#ef4444', tension: .3 },
            ] },
        });
        const cat = @json($catChart);
        new Chart(document.getElementById('catChart'), {
            type: 'doughnut',
            data: { labels: cat.labels, datasets: [{ data: cat.values, backgroundColor: cat.colors }] },
            options: { plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } } },
        });
        const reg = @json($regionChart);
        new Chart(document.getElementById('regionChart'), {
            type: 'bar',
            data: { labels: reg.labels, datasets: [{ label: 'Мәтін саны', data: reg.values, backgroundColor: '#6366f1' }] },
            options: { indexAxis: 'y' },
        });
    </script>
</x-app-layout>
