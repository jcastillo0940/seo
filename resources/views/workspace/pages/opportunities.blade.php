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
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Keyword Magic Tool
            </a>
        </div>
    </div>

    <!-- Widgets Resumen -->
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-amber-400">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Quick Wins (Pos 11-20)</p>
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

    <!-- Filtros de Oportunidades -->
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3">
            <span class="text-xs font-semibold text-slate-500">Impacto Estimado:</span>
            <button class="flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                Alto <svg class="h-3 w-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
            </button>
        </div>
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3 pl-2">
            <span class="text-xs font-semibold text-slate-500">Esfuerzo:</span>
            <button class="flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                Bajo <svg class="h-3 w-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
            </button>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <!-- Tabla Quick Wins -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Keywords Cercanas (Quick Wins)</h2>
                <p class="text-xs text-slate-500">Palabras clave en la segunda página (Pos 11-20).</p>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[500px]">
                @forelse ($quickWins as $win)
                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors group cursor-pointer">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-primary transition-colors">{{ $win->keyword }}</p>
                            <span class="inline-flex items-center justify-center rounded bg-amber-100 px-2 py-1 text-[10px] font-bold text-amber-700 whitespace-nowrap">Pos {{ number_format($win->avg_position, 1) }}</span>
                        </div>
                        <div class="flex gap-4 text-xs">
                            <div class="flex flex-col">
                                <span class="text-slate-400">Impresiones</span>
                                <span class="font-medium text-slate-700">{{ number_format($win->impressions) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-slate-400">Volumen</span>
                                <span class="font-medium text-slate-700">{{ number_format(rand(500, 5000)) }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">No hay quick wins detectados por el momento.</div>
                @endforelse
            </div>
        </div>

        <!-- Tabla de Páginas -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Páginas a Empujar</h2>
                <p class="text-xs text-slate-500">Páginas con alto potencial y métricas mixtas.</p>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[500px]">
                @forelse ($pageOpportunities->take(8) as $opportunity)
                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-primary truncate hover:underline cursor-pointer">{{ $opportunity['name'] ?? ($opportunity['url'] ?? 'Página') }}</p>
                                <p class="mt-0.5 text-[10px] font-medium text-rose-500 uppercase">{{ $opportunity['top_issue'] ?? 'Sin issue fuerte' }}</p>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-[9px] uppercase font-bold text-slate-400">Score</span>
                                <span class="font-bold text-slate-800">{{ $opportunity['score'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3 pt-3 border-t border-slate-50 text-[10px]">
                            <div class="flex flex-col"><span class="text-slate-400">Sesiones</span><span class="font-semibold text-slate-700">{{ number_format($opportunity['sessions'] ?? 0) }}</span></div>
                            <div class="flex flex-col"><span class="text-slate-400">Conversiones</span><span class="font-semibold text-emerald-600">{{ number_format($opportunity['conversions'] ?? 0) }}</span></div>
                            <div class="flex flex-col text-right"><span class="text-slate-400">Issues</span><span class="font-semibold text-rose-500">{{ $opportunity['issue_count'] ?? 0 }}</span></div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">Aún no hay oportunidades por página calculadas.</div>
                @endforelse
            </div>
        </div>

        <!-- Tabla de Brechas -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Brechas (Keyword Gap)</h2>
                <p class="text-xs text-slate-500">Keywords donde tus competidores te superan.</p>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto max-h-[500px]">
                @forelse ($competitorGaps->take(8) as $gap)
                    <div class="px-5 py-4 hover:bg-slate-50 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <p class="text-sm font-semibold text-slate-800">{{ $gap['keyword'] }}</p>
                            <span class="inline-flex items-center justify-center rounded bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">Gap {{ $gap['gap'] }} pos</span>
                        </div>
                        <div class="flex items-center gap-4 text-xs mt-2 relative">
                            <!-- Barra comparativa visual -->
                            <div class="absolute top-1/2 left-0 right-0 h-px bg-slate-200 -z-10"></div>
                            <div class="flex flex-col items-center flex-1 bg-white">
                                <span class="font-bold text-primary">#{{ $gap['own_position'] }}</span>
                                <span class="text-[9px] uppercase text-slate-400 mt-1">Tú</span>
                            </div>
                            <div class="flex flex-col items-center flex-1 bg-white">
                                <span class="font-bold text-slate-700">#{{ $gap['position'] }}</span>
                                <span class="text-[9px] uppercase text-slate-400 mt-1 truncate max-w-[80px]" title="{{ $gap['competitor'] }}">{{ $gap['competitor'] }}</span>
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
