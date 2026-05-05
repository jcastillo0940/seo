<x-layouts.app>
    <main class="mx-auto flex min-h-screen max-w-6xl items-center px-6 py-16">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-center">
            <section class="space-y-6">
                <span class="inline-flex rounded-full border border-teal-400/30 bg-teal-300/10 px-4 py-1 text-sm text-teal-200">Acceso Restringido</span>
                <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-white sm:text-6xl">Inicia sesion para entrar al panel SEO.</h1>
                <p class="max-w-2xl text-lg leading-8 text-slate-300">La aplicacion solo permite acceso con Google OAuth y usuarios autorizados mediante lista blanca.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('auth.google.redirect') }}" class="inline-flex items-center rounded-2xl bg-white px-6 py-3 text-sm font-semibold text-slate-900 shadow-lg shadow-teal-950/20 transition hover:-translate-y-0.5">Iniciar sesion con Google</a>
                </div>
                @if (session('status'))
                    <p class="text-sm text-amber-200">{{ session('status') }}</p>
                @endif
            </section>

            <section class="rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-black/30 backdrop-blur">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Auth</p>
                        <p class="mt-3 text-2xl font-semibold">Google OAuth2</p>
                        <p class="mt-2 text-sm text-slate-400">Solo usuarios autorizados pueden completar el acceso.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Proteccion</p>
                        <p class="mt-3 text-2xl font-semibold">Sesion requerida</p>
                        <p class="mt-2 text-sm text-slate-400">Las rutas internas del sistema solo responden a usuarios autenticados.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Control</p>
                        <p class="mt-3 text-2xl font-semibold">Lista blanca</p>
                        <p class="mt-2 text-sm text-slate-400">Puedes limitar acceso por correo o por dominio corporativo.</p>
                    </div>
                    <div class="rounded-3xl border border-white/10 bg-slate-900/80 p-5">
                        <p class="text-sm text-slate-400">Operacion</p>
                        <p class="mt-3 text-2xl font-semibold">Panel privado</p>
                        <p class="mt-2 text-sm text-slate-400">Scraping, SEO tecnico, Magento y SERP solo se gestionan despues del login.</p>
                    </div>
                </div>
            </section>
        </div>
    </main>
</x-layouts.app>
