<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Priorizacion · impacto primero</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Oportunidades</h1>
        </div>
        <a href="{{ route('workspace.keyword-hunter') }}" class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Abrir keyword hunter</a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Quick wins</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ $quickWins->count() }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Paginas con score</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ $pageOpportunities->count() }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Brechas competitivas</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ $competitorGaps->count() }}</p></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr_0.95fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Quick wins</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Keywords cercanas</h2>
            <div class="mt-4 space-y-3">
                @forelse ($quickWins as $win)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $win->keyword }}</p>
                        <p class="mt-1 text-xs text-slate-500">Pos {{ number_format($win->avg_position, 1) }} · {{ number_format($win->impressions) }} impresiones</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No hay quick wins detectados.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Page opportunities</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Paginas a empujar</h2>
            <div class="mt-4 space-y-3">
                @forelse ($pageOpportunities->take(8) as $opportunity)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $opportunity['name'] ?? ($opportunity['url'] ?? 'Pagina') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $opportunity['top_issue'] ?? 'Sin issue fuerte' }}</p>
                            </div>
                            <span class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary">{{ $opportunity['score'] ?? 0 }}</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ number_format($opportunity['sessions'] ?? 0) }} sesiones · {{ number_format($opportunity['conversions'] ?? 0) }} conversiones · {{ $opportunity['issue_count'] ?? 0 }} issues</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay oportunidades por pagina calculadas.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Competitor gaps</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Brechas visibles</h2>
            <div class="mt-4 space-y-3">
                @forelse ($competitorGaps->take(8) as $gap)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $gap['keyword'] }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $gap['competitor'] }} adelante por {{ $gap['gap'] }} posiciones</p>
                        <p class="mt-2 text-xs font-medium text-slate-600">Ellos #{{ $gap['position'] }} · tu dominio #{{ $gap['own_position'] }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay brechas competitivas suficientes.</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
