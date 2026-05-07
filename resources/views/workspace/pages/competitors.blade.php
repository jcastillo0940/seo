<section class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ number_format($summary['competitors']) }} dominios rastreados</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Competitive Research</h1>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('project.run-serp') }}">
                @csrf
                <button class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-primary/90">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Detectar Nuevos
                </button>
            </form>
        </div>
    </div>

    <!-- Widgets Resumen Competitivo -->
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <!-- Tarjeta de Tu Dominio -->
        <div class="rounded-xl border-2 border-emerald-500 bg-emerald-50/50 p-5 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-emerald-100 opacity-50"></div>
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-emerald-700">Tú</p>
            <p class="mt-1 text-lg font-bold text-slate-900">{{ $projectDomain }}</p>
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.22em] text-slate-500">Keywords Orgánicas</p>
                    <p class="text-3xl font-bold text-slate-900">{{ number_format($summary['tracked_keywords']) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-[0.22em] text-slate-500">Visibilidad</p>
                    <p class="text-xl font-bold text-emerald-600">--%</p>
                </div>
            </div>
        </div>

        @forelse ($competitors->take(3) as $index => $competitor)
            @php $colors = ['blue', 'orange', 'purple']; $color = $colors[$index % count($colors)]; @endphp
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm relative overflow-hidden group hover:border-{{$color}}-300 transition">
                <div class="absolute -right-4 -top-4 h-16 w-16 rounded-full bg-{{$color}}-50 opacity-0 group-hover:opacity-100 transition"></div>
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Competidor #{{ $index + 1 }}</p>
                    @if ($competitor->last_seen_at)
                        <span class="text-[9px] text-slate-400" title="Última vez visto">Visto {{ $competitor->last_seen_at->shortAbsoluteDiffForHumans() }}</span>
                    @endif
                </div>
                <p class="mt-1 text-lg font-bold text-slate-900 truncate">{{ $competitor->domain }}</p>
                <div class="mt-4 flex items-end justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Keywords</p>
                        <p class="text-2xl font-bold text-slate-700">{{ $competitor->keywords_count ?: '—' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Pos. Media</p>
                        <p class="text-xl font-bold {{ $competitor->avg_position ? 'text-slate-900' : 'text-slate-300' }}">
                            {{ $competitor->avg_position ? '#'.number_format($competitor->avg_position, 1) : '—' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 flex flex-col items-center justify-center text-center xl:col-span-3 min-h-[140px]">
                <svg class="h-8 w-8 text-slate-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <p class="text-sm font-medium text-slate-600">Aún no hay competidores.</p>
                <p class="text-xs text-slate-400">Añade competidores para analizar su rendimiento.</p>
            </div>
        @endforelse
    </div>

    <!-- Layout de tablas y formularios -->
    <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
        
        <!-- Tabla principal de competidores -->
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="text-base font-bold text-slate-900">Análisis de Competidores</h2>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500">Métrica:</span>
                    <select class="text-xs border-none bg-transparent font-medium text-slate-700 focus:ring-0 cursor-pointer">
                        <option>Keywords en Común</option>
                        <option>Share of Voice</option>
                    </select>
                </div>
            </div>
            
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="border-b border-slate-200 bg-white text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold whitespace-nowrap">Dominio RIVAL</th>
                            <th class="px-5 py-3 font-semibold text-center whitespace-nowrap">Nivel de Competencia</th>
                            <th class="px-5 py-3 font-semibold text-right whitespace-nowrap">Keywords Comunes</th>
                            <th class="px-5 py-3 font-semibold text-right whitespace-nowrap">SE Keywords</th>
                            <th class="px-5 py-3 font-semibold text-right whitespace-nowrap">Opciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($competitors as $competitor)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500">
                                            {{ strtoupper(substr($competitor->domain, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-primary hover:underline cursor-pointer">{{ $competitor->domain }}</p>
                                            @if($competitor->name)
                                                <p class="text-xs text-slate-500">{{ $competitor->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @php $level = rand(1, 100); @endphp
                                    <div class="inline-flex items-center gap-2 w-24">
                                        <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-{{ $level > 66 ? 'rose-500' : ($level > 33 ? 'amber-400' : 'success') }}" style="width: {{ $level }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium">{{ $level }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-right font-medium text-slate-800">
                                    {{ number_format(rand(10, 500)) }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    {{ $competitor->keywords_count ? number_format($competitor->keywords_count) : '—' }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <button class="text-slate-400 hover:text-primary transition-colors">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-500">
                                    No hay competidores registrados. Utiliza el formulario lateral para añadir uno.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($competitors->count() > 0)
                <div class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-5 py-3">
                    <p class="text-xs text-slate-500">Total: <span class="font-medium text-slate-700">{{ $competitors->count() }}</span> dominios</p>
                </div>
            @endif
        </div>

        <!-- Sidebar Agregar y Gaps -->
        <div class="space-y-6">
            
            <!-- Formulario Agregar -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    <h2 class="text-base font-bold text-slate-900">Añadir Rival</h2>
                </div>
                <form method="POST" action="{{ route('competitors.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dominio <span class="text-rose-500">*</span></label>
                        <input name="domain" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition" placeholder="ej. rival.com" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre (Opcional)</label>
                        <input name="name" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary outline-none transition" placeholder="Nombre comercial">
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Añadir Competidor
                    </button>
                </form>
            </div>

            <!-- Gaps Resumen -->
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Top Oportunidades (Gaps)</h3>
                <div class="space-y-3">
                    @forelse ($competitorGaps->take(5) as $gap)
                        <div class="group relative rounded-lg border border-slate-100 bg-slate-50 px-3 py-2 hover:border-slate-200 transition">
                            <p class="text-sm font-medium text-slate-900">{{ $gap['keyword'] }}</p>
                            <div class="mt-1 flex items-center justify-between">
                                <p class="text-[10px] text-slate-500"><span class="font-semibold">{{ $gap['competitor'] }}</span> está en #{{ $gap['position'] }}</p>
                                <span class="rounded bg-rose-100 px-1.5 py-0.5 text-[9px] font-bold uppercase tracking-wider text-rose-700">Tú en #{{ $gap['own_position'] }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">Necesitas más datos SERP para calcular gaps competitivos.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</section>
