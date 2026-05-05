<x-layouts.app>
    @php
        $hasProject = (bool) $project;
        $hasKeywordData = $summary['keywords'] > 0;
        $hasTrackedKeywords = $summary['tracked_keywords'] > 0;
        $hasCompetitors = $summary['competitors'] > 0;
        $hasCatalog = $summary['catalog_pages'] > 0;
        $hasOrganicPages = $summary['organic_pages'] > 0;
        $hasSerpSnapshot = $latestSerpSnapshot !== null;
        $hasCrawl = $latestCrawlRun !== null;
        
        $menuItems = [
            ['id' => 'overview', 'label' => 'Resumen', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'],
            ['id' => 'deep_scan', 'label' => 'Deep Scan', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>'],
            ['id' => 'keyword_hunter', 'label' => 'Keyword Hunter', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/></svg>'],
            ['id' => 'serp_tracking', 'label' => 'SERP Tracking', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>'],
            ['id' => 'competitors', 'label' => 'Competidores', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'],
            ['id' => 'connections', 'label' => 'Conexiones', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'],
            ['id' => 'opportunities', 'label' => 'Oportunidades', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>'],
            ['id' => 'audit', 'label' => 'Auditoria', 'icon' => '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>']
        ];
    @endphp

    <div
        class="flex h-screen overflow-hidden bg-brandbg text-slate-800 font-sans"
        x-data='{
            activeSection: "competitors",
            sidebarOpen: false,
            sectionLabels: {
                overview: "Resumen",
                deep_scan: "Deep Scan",
                keyword_hunter: "Keyword Hunter",
                serp_tracking: "SERP Tracking",
                competitors: "Competidores",
                connections: "Conexiones",
                opportunities: "Oportunidades",
                audit: "Auditoria"
            }
        }'
    >
        
        <!-- Overlay for mobile sidebar -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-900/50 md:hidden" x-cloak></div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 transform bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex-shrink-0 flex flex-col shadow-[4px_0_24px_rgba(0,0,0,0.02)]"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        O
                    </div>
                    <span class="font-bold text-primary tracking-wide text-lg">360<span class="text-slate-400 font-normal">&middot;</span>SEO</span>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar space-y-6">
                <!-- Vistas -->
                <div>
                    <h3 class="px-3 text-xs font-bold text-slate-400 tracking-wider mb-2 uppercase">Vistas</h3>
                    <nav class="space-y-0.5">
                        @foreach($menuItems as $item)
                        <button 
                            @click="activeSection = '{{ $item['id'] }}'; sidebarOpen = false;"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                            :class="activeSection === '{{ $item['id'] }}' ? 'bg-primary/5 text-primary' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            <div class="flex items-center gap-3">
                                <div :class="activeSection === '{{ $item['id'] }}' ? 'text-primary' : 'text-slate-400'">
                                    {!! $item['icon'] !!}
                                </div>
                                <span>{{ $item['label'] }}</span>
                            </div>
                        </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Workspaces -->
                <div>
                    <h3 class="px-3 text-xs font-bold text-slate-400 tracking-wider mb-2 uppercase">Workspaces</h3>
                    <nav class="space-y-0.5">
                        @if($project)
                            <button class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-primary/5 text-primary transition-colors">
                                <div class="w-1.5 h-1.5 rounded-full bg-success mr-3"></div>
                                <span class="truncate">{{ $project->name }}</span>
                            </button>
                        @else
                            <button class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-lg text-slate-400 bg-slate-50 border border-dashed border-slate-200">
                                <span class="truncate">Sin proyecto activo</span>
                            </button>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- User profile -->
            <div class="p-4 border-t border-slate-100 shrink-0">
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/50 p-2 shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center text-xs font-bold shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                        <p class="text-[10px] text-slate-500 uppercase tracking-wider truncate">{{ $project ? $project->name : 'Dashboard' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button class="w-full py-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">Cerrar sesión</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-brandbg relative">
            
            <!-- Topbar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 md:px-6 shrink-0 z-10 shadow-[0_4px_24px_rgba(0,0,0,0.02)]">
                <div class="flex items-center gap-3 md:gap-4">
                    <button @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-slate-800 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="text-sm hidden sm:flex items-center gap-3">
                        <span class="font-bold text-slate-800" x-text="sectionLabels[activeSection] || 'Resumen'"></span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-400 max-w-[150px] truncate">{{ $project ? ($project->domain ?: $project->name) : 'Sin proyecto' }}</span>
                        <span class="text-slate-300 hidden lg:inline">/</span>
                        <span class="text-slate-400 uppercase tracking-wider text-xs font-semibold hidden lg:inline">MX · es-MX</span>
                    </div>
                    <div class="text-sm sm:hidden font-bold text-slate-800" x-text="sectionLabels[activeSection] || 'Resumen'"></div>
                </div>
                
                <div class="flex items-center gap-2 md:gap-4">
                    <div class="relative hidden lg:block">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" placeholder="Saltar a vista o keyword..." class="pl-9 pr-12 py-1.5 w-64 xl:w-72 rounded-lg border border-slate-200 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        <div class="absolute right-2 top-1/2 -translate-y-1/2 flex gap-1">
                            <kbd class="hidden xl:inline-block border border-slate-200 rounded px-1.5 py-0.5 text-[10px] font-sans text-slate-400 bg-white shadow-sm">⌘</kbd>
                            <kbd class="hidden xl:inline-block border border-slate-200 rounded px-1.5 py-0.5 text-[10px] font-sans text-slate-400 bg-white shadow-sm">K</kbd>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 shadow-sm">
                        <div class="w-2 h-2 rounded-full bg-success animate-pulse shrink-0"></div>
                        <span class="text-xs font-semibold text-slate-600 hidden sm:inline">live</span>
                        <span class="text-[10px] text-slate-400 hidden sm:inline">- last sync {{ $project && $project->last_synced_at ? $project->last_synced_at->shortAbsoluteDiffForHumans() : 'never' }}</span>
                    </div>

                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors hidden sm:flex">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </button>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 custom-scrollbar">
                <div class="max-w-[1400px] mx-auto">
                    
                    @if (!$project)
                        <!-- Setup del Proyecto -->
                        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm mb-6">
                            <h2 class="text-xl font-bold text-slate-800">Conecta tu propiedad de Search Console</h2>
                            <p class="mt-2 text-sm text-slate-500">Selecciona tu propiedad para desbloquear todo el panel y ver el rendimiento real.</p>
                            @if (isset($properties) && count($properties) > 0)
                                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($properties as $property)
                                        <form method="POST" action="{{ route('projects.store') }}" class="rounded-xl border border-slate-200 bg-slate-50 p-4 hover:border-primary/30 hover:shadow-sm transition">
                                            @csrf
                                            <input type="hidden" name="property_id" value="{{ $property['property_id'] }}">
                                            <input type="hidden" name="name" value="{{ $property['name'] }}">
                                            <input type="hidden" name="url" value="{{ $property['url'] }}">
                                            <p class="font-bold text-slate-800">{{ $property['name'] }}</p>
                                            <p class="mt-1 text-xs text-slate-500 truncate">{{ $property['url'] }}</p>
                                            <button class="mt-4 w-full rounded-lg bg-white border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition">Usar propiedad</button>
                                        </form>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- VISTA COMPETIDORES -->
                    <div x-show="activeSection === 'competitors'" x-cloak>
                        
                        <!-- Pulse Banner -->
                        <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center justify-between rounded-xl border border-primary/20 bg-gradient-to-r from-primary/5 to-white p-4 shadow-sm relative overflow-hidden gap-4">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                            <div class="flex items-start gap-4">
                                <div class="mt-1 flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-primary">360 Pulse · Sugerencia AI</p>
                                    <p class="mt-0.5 text-sm font-medium text-slate-800">Tienes 3 keywords en página 2 donde la competencia ha perdido fuerza — <span class="font-bold text-success">Optimízalas hoy.</span></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 self-end sm:self-auto shrink-0">
                                <button class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">Ignorar</button>
                                <button class="rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-primary/90 transition">Ver oportunidades &rarr;</button>
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ isset($competitors) ? $competitors->count() : '0' }} DOMINIOS RASTREADOS</p>
                                <h1 class="mt-1 text-2xl md:text-3xl font-bold text-slate-900">Competidores</h1>
                            </div>
                            <div class="flex flex-wrap gap-2 md:gap-3">
                                <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 md:px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                    <span class="hidden sm:inline">Agregar dominio</span>
                                    <span class="sm:hidden">Agregar</span>
                                </button>
                                <form method="POST" action="{{ route('project.run-serp') }}" class="inline">
                                    @csrf
                                    <button class="flex items-center gap-2 rounded-lg bg-primary px-3 md:px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-primary/90 transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span class="hidden sm:inline">Sincronizar datos</span>
                                        <span class="sm:hidden">Sync</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Cards Grid (Datos Reales) -->
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 mb-8">
                            
                            <!-- YOU Card -->
                            <div class="rounded-xl border border-success bg-success/5 p-4 shadow-sm relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-success/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-success shadow-[0_0_8px_rgba(0,194,127,0.8)]"></div>
                                    <span class="text-[10px] font-bold text-success tracking-widest">YOU</span>
                                </div>
                                <p class="font-bold text-slate-800 text-sm truncate">{{ $project ? ($project->domain ?: $project->name) : 'Tu Dominio' }}</p>
                                <div class="mt-4 flex items-end justify-between">
                                    <div>
                                        <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">Keywords TOP 10</p>
                                        <p class="text-3xl font-bold text-slate-800 mt-1">{{ number_format($summary['tracked_keywords'] ?? 0) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] uppercase text-slate-500 font-bold tracking-wider">SoV</p>
                                        <p class="text-2xl font-bold text-slate-800 mt-1">--%</p> <!-- Reemplazar con variable SoV cuando exista -->
                                    </div>
                                </div>
                            </div>

                            <!-- Rivals Cards Loop -->
                            @if(isset($competitors) && $competitors->count() > 0)
                                @php
                                    $colors = ['bg-primary text-primary border-primary/20', 'bg-rival3 text-rival3 border-rival3/20', 'bg-rival2 text-rival2 border-rival2/20', 'bg-rival1 text-rival1 border-rival1/20', 'bg-rival4 text-rival4 border-rival4/20'];
                                @endphp
                                @foreach($competitors->take(5) as $index => $competitor)
                                    @php
                                        $colorSet = $colors[$index % count($colors)];
                                        $bgClass = explode(' ', $colorSet)[0];
                                        $textClass = explode(' ', $colorSet)[1];
                                    @endphp
                                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="w-2 h-2 rounded-full {{ $bgClass }}"></div>
                                            <span class="text-[10px] font-bold text-slate-400 tracking-widest">RIVAL</span>
                                        </div>
                                        <p class="font-bold text-slate-800 text-sm truncate" title="{{ $competitor->domain ?? $competitor->name }}">{{ $competitor->domain ?? $competitor->name }}</p>
                                        <div class="mt-4 flex items-end justify-between">
                                            <div>
                                                <p class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">Notas</p>
                                                <p class="text-xs font-medium text-slate-600 mt-1 truncate max-w-[100px]">{{ $competitor->notes ?: '--' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] uppercase text-slate-400 font-bold tracking-wider">SoV Estimado</p>
                                                <p class="text-2xl font-bold text-slate-800 mt-1">--%</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 flex flex-col items-center justify-center text-center">
                                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-slate-300 mb-3 shadow-sm border border-slate-100">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-700">Sin competidores aún</h3>
                                    <p class="text-xs text-slate-500 mt-1">Agrega dominios rivales para comparar posiciones.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Competitor Gap (Datos Reales) -->
                        <div class="rounded-xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">ANÁLISIS DE BRECHAS</p>
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 mt-1">
                                <h2 class="text-lg font-bold text-slate-900">Competitor Gap</h2>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">{{ isset($competitorGaps) ? $competitorGaps->count() : '0' }} brechas</span>
                            </div>
                            
                            @if(isset($competitorGaps) && $competitorGaps->count() > 0)
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($competitorGaps as $gap)
                                        <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 hover:bg-slate-100 transition">
                                            <p class="font-bold text-slate-800">{{ $gap['keyword'] }}</p>
                                            <p class="mt-2 text-xs text-slate-500 flex justify-between items-center">
                                                <span>{{ $gap['competitor'] }}: <strong class="text-slate-800">#{{ $gap['position'] }}</strong></span>
                                                <span class="text-slate-300">|</span>
                                                <span>Tú: <strong class="text-slate-800">#{{ $gap['own_position'] }}</strong></span>
                                            </p>
                                            <div class="mt-3 inline-flex items-center rounded bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-600 border border-amber-200/50">
                                                Brecha: {{ $gap['gap'] }} pos.
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-sm text-slate-500">Aún no hay suficiente historial SERP para calcular brechas con tus competidores.</p>
                                </div>
                            @endif
                        </div>

                    </div>
                    
                    <!-- VISTA RESUMEN -->
                    <div x-show="activeSection === 'overview'" x-cloak>
                        
                        <div class="grid lg:grid-cols-3 gap-6 mb-6">
                            <!-- Quick Stats -->
                            <div class="lg:col-span-3 grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Clicks</p>
                                    <p class="text-2xl md:text-3xl font-bold text-slate-800 mt-2">{{ number_format($summary['clicks'] ?? 0) }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Impresiones</p>
                                    <p class="text-2xl md:text-3xl font-bold text-slate-800 mt-2">{{ number_format($summary['impressions'] ?? 0) }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Keywords Activas</p>
                                    <p class="text-2xl md:text-3xl font-bold text-slate-800 mt-2">{{ number_format($summary['keywords'] ?? 0) }}</p>
                                </div>
                                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col justify-between">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Páginas Index.</p>
                                    <p class="text-2xl md:text-3xl font-bold text-slate-800 mt-2">{{ number_format($summary['organic_pages'] ?? 0) }}</p>
                                </div>
                            </div>

                            <!-- Visibility Trend Chart (Reemplazo Radar) -->
                            <div class="lg:col-span-2 rounded-xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm min-h-[300px] flex flex-col">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Evolución SEO</p>
                                        <h2 class="mt-1 text-base font-bold text-slate-900">Tráfico e Impresiones de los últimos 30 días</h2>
                                    </div>
                                </div>
                                <div class="relative flex-1 w-full mt-2 h-64">
                                    <canvas id="seoTrendChart" class="w-full h-full"></canvas>
                                </div>
                            </div>

                            <!-- Oportunidades Rápidas (Wins) -->
                            <div class="rounded-xl border border-slate-200 bg-white p-0 shadow-sm flex flex-col">
                                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-xl">
                                    <span class="text-xs font-bold text-slate-600 uppercase tracking-wide">Quick Wins (Pos 11-20)</span>
                                </div>
                                <div class="flex-1 overflow-auto max-h-[320px]">
                                    @if(isset($quickWins) && $quickWins->count() > 0)
                                        @foreach($quickWins->take(5) as $win)
                                            <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition flex justify-between items-center">
                                                <div class="overflow-hidden pr-2">
                                                    <p class="text-sm font-semibold text-slate-800 truncate" title="{{ $win->keyword }}">{{ $win->keyword }}</p>
                                                    <p class="text-[10px] text-slate-400 mt-0.5 uppercase font-bold">Pos {{ number_format($win->avg_position, 1) }}</p>
                                                </div>
                                                <div class="text-right shrink-0">
                                                    <p class="text-sm font-bold text-primary">{{ number_format($win->impressions) }} imp</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="p-6 text-center text-sm text-slate-400">No hay quick wins detectados por ahora.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- SERP Movements (Real Data) -->
                        <div class="rounded-xl border border-slate-200 bg-white p-0 shadow-sm flex flex-col mb-8">
                            <div class="p-4 md:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">SNAPSHOT SERP</p>
                                    <h2 class="mt-1 text-sm font-bold text-slate-900">
                                        @if($latestSerpSnapshot)
                                            Últimos resultados para: <span class="text-primary">{{ $latestSerpSnapshot->trackedKeyword->keyword ?? 'Keyword' }}</span>
                                        @else
                                            Últimos movimientos SERP
                                        @endif
                                    </h2>
                                </div>
                                <form method="POST" action="{{ route('project.run-serp') }}">
                                    @csrf
                                    <button class="text-xs font-bold bg-slate-50 hover:bg-slate-100 text-slate-700 py-1.5 px-3 rounded-lg border border-slate-200 transition">Correr Snapshot &rarr;</button>
                                </form>
                            </div>
                            <div class="flex-1 overflow-auto p-2">
                                @if($latestSerpSnapshot && $latestSerpSnapshot->results->isNotEmpty())
                                    @foreach($latestSerpSnapshot->results->sortBy('position')->take(10) as $result)
                                        <div class="p-3 flex flex-col sm:flex-row sm:justify-between sm:items-center hover:bg-slate-50 rounded-lg transition border-t border-slate-50 gap-2">
                                            <div class="flex-1 overflow-hidden pr-4">
                                                <p class="text-sm font-semibold text-slate-800 truncate" title="{{ $result->title ?: $result->domain }}">{{ $result->title ?: $result->domain }}</p>
                                                <p class="text-xs text-slate-400 truncate">{{ $result->url ?? $result->domain }}</p>
                                            </div>
                                            <div class="flex items-center gap-4 shrink-0">
                                                @if($result->is_own_domain)
                                                    <span class="inline-flex rounded-full bg-success/10 px-2 py-0.5 text-[10px] font-bold text-success border border-success/20">Tu Dominio</span>
                                                @endif
                                                <span class="text-lg font-bold {{ $result->is_own_domain ? 'text-success' : 'text-slate-700' }} w-8 text-right">#{{ $result->position }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="p-6 text-center text-sm text-slate-500">
                                        Aún no hay snapshots SERP recientes. Dale clic a "Correr Snapshot" para capturar la foto de la primera página de Google.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-6 lg:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">KEYWORDS TRACKING</p>
                                        <h2 class="mt-1 text-base font-bold text-slate-900">Keywords objetivo</h2>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        {{ isset($trackedKeywords) ? $trackedKeywords->count() : 0 }}
                                    </span>
                                </div>
                                <div class="mt-4 space-y-3">
                                    @forelse(($trackedKeywords ?? collect())->take(4) as $trackedKeyword)
                                        <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-3">
                                            <p class="text-sm font-semibold text-slate-800">{{ $trackedKeyword->keyword }}</p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $trackedKeyword->country_code }} · {{ $trackedKeyword->language_code }} · {{ $trackedKeyword->device }}
                                            </p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">Todavia no hay keywords objetivo cargadas.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">MAGENTO</p>
                                        <h2 class="mt-1 text-base font-bold text-slate-900">Catalogo Magento</h2>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        {{ isset($topCatalogPages) ? $topCatalogPages->count() : 0 }}
                                    </span>
                                </div>
                                <div class="mt-4 space-y-3">
                                    @forelse(($topCatalogPages ?? collect())->take(3) as $catalogPage)
                                        <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-3">
                                            <p class="text-sm font-semibold text-slate-800">{{ $catalogPage->name }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ strtoupper($catalogPage->type) }} · {{ $catalogPage->slug ?: '/' }}</p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">Aun no hay catalogo sincronizado desde Magento.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-4 md:p-6 shadow-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">GA4 ORGANICO</p>
                                        <h2 class="mt-1 text-base font-bold text-slate-900">Landing pages organicas</h2>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">
                                        {{ isset($topOrganicPages) ? $topOrganicPages->count() : 0 }}
                                    </span>
                                </div>
                                <div class="mt-4 space-y-3">
                                    @forelse(($topOrganicPages ?? collect())->take(3) as $organicPage)
                                        <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-3">
                                            <p class="text-sm font-semibold text-slate-800">{{ $organicPage->page_title ?: $organicPage->page_path }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $organicPage->page_path }}</p>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">Aun no hay datos de landing pages organicas desde GA4.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <!-- VISTAS PLACEHOLDERS (Para no romper los menús) -->
                    <div x-show="activeSection !== 'competitors' && activeSection !== 'overview'" x-cloak>
                        <div class="rounded-xl border border-slate-200 bg-white p-8 md:p-12 text-center shadow-sm">
                            <div class="mx-auto w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 mb-4 border border-slate-100">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            </div>
                            <h2 class="text-xl font-bold text-slate-800" x-text="'Vista ' + (sectionLabels[activeSection] || '')"></h2>
                            <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">Esta sección estará disponible próximamente en este nuevo rediseño responsivo.</p>
                            <button @click="activeSection = 'overview'" class="mt-6 rounded-lg bg-primary px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-primary/90 transition">Volver a Resumen</button>
                        </div>
                    </div>
                    
                </div>
            </main>
        </div>
    </div>

    <!-- Script para renderizar gráficos Chart.js reales -->
    <script>
        document.addEventListener('alpine:init', () => {
            const ctx = document.getElementById('seoTrendChart');
            if (ctx && typeof Chart !== 'undefined') {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels ?? []),
                        datasets: [
                            {
                                label: 'Clicks',
                                data: @json($chartClicks ?? []),
                                borderColor: '#00c27f', // Success green
                                backgroundColor: 'rgba(0, 194, 127, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHitRadius: 10
                            },
                            {
                                label: 'Impresiones',
                                data: @json($chartImpressions ?? []),
                                borderColor: '#3d5afe', // Primary blue
                                backgroundColor: 'rgba(61, 90, 254, 0.05)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHitRadius: 10
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                titleFont: { family: 'Inter', size: 12 },
                                bodyFont: { family: 'Inter', size: 12 },
                                padding: 10,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: { 
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 10 }, color: '#94a3b8' }
                            },
                            y: { 
                                border: { dash: [4, 4], display: false }, 
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { family: 'Inter', size: 10 }, color: '#94a3b8' }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-layouts.app>
