<?php

namespace App\Http\Controllers;

use App\Jobs\SyncGoogleAnalyticsPages;
use App\Jobs\SyncMagentoCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectSettingsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $project = $request->user()->projects()->latest()->firstOrFail();

        $data = $request->validate([
            'magento_base_url' => ['nullable', 'url'],
            'magento_store_code' => ['nullable', 'string', 'max:50'],
            'magento_website_code' => ['nullable', 'string', 'max:50'],
            'ga4_property_id' => ['nullable', 'string', 'max:50'],
        ]);

        $project->update($data);

        return back()->with('status', 'Configuracion de Magento y Google guardada.');
    }

    public function syncMagento(): RedirectResponse
    {
        $project = request()->user()->projects()->latest()->firstOrFail();

        SyncMagentoCatalog::dispatch($project);

        return back()->with('status', 'Sincronizacion de Magento en cola. Ejecuta `php artisan queue:work` para procesarla.');
    }

    public function syncGoogleAnalytics(): RedirectResponse
    {
        $project = request()->user()->projects()->latest()->firstOrFail();

        SyncGoogleAnalyticsPages::dispatch($project);

        return back()->with('status', 'Sincronizacion de GA4 en cola. Ejecuta `php artisan queue:work` para procesarla.');
    }
}
