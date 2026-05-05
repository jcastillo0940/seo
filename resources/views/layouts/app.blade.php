<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        
        <!-- Google Fonts: Inter -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'system-ui', 'sans-serif']
                        },
                        colors: {
                            primary: '#3d5afe', /* Semrush-like bright blue/purple */
                            secondary: '#64748b',
                            success: '#00c27f', /* Semrush YOU green */
                            rival1: '#ff6b6b',
                            rival2: '#ffd166',
                            rival3: '#9b5de5',
                            rival4: '#00bbf9',
                            brandbg: '#f8fafc',
                        }
                    }
                }
            };
        </script>
        <style>
            [x-cloak] { display: none !important; }
            /* Custom Scrollbar for a premium look */
            ::-webkit-scrollbar { width: 8px; height: 8px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        </style>
    </head>
    <body class="bg-brandbg text-slate-800 font-sans antialiased">
        {{ $slot }}
    </body>
</html>
