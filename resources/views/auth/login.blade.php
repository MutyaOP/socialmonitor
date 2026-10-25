<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900">Жүйеге кіру</h2>
        <p class="mt-1 text-sm text-gray-500">Жалғастыру үшін аккаунтыңызға кіріңіз.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    placeholder="example@mail.kz"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-400 @enderror">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium text-gray-700">Құпиясөз</label>
                <div class="relative mt-1">
                    <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                        class="block w-full rounded-lg border-gray-300 pr-20 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-400 @enderror">
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-gray-500 hover:text-indigo-600"
                        x-text="show ? 'Жасыру' : 'Көрсету'"></button>
                </div>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    Мені есте сақтау
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:underline">Құпиясөзді ұмыттыңыз ба?</a>
                @endif
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Кіру
            </button>
        </form>

        @if (Route::has('register'))
            <p class="mt-6 text-center text-sm text-gray-600">
                Аккаунтыңыз жоқ па?
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:underline">Тіркелу</a>
            </p>
        @endif
    </div>
</x-guest-layout>
