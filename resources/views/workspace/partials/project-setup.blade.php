<section class="space-y-6">
    <div>
        <p class="text-[11px] font-bold uppercase tracking-[0.28em] text-slate-400">Setup inicial</p>
        <h1 class="mt-2 text-3xl font-bold text-slate-900">Conecta tu propiedad para activar todas las vistas</h1>
        <p class="mt-3 max-w-3xl text-sm text-slate-500">
            Estas pantallas usan Search Console, GA4, Magento, tracking SERP y crawl. Primero selecciona una propiedad
            para crear el proyecto base y desbloquear el workspace.
        </p>
    </div>

    @if ($propertyError)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-700">
            {{ $propertyError }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-3">
        @forelse ($properties as $property)
            <form method="POST" action="{{ route('projects.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                @csrf
                <input type="hidden" name="property_id" value="{{ $property['property_id'] }}">
                <input type="hidden" name="name" value="{{ $property['name'] }}">
                <input type="hidden" name="url" value="{{ $property['url'] }}">
                <input type="hidden" name="type" value="{{ $property['type'] }}">
                <p class="text-lg font-bold text-slate-900">{{ $property['name'] }}</p>
                <p class="mt-2 text-sm text-slate-500">{{ $property['url'] }}</p>
                <button class="mt-5 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90">Usar esta propiedad</button>
            </form>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500 lg:col-span-3">
                No se encontraron propiedades disponibles. Revisa la conexion con Google para continuar.
            </div>
        @endforelse
    </div>
</section>
