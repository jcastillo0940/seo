@php($activeTab = request()->query('tab', 'overview'))

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">Domain Overview</span>
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ $projectDomain }} · {{ $workspaceLocale['market_label'] ?? 'PA · es-pa' }}</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Command Center</h1>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('dashboard.sync') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Últimos 30 días
                </button>
            </form>
            <form method="POST" action="{{ route('dashboard.audit') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Snapshot ahora
                </button>
            </form>
        </div>
    </div>

    <div class="border-b border-slate-200">
        <nav class="-mb-px flex flex-wrap gap-6">
            @foreach ([
                'overview' => 'Vista General',
                'organic' => 'Búsqueda Orgánica',
                'health' => 'Salud del Sitio',
                'performance' => 'Rendimiento',
            ] as $tabKey => $tabLabel)
                <a
                    href="{{ route('workspace.summary', ['tab' => $tabKey]) }}"
                    class="whitespace-nowrap border-b-2 py-4 px-1 text-sm font-semibold transition {{ $activeTab === $tabKey ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700' }}"
                >
                    {{ $tabLabel }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Visibilidad</p>
            <p class="text-3xl font-bold text-slate-900">{{ $latestAudit?->seo_score ?? '--' }}<span class="text-lg text-slate-400 font-normal">%</span></p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Clics Totales</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['clicks'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Impresiones</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['impressions'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Keywords Trackeadas</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['tracked_keywords'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Keywords Detectadas</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['keywords'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Competidores</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['competitors'] ?? 0) }}</p>
        </div>
    </div>

    @if ($activeTab === 'organic')
        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Keyword mejor posicionada</p>
                <p class="mt-3 text-lg font-bold text-slate-900">{{ $summaryOrganic['best_keyword']->keyword ?? 'Sin datos' }}</p>
                <p class="mt-1 text-sm text-slate-500">Posición media {{ number_format($summaryOrganic['best_keyword']->avg_position ?? 0, 1) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Mayor CTR</p>
                <p class="mt-3 text-lg font-bold text-slate-900">{{ $summaryOrganic['growth_keyword']->keyword ?? 'Sin datos' }}</p>
                <p class="mt-1 text-sm text-slate-500">CTR {{ number_format($summaryOrganic['growth_keyword']->ctr ?? 0, 2) }}%</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Página orgánica líder</p>
                <p class="mt-3 text-lg font-bold text-slate-900">{{ $summaryOrganic['best_page']->page_title ?? 'Sin datos' }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ number_format($summaryOrganic['best_page']->sessions ?? 0) }} sesiones orgánicas</p>
            </div>
        </div>
    @elseif ($activeTab === 'health')
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Páginas rastreadas</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($summaryHealth['pages_crawled']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Indexables</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $summaryHealth['indexable_rate'] }}%</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Críticos</p>
                <p class="mt-3 text-3xl font-bold text-rose-600">{{ $summaryHealth['critical'] }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Advertencias</p>
                <p class="mt-3 text-3xl font-bold text-amber-500">{{ $summaryHealth['warnings'] }}</p>
            </div>
        </div>
    @elseif ($activeTab === 'performance')
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Performance Score</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ $summaryPerformance['performance_score'] ?: '--' }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">CTR Promedio</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($summaryPerformance['ctr'], 2) }}%</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Promedio de clics/día</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($summaryPerformance['daily_average_clicks'], 1) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Impresiones por clic</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($summaryPerformance['impressions_per_click'], 1) }}</p>
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Tráfico Orgánico vs Impresiones</h2>
                    <p class="text-xs text-slate-500">Últimos 30 días</p>
                </div>
                <div class="flex gap-2 text-xs">
                    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-[#00c27f]"></span> Clics</span>
                    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-[#3d5afe]"></span> Impresiones</span>
                </div>
            </div>
            <div class="p-5 flex-1 relative min-h-[300px]">
                <canvas id="seoTrendChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-5 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Oportunidades</h2>
                    <p class="text-xs text-slate-500">Keywords y páginas con mayor margen de mejora</p>
                </div>
                <a href="{{ route('workspace.opportunities') }}" class="text-xs font-semibold text-primary hover:underline">Ver todas</a>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto">
                @forelse ($quickWins->take(5) as $win)
                    <div class="px-5 py-3 hover:bg-slate-50 transition-colors">
                        <p class="text-sm font-semibold text-slate-800">{{ $win->keyword }}</p>
                        <p class="mt-0.5 text-[11px] text-slate-500">{{ number_format($win->impressions) }} impresiones · CTR {{ number_format($win->ctr, 2) }}%</p>
                        <p class="mt-2 text-xs text-primary">{{ $win->action }}</p>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center">
                        <p class="text-sm font-medium text-slate-600">No hay quick wins detectados.</p>
                        <p class="text-xs text-slate-400 mt-1">Falta más recolección de datos de Search Console.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900">Keywords Objetivo</h2>
                <a href="{{ route('workspace.keyword-hunter') }}" class="text-slate-400 hover:text-primary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
            </div>
            <div class="divide-y divide-slate-100 p-2">
                @forelse ($trackedKeywords->take(5) as $trackedKeyword)
                    @php($serpRow = $serpRows->firstWhere('keyword', $trackedKeyword->keyword))
                    <div class="px-2 py-2 flex justify-between items-center hover:bg-slate-50 rounded">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $trackedKeyword->keyword }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">{{ $trackedKeyword->country_code }} · {{ $trackedKeyword->device }}</p>
                        </div>
                        <span class="text-xs font-bold text-slate-600">#{{ $serpRow?->position ?? '—' }}</span>
                    </div>
                @empty
                    <p class="text-xs text-center text-slate-500 py-4">Sin keywords configuradas.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900">Landing pages organicas</h2>
            </div>
            <div class="divide-y divide-slate-100 p-2">
                @forelse ($topOrganicPages->take(5) as $organicPage)
                    <div class="px-2 py-2 flex justify-between items-center hover:bg-slate-50 rounded group">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-primary truncate">{{ $organicPage->page_title ?: $organicPage->page_path }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ $organicPage->page_path }}</p>
                        </div>
                        <span class="text-xs font-bold text-slate-700">{{ number_format($organicPage->sessions ?? 0) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-center text-slate-500 py-4">Sin datos orgánicos.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900">Catalogo Magento</h2>
            </div>
            <div class="divide-y divide-slate-100 p-2">
                @forelse ($topCatalogPages->take(5) as $catalogPage)
                    <div class="px-2 py-2 flex justify-between items-center hover:bg-slate-50 rounded">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $catalogPage->name }}</p>
                            <p class="text-[10px] text-slate-500">{{ $catalogPage->slug ?: '/' }}</p>
                        </div>
                        <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[9px] uppercase font-bold text-slate-600">{{ $catalogPage->type }}</span>
                    </div>
                @empty
                    <p class="text-xs text-center text-slate-500 py-4">Sin catálogo sincronizado.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900">Rivales Principales</h2>
                <a href="{{ route('workspace.competitors') }}" class="text-slate-400 hover:text-primary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
            </div>
            <div class="divide-y divide-slate-100 p-2">
                @forelse ($competitors->take(5) as $competitor)
                    <div class="px-2 py-2 flex justify-between items-center hover:bg-slate-50 rounded group">
                        <div class="min-w-0 flex items-center gap-2">
                            <div class="flex h-5 w-5 items-center justify-center rounded bg-slate-200 text-[9px] font-bold text-slate-600">
                                {{ strtoupper(substr($competitor->domain, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-primary truncate">{{ $competitor->domain }}</p>
                                @if($competitor->name)<p class="text-[10px] text-slate-500">{{ $competitor->name }}</p>@endif
                            </div>
                        </div>
                        <span class="text-xs font-bold text-slate-700">{{ $competitor->best_position ? '#'.$competitor->best_position : '—' }}</span>
                    </div>
                @empty
                    <p class="text-xs text-center text-slate-500 py-4">Aún no hay rivales detectados.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('seoTrendChart');
            if (ctx && typeof Chart !== 'undefined') {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels),
                        datasets: [
                            {
                                label: 'Clics',
                                data: @json($chartClicks),
                                borderColor: '#00c27f',
                                backgroundColor: 'rgba(0, 194, 127, 0.1)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                            },
                            {
                                label: 'Impresiones',
                                data: @json($chartImpressions),
                                borderColor: '#3d5afe',
                                backgroundColor: 'rgba(61, 90, 254, 0.05)',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                fill: true,
                                tension: 0.4,
                                pointRadius: 0,
                                pointHoverRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: true,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { color: '#64748b', font: { size: 11 } }
                            },
                            y: {
                                grid: { color: '#f1f5f9', borderDash: [4, 4] },
                                ticks: { color: '#64748b', font: { size: 11 }, maxTicksLimit: 6 }
                            }
                        }
                    }
                });
            }
        });
    </script>
</section>
