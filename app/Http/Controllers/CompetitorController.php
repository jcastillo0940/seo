<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompetitorController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $project = $request->user()->projects()->latest()->firstOrFail();

        $data = $request->validate([
            'domain' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $domain = Str::of($data['domain'])
            ->lower()
            ->replace(['https://', 'http://'], '')
            ->trim('/')
            ->before('/');

        $project->competitors()->updateOrCreate(
            ['domain' => (string) $domain],
            [
                'name' => $data['name'] ?: (string) $domain,
                'notes' => $data['notes'] ?: null,
            ]
        );

        return back()->with('status', 'Competidor guardado. Ya queda listo para tracking SERP y keyword gap.');
    }
}
