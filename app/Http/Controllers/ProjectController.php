<?php

namespace App\Http\Controllers;

use App\Jobs\SyncProjectKeywordMetrics;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'property_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url'],
            'type' => ['required', 'string', 'max:50'],
            'ga4_property_id' => ['nullable', 'string', 'max:50'],
        ]);

        $project = Project::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'google_property_id' => $data['property_id'],
            ],
            [
                'name' => $data['name'],
                'url' => $data['url'],
                'google_property_type' => $data['type'],
                'ga4_property_id' => $data['ga4_property_id'] ?? null,
            ]
        );

        SyncProjectKeywordMetrics::dispatch($project);

        return redirect()->route('dashboard')->with('status', 'Proyecto conectado. La primera ingesta quedo en cola.');
    }
}
