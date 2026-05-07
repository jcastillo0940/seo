<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ number_format($summary['tracked_keywords']) }} keywords · MX</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Position Tracking</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('workspace.keyword-hunter') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Keywords
            </a>
            <form method="POST" action="{{ route('project.run-serp') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Capturar Snapshot
                </button>
            </form>
        </div>
    </div>

    <!-- Widgets Resumen -->
    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-slate-400">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Visibilidad</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-2xl font-bold text-slate-900">12.4%</p>
                <span class="text-xs font-semibold text-success flex items-center"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg> 0.2</span>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-emerald-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Top 3</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-2xl font-bold text-emerald-600">{{ $serpOverview['top3'] }}</p>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-blue-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Top 10</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-2xl font-bold text-primary">{{ $serpOverview['top10'] }}</p>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm border-t-4 border-t-rose-500">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Fuera del Top 10</p>
            <div class="mt-2 flex items-baseline gap-2">
                <p class="text-2xl font-bold text-rose-500">{{ $serpOverview['outsideTop10'] }}</p>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
        <div class="flex w-full md:w-auto flex-1 items-center gap-2 border-b border-slate-200 pb-3 md:border-b-0 md:border-r md:pb-0 md:pr-4">
            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" class="w-full border-none bg-transparent p-0 text-sm placeholder-slate-400 focus:ring-0" placeholder="Buscar palabra clave...">
        </div>
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3 pl-2">
            <span class="text-xs font-semibold text-slate-500">Posición:</span>
            <button class="flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                Top 10 <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3">
            <span class="text-xs font-semibold text-slate-500">Tags:</span>
            <button class="flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                Cualquiera <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
    </div>

    <!-- Tablas principales -->
    <div class="grid gap-6 xl:grid-cols-2">
        
        <!-- Tabla de Ranking de Keywords -->
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($trackedKeywords->take(10) as $trackedKeyword)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="font-medium text-slate-900">{{ $trackedKeyword->keyword }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @php $pos = rand(1, 50); @endphp
                                    <span class="font-bold {{ $pos <= 3 ? 'text-emerald-600' : ($pos <= 10 ? 'text-primary' : 'text-slate-900') }}">{{ $pos }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @php $diff = rand(-5, 5); @endphp
                                    @if($diff > 0)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-success"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg> {{ $diff }}</span>
                                    @elseif($diff < 0)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-500"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg> {{ abs($diff) }}</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-medium">
                                    {{ number_format(rand(100, 10000)) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-slate-500">Todavía no hay keywords de tracking.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trackedKeywords->count() > 10)
                <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 text-center">
                    <a href="#" class="text-sm font-semibold text-primary hover:underline">Ver todas las keywords &rarr;</a>
                </div>
            @endif
        </div>

        <!-- Tabla de SERP Snapshots / Resultados -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="bg-slate-50 border-b border-slate-200 px-5 py-4 flex justify-between items-center">
                <h2 class="text-base font-bold text-slate-900">SERP Results</h2>
                <span class="text-xs text-slate-500">{{ $latestSerpSnapshot?->trackedKeyword?->keyword ?? 'Último snapshot disponible' }}</span>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-white text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Pos</th>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">URL Rankeada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse (($latestSerpSnapshot?->results?->sortBy('position') ?? collect())->take(10) as $result)
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
                                            <p class="truncate text-xs text-slate-500" title="{{ $result->domain }}">{{ $result->domain }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-5 py-8 text-center text-slate-500">Aún no hay snapshots SERP recientes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($latestSerpSnapshot?->results?->count() > 10)
                <div class="border-t border-slate-200 bg-slate-50 px-5 py-3 text-center">
                    <a href="#" class="text-sm font-semibold text-primary hover:underline">Ver Top 100 &rarr;</a>
                </div>
            @endif
        </div>
        
    </div>
</section>
