<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Settings & Integrations</p>
                <span class="rounded bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600">Sincronizado</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Conexiones del Proyecto</h1>
        </div>
        <div class="flex gap-2">
            <button form="settingsForm" type="submit" class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Guardar Ajustes
            </button>
        </div>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700 shadow-sm flex items-center gap-3">
            <svg class="h-5 w-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_350px]">
        <!-- Credenciales -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                <h2 class="text-base font-bold text-slate-900">Configuración Base</h2>
                <p class="mt-1 text-sm text-slate-500">Administra las credenciales y el dominio principal del proyecto.</p>
            </div>
            
            <form id="settingsForm" method="POST" action="{{ route('project.settings.update') }}" class="p-6 space-y-6 flex-1">
                @csrf
                
                <div class="space-y-4">
                    {{-- Dominio --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dominio Principal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <input type="text" class="w-full rounded-lg border border-slate-200 bg-slate-50 pl-10 px-3 py-2 text-sm font-medium text-slate-600 focus:outline-none cursor-not-allowed" value="{{ $projectDomain }}" readonly disabled>
                        </div>
                    </div>

                    {{-- GA4 Property ID --}}
                    <div>
                        <label for="ga4_property_id" class="block text-xs font-semibold text-slate-700 mb-1">Google Analytics 4 Property ID</label>
                        <input id="ga4_property_id" name="ga4_property_id" value="{{ old('ga4_property_id', $project?->ga4_property_id) }}" placeholder="Ej: 473129577" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                        <p class="mt-1.5 text-[11px] text-slate-500">Analytics &rarr; Admin &rarr; Property Settings &rarr; Property ID.</p>
                    </div>

                    <hr class="border-slate-100 my-4">
                    <h3 class="text-sm font-bold text-slate-800">Conexión Magento (E-commerce)</h3>

                    {{-- Magento Base URL --}}
                    <div>
                        <label for="magento_base_url" class="block text-xs font-semibold text-slate-700 mb-1">URL Base de Magento API</label>
                        <input id="magento_base_url" name="magento_base_url" value="{{ old('magento_base_url', $project?->magento_base_url) }}" placeholder="https://store.ejemplo.com" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                        <p class="mt-1.5 text-[11px] text-slate-500">Debe incluir el protocolo HTTPS.</p>
                    </div>

                    {{-- Magento Bearer Token --}}
                    <div>
                        <label for="magento_api_token" class="block text-xs font-semibold text-slate-700 mb-1">Bearer Token de Acceso</label>
                        <input id="magento_api_token" name="magento_api_token" type="password" value="{{ old('magento_api_token', $project?->magento_api_token) }}" placeholder="{{ $project?->magento_api_token ? '••••••••••••••••' : 'Pegar token aquí' }}" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition font-mono">
                        <p class="mt-1.5 text-[11px] text-slate-500">System &rarr; Integrations &rarr; Activar token.</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estado de Integraciones -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-slate-900 mb-2">Estado de Conectores</h3>
            
            <!-- GSC -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col hover:border-slate-300 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-rose-50 flex items-center justify-center text-rose-500 shrink-0">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-9l6 4.5-6 4.5z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 leading-tight">Search Console</h3>
                            <span class="inline-flex mt-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $connectionStatus['search_console']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['search_console']['label'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-end justify-between border-t border-slate-100 pt-3">
                    <p class="text-xs text-slate-500"><strong class="text-slate-700">{{ number_format($summary['keywords']) }}</strong> kws</p>
                    <form method="POST" action="{{ route('dashboard.sync') }}">@csrf<button class="text-xs font-semibold text-primary hover:underline">Re-sincronizar</button></form>
                </div>
            </div>

            <!-- GA4 -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col hover:border-slate-300 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 shrink-0">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 leading-tight">Google Analytics 4</h3>
                            <span class="inline-flex mt-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $connectionStatus['ga4']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['ga4']['label'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-end justify-between border-t border-slate-100 pt-3">
                    <p class="text-[11px] text-slate-500 truncate" title="{{ $project?->ga4_property_id ?: 'No configurado' }}">ID: {{ $project?->ga4_property_id ?: '---' }}</p>
                    <form method="POST" action="{{ route('project.sync-google-analytics') }}">@csrf<button class="text-xs font-semibold text-primary hover:underline">Sincronizar</button></form>
                </div>
            </div>

            <!-- Magento -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col hover:border-slate-300 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 leading-tight">Magento E-commerce</h3>
                            <span class="inline-flex mt-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $connectionStatus['magento']['status'] === 'connected' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['magento']['label'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-end justify-between border-t border-slate-100 pt-3">
                    <p class="text-xs text-slate-500"><strong class="text-slate-700">{{ number_format($summary['catalog_pages']) }}</strong> prods</p>
                    <form method="POST" action="{{ route('project.sync-magento') }}">@csrf<button class="text-xs font-semibold text-primary hover:underline">Sincronizar</button></form>
                </div>
            </div>

            <!-- Crawler -->
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col hover:border-slate-300 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 leading-tight">Site Crawler</h3>
                            <span class="inline-flex mt-1 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $connectionStatus['crawler']['status'] === 'connected' ? 'bg-primary/10 text-primary' : 'bg-amber-100 text-amber-700' }}">{{ $connectionStatus['crawler']['label'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 flex items-end justify-between border-t border-slate-100 pt-3">
                    <p class="text-xs text-slate-500"><strong class="text-slate-700">{{ number_format($latestCrawlRun?->pages_crawled ?? 0) }}</strong> rastreadas</p>
                    <form method="POST" action="{{ route('project.run-crawl') }}">@csrf<button class="text-xs font-semibold text-primary hover:underline">Lanzar Crawl</button></form>
                </div>
            </div>

        </div>
    </div>
</section>
