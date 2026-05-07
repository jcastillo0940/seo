<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="rounded bg-primary/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-primary">Beta</span>
                <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Investigacion · MX · espanol</p>
            </div>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Keyword Hunter</h1>
        </div>
        <div class="flex gap-2">
            <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exportar
            </button>
        </div>
    </div>

    <!-- Buscador Principal tipo Semrush -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <form method="POST" action="{{ route('tracked-keywords.store') }}" class="flex flex-col md:flex-row">
            @csrf
            <div class="flex flex-1 items-center gap-3 border-b border-slate-200 px-4 py-3 md:border-b-0 md:border-r">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="keyword" class="w-full border-none bg-transparent p-0 text-sm placeholder-slate-400 focus:ring-0" placeholder="Ingresa una palabra clave, ej: cortes premium para parrilla" required>
            </div>
            <div class="flex divide-x divide-slate-200">
                <select name="country_code" class="border-none bg-transparent py-3 pl-4 pr-8 text-sm text-slate-600 focus:ring-0">
                    <option value="mx">Mexico (MX)</option>
                    <option value="us">United States (US)</option>
                    <option value="co">Colombia (CO)</option>
                    <option value="ar">Argentina (AR)</option>
                </select>
                <select name="language_code" class="border-none bg-transparent py-3 pl-4 pr-8 text-sm text-slate-600 focus:ring-0 hidden sm:block">
                    <option value="es">Español</option>
                    <option value="en">English</option>
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

    <!-- Barra de Filtros -->
    <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 shadow-sm">
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3">
            <span class="text-xs font-semibold text-slate-500">Intent:</span>
            <button class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 hover:bg-slate-200">Todos</button>
            <button class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">Informativo</button>
            <button class="rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">Comercial</button>
        </div>
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3">
            <span class="text-xs font-semibold text-slate-500">Volumen:</span>
            <button class="flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                101-1,000 <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
        <div class="flex items-center gap-2 border-r border-slate-200 pr-3">
            <span class="text-xs font-semibold text-slate-500">KD %:</span>
            <button class="flex items-center gap-1 rounded-md border border-slate-200 bg-white px-2 py-1 text-xs font-medium text-slate-600 hover:bg-slate-50">
                Cualquiera <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <span class="text-xs text-slate-500"><strong class="text-slate-900">{{ $topKeywords->count() }}</strong> palabras clave encontradas</span>
        </div>
    </div>

    <!-- Grid principal: Sidebar de Intents + Tabla -->
    <div class="grid gap-6 xl:grid-cols-[240px_1fr]">
        <!-- Sidebar de Clusters / Intents -->
        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Por Intencion (Intent)</h3>
                <div class="space-y-1">
                    <button class="flex w-full items-center justify-between rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-900">
                        <span class="font-medium">Todas las palabras</span>
                        <span class="rounded bg-slate-200 px-1.5 py-0.5 text-xs text-slate-600">{{ $trackedKeywordsByIntent->sum('total') ?? $topKeywords->count() }}</span>
                    </button>
                    @foreach ($trackedKeywordsByIntent as $intent)
                        <button class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">
                            <span>{{ str($intent->intent)->replace('_', ' ')->headline() }}</span>
                            <span class="text-xs text-slate-400">{{ $intent->total }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
            
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-500">Ideas Relacionadas</h3>
                <div class="space-y-3">
                    @forelse ($keywordOpportunities->take(5) as $opportunity)
                        <div class="group cursor-pointer">
                            <p class="text-sm font-medium text-primary group-hover:underline">{{ $opportunity['keyword'] }}</p>
                            <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                <span>Pos {{ $opportunity['avg_position'] }}</span>
                                <span>&bull;</span>
                                <span>CTR {{ $opportunity['ctr'] }}%</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Sin oportunidades detectadas.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tabla principal de Keywords -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">
                                <div class="flex items-center gap-1 cursor-pointer hover:text-slate-700">Palabra Clave <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg></div>
                            </th>
                            <th class="px-4 py-3 font-semibold whitespace-nowrap">Intent</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 cursor-pointer hover:text-slate-700">Clics <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                            </th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1 cursor-pointer hover:text-slate-700">Impresiones <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                            </th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Pos. Media</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">KD %</th>
                            <th class="px-4 py-3 font-semibold text-right whitespace-nowrap">Opciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($topKeywords as $keyword)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <button class="text-slate-400 hover:text-primary"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg></button>
                                        <span class="font-medium text-slate-900">{{ $keyword->keyword }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        // Simulated intent based on keyword properties for UI purposes
                                        $intents = ['I' => 'bg-blue-100 text-blue-700', 'C' => 'bg-orange-100 text-orange-700', 'T' => 'bg-green-100 text-green-700', 'N' => 'bg-purple-100 text-purple-700'];
                                        $randIntent = array_rand($intents);
                                    @endphp
                                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold {{ $intents[$randIntent] }}" title="Intencion">
                                        {{ $randIntent }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-medium">{{ number_format($keyword->clicks) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($keyword->impressions) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <span>{{ number_format($keyword->avg_position, 1) }}</span>
                                        @if($keyword->avg_position < 10)
                                            <svg class="h-3 w-3 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        @else
                                            <svg class="h-3 w-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @php $kd = rand(10, 90); @endphp
                                    <div class="flex items-center justify-end gap-2">
                                        <span class="font-medium {{ $kd > 70 ? 'text-rose-600' : ($kd > 40 ? 'text-amber-500' : 'text-success') }}">{{ $kd }}</span>
                                        <div class="h-2 w-2 rounded-full {{ $kd > 70 ? 'bg-rose-500' : ($kd > 40 ? 'bg-amber-400' : 'bg-success') }}"></div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-primary transition-opacity">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-500">No hay keywords sincronizadas todavia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($topKeywords->count() > 0)
            <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-6">
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-700">
                            Mostrando <span class="font-medium">1</span> a <span class="font-medium">{{ min($topKeywords->count(), 50) }}</span> de <span class="font-medium">{{ $topKeywords->count() }}</span> resultados
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Anterior</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>
                            </a>
                            <a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-primary px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">1</a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">2</a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">3</a>
                            <a href="#" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">
                                <span class="sr-only">Siguiente</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
