<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TrackedKeywordController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $project = $request->user()->projects()->latest()->firstOrFail();

        $data = $request->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'size:2'],
            'language_code' => ['required', 'string', 'max:5'],
            'device' => ['required', 'string', 'in:mobile,desktop'],
            'search_intent' => ['nullable', 'string', 'in:informational,commercial,transactional,navigational'],
            'priority' => ['required', 'integer', 'between:1,5'],
        ]);

        $project->trackedKeywords()->updateOrCreate(
            [
                'keyword' => $data['keyword'],
                'country_code' => strtoupper($data['country_code']),
                'language_code' => strtolower($data['language_code']),
                'device' => $data['device'],
            ],
            [
                'search_intent' => $data['search_intent'] ?: null,
                'priority' => $data['priority'],
                'source' => 'manual',
            ]
        );

        return back()->with('status', 'Keyword agregada. Ya queda preparada para monitorear tu dominio y la competencia.');
    }
}
