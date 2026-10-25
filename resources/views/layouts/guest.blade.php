<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SocialMonitor') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-indigo-700 via-indigo-600 to-purple-700 px-4 py-10">

        {{-- Логотип --}}
        <div class="mb-6 flex items-center gap-3 text-white">
            <svg class="h-11 w-11" viewBox="0 0 40 40" fill="none">
                <rect width="40" height="40" rx="10" fill="white" fill-opacity=".2"/>
                <rect x="9" y="22" width="5" height="9" rx="1.5" fill="white"/>
                <rect x="17.5" y="15" width="5" height="16" rx="1.5" fill="white"/>
                <rect x="26" y="9" width="5" height="22" rx="1.5" fill="white"/>
            </svg>
            <span class="text-3xl font-bold tracking-tight">SocialMonitor</span>
        </div>

        {{-- Форма ортада --}}
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>

        <p class="mt-6 text-sm text-indigo-200">Әлеуметтік мәселелерді мәтіндер арқылы мониторингтеу жүйесі</p>
    </div>
</body>
</html>
