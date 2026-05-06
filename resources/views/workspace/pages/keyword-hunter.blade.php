<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Investigacion · MX · espanol</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Keyword Hunter</h1>
        </div>
        <span class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white">{{ $topKeywords->count() }} keywords</span>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-base text-slate-500">Agrega keywords objetivo y usa las oportunidades de Search Console para convertir consultas cercanas en clusters priorizados.</p>
            </div>
            <form method="POST" action="{{ route('tracked-keywords.store') }}" class="grid gap-3 md:grid-cols-5 xl:min-w-[880px]">
                @csrf
                <input name="keyword" class="rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="cortes premium para parrilla" required>
                <select name="country_code" class="rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="mx">MX</option>
                    <option value="us">US</option>
                    <option value="co">CO</option>
                    <option value="ar">AR</option>
                </select>
                <select name="language_code" class="rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="es">es</option>
                    <option value="en">en</option>
                </select>
                <input type="hidden" name="device" value="desktop">
                <select name="search_intent" class="rounded-xl border border-slate-200 px-4 py-3 text-sm">
                    <option value="">intent opcional</option>
                    <option value="informational">informational</option>
                    <option value="commercial">commercial</option>
                    <option value="transactional">transactional</option>
                    <option value="navigational">navigational</option>
                </select>
                <div class="flex gap-3">
                    <select name="priority" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm">
                        <option value="1">P1</option>
                        <option value="2">P2</option>
                        <option value="3" selected>P3</option>
                        <option value="4">P4</option>
                        <option value="5">P5</option>
                    </select>
                    <button class="rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary/90">Agregar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.4fr_0.95fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Dificultad vs oportunidad</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Top keywords por clicks y posicion media</h2>
            <div class="mt-5 space-y-3">
                @forelse ($topKeywords as $keyword)
                    <div class="grid gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $keyword->keyword }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ number_format($keyword->clicks) }} clicks · {{ number_format($keyword->impressions) }} impresiones</p>
                        </div>
                        <div class="text-sm font-semibold text-slate-700">Pos {{ number_format($keyword->avg_position, 1) }}</div>
                        <div class="text-xs font-semibold uppercase tracking-[0.22em] text-primary">keyword</div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Todavia no hay keywords sincronizadas.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Intent mix</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Distribucion de keywords monitoreadas</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($trackedKeywordsByIntent as $intent)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="text-sm font-semibold text-slate-800">{{ str($intent->intent)->replace('_', ' ')->headline() }}</span>
                            <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $intent->total }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Todavia no hay intents registrados.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Ideas para seguir</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Oportunidades de keyword</h2>
                <div class="mt-4 space-y-3">
                    @forelse ($keywordOpportunities->take(8) as $opportunity)
                        <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                            <p class="text-sm font-semibold text-slate-800">{{ $opportunity['keyword'] }}</p>
                            <p class="mt-1 text-xs text-slate-500">Pos {{ $opportunity['avg_position'] }} · CTR {{ $opportunity['ctr'] }}%</p>
                            <p class="mt-2 text-xs text-slate-600">{{ $opportunity['hint'] }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Aun no hay oportunidades de keyword calculadas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
