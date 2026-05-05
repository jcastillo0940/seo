<x-layouts.app>
    <main class="mx-auto max-w-7xl px-6 py-8" x-data="{ tab: 'top' }">
        <div class="mb-8 flex flex-col gap-4 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.2em] text-teal-300">SEO Command Center</p>
                <h1 class="mt-2 text-3xl font-semibold text-white">Hola, {{ $user->name }}</h1>
                <p class="mt-2 text-slate-300">{{ $project ? "Proyecto activo: {$project->name}" : 'Conecta una propiedad de Search Console para comenzar.' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if ($project)
                    <form method="POST" action="{{ route('dashboard.sync') }}">
                        @csrf
                        <button class="rounded-2xl bg-teal-400 px-5 py-3 text-sm font-semibold text-slate-950">Sincronizar 30 dias</button>
                    </form>
                    <form method="POST" action="{{ route('dashboard.audit') }}">
                        @csrf
                        <button class="rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white">Lanzar auditoria</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-2xl border border-white/15 px-5 py-3 text-sm text-slate-300">Salir</button>
                </form>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-teal-400/20 bg-teal-400/10 px-5 py-4 text-sm text-teal-100">{{ session('status') }}</div>
        @endif

        <div class="mb-6 grid gap-4 md:grid-cols-6">
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm text-slate-400">Keywords rastreadas</p>
                <p class="mt-3 text-4xl font-semibold">{{ number_format($summary['keywords']) }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm text-slate-400">Clicks acumulados</p>
                <p class="mt-3 text-4xl font-semibold">{{ number_format($summary['clicks']) }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm text-slate-400">Impresiones acumuladas</p>
                <p class="mt-3 text-4xl font-semibold">{{ number_format($summary['impressions']) }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm text-slate-400">Keywords objetivo</p>
                <p class="mt-3 text-4xl font-semibold">{{ number_format($summary['tracked_keywords']) }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm text-slate-400">Competidores</p>
                <p class="mt-3 text-4xl font-semibold">{{ number_format($summary['competitors']) }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm text-slate-400">Catalog pages</p>
                <p class="mt-3 text-4xl font-semibold">{{ number_format($summary['catalog_pages']) }}</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-900/80 p-5">
                <p class="text-sm text-slate-400">Landing pages SEO</p>
                <p class="mt-3 text-4xl font-semibold">{{ number_format($summary['organic_pages']) }}</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
            <section class="space-y-6">
                @unless ($project)
                    <div class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                        <h2 class="text-xl font-semibold">Selecciona tu propiedad</h2>
                        <p class="mt-2 text-sm text-slate-400">La lista viene de Search Console cuando Google esta conectado. En modo demo se muestran propiedades simuladas.</p>
                        @if ($propertyError)
                            <div class="mt-4 rounded-2xl border border-amber-400/20 bg-amber-400/10 px-4 py-3 text-sm text-amber-100">{{ $propertyError }}</div>
                        @endif
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            @foreach ($properties as $property)
                                <form method="POST" action="{{ route('projects.store') }}" class="rounded-3xl border border-white/10 bg-white/5 p-5">
                                    @csrf
                                    <input type="hidden" name="property_id" value="{{ $property['property_id'] }}">
                                    <input type="hidden" name="name" value="{{ $property['name'] }}">
                                    <input type="hidden" name="url" value="{{ $property['url'] }}">
                                    <input type="hidden" name="type" value="{{ $property['type'] }}">
                                    <p class="text-lg font-semibold text-white">{{ $property['name'] }}</p>
                                    <p class="mt-2 text-sm text-slate-400">{{ $property['url'] }}</p>
                                    <button class="mt-5 rounded-2xl bg-white px-4 py-2 text-sm font-semibold text-slate-900">Usar esta propiedad</button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                        <div class="mb-4 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold">Evolucion SEO</h2>
                                <p class="text-sm text-slate-400">Clicks e impresiones de los ultimos 30 dias.</p>
                            </div>
                            <div class="text-right text-sm text-slate-400">
                                <p>Ultima sincronizacion</p>
                                <p class="text-slate-200">{{ $project->last_synced_at?->diffForHumans() ?? 'Pendiente' }}</p>
                            </div>
                        </div>
                        <canvas id="seoTrendChart" height="120"></canvas>
                    </div>

                    <div class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold">Insights accionables</h2>
                                <p class="text-sm text-slate-400">Top keywords por clicks y quick wins por impresiones.</p>
                            </div>
                            <div class="inline-flex rounded-2xl border border-white/10 bg-white/5 p-1 text-sm">
                                <button class="rounded-xl px-4 py-2" :class="tab === 'top' ? 'bg-white text-slate-900' : 'text-slate-300'" @click="tab = 'top'">Top 10</button>
                                <button class="rounded-xl px-4 py-2" :class="tab === 'wins' ? 'bg-white text-slate-900' : 'text-slate-300'" @click="tab = 'wins'">Quick Wins</button>
                            </div>
                        </div>

                        <div x-show="tab === 'top'" x-cloak class="space-y-3">
                            @forelse ($topKeywords as $keyword)
                                <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <div>
                                        <p class="font-medium text-white">{{ $keyword->keyword }}</p>
                                        <p class="text-sm text-slate-400">Posicion media {{ number_format($keyword->avg_position, 1) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-teal-300">{{ number_format($keyword->clicks) }} clicks</p>
                                        <p class="text-sm text-slate-400">{{ number_format($keyword->impressions) }} impresiones</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Aun no hay datos sincronizados.</p>
                            @endforelse
                        </div>

                        <div x-show="tab === 'wins'" x-cloak class="space-y-3">
                            @forelse ($quickWins as $keyword)
                                <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <div>
                                        <p class="font-medium text-white">{{ $keyword->keyword }}</p>
                                        <p class="text-sm text-amber-300">Posicion media {{ number_format($keyword->avg_position, 1) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-white">{{ number_format($keyword->impressions) }} impresiones</p>
                                        <p class="text-sm text-slate-400">{{ number_format($keyword->clicks) }} clicks</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Todavia no se detectan quick wins entre posicion 11 y 20.</p>
                            @endforelse
                        </div>
                    </div>
                @endunless

                @if ($project)
                    <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                        <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <h2 class="text-xl font-semibold">Conexiones del proyecto</h2>
                                <p class="text-sm text-slate-400">Aqui conectamos Magento y afinamos Google para que el proyecto traiga catalogo, landings y datos SEO reales.</p>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('project.sync-magento') }}">
                                    @csrf
                                    <button class="rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-900">Sync Magento</button>
                                </form>
                                <form method="POST" action="{{ route('project.sync-google-analytics') }}">
                                    @csrf
                                    <button class="rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white">Sync GA4</button>
                                </form>
                                <form method="POST" action="{{ route('project.run-crawl') }}">
                                    @csrf
                                    <button class="rounded-2xl border border-teal-400/30 bg-teal-400/10 px-4 py-3 text-sm font-semibold text-teal-200">Run Crawl</button>
                                </form>
                                <form method="POST" action="{{ route('project.run-serp') }}">
                                    @csrf
                                    <button class="rounded-2xl border border-amber-400/30 bg-amber-400/10 px-4 py-3 text-sm font-semibold text-amber-200">Run SERP</button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('project.settings.update') }}" class="mt-6 grid gap-4 lg:grid-cols-2">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm text-slate-400">Magento base URL</label>
                                    <input name="magento_base_url" value="{{ old('magento_base_url', $project->magento_base_url) }}" placeholder="https://tienda.com" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500">
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-sm text-slate-400">Store code</label>
                                        <input name="magento_store_code" value="{{ old('magento_store_code', $project->magento_store_code ?: 'default') }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white">
                                    </div>
                                    <div>
                                        <label class="mb-2 block text-sm text-slate-400">Website code</label>
                                        <input name="magento_website_code" value="{{ old('magento_website_code', $project->magento_website_code) }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white">
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500">Ultima sync Magento: {{ $project->magento_last_synced_at?->diffForHumans() ?? 'pendiente' }}</p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="mb-2 block text-sm text-slate-400">GA4 Property ID</label>
                                    <input name="ga4_property_id" value="{{ old('ga4_property_id', $project->ga4_property_id) }}" placeholder="123456789" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500">
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300">
                                    <p>Search Console property: {{ $project->google_property_id }}</p>
                                    <p class="mt-2">Google scopes listos para Search Console, PageSpeed y Analytics readonly.</p>
                                </div>
                                <button class="rounded-2xl bg-teal-400 px-4 py-3 text-sm font-semibold text-slate-950">Guardar conexiones</button>
                            </div>
                        </form>
                    </section>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold">Prioridades SEO</h2>
                                    <p class="text-sm text-slate-400">Cruce de crawl, GA4, Magento y conversiones para decidir por donde atacar primero.</p>
                                </div>
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $pageOpportunities->count() }} paginas</span>
                            </div>
                            <div class="mt-6 space-y-3">
                                @forelse ($pageOpportunities as $opportunity)
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="font-medium text-white">{{ $opportunity['name'] }}</p>
                                                <p class="text-sm text-slate-400">{{ strtoupper($opportunity['type']) }} · {{ $opportunity['url'] }}</p>
                                                <p class="mt-2 text-xs text-amber-200">{{ $opportunity['top_issue'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-teal-300">Score {{ $opportunity['score'] }}</p>
                                                <p class="text-xs text-slate-400">{{ number_format($opportunity['sessions']) }} sesiones · {{ number_format($opportunity['conversions']) }} conv</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-400">Aun faltan datos para calcular prioridades de pagina.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold">Oportunidades de keyword</h2>
                                    <p class="text-sm text-slate-400">Keywords con espacio real para subir CTR o romper el top 10.</p>
                                </div>
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $keywordOpportunities->count() }} detectadas</span>
                            </div>
                            <div class="mt-6 space-y-3">
                                @forelse ($keywordOpportunities as $opportunity)
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="font-medium text-white">{{ $opportunity['keyword'] }}</p>
                                                <p class="text-sm text-slate-400">Posicion media {{ number_format($opportunity['avg_position'], 1) }} · CTR {{ number_format($opportunity['ctr'], 2) }}%</p>
                                                <p class="mt-2 text-xs text-amber-200">{{ $opportunity['hint'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-white">{{ number_format($opportunity['impressions']) }} imp</p>
                                                <p class="text-xs text-slate-400">{{ number_format($opportunity['clicks']) }} clicks</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-400">Aun no hay suficientes keywords para priorizar oportunidades.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold">Competencia</h2>
                                    <p class="text-sm text-slate-400">Dominios que vamos a medir contra tu tienda.</p>
                                </div>
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $competitors->count() }} guardados</span>
                            </div>

                            <form method="POST" action="{{ route('competitors.store') }}" class="mt-6 space-y-3">
                                @csrf
                                <input name="domain" placeholder="competidor.com" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500" required>
                                <input name="name" placeholder="Nombre visible" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500">
                                <input name="notes" placeholder="Notas opcionales" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500">
                                <button class="rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-900">Agregar competidor</button>
                            </form>

                            <div class="mt-6 space-y-3">
                                @forelse ($competitors as $competitor)
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <p class="font-medium text-white">{{ $competitor->name }}</p>
                                        <p class="text-sm text-slate-400">{{ $competitor->domain }}</p>
                                        @if ($competitor->notes)
                                            <p class="mt-2 text-xs text-slate-500">{{ $competitor->notes }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-400">Todavia no has agregado competidores para comparar rankings.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold">Keywords objetivo</h2>
                                    <p class="text-sm text-slate-400">Base para monitorear SERP, share of voice y keyword gap.</p>
                                </div>
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $trackedKeywords->count() }} activas</span>
                            </div>

                            <form method="POST" action="{{ route('tracked-keywords.store') }}" class="mt-6 grid gap-3">
                                @csrf
                                <input name="keyword" placeholder="zapatillas de running hombre" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder:text-slate-500" required>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="country_code" value="US" maxlength="2" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white">
                                    <input name="language_code" value="es" maxlength="5" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white">
                                </div>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    <select name="device" class="w-full rounded-2xl border border-white/10 bg-slate-900 px-4 py-3 text-sm text-white">
                                        <option value="mobile">mobile</option>
                                        <option value="desktop">desktop</option>
                                    </select>
                                    <select name="search_intent" class="w-full rounded-2xl border border-white/10 bg-slate-900 px-4 py-3 text-sm text-white">
                                        <option value="">intent opcional</option>
                                        <option value="informational">informational</option>
                                        <option value="commercial">commercial</option>
                                        <option value="transactional">transactional</option>
                                        <option value="navigational">navigational</option>
                                    </select>
                                    <select name="priority" class="w-full rounded-2xl border border-white/10 bg-slate-900 px-4 py-3 text-sm text-white">
                                        <option value="1">prioridad 1</option>
                                        <option value="2">prioridad 2</option>
                                        <option value="3" selected>prioridad 3</option>
                                        <option value="4">prioridad 4</option>
                                        <option value="5">prioridad 5</option>
                                    </select>
                                </div>
                                <button class="rounded-2xl bg-teal-400 px-4 py-3 text-sm font-semibold text-slate-950">Agregar keyword</button>
                            </form>

                            <div class="mt-6 space-y-3">
                                @forelse ($trackedKeywords as $trackedKeyword)
                                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <div>
                                            <p class="font-medium text-white">{{ $trackedKeyword->keyword }}</p>
                                            <p class="text-sm text-slate-400">{{ $trackedKeyword->country_code }} · {{ $trackedKeyword->language_code }} · {{ $trackedKeyword->device }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-white">P{{ $trackedKeyword->priority }}</p>
                                            <p class="text-xs text-slate-500">{{ $trackedKeyword->search_intent ?: 'sin intent' }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-400">Todavia no has definido keywords objetivo para el monitoreo competitivo.</p>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold">Catalogo Magento</h2>
                                    <p class="text-sm text-slate-400">Productos, categorias y CMS sincronizados para auditoria SEO.</p>
                                </div>
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $topCatalogPages->count() }} visibles</span>
                            </div>
                            <div class="mt-6 space-y-3">
                                @forelse ($topCatalogPages as $catalogPage)
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <div class="flex items-center justify-between gap-4">
                                            <div>
                                                <p class="font-medium text-white">{{ $catalogPage->name }}</p>
                                                <p class="text-sm text-slate-400">{{ strtoupper($catalogPage->type) }} · {{ $catalogPage->slug ?: '/' }}</p>
                                            </div>
                                            <p class="text-sm text-slate-300">{{ number_format($catalogPage->product_count) }} items</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-400">Aun no hay catalogo sincronizado desde Magento.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-semibold">Landing pages organicas</h2>
                                    <p class="text-sm text-slate-400">GA4 organico para cruzar rendimiento con Search Console y catalogo.</p>
                                </div>
                                <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $topOrganicPages->count() }} paginas</span>
                            </div>
                            <div class="mt-6 space-y-3">
                                @forelse ($topOrganicPages as $organicPage)
                                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                        <div class="flex items-center justify-between gap-4">
                                            <div>
                                                <p class="font-medium text-white">{{ $organicPage->page_title ?: $organicPage->page_path }}</p>
                                                <p class="text-sm text-slate-400">{{ $organicPage->page_path }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-semibold text-teal-300">{{ number_format($organicPage->sessions) }} sesiones</p>
                                                <p class="text-xs text-slate-400">{{ number_format($organicPage->conversions) }} conversiones</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-400">Aun no hay datos de landing pages organicas desde GA4.</p>
                                @endforelse
                            </div>
                        </section>

                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <h2 class="text-xl font-semibold">SERP Tracking</h2>
                            <p class="mt-2 text-sm text-slate-400">La estructura ya esta lista para guardar snapshots por keyword, dispositivo y pais.</p>
                            @if ($latestSerpSnapshot)
                                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-sm text-slate-400">Ultimo snapshot</p>
                                    <p class="mt-2 text-lg font-semibold text-white">{{ $latestSerpSnapshot->trackedKeyword->keyword }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ $latestSerpSnapshot->captured_at->diffForHumans() }} · {{ $latestSerpSnapshot->provider }} · {{ $latestSerpSnapshot->results_count }} resultados</p>
                                    <div class="mt-4 space-y-2">
                                        @foreach ($latestSerpSnapshot->results->sortBy('position')->take(5) as $result)
                                            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-slate-950/40 px-3 py-2">
                                                <div>
                                                    <p class="text-sm text-white">{{ $result->title ?: $result->domain }}</p>
                                                    <p class="text-xs text-slate-500">{{ $result->domain }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-sm font-semibold {{ $result->is_own_domain ? 'text-teal-300' : 'text-amber-200' }}">#{{ $result->position }}</p>
                                                    <p class="text-xs text-slate-500">{{ number_format($result->estimated_traffic ?? 0) }} est</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="mt-6 rounded-2xl border border-dashed border-white/10 bg-white/5 p-4 text-sm text-slate-400">
                                    Aun no hay snapshots SERP. Ya puedes lanzar el primero desde Run SERP.
                                </div>
                            @endif
                        </section>

                        <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                            <h2 class="text-xl font-semibold">Crawler SEO</h2>
                            <p class="mt-2 text-sm text-slate-400">Tambien queda preparada la capa para guardar corridas de rastreo y hallazgos por URL.</p>
                            @if ($latestCrawlRun)
                                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <p class="text-sm text-slate-400">Ultima corrida</p>
                                    <p class="mt-2 text-lg font-semibold text-white">{{ ucfirst($latestCrawlRun->status) }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ number_format($latestCrawlRun->pages_crawled) }} paginas · {{ number_format($latestCrawlRun->issue_count) }} issues</p>
                                    @if ($latestCrawlRun->summary)
                                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3 text-sm text-slate-300">
                                                <p>{{ number_format($latestCrawlRun->summary['pages_with_issues'] ?? 0) }} paginas con issues</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ number_format($latestCrawlRun->summary['indexable_pages'] ?? 0) }} indexables</p>
                                            </div>
                                            <div class="rounded-2xl border border-white/10 bg-slate-950/40 p-3 text-sm text-slate-300">
                                                <p>{{ number_format($latestCrawlRun->summary['images_without_alt'] ?? 0) }} imagenes sin alt</p>
                                                <p class="mt-1 text-xs text-slate-500">{{ number_format($latestCrawlRun->summary['missing_descriptions'] ?? 0) }} metas ausentes</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($latestCrawlIssues->isNotEmpty())
                                        <div class="mt-4 space-y-2">
                                            @foreach ($latestCrawlIssues as $issue)
                                                <div class="rounded-2xl border border-white/10 bg-slate-950/40 px-3 py-2">
                                                    <p class="text-sm text-white">{{ $issue['label'] }}</p>
                                                    <p class="text-xs text-slate-500">{{ $issue['url'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="mt-6 rounded-2xl border border-dashed border-white/10 bg-white/5 p-4 text-sm text-slate-400">
                                    Aun no hay corridas de crawl. Ya puedes lanzar la primera desde el boton Run Crawl.
                                </div>
                            @endif
                        </section>
                    </div>

                    <section class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold">Competitor Gap</h2>
                                <p class="text-sm text-slate-400">Keywords donde un competidor ya va por delante y tu dominio tiene margen claro.</p>
                            </div>
                            <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $competitorGaps->count() }} gaps</span>
                        </div>
                        <div class="mt-6 grid gap-3 md:grid-cols-2">
                            @forelse ($competitorGaps as $gap)
                                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                                    <p class="font-medium text-white">{{ $gap['keyword'] }}</p>
                                    <p class="mt-1 text-sm text-slate-400">{{ $gap['competitor'] }} en #{{ $gap['position'] }} · tu dominio en #{{ $gap['own_position'] }}</p>
                                    <p class="mt-2 text-xs text-amber-200">Brecha estimada: {{ $gap['gap'] }} posiciones</p>
                                </div>
                            @empty
                                <p class="text-sm text-slate-400">Aun no hay suficiente historial SERP para mostrar brechas de competencia.</p>
                            @endforelse
                        </div>
                    </section>
                @endif
            </section>

            <aside class="space-y-6">
                <div class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Auditoria tecnica</p>
                    <div class="mt-5 grid grid-cols-2 gap-4">
                        <div class="rounded-3xl bg-white/5 p-4">
                            <p class="text-sm text-slate-400">Performance</p>
                            <p class="mt-3 text-4xl font-semibold text-teal-300">{{ $latestAudit?->performance_score ?? '--' }}</p>
                        </div>
                        <div class="rounded-3xl bg-white/5 p-4">
                            <p class="text-sm text-slate-400">SEO</p>
                            <p class="mt-3 text-4xl font-semibold text-amber-300">{{ $latestAudit?->seo_score ?? '--' }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-400">{{ $latestAudit ? 'Ultima auditoria '.$latestAudit->audited_at->diffForHumans() : 'Todavia no se ha ejecutado una auditoria.' }}</p>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-slate-900/80 p-6">
                    <h2 class="text-xl font-semibold">Stack del MVP</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-300">
                        <li>Laravel 13 sobre PHP 8.3.</li>
                        <li>MySQL 8 listo en migraciones; localmente arranca con SQLite.</li>
                        <li>Queue driver database para no bloquear Blade.</li>
                        <li>Tailwind CDN, Alpine.js y Chart.js sin build frontend.</li>
                    </ul>
                </div>
            </aside>
        </div>
    </main>

    @if ($project)
        <script>
            const ctx = document.getElementById('seoTrendChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Clicks',
                                data: @json($chartClicks),
                                borderColor: '#2dd4bf',
                                backgroundColor: 'rgba(45, 212, 191, 0.18)',
                                tension: 0.35,
                                fill: true
                            },
                            {
                                label: 'Impresiones',
                                data: @json($chartImpressions),
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.12)',
                                tension: 0.35,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#cbd5e1'
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: '#94a3b8' },
                                grid: { color: 'rgba(148, 163, 184, 0.15)' }
                            },
                            y: {
                                ticks: { color: '#94a3b8' },
                                grid: { color: 'rgba(148, 163, 184, 0.15)' }
                            }
                        }
                    }
                });
            }
        </script>
    @endif
</x-layouts.app>
