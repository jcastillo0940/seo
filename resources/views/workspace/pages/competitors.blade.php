<section class="space-y-6">
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">{{ number_format($summary['competitors']) }} dominios rastreados</p>
            <h1 class="mt-2 text-3xl font-bold text-slate-900">Competidores</h1>
        </div>
        <form method="POST" action="{{ route('project.run-serp') }}">
            @csrf
            <button class="rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Detectar nuevos</button>
        </form>
    </div>

    <div class="grid gap-4 xl:grid-cols-5">
        <div class="rounded-2xl border border-success bg-emerald-50 p-5 shadow-sm xl:col-span-1">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-emerald-600">YOU</p>
            <p class="mt-2 text-lg font-bold text-slate-900">{{ $projectDomain }}</p>
            <div class="mt-5 flex items-end justify-between">
                <div><p class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Keywords</p><p class="mt-1 text-4xl font-bold text-slate-900">{{ number_format($summary['tracked_keywords']) }}</p></div>
                <div class="text-right"><p class="text-[10px] uppercase tracking-[0.22em] text-slate-400">SoV</p><p class="mt-1 text-3xl font-bold text-slate-900">--%</p></div>
            </div>
        </div>
        @forelse ($competitors->take(4) as $competitor)
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">RIVAL</p>
                <p class="mt-2 text-lg font-bold text-slate-900">{{ $competitor->domain }}</p>
                <div class="mt-5 flex items-end justify-between">
                    <div><p class="text-[10px] uppercase tracking-[0.22em] text-slate-400">Nombre</p><p class="mt-1 text-sm font-semibold text-slate-800">{{ $competitor->name }}</p></div>
                    <div class="text-right"><p class="text-[10px] uppercase tracking-[0.22em] text-slate-400">SoV</p><p class="mt-1 text-3xl font-bold text-slate-900">--%</p></div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-5 text-sm text-slate-500 xl:col-span-4">Todavia no has agregado competidores.</div>
        @endforelse
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Share of voice por cluster</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Quien te roba share of voice</h2>
            <div class="mt-5 space-y-4">
                @forelse ($competitorGaps->take(6) as $gap)
                    <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $gap['keyword'] }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $gap['competitor'] }} en #{{ $gap['position'] }} · tu dominio en #{{ $gap['own_position'] }}</p>
                            </div>
                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">gap {{ $gap['gap'] }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Aun no hay suficiente historial SERP para calcular brechas.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-[10px] font-bold uppercase tracking-[0.24em] text-slate-400">Agregar dominio</p>
            <h2 class="mt-1 text-lg font-bold text-slate-900">Nuevo rival</h2>
            <form method="POST" action="{{ route('competitors.store') }}" class="mt-5 space-y-3">
                @csrf
                <input name="domain" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="competidor.com" required>
                <input name="name" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="Nombre visible">
                <input name="notes" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm" placeholder="Notas">
                <button class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-primary/90">Guardar competidor</button>
            </form>
        </div>
    </div>
</section>
