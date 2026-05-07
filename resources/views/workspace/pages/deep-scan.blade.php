<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ $projectDomain }} · crawler · {{ $latestCrawlRun?->finished_at?->diffForHumans() ?? 'sin corrida' }}</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Deep Scan - Auditoría Técnica</h1>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('project.run-crawl') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    Re-crawl
                </button>
            </form>
        </div>
    </div>

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

    @if (($latestCrawlRun?->pages_crawled ?? 0) === 0)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            El crawl aparece como completado, pero no encontró páginas para evaluar. Eso normalmente significa que falta sincronizar el catálogo o que no hay URLs cargadas en el proyecto.
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[320px_1fr]">
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Hallazgos principales</h3>
                <div class="space-y-2">
                    @forelse ($issueBuckets->take(8) as $bucket)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-sm font-medium text-slate-800">{{ $bucket['label'] }}</span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $bucket['severity'] === 'error' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">{{ $bucket['count'] }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ $bucket['action'] }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 py-2">No hay issues encontrados.</p>
                    @endforelse
                </div>
            </div>

            @if ($priorityCrawlPage)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Página más urgente</h3>
                    <p class="text-sm font-semibold text-primary break-all">{{ $priorityCrawlPage['path'] }}</p>
                    <div class="mt-3 space-y-2 text-xs text-slate-600">
                        <p>Status HTTP: <strong>{{ $priorityCrawlPage['status_code'] ?: 'N/A' }}</strong></p>
                        <p>Title: <strong>{{ $priorityCrawlPage['title'] ?: 'Falta' }}</strong></p>
                        <p>Meta description: <strong>{{ $priorityCrawlPage['meta_description'] ? 'Sí' : 'Falta' }}</strong></p>
                        <p>H1: <strong>{{ $priorityCrawlPage['h1'] ? 'Sí' : 'Falta' }}</strong></p>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach ($priorityCrawlPage['recommendations'] as $recommendation)
                            <p class="text-xs text-slate-600">{{ $recommendation }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">URL Rastreada</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Estado</th>
                            <th class="px-4 py-3 font-semibold text-center whitespace-nowrap">Indexable</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Issues</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Qué corregir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($latestCrawlPages->take(15) as $crawlPage)
                            @php
                                $pageIssues = collect($crawlPage->issues ?? []);
                                $firstIssue = $pageIssues->first();
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 min-w-[280px] max-w-[420px]">
                                    <div class="flex flex-col min-w-0">
                                        <p class="truncate font-medium text-slate-900" title="{{ $crawlPage->title ?: 'Sin title' }}">
                                            {{ $crawlPage->title ?: (parse_url((string) $crawlPage->url, PHP_URL_PATH) ?: $crawlPage->url) }}
                                        </p>
                                        <a href="{{ $crawlPage->url }}" target="_blank" class="truncate text-xs text-primary hover:underline" title="{{ $crawlPage->url }}">
                                            {{ $crawlPage->url }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php($code = $crawlPage->status_code ?? 0)
                                    <span class="inline-flex rounded-md px-2 py-1 text-xs font-semibold {{ $code == 200 ? 'bg-emerald-100 text-emerald-700' : ($code >= 400 ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ $code ?: 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs font-semibold {{ $crawlPage->is_indexable ? 'text-success' : 'text-slate-400' }}">
                                        {{ $crawlPage->is_indexable ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">
                                    @if($pageIssues->count() > 0)
                                        <span class="rounded bg-rose-50 px-2 py-1 text-rose-600 border border-rose-200">{{ $pageIssues->count() }}</span>
                                    @else
                                        <span class="text-slate-400">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($firstIssue)
                                        <p class="text-xs text-slate-600">{{ match($firstIssue['code'] ?? '') {
                                            'missing_title' => 'Crear un title único con la keyword principal.',
                                            'missing_meta_description' => 'Agregar una meta description clara y comercial.',
                                            'missing_h1' => 'Definir un H1 alineado con la intención de búsqueda.',
                                            'short_content' => 'Sumar texto útil, FAQs y más enlazado interno.',
                                            'images_missing_alt' => 'Completar atributos alt en imágenes clave.',
                                            'http_error' => 'Corregir la respuesta HTTP o aplicar redirección.',
                                            default => 'Revisar el hallazgo y corregir esta URL.',
                                        } }}</p>
                                    @else
                                        <p class="text-xs text-slate-400">Sin correcciones urgentes.</p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500">No hay páginas rastreadas disponibles.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
