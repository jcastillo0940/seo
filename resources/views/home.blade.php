<x-layouts.app>
    <main class="mx-auto flex min-h-screen max-w-6xl items-center px-6 py-16">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
            <section class="space-y-6">
                <span class="inline-flex rounded-full border border-teal-400/30 bg-teal-300/10 px-4 py-1 text-sm text-teal-200">Laravel 13 + Blade + Zero-NPM</span>
                <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-white sm:text-6xl">SEO Tool MVP para Search Console, keywords y salud técnica.</h1>
                <p class="max-w-2xl text-lg leading-8 text-slate-300">Conecta Google, ingiere los ultimos 30 dias de datos, encuentra quick wins entre posiciones 11 y 20 y lanza auditorias de PageSpeed sin salir de Blade.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('auth.google.redirect') }}" class="inline-flex items-center rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow-lg shadow-teal-950/20 transition hover:-translate-y-0.5">Conectar con Google</a>
                    <span class="inline-flex items-center rounded-2xl border border-slate-700 px-6 py-3 text-sm text-slate-300">Queue driver: {{ config('queue.default') }}</span>
                </div>
                <p class="text-sm text-slate-400">Modo demo: {{ config('seo.demo_mode') ? 'activo' : 'desactivado' }}. En demo puedes recorrer todo el flujo aunque Composer aun no haya instalado Socialite y Google API Client.</p>
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Auth</p>
                        <p class="mt-3 text-2xl font-semibold">Google OAuth2</p>
                        <p class="mt-2 text-sm text-slate-400">Socialite para Search Console y acceso seguro con refresh token.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Ingesta</p>
                        <p class="mt-3 text-2xl font-semibold">30 dias</p>
                        <p class="mt-2 text-sm text-slate-400">Jobs en background con driver database o Redis.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Frontend</p>
                        <p class="mt-3 text-2xl font-semibold">Blade + Alpine</p>
                        <p class="mt-2 text-sm text-slate-400">Interaccion rapida con CDN y cero build para UI.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Reporting</p>
                        <p class="mt-3 text-2xl font-semibold">Quick Wins</p>
                        <p class="mt-2 text-sm text-slate-400">Keywords con alta impresion entre posicion 11 y 20.</p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</x-layouts.app>
