<section class="mb-6 rounded-2xl border border-primary/15 bg-white shadow-sm">
    <div class="flex flex-col gap-4 px-5 py-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-start gap-4">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary/10 text-primary">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.28em] text-primary">360 Pulse · que hacer hoy</p>
                <p class="mt-1 text-base text-slate-800">
                    @if ($quickWins->isNotEmpty())
                        {{ $quickWins->count() }} keywords en pagina dos pueden convertirse en victorias rapidas <span class="font-semibold text-emerald-600">si las empujas hoy.</span>
                    @elseif ($latestCrawlIssues->isNotEmpty())
                        El crawler detecto {{ $latestCrawlIssues->count() }} hallazgos recientes <span class="font-semibold text-emerald-600">que conviene resolver antes del siguiente snapshot.</span>
                    @else
                        Tu workspace ya tiene la base lista. El siguiente paso es completar tracking, auditoria y conexiones.
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('workspace.opportunities') }}" class="rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-primary/90">Ver lista</a>
        </div>
    </div>
</section>
