@php($intentOptions = ['all' => 'Todos', 'informational' => 'Informativo', 'commercial' => 'Comercial', 'transactional' => 'Transaccional'])

<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">Panamá</span>
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Investigación · {{ $workspaceLocale['market_label'] ?? 'PA · es-pa' }}</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Keyword Hunter</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('workspace.opportunities') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                Ver oportunidades
            </a>
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="POST" action="{{ route('tracked-keywords.store') }}" class="flex flex-col md:flex-row">
            @csrf
            <div class="flex flex-1 items-center gap-3 border-b border-slate-200 px-4 py-3 md:border-b-0 md:border-r">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="keyword" class="w-full border-none bg-transparent p-0 text-sm placeholder-slate-400 focus:ring-0" placeholder="Ingresa una palabra clave, ej: supermercado en panama" required>
            </div>
            <div class="flex divide-x divide-slate-200">
                <select name="country_code" class="border-none bg-transparent py-3 pl-4 pr-8 text-sm text-slate-600 focus:ring-0">
                    <option value="pa" selected>Panamá (PA)</option>
                </select>
                <select name="language_code" class="border-none bg-transparent py-3 pl-4 pr-8 text-sm text-slate-600 focus:ring-0 hidden sm:block">
                    <option value="es" selected>Español</option>
                </select>
                <select name="search_intent" class="border-none bg-transparent py-3 pl-4 pr-8 text-sm text-slate-600 focus:ring-0 hidden sm:block">
                    <option value="">Intento automático</option>
                    <option value="informational">Informativo</option>
                    <option value="commercial">Comercial</option>
                    <option value="transactional">Transaccional</option>
                </select>
                <select name="priority" class="border-none bg-transparent py-3 pl-4 pr-8 text-sm text-slate-600 focus:ring-0 hidden sm:block">
                    <option value="1">Prioridad Alta (P1)</option>
                    <option value="3" selected>Prioridad Media (P3)</option>
                    <option value="5">Prioridad Baja (P5)</option>
                </select>
                <input type="hidden" name="device" value="desktop">
                <button class="bg-success px-6 py-3 text-sm font-bold text-white transition hover:bg-success/90 rounded-r-xl md:rounded-l-none">
                    Analizar
                </button>
            </div>
        </form>
    </div>

    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
        <div class="flex flex-wrap items-center gap-2 border-r border-slate-200 pr-3">
            <span class="text-xs font-semibold text-slate-500">Intent:</span>
            @foreach ($intentOptions as $intentValue => $intentLabel)
                <a
                    href="{{ route('workspace.keyword-hunter', ['intent' => $intentValue]) }}"
                    class="rounded-md px-2 py-1 text-xs font-medium transition {{ $keywordIntentFilter === $intentValue ? 'bg-slate-100 text-slate-700' : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50' }}"
                >
                    {{ $intentLabel }}
                </a>
            @endforeach
        </div>
        <div class="ml-auto flex items-center gap-2 text-xs text-slate-500">
            <span><strong class="text-slate-900">{{ $topKeywords->count() }}</strong> keywords visibles en Panamá</span>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[240px_1fr]">
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Por intención</h3>
                <div class="space-y-1">
                    <a href="{{ route('workspace.keyword-hunter', ['intent' => 'all']) }}" class="flex w-full items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-900">
                        <span class="font-medium">Todas las palabras</span>
                        <span class="rounded bg-slate-200 px-1.5 py-0.5 text-xs text-slate-600">{{ $trackedKeywordsByIntent->sum('total') ?: $topKeywords->count() }}</span>
                    </a>
                    @foreach ($trackedKeywordsByIntent as $intent)
                        <a href="{{ route('workspace.keyword-hunter', ['intent' => $intent->intent]) }}" class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            <span>{{ match($intent->intent) { 'transactional' => 'Transaccional', 'commercial' => 'Comercial', 'informational' => 'Informativa', default => 'Sin clasificar' } }}</span>
                            <span class="text-xs text-slate-400">{{ $intent->total }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Ideas relacionadas</h3>
                <div class="space-y-3">
                    @forelse ($keywordOpportunities->take(5) as $opportunity)
                        <div>
                            <p class="text-sm font-medium text-primary">{{ $opportunity['keyword'] }}</p>
                            <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                <span>Pos {{ $opportunity['avg_position'] }}</span>
                                <span>CTR {{ $opportunity['ctr'] }}%</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $opportunity['hint'] }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sin oportunidades detectadas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Palabra Clave</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Intent</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Clics</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Impresiones</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Pos. Media</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">KD %</th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Qué hacer</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($topKeywords as $keyword)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-slate-900">{{ $keyword->keyword }}</span>
                                        <span class="text-[10px] uppercase text-slate-400">{{ $keyword->country_code }} · {{ $keyword->language_code }} · {{ $keyword->device }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $keyword->intent_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($keyword->clicks) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($keyword->impressions) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($keyword->avg_position, 1) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-medium {{ $keyword->difficulty > 70 ? 'text-rose-600' : ($keyword->difficulty > 40 ? 'text-amber-500' : 'text-success') }}">
                                        {{ $keyword->difficulty }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-xs text-slate-600">{{ $keyword->action }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">No hay keywords sincronizadas todavía.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
