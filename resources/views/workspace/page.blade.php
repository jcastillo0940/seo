@php
    $workspacePages = [
        'summary' => ['label' => 'Resumen', 'route' => 'workspace.summary'],
        'deep-scan' => ['label' => 'Deep Scan', 'route' => 'workspace.deep-scan'],
        'keyword-hunter' => ['label' => 'Keyword Hunter', 'route' => 'workspace.keyword-hunter'],
        'serp-tracking' => ['label' => 'SERP Tracking', 'route' => 'workspace.serp-tracking'],
        'competitors' => ['label' => 'Competidores', 'route' => 'workspace.competitors'],
        'connections' => ['label' => 'Conexiones', 'route' => 'workspace.connections'],
        'opportunities' => ['label' => 'Oportunidades', 'route' => 'workspace.opportunities'],
        'audit' => ['label' => 'Auditoria', 'route' => 'workspace.audit'],
    ];
    $currentPage = $workspacePages[$page] ?? $workspacePages['summary'];
    $latestSync = $project?->last_synced_at?->diffForHumans() ?? 'never';
@endphp

<x-layouts.app>
    <div class="flex min-h-screen overflow-hidden bg-brandbg text-slate-800 font-sans" x-data="{ sidebarOpen: false }">
        <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/40 md:hidden"></div>

        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col border-r border-slate-200 bg-white shadow-[8px_0_30px_rgba(15,23,42,0.04)] transition-transform md:relative md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center justify-between border-b border-slate-100 px-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary text-sm font-bold text-white">O</div>
                    <div>
                        <p class="text-lg font-bold tracking-wide text-primary">360·SEO</p>
                    </div>
                </div>
                <button class="text-slate-400 md:hidden" @click="sidebarOpen = false">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                <section>
                    <p class="px-3 text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Vistas</p>
                    <nav class="mt-3 space-y-1">
                        @foreach ($workspacePages as $key => $workspacePage)
                            <a
                                href="{{ route($workspacePage['route']) }}"
                                class="flex items-center justify-between rounded-xl px-3 py-2.5 text-sm font-medium transition"
                                @class([
                                    'bg-primary/8 text-primary' => $page === $key,
                                    'text-slate-600 hover:bg-slate-50 hover:text-slate-900' => $page !== $key,
                                ])
                            >
                                <span>{{ $workspacePage['label'] }}</span>
                                <span class="rounded-md border border-slate-200 px-1.5 py-0.5 text-[10px] text-slate-400">
                                    {{ strtoupper(substr($workspacePage['label'], 0, 1)) }}
                                </span>
                            </a>
                        @endforeach
                    </nav>
                </section>

                <section>
                    <p class="px-3 text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Workspace</p>
                    <div class="mt-3 rounded-2xl bg-slate-50 px-4 py-4">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-success"></span>
                            <p class="text-sm font-semibold text-slate-800">{{ $projectDomain }}</p>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">{{ $project?->name ?: 'Sin proyecto conectado' }}</p>
                    </div>
                </section>
            </div>

            <div class="border-t border-slate-100 p-4">
                <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-3">
                    <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                    <p class="mt-1 text-[11px] uppercase tracking-[0.22em] text-slate-400">admin</p>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">Cerrar sesion</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center justify-between gap-4 px-4 md:px-6">
                    <div class="flex min-w-0 items-center gap-3">
                        <button class="text-slate-500 md:hidden" @click="sidebarOpen = true">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="min-w-0">
                            <div class="flex items-center gap-3 text-sm">
                                <span class="font-bold text-slate-900">{{ $currentPage['label'] }}</span>
                                <span class="text-slate-300">/</span>
                                <span class="truncate text-slate-400">{{ $projectDomain }}</span>
                                <span class="hidden text-slate-300 lg:inline">/</span>
                                <span class="hidden text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 lg:inline">MX · es-MX</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 md:gap-3">
                        <div class="hidden items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-500 sm:flex">
                            <span class="h-2 w-2 rounded-full bg-success"></span>
                            <span class="font-semibold text-slate-700">live</span>
                            <span>- last sync {{ $latestSync }}</span>
                        </div>
                        <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">Dashboard</a>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-6 lg:p-7">
                <div class="mx-auto max-w-[1440px]">
                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-success/20 bg-success/10 px-5 py-4 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @include('workspace.partials.pulse-banner')

                    @if (! $project)
                        @include('workspace.partials.project-setup')
                    @else
                        @includeIf('workspace.pages.'.$page)
                    @endif
                </div>
            </main>
        </div>
    </div>
</x-layouts.app>
