<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ number_format($summary['tracked_keywords']) }} keywords · {{ $workspaceLocale['country_label'] ?? 'Panamá' }}</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Position Tracking</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('workspace.keyword-hunter') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Keywords
            </a>
            <form method="POST" action="{{ route('project.run-serp') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    Capturar Snapshot
                </button>
            </form>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-slate-400">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Visibilidad</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-2xl font-bold text-slate-900">{{ number_format($serpOverview['visibility'], 1) }}%</p>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-emerald-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Top 3</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $serpOverview['top3'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-blue-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Top 10</p>
            <p class="mt-2 text-2xl font-bold text-primary">{{ $serpOverview['top10'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-rose-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Fuera del Top 10</p>
            <p class="mt-2 text-2xl font-bold text-rose-500">{{ $serpOverview['outsideTop10'] }}</p>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3 pl-2">
            <span class="text-xs font-semibold text-slate-500">Posición:</span>
            @foreach (['all' => 'Todas', 'top3' => 'Top 3', 'top10' => 'Top 10', 'outside10' => 'Fuera Top 10'] as $bucketValue => $bucketLabel)
                <a
                    href="{{ route('workspace.serp-tracking', ['bucket' => $bucketValue]) }}"
                    class="rounded-md px-2 py-1 text-xs font-medium transition {{ $serpBucketFilter === $bucketValue ? 'bg-slate-100 text-slate-700' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}"
                >
                    {{ $bucketLabel }}
                </a>
            @endforeach
        </div>
        <div class="ml-auto text-xs text-slate-500">
            Solo se muestran keywords y dominios relevantes para Panamá.
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Ranking por Keyword</h2>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-white text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Keyword</th>
                            <th class="px-5 py-3 font-semibold text-right whitespace-nowrap">Posición</th>
                            <th class="px-5 py-3 font-semibold text-right whitespace-nowrap">Diferencia</th>
                            <th class="px-5 py-3 font-semibold text-right whitespace-nowrap">Volumen</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Rival líder</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($serpRows as $row)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="font-medium text-slate-900">{{ $row->keyword }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <span class="font-bold {{ ($row->position ?? 99) <= 3 ? 'text-emerald-600' : (($row->position ?? 99) <= 10 ? 'text-primary' : 'text-slate-900') }}">
                                        {{ $row->position ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @if(! is_null($row->difference))
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $row->difference > 0 ? 'text-success' : ($row->difference < 0 ? 'text-rose-500' : 'text-slate-400') }}">
                                            {{ $row->difference > 0 ? '+' : '' }}{{ $row->difference }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-medium">{{ number_format($row->volume) }}</td>
                                <td class="px-5 py-3">
                                    <span class="text-xs text-slate-600">{{ $row->best_competitor ?: 'Sin rival local detectado' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-500">Todavía no hay keywords de tracking.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 flex justify-between items-center">
                <h2 class="text-base font-bold text-slate-900">SERP local filtrada</h2>
                <span class="text-xs text-slate-500">{{ $latestSerpSnapshot?->trackedKeyword?->keyword ?? 'Último snapshot disponible' }}</span>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-white text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Pos</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Dominio / URL</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($latestSerpResults->take(10) as $result)
                            <tr class="hover:bg-slate-50 transition-colors {{ $result->is_own_domain ? 'bg-emerald-50/50' : '' }}">
                                <td class="px-5 py-3 w-16">
                                    <span class="font-bold {{ $result->is_own_domain ? 'text-emerald-600' : 'text-slate-900' }}">
                                        {{ $result->position }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-col min-w-0">
                                        <p class="truncate font-medium {{ $result->is_own_domain ? 'text-emerald-700' : 'text-slate-800' }}" title="{{ $result->title ?: $result->domain }}">
                                            {{ $result->title ?: $result->domain }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            @if ($result->is_own_domain)
                                                <span class="rounded bg-emerald-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-emerald-700">Tú</span>
                                            @endif
                                            <p class="truncate text-xs text-slate-500" title="{{ $result->url }}">{{ $result->domain }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-8 text-center text-slate-500">Aún no hay snapshots SERP recientes filtrados para Panamá.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
