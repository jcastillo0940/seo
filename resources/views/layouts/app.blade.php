<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            ink: '#0f172a',
                            mist: '#e2e8f0',
                            signal: '#0f766e',
                            sun: '#f59e0b',
                            coral: '#f97316'
                        },
                        fontFamily: {
                            sans: ['ui-sans-serif', 'system-ui', 'sans-serif']
                        }
                    }
                }
            };
        </script>
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <div class="fixed inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(20,184,166,0.18),_transparent_35%),radial-gradient(circle_at_right,_rgba(249,115,22,0.16),_transparent_30%),linear-gradient(180deg,_#020617,_#0f172a)]"></div>
        {{ $slot }}
    </body>
</html>
