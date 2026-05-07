<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ $projectDomain }} · Site Audit</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Auditoría del Sitio</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('workspace.deep-scan') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Deep Scan
            </a>
            <form method="POST" action="{{ route('dashboard.audit') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    Re-Auditar
                </button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-center items-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
            <h3 class="text-sm font-bold text-slate-600 relative z-10 mb-4">Salud del Sitio</h3>
            <div class="relative flex h-32 w-32 items-center justify-center rounded-full border-8 border-slate-100 mb-4 z-10">
                <svg class="absolute inset-0 h-full w-full -rotate-90 transform" viewBox="0 0 100 100">
                    @php $score = intval($latestAudit?->seo_score ?? 0); $circumference = 2 * pi() * 46; $offset = $circumference - ($score / 100) * $circumference; @endphp
                    <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="8" class="{{ $score > 80 ? 'text-success' : ($score > 50 ? 'text-amber-400' : 'text-rose-500') }}" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $offset }}" stroke-linecap="round"></circle>
                </svg>
                <div class="flex flex-col items-center">
                    <span class="text-3xl font-black text-slate-900">{{ $latestAudit?->seo_score ?? '--' }}</span>
                    <span class="text-[10px] uppercase font-bold text-slate-400">/ 100</span>
                </div>
            </div>
            <p class="text-xs text-slate-500 text-center z-10">Basado en {{ number_format($summary['crawl_issues'] ?? 0) }} checks realizados en tu dominio.</p>
        </div>

        <div class="lg:col-span-2 grid gap-4 grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Performance Score</p>
                <p class="mt-4 text-4xl font-bold text-slate-900">{{ $latestAudit?->performance_score ?? '--' }}</p>
                <p class="text-xs text-slate-500 mt-1">Velocidad y Core Web Vitals.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Páginas auditadas</p>
                <p class="mt-4 text-4xl font-bold text-slate-900">{{ number_format($latestCrawlRun?->pages_crawled ?? 0) }}</p>
                <p class="text-xs text-slate-500 mt-1">URLs revisadas en el último crawl.</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-rose-500">Errores Críticos</p>
                <p class="mt-4 text-4xl font-bold text-rose-600">{{ $crawlSeveritySummary['error'] ?? 0 }}</p>
                <p class="text-xs text-rose-500 mt-1">Requieren atención inmediata.</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-amber-600">Advertencias</p>
                <p class="mt-4 text-4xl font-bold text-amber-600">{{ $crawlSeveritySummary['warn'] ?? 0 }}</p>
                <p class="text-xs text-amber-600 mt-1">Oportunidades de mejora.</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Qué hacer para subir SEO</h2>
                <a href="{{ route('workspace.opportunities') }}" class="text-xs font-semibold text-primary hover:underline">Ver oportunidades</a>
            </div>
            <div class="divide-y divide-slate-100 flex-1">
                @forelse ($auditActions as $action)
                    <div class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-bold text-slate-800">{{ $action['title'] }}</p>
                            <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $action['priority'] === 'Alta' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700' }}">{{ $action['priority'] }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">{{ $action['body'] }}</p>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        No hay acciones disponibles todavía. Ejecuta un crawl y una auditoría para generar recomendaciones.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <h2 class="text-base font-bold text-slate-900">Hallazgos técnicos</h2>
                </div>
                <div class="space-y-3">
                    @forelse ($topFindings as $finding)
                        <div class="rounded-lg border border-slate-200 p-3">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-slate-800">{{ $finding['label'] }}</p>
                                <span class="text-xs font-bold {{ $finding['severity'] === 'error' ? 'text-rose-600' : 'text-amber-600' }}">{{ $finding['count'] }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">{{ $finding['action'] }}</p>
                            @if(collect($finding['affected_pages'])->isNotEmpty())
                                <p class="mt-2 text-[11px] text-slate-400">{{ collect($finding['affected_pages'])->join(' · ') }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No hay hallazgos técnicos detectados.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-4">Reportes temáticos</h2>
                <div class="space-y-3">
                    @foreach ($thematicReports as $report)
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3">
                            <div>
                                <span class="font-semibold text-slate-700 text-sm">{{ $report['label'] }}</span>
                                <p class="mt-1 text-xs text-slate-500">{{ $report['hint'] }}</p>
                            </div>
                            <span class="text-xs font-bold {{ $report['score'] >= 80 ? 'text-emerald-600' : ($report['score'] >= 60 ? 'text-amber-500' : 'text-rose-500') }}">{{ $report['score'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
