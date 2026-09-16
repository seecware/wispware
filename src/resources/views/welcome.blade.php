<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans">
        <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
            <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            <img class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]" src="https://avatars.githubusercontent.com/u/179284048?v=4">
                        </div>
@if (Route::has('login'))
    <nav class="-mx-3 flex flex-1 justify-end">
        @auth
            <a href="{{ url('/dashboard') }}" class="rounded-md px-3 py-2 text-white ring-1 ring-transparent transition hover:text-white/70 focus:outline-none">
                Dashboard
            </a>
        @else
            <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-white/70 focus:outline-none">
                Log in
            </a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-white/70 focus:outline-none">
                    Register
                </a>
            @endif
        @endauth
    </nav>
@endif
                    </header>

                    <main class="mt-6">
                        <div style="background: #1e293b; color: #f8fafc; padding: 20px; border-radius: 8px; font-family: monospace; max-width: 600px; margin: 20px auto;">
    <h2 style="color: #38bdf8; margin-top: 0;">📡 Información del Cliente (Wispware)</h2>
    <p><strong>IP Local:</strong> <span style="color: #4ade80;">{{ $clientIp }}</span></p>
    <p><strong>Dirección MAC:</strong> <span style="color: #facc15;">{{ $clientMac }}</span></p>
    <p><strong>Navegador / Dispositivo:</strong><br><small style="color: #94a3b8;">{{ $userAgent }}</small></p>


    <livewire:agregar-filtro-mac :mac="$clientMac" />
</div>

                    </main>

                    <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                        Wispware v1.0.1 (Laravel v12.50.0) | Francisco M.
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>
