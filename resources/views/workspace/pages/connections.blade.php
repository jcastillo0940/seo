<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Fuentes de datos activas</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Conexiones del proyecto</h1>
        </div>
        <span class="rounded-full border border-success/20 bg-success/10 px-4 py-2 text-sm font-semibold text-emerald-700">sincronizado</span>
    </div>

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Configuracion del proyecto</p>
        <h2 class="mt-1 text-lg font-bold text-slate-900">Credenciales y conexiones</h2>

        <form method="POST" action="{{ route('project.settings.update') }}" class="mt-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">

                {{-- Dominio --}}
                <div class="rounded-xl border border-slate-200 px-4 py-3">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Dominio</p>
                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $projectDomain }}</p>
                </div>

                {{-- GA4 Property ID --}}
                <div class="rounded-xl border border-slate-200 px-4 py-3">
                    <label for="ga4_property_id" class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">GA4 Property ID</label>
                    <input id="ga4_property_id" name="ga4_property_id"
                        value="{{ old('ga4_property_id', $project?->ga4_property_id) }}"
                        placeholder="473129577"
                        class="mt-2 w-full border-0 p-0 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-0">
                    <p class="mt-1 text-[10px] text-slate-400">Analytics → Admin → numero de la propiedad</p>
                </div>

                {{-- Magento Base URL --}}
                <div class="rounded-xl border border-slate-200 px-4 py-3">
                    <label for="magento_base_url" class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Magento base URL</label>
                    <input id="magento_base_url" name="magento_base_url"
                        value="{{ old('magento_base_url', $project?->magento_base_url) }}"
                        placeholder="https://supercarnes.com"
                        class="mt-2 w-full border-0 p-0 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-0">
                    <p class="mt-1 text-[10px] text-slate-400">Todas las tiendas se sincronizan automaticamente</p>
                </div>

                {{-- Magento Bearer Token --}}
                <div class="rounded-xl border border-slate-200 px-4 py-3">
                    <label for="magento_api_token" class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Bearer token de Magento</label>
                    <input id="magento_api_token" name="magento_api_token" type="password"
                        value="{{ old('magento_api_token', $project?->magento_api_token) }}"
                        placeholder="{{ $project?->magento_api_token ? '••••••••••••' : 'pegar token aqui' }}"
                        class="mt-2 w-full border-0 p-0 text-sm text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-0">
                    <p class="mt-1 text-[10px] text-slate-400">System → Integrations → token de acceso</p>
                </div>

            </div>

            <div class="mt-5">
                <button type="submit"
                    class="rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-primary/90">
                    Guardar ajustes
                </button>
            </div>
        </form>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Google Search Console</h3>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $connectionStatus['search_console']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['search_console']['label'] }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($summary['keywords']) }} keywords · 30 dias</p>
            <form method="POST" action="{{ route('dashboard.sync') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Re-sincronizar</button></form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Google Analytics 4</h3>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $connectionStatus['ga4']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['ga4']['label'] }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($summary['organic_pages']) }} paginas organicas · Property: {{ $project?->ga4_property_id ?: 'no configurado' }}</p>
            <form method="POST" action="{{ route('project.sync-google-analytics') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Re-sincronizar</button></form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Magento Commerce</h3>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $connectionStatus['magento']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['magento']['label'] }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($summary['catalog_pages']) }} paginas de catalogo · Todas las tiendas</p>
            <form method="POST" action="{{ route('project.sync-magento') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Re-sincronizar</button></form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">360 Crawler</h3>
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $connectionStatus['crawler']['status'] === 'connected' ? 'bg-primary/10 text-primary' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['crawler']['label'] }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($latestCrawlRun?->pages_crawled ?? 0) }} URLs rastreadas</p>
            <form method="POST" action="{{ route('project.run-crawl') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Lanzar crawl</button></form>
        </div>
    </div>
</section>
