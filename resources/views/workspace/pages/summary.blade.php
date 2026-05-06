<section class="space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ $projectDomain }} · MX · es-MX</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Command Center</h1>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('dashboard.sync') }}">
                @csrf
                <button class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Ultimos 30 dias</button>
            </form>
            <form method="POST" action="{{ route('dashboard.audit') }}">
                @csrf
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Snapshot ahora</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Keywords rastreadas</p><p class="mt-3 text-4xl font-bold text-slate-900">{{ number_format($summary['keywords']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Impresiones</p><p class="mt-3 text-4xl font-bold text-slate-900">{{ number_format($summary['impressions']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Clicks</p><p class="mt-3 text-4xl font-bold text-slate-900">{{ number_format($summary['clicks']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Keywords objetivo</p><p class="mt-3 text-4xl font-bold text-slate-900">{{ number_format($summary['tracked_keywords']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Competidores</p><p class="mt-3 text-4xl font-bold text-slate-900">{{ number_format($summary['competitors']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Visibilidad</p><p class="mt-3 text-4xl font-bold text-slate-900">{{ $latestAudit?->seo_score ?? '--' }}</p></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.7fr_0.9fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Evolucion SEO</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Clicks e impresiones de los ultimos 30 dias</h2>
                </div>
            </div>
            <div class="mt-4 h-72">
                <canvas id="seoTrendChart"></canvas>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Quick Wins</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Tareas de 2 minutos</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($quickWins->take(5) as $win)
                    <div class="px-5 py-4">
                        <p class="text-sm font-semibold text-slate-800">{{ $win->keyword }}</p>
                        <p class="mt-1 text-xs text-slate-500">Pos {{ number_format($win->avg_position, 1) }} · {{ number_format($win->impressions) }} impresiones</p>
                    </div>
                @empty
                    <div class="px-5 py-6 text-sm text-slate-500">No hay quick wins detectados por ahora.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Tracked keywords</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Keywords objetivo</h2>
            <div class="mt-4 space-y-3">
                @forelse ($trackedKeywords->take(4) as $trackedKeyword)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $trackedKeyword->keyword }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $trackedKeyword->country_code }} · {{ $trackedKeyword->language_code }} · {{ $trackedKeyword->device }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Todavia no has definido keywords objetivo.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Magento</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Catalogo Magento</h2>
            <div class="mt-4 space-y-3">
                @forelse ($topCatalogPages->take(4) as $catalogPage)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $catalogPage->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ strtoupper($catalogPage->type) }} · {{ $catalogPage->slug ?: '/' }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay catalogo sincronizado desde Magento.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">GA4 organico</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Landing pages organicas</h2>
            <div class="mt-4 space-y-3">
                @forelse ($topOrganicPages->take(4) as $organicPage)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $organicPage->page_title ?: $organicPage->page_path }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $organicPage->page_path }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay datos de landing pages organicas desde GA4.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Competidores</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Rivales activos</h2>
            <div class="mt-4 space-y-3">
                @forelse ($competitors->take(4) as $competitor)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-3 py-3">
                        <p class="text-sm font-semibold text-slate-800">{{ $competitor->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $competitor->domain }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Todavia no hay competidores cargados.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('seoTrendChart');
        if (ctx && typeof Chart !== 'undefined') {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Clicks',
                            data: @json($chartClicks),
                            borderColor: '#00c27f',
                            backgroundColor: 'rgba(0, 194, 127, 0.08)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                        },
                        {
                            label: 'Impresiones',
                            data: @json($chartImpressions),
                            borderColor: '#3d5afe',
                            backgroundColor: 'rgba(61, 90, 254, 0.04)',
                            fill: true,
                            tension: 0.35,
                            pointRadius: 0,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                        y: { grid: { color: '#e2e8f0' }, ticks: { color: '#94a3b8' } }
                    }
                }
            });
        }
    </script>
