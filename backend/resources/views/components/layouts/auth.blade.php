<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ config('app.name', 'Finance App') }}</title>
        @if (is_file(public_path('hot')) || is_file(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @livewireStyles
    </head>
    <body class="min-h-dvh bg-slate-100 text-slate-900 antialiased">
        <div class="min-h-dvh bg-[radial-gradient(circle_at_70%_20%,rgba(81,26,183,0.18),transparent_45%),radial-gradient(circle_at_30%_80%,rgba(0,0,112,0.14),transparent_45%)]">
            <div class="mx-auto flex min-h-dvh max-w-md items-center px-4 py-10">
                <div class="w-full rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-brand-500 to-brand-900"></div>
                        <div>
                            <div class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Finance App') }}</div>
                            <div class="text-xs text-slate-500">Acesse sua conta</div>
                        </div>
                    </div>

                    <div class="mt-6">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>

