<?php

namespace App\Http\Controllers;

use App\Jobs\SyncProjectSerpTracking;
use Illuminate\Http\RedirectResponse;

class SerpTrackingController extends Controller
{
    public function store(): RedirectResponse
    {
        $project = request()->user()->projects()->latest()->firstOrFail();

        SyncProjectSerpTracking::dispatch($project);

        return back()->with('status', 'Tracking SERP en cola. Ejecuta `php artisan queue:work` para procesarlo.');
    }
}
