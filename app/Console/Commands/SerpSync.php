<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\SerpSnapshot;
use App\Services\SerpTrackingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SerpSync extends Command
{
    protected $signature = 'serp:sync
                            {--limit= : Override daily limit for this run}
                            {--fresh : Skip keywords already tracked in the last 7 days}
                            {--dry-run : Show what would run without making API calls}';

    protected $description = 'Run SERP tracking for priority keywords (respects SEO_SERP_DAILY_LIMIT)';

    public function handle(SerpTrackingService $service): int
    {
        $project = Project::latest()->first();

        if (! $project) {
            $this->error('No project found.');
            return self::FAILURE;
        }

        $dailyLimit = (int) ($this->option('limit') ?? config('seo.serp_daily_limit', 6));
        $cacheKey = 'serp_daily_queries:'.now()->toDateString();
        $usedToday = (int) Cache::get($cacheKey, 0);
        $remaining = max(0, $dailyLimit - $usedToday);

        $this->line("Proyecto : <comment>{$project->name}</comment>");
        $this->line("Limite   : <comment>{$usedToday}/{$dailyLimit}</comment> usadas hoy — <comment>{$remaining}</comment> disponibles");

        if ($remaining === 0) {
            $this->warn('Limite diario alcanzado. Usa --limit=N para sobreescribirlo o espera manana.');
            return self::SUCCESS;
        }

        $recentIds = $this->option('fresh')
            ? $project->serpSnapshots()->where('captured_at', '>=', now()->subDays(7))->pluck('tracked_keyword_id')->unique()
            : collect();

        $keywords = $project->trackedKeywords()
            ->when($recentIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $recentIds))
            ->orderByRaw("CASE WHEN search_intent = 'transactional' THEN 0 ELSE 1 END")
            ->orderBy('priority')
            ->orderBy('keyword')
            ->limit($remaining)
            ->get();

        if ($keywords->isEmpty()) {
            $this->info('No hay keywords pendientes para trackear.');
            return self::SUCCESS;
        }

        $this->line("Keywords a trackear: <comment>{$keywords->count()}</comment>");
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->table(['Intent', 'Priority', 'Keyword'], $keywords->map(fn ($k) => [
                $k->search_intent ?? '—',
                $k->priority,
                $k->keyword,
            ]));
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($keywords->count());
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — %message%');
        $bar->start();

        $snapshots = 0;
        $results = 0;

        foreach ($keywords as $kw) {
            $bar->setMessage($kw->keyword);

            try {
                $before = $project->serpSnapshots()->count();
                $service->syncSingleKeyword($project, $kw);
                $after = $project->serpSnapshots()->count();
                if ($after > $before) {
                    $snapshots++;
                    $results += $project->serpSnapshots()->latest('captured_at')->first()?->results()->count() ?? 0;
                }
            } catch (\Throwable $e) {
                $bar->setMessage("<error>{$kw->keyword}: {$e->getMessage()}</error>");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Completado: {$snapshots} snapshots, {$results} resultados.");

        return self::SUCCESS;
    }
}
