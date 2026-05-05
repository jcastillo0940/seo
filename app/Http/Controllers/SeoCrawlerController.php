<?php

namespace App\Http\Controllers;

use App\Jobs\RunSeoCrawl;
use Illuminate\Http\RedirectResponse;

class SeoCrawlerController extends Controller
{
    public function store(): RedirectResponse
    {
        $project = request()->user()->projects()->latest()->firstOrFail();

        RunSeoCrawl::dispatch($project);

        return back()->with('status', 'Crawler SEO en cola. Ejecuta `php artisan queue:work` para procesarlo.');
    }
}
