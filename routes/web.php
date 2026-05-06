<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CompetitorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectSettingsController;
use App\Http\Controllers\SeoCrawlerController;
use App\Http\Controllers\SerpTrackingController;
use App\Http\Controllers\TrackedKeywordController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => view('home'))->name('home');
    Route::get('/login', fn () => redirect()->route('home'))->name('login');
    Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/resumen', [WorkspaceController::class, 'summary'])->name('workspace.summary');
    Route::get('/deep-scan', [WorkspaceController::class, 'deepScan'])->name('workspace.deep-scan');
    Route::get('/keyword-hunter', [WorkspaceController::class, 'keywordHunter'])->name('workspace.keyword-hunter');
    Route::get('/serp-tracking', [WorkspaceController::class, 'serpTracking'])->name('workspace.serp-tracking');
    Route::get('/competidores', [WorkspaceController::class, 'competitors'])->name('workspace.competitors');
    Route::get('/conexiones', [WorkspaceController::class, 'connections'])->name('workspace.connections');
    Route::get('/oportunidades', [WorkspaceController::class, 'opportunities'])->name('workspace.opportunities');
    Route::get('/auditoria', [WorkspaceController::class, 'audit'])->name('workspace.audit');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::post('/project/settings', [ProjectSettingsController::class, 'update'])->name('project.settings.update');
    Route::post('/project/sync-magento', [ProjectSettingsController::class, 'syncMagento'])->name('project.sync-magento');
    Route::post('/project/sync-google-analytics', [ProjectSettingsController::class, 'syncGoogleAnalytics'])->name('project.sync-google-analytics');
    Route::post('/project/run-crawl', [SeoCrawlerController::class, 'store'])->name('project.run-crawl');
    Route::post('/project/run-serp', [SerpTrackingController::class, 'store'])->name('project.run-serp');
    Route::post('/competitors', [CompetitorController::class, 'store'])->name('competitors.store');
    Route::post('/tracked-keywords', [TrackedKeywordController::class, 'store'])->name('tracked-keywords.store');
    Route::post('/dashboard/sync', [DashboardController::class, 'sync'])->name('dashboard.sync');
    Route::post('/dashboard/audit', [DashboardController::class, 'audit'])->name('dashboard.audit');
    Route::post('/logout', [GoogleAuthController::class, 'destroy'])->name('logout');
});
