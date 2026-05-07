<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-rose-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-rose-600">Priorización Estratégica</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Keyword Gap & Oportunidades</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('workspace.keyword-hunter') }}" class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                Keyword Hunter
            </a>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-amber-400">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Quick Wins (Pos 8-20)</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-3xl font-bold text-slate-900">{{ $quickWins->count() }}</p>
                <span class="text-xs text-slate-500">Keywords</span>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-blue-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Páginas a Optimizar</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-3xl font-bold text-slate-900">{{ $pageOpportunities->count() }}</p>
                <span class="text-xs text-slate-500">URLs</span>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-purple-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Brechas vs Rivales</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-3xl font-bold text-slate-900">{{ $competitorGaps->count() }}</p>
                <span class="text-xs text-slate-500">Oportunidades</span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Keywords Cercanas</h2>
                <p class="text-xs text-slate-500">Palabras clave a un empujón del top 10.</p>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[520px]">
                @forelse ($quickWins as $win)
                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-semibold text-slate-800">{{ $win->keyword }}</p>
                            <span class="inline-flex items-center justify-center rounded bg-amber-100 px-2 py-1 text-[10px] font-bold text-amber-700 whitespace-nowrap">Pos {{ number_format($win->avg_position, 1) }}</span>
                        </div>
                        <div class="flex gap-4 text-xs text-slate-500">
                            <span>{{ number_format($win->impressions) }} impresiones</span>
                            <span>CTR {{ number_format($win->ctr, 2) }}%</span>
                            <span>KD {{ $win->difficulty }}</span>
                        </div>
                        <p class="mt-2 text-xs text-primary">{{ $win->action }}</p>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">No hay quick wins detectados por el momento.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Páginas a Empujar</h2>
                <p class="text-xs text-slate-500">Páginas con alto potencial y métricas mixtas.</p>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[520px]">
                @forelse ($pageOpportunities as $opportunity)
                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-primary truncate">{{ $opportunity['name'] ?? ($opportunity['url'] ?? 'Página') }}</p>
                                <p class="mt-0.5 text-[10px] font-medium text-rose-500 uppercase">{{ $opportunity['top_issue'] ?? 'Sin issue fuerte' }}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[9px] uppercase font-bold text-slate-400">Score</span>
                                <span class="font-bold text-slate-800">{{ $opportunity['score'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 gap-2 mt-3 pt-3 border-t border-slate-50 text-[10px]">
                            <div class="flex flex-col"><span class="text-slate-400">Sesiones</span><span class="font-semibold text-slate-700">{{ number_format($opportunity['sessions'] ?? 0) }}</span></div>
                            <div class="flex flex-col"><span class="text-slate-400">Conv.</span><span class="font-semibold text-emerald-600">{{ number_format($opportunity['conversions'] ?? 0) }}</span></div>
                            <div class="flex flex-col"><span class="text-slate-400">Impacto</span><span class="font-semibold text-slate-700">{{ $opportunity['impact'] ?? 'Medio' }}</span></div>
                            <div class="flex flex-col text-right"><span class="text-slate-400">Esfuerzo</span><span class="font-semibold text-slate-700">{{ $opportunity['effort'] ?? 'Bajo' }}</span></div>
                        </div>
                        <details class="mt-3 rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <summary class="cursor-pointer text-xs font-semibold text-slate-800">Qué debo hacer para empujar esta página</summary>
                            <div class="mt-3 space-y-2">
                                @forelse (($opportunity['actions'] ?? []) as $action)
                                    <p class="text-xs text-slate-600">{{ $action }}</p>
                                @empty
                                    <p class="text-xs text-slate-500">Esta página necesita más datos antes de recomendar cambios específicos.</p>
                                @endforelse
                            </div>
                        </details>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">Aún no hay oportunidades por página calculadas.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Brechas Locales</h2>
                <p class="text-xs text-slate-500">Keywords donde un competidor de Panamá te supera.</p>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[520px]">
                @forelse ($competitorGaps->take(8) as $gap)
                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <p class="text-sm font-semibold text-slate-800">{{ $gap['keyword'] }}</p>
                            <span class="inline-flex items-center justify-center rounded bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">Gap {{ $gap['gap'] }} pos</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex flex-col">
                                <span class="text-slate-400">Tu posición</span>
                                <span class="font-bold text-primary">#{{ $gap['own_position'] }}</span>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-slate-400">{{ $gap['competitor'] }}</span>
                                <span class="font-bold text-slate-700">#{{ $gap['position'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">Aún no hay brechas competitivas suficientes.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
