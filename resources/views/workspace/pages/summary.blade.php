<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">Domain Overview</span>
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ $projectDomain }} · MX · es-MX</p>
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

    <!-- Pestañas (simuladas visualmente como Semrush) -->
    <div class="border-b border-slate-200">
        <nav class="-mb-px flex space-x-8">
            <a href="#" class="border-primary text-primary whitespace-nowrap border-b-2 py-4 px-1 text-sm font-bold">Vista General</a>
            <a href="#" class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">Búsqueda Orgánica</a>
            <a href="#" class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">Salud del Sitio</a>
            <a href="#" class="border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">Rendimiento</a>
        </nav>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1"><svg class="h-3 w-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Visibilidad</p>
            <p class="text-3xl font-bold text-slate-900">{{ $latestAudit?->seo_score ?? '--' }}<span class="text-lg text-slate-400 font-normal">%</span></p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1"><svg class="h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg> Clics Totales</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['clicks'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1"><svg class="h-3 w-3 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> Impresiones</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['impressions'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1"><svg class="h-3 w-3 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg> Tracked KWs</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['tracked_keywords'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1"><svg class="h-3 w-3 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg> Total KWs</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['keywords'] ?? 0) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 flex items-center gap-1"><svg class="h-3 w-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg> Competidores</p>
            <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['competitors'] ?? 0) }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
        <!-- Gráfico de Evolución SEO -->
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

        <!-- Quick Wins (Semrush-style list) -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-5 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Oportunidades (Quick Wins)</h2>
                    <p class="text-xs text-slate-500">Keywords en posiciones 11-20</p>
                </div>
                <button class="text-xs font-semibold text-primary hover:underline">Ver todas</button>
            </div>
            <div class="divide-y divide-slate-100 flex-1 overflow-y-auto">
                @forelse ($quickWins->take(5) as $win)
                    <div class="px-5 py-3 hover:bg-slate-50 transition-colors group cursor-pointer flex justify-between items-center">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-primary transition-colors">{{ $win->keyword }}</p>
                            <p class="mt-0.5 text-[11px] text-slate-500">{{ number_format($win->impressions) }} impresiones</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center justify-center rounded bg-amber-100 px-2 py-1 text-xs font-bold text-amber-700">Pos {{ number_format($win->avg_position, 1) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-12 flex flex-col items-center justify-center text-center">
                        <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <p class="text-sm font-medium text-slate-600">No hay quick wins detectados.</p>
                        <p class="text-xs text-slate-400 mt-1">Espera más recolección de datos.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 4 Widgets de tablas pequeñas al estilo Semrush -->
    <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
        <!-- Top Tracked Keywords -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900">Keywords Objetivo</h2>
                <a href="{{ route('workspace.keyword-hunter') }}" class="text-slate-400 hover:text-primary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
            </div>
            <div class="divide-y divide-slate-100 p-2">
                @forelse ($trackedKeywords->take(5) as $trackedKeyword)
                    <div class="px-2 py-2 flex justify-between items-center hover:bg-slate-50 rounded">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $trackedKeyword->keyword }}</p>
                            <p class="text-[10px] text-slate-500 uppercase">{{ $trackedKeyword->country_code }} · {{ $trackedKeyword->device }}</p>
                        </div>
                        <span class="text-xs font-bold text-slate-600">#{{ rand(1, 50) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-center text-slate-500 py-4">Sin keywords configuradas.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Organic Pages -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900">Mejores Páginas (GA4)</h2>
                <button class="text-slate-400 hover:text-primary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
            </div>
            <div class="divide-y divide-slate-100 p-2">
                @forelse ($topOrganicPages->take(5) as $organicPage)
                    <div class="px-2 py-2 flex justify-between items-center hover:bg-slate-50 rounded group">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-primary truncate group-hover:underline cursor-pointer">{{ $organicPage->page_title ?: $organicPage->page_path }}</p>
                            <p class="text-[10px] text-slate-500 truncate">{{ $organicPage->page_path }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-center text-slate-500 py-4">Sin datos orgánicos.</p>
                @endforelse
            </div>
        </div>

        <!-- Catálogo Magento -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm flex flex-col">
            <div class="border-b border-slate-200 px-4 py-3 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900">Catálogo (Magento)</h2>
                <button class="text-slate-400 hover:text-primary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
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

        <!-- Competidores Activos -->
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
                                <p class="text-xs font-semibold text-primary truncate group-hover:underline cursor-pointer">{{ $competitor->domain }}</p>
                                @if($competitor->name)<p class="text-[10px] text-slate-500">{{ $competitor->name }}</p>@endif
                            </div>
                        </div>
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
                                titleFont: { size: 13, family: "'Inter', sans-serif" },
                                bodyFont: { size: 12, family: "'Inter', sans-serif" },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: true,
                            }
                        },
                        scales: {
                            x: { 
                                grid: { display: false }, 
                                ticks: { color: '#64748b', font: { family: "'Inter', sans-serif", size: 11 } } 
                            },
                            y: { 
                                grid: { color: '#f1f5f9', borderDash: [4, 4] }, 
                                ticks: { color: '#64748b', font: { family: "'Inter', sans-serif", size: 11 }, maxTicksLimit: 6 } 
                            }
                        }
                    }
                });
            }
        });
    </script>
</section>
