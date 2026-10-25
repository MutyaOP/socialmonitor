<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900">Тіркелу</h2>
        <p class="mt-1 text-sm text-gray-500">Жаңа аккаунт жасаңыз. Бастапқы рөл — «Оператор».</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Аты-жөні</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    placeholder="example@mail.kz"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-400 @enderror">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div x-data="{ show: false }" class="space-y-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Құпиясөз</label>
                    <div class="relative mt-1">
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                            class="block w-full rounded-lg border-gray-300 pr-20 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-400 @enderror">
                        <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-0 px-3 text-xs font-medium text-gray-500 hover:text-indigo-600"
                            x-text="show ? 'Жасыру' : 'Көрсету'"></button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Кемінде 8 таңба.</p>
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Құпиясөзді қайталаңыз</label>
                    <input id="password_confirmation" :type="show ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('password_confirmation')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-indigo-600 px-4 py-2.5 font-semibold text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Тіркелу
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Аккаунтыңыз бар ма?
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:underline">Кіру</a>
        </p>
    </div>
</x-guest-layout>
