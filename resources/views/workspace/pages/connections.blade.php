<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Fuentes de datos activas</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Conexiones del proyecto</h1>
        </div>
        <span class="rounded-full border border-success/20 bg-success/10 px-4 py-2 text-sm font-semibold text-emerald-700">sincronizado</span>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Configuracion del proyecto</p>
        <h2 class="mt-1 text-lg font-bold text-slate-900">Edita credenciales y sincroniza sin salir del workspace</h2>
        <form method="POST" action="{{ route('project.settings.update') }}" class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @csrf
            <div class="rounded-xl border border-slate-200 px-4 py-3">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Dominio</p>
                <p class="mt-2 text-sm font-semibold text-slate-800">{{ $projectDomain }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3">
                <label class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">GA4 property ID</label>
                <input name="ga4_property_id" value="{{ $project?->ga4_property_id }}" class="mt-2 w-full border-0 p-0 text-sm text-slate-800 focus:outline-none focus:ring-0">
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3">
                <label class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Magento base URL</label>
                <input name="magento_base_url" value="{{ $project?->magento_base_url }}" class="mt-2 w-full border-0 p-0 text-sm text-slate-800 focus:outline-none focus:ring-0">
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3">
                <label class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Magento store code</label>
                <input name="magento_store_code" value="{{ $project?->magento_store_code }}" class="mt-2 w-full border-0 p-0 text-sm text-slate-800 focus:outline-none focus:ring-0">
            </div>
            <div class="rounded-xl border border-slate-200 px-4 py-3">
                <label class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Magento website code</label>
                <input name="magento_website_code" value="{{ $project?->magento_website_code }}" class="mt-2 w-full border-0 p-0 text-sm text-slate-800 focus:outline-none focus:ring-0">
            </div>
            <div class="flex items-end">
                <button class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary/90">Guardar ajustes</button>
            </div>
        </form>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-bold text-slate-900">Google Search Console</h3><span class="rounded-full {{ $connectionStatus['search_console']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-semibold">{{ $connectionStatus['search_console']['label'] }}</span></div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($summary['keywords']) }} keywords · 30 dias</p>
            <form method="POST" action="{{ route('dashboard.sync') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Re-sincronizar</button></form>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-bold text-slate-900">Google Analytics 4</h3><span class="rounded-full {{ $connectionStatus['ga4']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-semibold">{{ $connectionStatus['ga4']['label'] }}</span></div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($summary['organic_pages']) }} paginas organicas</p>
            <form method="POST" action="{{ route('project.sync-google-analytics') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Re-sincronizar</button></form>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-bold text-slate-900">Magento Commerce</h3><span class="rounded-full {{ $connectionStatus['magento']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-semibold">{{ $connectionStatus['magento']['label'] }}</span></div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($summary['catalog_pages']) }} paginas de catalogo</p>
            <form method="POST" action="{{ route('project.sync-magento') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Re-sincronizar</button></form>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-bold text-slate-900">360 Crawler</h3><span class="rounded-full {{ $connectionStatus['crawler']['status'] === 'connected' ? 'bg-primary/10 text-primary' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-semibold">{{ $connectionStatus['crawler']['label'] }}</span></div>
            <p class="mt-2 text-sm text-slate-500">{{ number_format($latestCrawlRun?->pages_crawled ?? 0) }} URLs rastreadas</p>
            <form method="POST" action="{{ route('project.run-crawl') }}" class="mt-4">@csrf<button class="text-sm font-semibold text-primary">Lanzar crawl</button></form>
        </div>
    </div>
</section>
