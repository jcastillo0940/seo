<?php

namespace App\Http\Controllers;

use App\Services\WorkspaceViewDataService;
use Illuminate\Contracts\View\View;

class WorkspaceController extends Controller
{
    public function __construct(
        protected WorkspaceViewDataService $workspaceViewDataService
    ) {
    }

    public function summary(): View
    {
        return $this->page('summary');
    }

    public function deepScan(): View
    {
        return $this->page('deep-scan');
    }

    public function keywordHunter(): View
    {
        return $this->page('keyword-hunter');
    }

    public function serpTracking(): View
    {
        return $this->page('serp-tracking');
    }

    public function competitors(): View
    {
        return $this->page('competitors');
    }

    public function connections(): View
    {
        return $this->page('connections');
    }

    public function opportunities(): View
    {
        return $this->page('opportunities');
    }

    public function audit(): View
    {
        return $this->page('audit');
    }

    protected function page(string $page): View
    {
        return view('workspace.page', [
            'page' => $page,
            ...$this->workspaceViewDataService->forUser(request()->user()),
        ]);
    }
}
