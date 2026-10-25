<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Қолданушылар және рөлдер</h2></x-slot>
    <div class="py-8"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <x-flash />
        <form method="GET" class="bg-white p-4 shadow sm:rounded-lg mb-4 flex gap-3 text-sm">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Аты немесе email" class="rounded border-gray-300 text-sm">
            <button class="px-4 py-2 bg-gray-800 text-white rounded">Іздеу</button>
        </form>
        <div class="bg-white shadow sm:rounded-lg p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-left"><tr><th class="p-2">Аты</th><th class="p-2">Email</th><th class="p-2">Рөлі</th></tr></thead>
                <tbody>
                @foreach ($items as $u)
                    <tr class="border-t">
                        <td class="p-2">{{ $u->name }}</td>
                        <td class="p-2">{{ $u->email }}</td>
                        <td class="p-2">
                            <form method="POST" action="{{ route('users.update', $u) }}" class="flex gap-2">
                                @csrf @method('PATCH')
                                <select name="role" class="rounded border-gray-300 text-sm" @disabled($u->id === auth()->id())>
                                    <option value="operator" @selected($u->role === 'operator')>Оператор</option>
                                    <option value="admin" @selected($u->role === 'admin')>Admin</option>
                                </select>
                                @if ($u->id !== auth()->id())<button class="px-3 py-1 bg-gray-800 text-white rounded">Сақтау</button>@endif
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="mt-4">{{ $items->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
