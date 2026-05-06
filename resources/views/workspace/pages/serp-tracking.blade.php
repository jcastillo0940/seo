<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ number_format($summary['tracked_keywords']) }} keywords · MX</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">SERP Tracking</h1>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('workspace.keyword-hunter') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Agregar keywords</a>
            <form method="POST" action="{{ route('project.run-serp') }}">
                @csrf
                <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Snapshot</button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Snapshots</p><p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($summary['serp_snapshots']) }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Top 3</p><p class="mt-3 text-3xl font-bold text-emerald-600">{{ $serpOverview['top3'] }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Top 10</p><p class="mt-3 text-3xl font-bold text-primary">{{ $serpOverview['top10'] }}</p></div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Fuera top 10</p><p class="mt-3 text-3xl font-bold text-rose-500">{{ $serpOverview['outsideTop10'] }}</p></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.25fr_1fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Resultados</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">{{ $latestSerpSnapshot?->trackedKeyword?->keyword ?? 'Ultimo snapshot disponible' }}</h2>
            <div class="mt-5 space-y-3">
                @forelse (($latestSerpSnapshot?->results?->sortBy('position') ?? collect())->take(10) as $result)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $result->title ?: $result->domain }}</p>
                            <p class="mt-1 truncate text-xs text-slate-500">{{ $result->domain }}</p>
                        </div>
                        <div class="ml-4 text-right">
                            @if ($result->is_own_domain)
                                <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-emerald-600">tu dominio</p>
                            @endif
                            <p class="text-lg font-bold {{ $result->is_own_domain ? 'text-emerald-600' : 'text-slate-800' }}">#{{ $result->position }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay snapshots SERP recientes.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Snapshots recientes</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Historial capturado</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($latestSnapshots as $snapshot)
                        @php($ownResult = $snapshot->results->firstWhere('is_own_domain', true))
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $snapshot->trackedKeyword?->keyword ?: 'Keyword' }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $snapshot->captured_at?->diffForHumans() }} · {{ strtoupper($snapshot->device) }}</p>
                                </div>
                                <span class="rounded-full {{ $ownResult && $ownResult->position <= 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }} px-2.5 py-1 text-xs font-semibold">
                                    {{ $ownResult ? '#'.$ownResult->position : 'sin ranking' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Todavia no hay historial de snapshots.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Keywords monitoreadas</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Lista activa</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($trackedKeywords as $trackedKeyword)
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-800">{{ $trackedKeyword->keyword }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $trackedKeyword->country_code }} · {{ $trackedKeyword->language_code }} · {{ $trackedKeyword->device }} · P{{ $trackedKeyword->priority }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Todavia no hay keywords de tracking.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
