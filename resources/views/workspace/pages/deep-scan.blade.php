<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ $projectDomain }} · crawler · {{ $latestCrawlRun?->finished_at?->diffForHumans() ?? 'sin corrida' }}</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Deep Scan</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('workspace.audit') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Ver auditoria</a>
            <form method="POST" action="{{ route('project.run-crawl') }}">
                @csrf
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Re-crawl</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Estado</p><p class="mt-3 text-2xl font-bold text-slate-900">{{ $latestCrawlRun ? ucfirst($latestCrawlRun->status) : 'Sin crawl' }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Paginas</p><p class="mt-3 text-2xl font-bold text-slate-900">{{ number_format($latestCrawlRun?->pages_crawled ?? 0) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Issues</p><p class="mt-3 text-2xl font-bold text-rose-500">{{ number_format($summary['crawl_issues']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Warnings</p><p class="mt-3 text-2xl font-bold text-amber-500">{{ $crawlSeveritySummary['warn'] }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Errores</p><p class="mt-3 text-2xl font-bold text-rose-600">{{ $crawlSeveritySummary['error'] }}</p></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.5fr_0.9fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Mapa del sitio</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Paginas rastreadas y su estado tecnico</h2>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $latestCrawlPages->count() }} nodos</span>
            </div>
            <div class="mt-5 space-y-3">
                @forelse ($latestCrawlPages->take(12) as $crawlPage)
                    @php($issueCount = count($crawlPage->issues ?? []))
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $crawlPage->title ?: (parse_url((string) $crawlPage->url, PHP_URL_PATH) ?: $crawlPage->url) }}</p>
                                <p class="mt-1 truncate text-xs text-slate-500">{{ $crawlPage->url }}</p>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="rounded-full {{ $issueCount > 0 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} px-2.5 py-1 font-semibold">
                                    {{ $issueCount > 0 ? $issueCount.' issues' : 'ok' }}
                                </span>
                                <span class="font-semibold text-slate-500">HTTP {{ $crawlPage->status_code ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay hallazgos de crawl disponibles.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Detalle del nodo</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">{{ $priorityCrawlPage['path'] ?? '/' }}</h2>
                @if ($priorityCrawlPage)
                    <div class="mt-4 space-y-3 text-sm">
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Title</p>
                            <p class="mt-1 font-medium text-slate-800">{{ $priorityCrawlPage['title'] ?: 'Sin title' }}</p>
                        </div>
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.22em] text-slate-400">H1</p>
                            <p class="mt-1 font-medium text-slate-800">{{ $priorityCrawlPage['h1'] ?: 'Sin H1' }}</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"><p class="text-xs uppercase tracking-[0.22em] text-slate-400">Indexable</p><p class="mt-1 font-medium text-slate-800">{{ $priorityCrawlPage['is_indexable'] ? 'Si' : 'No' }}</p></div>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"><p class="text-xs uppercase tracking-[0.22em] text-slate-400">En sitemap</p><p class="mt-1 font-medium text-slate-800">{{ $priorityCrawlPage['is_in_sitemap'] ? 'Si' : 'No' }}</p></div>
                        </div>
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-500">Todavia no hay alertas para inspeccionar.</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Hallazgos agrupados</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Que se repite en el crawl</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($issueBuckets->take(6) as $bucket)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $bucket['label'] }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.22em] text-slate-400">{{ $bucket['code'] }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $bucket['severity'] === 'error' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $bucket['count'] }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No hay issues agrupados disponibles.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
