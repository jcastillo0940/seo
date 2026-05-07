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
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 21h7a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v11m0 5l4.879-4.879m0 0a3 3 0 104.243-4.242 3 3 0 00-4.243 4.242z"/></svg>
                Deep Scan
            </a>
            <form method="POST" action="{{ route('dashboard.audit') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Re-Auditar
                </button>
            </form>
        </div>
    </div>

    <!-- Puntuación General y Métricas -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Site Health Score -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm flex flex-col justify-center items-center relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent"></div>
            <h3 class="text-sm font-bold text-slate-600 relative z-10 mb-4">Salud del Sitio (Site Health)</h3>
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
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Performance Score</p>
                    <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <p class="text-4xl font-bold text-slate-900">{{ $latestAudit?->performance_score ?? '--' }}</p>
                    <p class="text-xs text-slate-500 mt-1">Velocidad y core web vitals.</p>
                </div>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Páginas Auditadas</p>
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-4xl font-bold text-slate-900">542</p>
                    <p class="text-xs text-slate-500 mt-1">Límite de la campaña actual: 1,000</p>
                </div>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-rose-500">Errores Críticos</p>
                    <svg class="h-5 w-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <p class="text-4xl font-bold text-rose-600">{{ number_format($summary['crawl_issues']) }}</p>
                    <p class="text-xs text-rose-500 mt-1">Requieren atención inmediata.</p>
                </div>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-amber-600">Advertencias</p>
                    <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-4xl font-bold text-amber-600">32</p>
                    <p class="text-xs text-amber-600 mt-1">Oportunidades de mejora.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.5fr_1fr]">
        <!-- Top Issues List -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Top Hallazgos Técnicos</h2>
                <a href="#" class="text-xs font-semibold text-primary hover:underline">Ver todos los issues</a>
            </div>
            <div class="divide-y divide-slate-100 flex-1">
                @forelse ($latestCrawlIssues ?? [['label'=>'Múltiples etiquetas H1', 'url'=>'32 páginas afectadas', 'type'=>'error'], ['label'=>'Páginas sin meta description', 'url'=>'14 páginas afectadas', 'type'=>'warn']] as $issue)
                    <div class="p-4 hover:bg-slate-50 transition-colors flex items-start gap-4">
                        @if(($issue['type'] ?? 'error') === 'error')
                            <div class="mt-1 shrink-0 rounded-full bg-rose-100 p-1 text-rose-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        @else
                            <div class="mt-1 shrink-0 rounded-full bg-amber-100 p-1 text-amber-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-800">{{ $issue['label'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $issue['url'] }}</p>
                        </div>
                        <button class="text-xs font-semibold text-slate-400 hover:text-primary">Inspeccionar &rarr;</button>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500 text-sm">
                        No hay hallazgos técnicos detectados. ¡Excelente trabajo!
                    </div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <!-- Detalles del rastreo -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h2 class="text-base font-bold text-slate-900">Detalles de la Auditoría</h2>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Última actualización</span>
                        <span class="font-medium text-slate-900">{{ $latestAudit?->audited_at?->toDayDateTimeString() ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Profundidad del crawl</span>
                        <span class="font-medium text-slate-900">4 niveles</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-slate-500">Páginas excluidas</span>
                        <span class="font-medium text-slate-900">12 URLs</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-slate-500">User Agent</span>
                        <span class="font-medium text-slate-900 text-xs bg-slate-100 px-2 py-1 rounded">360SEO-Bot</span>
                    </div>
                </div>
            </div>

            <!-- Reportes Temáticos -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-bold text-slate-900 mb-4">Reportes Temáticos</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 hover:bg-slate-100 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white rounded shadow-sm text-blue-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg></div>
                            <span class="font-semibold text-slate-700 text-sm">Crawlability</span>
                        </div>
                        <span class="text-xs font-bold text-emerald-600">92%</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 hover:bg-slate-100 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white rounded shadow-sm text-purple-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg></div>
                            <span class="font-semibold text-slate-700 text-sm">Internal Linking</span>
                        </div>
                        <span class="text-xs font-bold text-amber-500">74%</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 hover:bg-slate-100 cursor-pointer transition">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-white rounded shadow-sm text-rose-500"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg></div>
                            <span class="font-semibold text-slate-700 text-sm">Markup & Tags</span>
                        </div>
                        <span class="text-xs font-bold text-rose-500">45%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
