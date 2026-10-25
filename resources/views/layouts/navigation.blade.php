@php
    $links = [
        ['dashboard', 'Басқару тақтасы', 'dashboard'],
        ['posts.index', 'Мәтіндер', 'posts.*'],
        ['analyze.index', 'Жылдам талдау', 'analyze.*'],
        ['alerts.index', 'Ескертулер', 'alerts.*'],
        ['categories.index', 'Санаттар', 'categories.*'],
        ['sources.index', 'Дереккөздер', 'sources.*'],
        ['reports.index', 'Есептер', 'reports.*'],
    ];
    if (auth()->user()->isAdmin()) {
        $links[] = ['users.index', 'Қолданушылар', 'users.*'];
    }
@endphp
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg text-indigo-700 mr-6">SocialMonitor</a>
                <div class="hidden xl:flex space-x-6">
                    @foreach ($links as [$route, $label, $pattern])
                        <x-nav-link :href="route($route)" :active="request()->routeIs($pattern)">{{ $label }}</x-nav-link>
                    @endforeach
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center">
                <span class="mr-3 text-xs rounded bg-indigo-100 text-indigo-800 px-2 py-1">{{ auth()->user()->isAdmin() ? 'Admin' : 'Оператор' }}</span>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">{{ Auth::user()->name }} ▾</button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Профиль</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Шығу</x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center xl:hidden">
                <button @click="open = ! open" class="p-2 rounded text-gray-500 hover:bg-gray-100">☰</button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden xl:hidden border-t">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($links as [$route, $label, $pattern])
                <x-responsive-nav-link :href="route($route)" :active="request()->routeIs($pattern)">{{ $label }}</x-responsive-nav-link>
            @endforeach
            <x-responsive-nav-link :href="route('profile.edit')">Профиль</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Шығу</x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
