<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ $projectDomain }} · crawler · {{ $latestCrawlRun?->finished_at?->diffForHumans() ?? 'sin corrida' }}</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Deep Scan - Auditoría Técnica</h1>
        </div>
        <div class="flex gap-2">
            <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exportar
            </button>
            <form method="POST" action="{{ route('project.run-crawl') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Re-crawl
                </button>
            </form>
        </div>
    </div>

    <!-- Widgets Resumen -->
    <div class="grid gap-4 md:grid-cols-5">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-slate-400">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Estado General</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ $latestCrawlRun ? ucfirst($latestCrawlRun->status) : 'Sin crawl' }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-blue-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Páginas Rastreadas</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($latestCrawlRun?->pages_crawled ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-rose-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Errores Críticos</p>
            <p class="mt-2 text-2xl font-bold text-rose-600">{{ $crawlSeveritySummary['error'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-amber-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Advertencias</p>
            <p class="mt-2 text-2xl font-bold text-amber-500">{{ $crawlSeveritySummary['warn'] ?? 0 }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-success">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Total Issues</p>
            <p class="mt-2 text-2xl font-bold text-slate-900">{{ number_format($summary['crawl_issues'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
        <div class="flex w-full md:w-auto flex-1 items-center gap-2 border-b border-slate-200 pb-3 md:border-b-0 md:border-r md:pb-0 md:pr-4">
            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" class="w-full border-none bg-transparent p-0 text-sm placeholder-slate-400 focus:ring-0" placeholder="Buscar por URL...">
        </div>
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3 pl-2">
            <span class="text-xs font-semibold text-slate-500">Estado:</span>
            <button class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200">Todos</button>
            <button class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">200 OK</button>
            <button class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">4xx / 5xx</button>
        </div>
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3">
            <span class="text-xs font-semibold text-slate-500">Issues:</span>
            <button class="flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                Cualquiera <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
    </div>

    <!-- Grid principal -->
    <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
        <!-- Sidebar de Issues -->
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Hallazgos y Problemas</h3>
                <div class="space-y-1">
                    @forelse ($issueBuckets->take(8) ?? [] as $bucket)
                        <button class="flex w-full items-center justify-between rounded-lg px-2 py-2 text-left hover:bg-slate-50 group transition">
                            <div class="flex flex-col min-w-0 pr-2">
                                <span class="text-sm font-medium text-slate-800 truncate">{{ $bucket['label'] }}</span>
                                <span class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $bucket['code'] }}</span>
                            </div>
                            <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold {{ $bucket['severity'] === 'error' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $bucket['count'] }}
                            </span>
                        </button>
                    @empty
                        <p class="text-sm text-slate-500 py-2">No hay issues encontrados.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tabla de Páginas Rastreadas -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">URL Rastreada</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Estado</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Indexable</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Sitemap</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Issues</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Detalle</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($latestCrawlPages->take(15) ?? [] as $crawlPage)
                            @php($issueCount = count($crawlPage->issues ?? []))
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-4 py-3 min-w-[280px] max-w-[400px]">
                                    <div class="flex flex-col min-w-0">
                                        <p class="truncate font-medium text-slate-900" title="{{ $crawlPage->title ?: 'Sin title' }}">
                                            {{ $crawlPage->title ?: (parse_url((string) $crawlPage->url, PHP_URL_PATH) ?: $crawlPage->url) }}
                                        </p>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <a href="{{ $crawlPage->url }}" target="_blank" class="truncate text-xs text-primary hover:underline" title="{{ $crawlPage->url }}">
                                                {{ $crawlPage->url }}
                                            </a>
                                            <svg class="h-3 w-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php($code = $crawlPage->status_code ?? 0)
                                    <span class="inline-flex rounded-md px-2 py-1 text-xs font-semibold {{ $code == 200 ? 'bg-emerald-100 text-emerald-700' : ($code >= 400 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $code ?: 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($crawlPage->is_indexable)
                                        <svg class="h-5 w-5 text-success mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="h-5 w-5 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($crawlPage->is_in_sitemap)
                                        <svg class="h-5 w-5 text-success mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="h-5 w-5 text-slate-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    @if($issueCount > 0)
                                        <span class="rounded bg-rose-50 px-2 py-1 text-rose-600 border border-rose-200">{{ $issueCount }}</span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button class="inline-flex items-center justify-center rounded-md p-1.5 text-slate-400 hover:bg-slate-100 hover:text-primary transition">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500">No hay páginas rastreadas disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($latestCrawlPages->count() > 0)
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-6 mt-auto">
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-700">
                            Mostrando <span class="font-medium">1</span> a <span class="font-medium">{{ min($latestCrawlPages->count(), 15) }}</span> de <span class="font-medium">{{ $latestCrawlPages->count() }}</span> nodos
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Anterior</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">1</a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">2</a>
                            <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Siguiente</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
