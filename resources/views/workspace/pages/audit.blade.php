<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Auditoria tecnica</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Auditoria</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('workspace.deep-scan') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Abrir deep scan</a>
            <form method="POST" action="{{ route('dashboard.audit') }}">
                @csrf
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Lanzar auditoria</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Performance</p><p class="mt-3 text-4xl font-bold text-emerald-600">{{ $latestAudit?->performance_score ?? '--' }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">SEO</p><p class="mt-3 text-4xl font-bold text-primary">{{ $latestAudit?->seo_score ?? '--' }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Issues crawl</p><p class="mt-3 text-4xl font-bold text-rose-500">{{ number_format($summary['crawl_issues']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Ultima corrida</p><p class="mt-3 text-lg font-bold text-slate-900">{{ $latestAudit?->audited_at?->diffForHumans() ?? 'sin auditoria' }}</p></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.25fr_0.95fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Hallazgos</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Alertas tecnicas mas recientes</h2>
            <div class="mt-5 space-y-3">
                @forelse ($latestCrawlIssues as $issue)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $issue['label'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $issue['url'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay hallazgos tecnicos disponibles.</p>
                @endforelse
            </div>
        </div>
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Audit raw</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Metadatos de la ultima corrida</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"><p class="text-xs uppercase tracking-[0.22em] text-slate-400">Fuente</p><p class="mt-1 font-medium text-slate-800">{{ $latestAudit?->json_raw_data['source'] ?? 'desconocida' }}</p></div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"><p class="text-xs uppercase tracking-[0.22em] text-slate-400">Auditado</p><p class="mt-1 font-medium text-slate-800">{{ $latestAudit?->audited_at?->toDayDateTimeString() ?? 'sin fecha' }}</p></div>
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Stack</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Estado del sistema</h2>
                <ul class="mt-5 space-y-3 text-sm text-slate-600">
                    <li>Laravel 13 sobre PHP 8.3.</li>
                    <li>Queue driver activo para sync, crawl y snapshot.</li>
                    <li>Search Console, GA4 y Magento alimentan las vistas operativas.</li>
                    <li>El crawler y la auditoria ya comparten hallazgos en este workspace.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
