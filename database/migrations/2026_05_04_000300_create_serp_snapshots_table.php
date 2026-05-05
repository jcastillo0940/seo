<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serp_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tracked_keyword_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 40)->default('manual');
            $table->string('search_engine', 20)->default('google');
            $table->string('country_code', 2)->default('US');
            $table->string('language_code', 5)->default('es');
            $table->string('device', 20)->default('mobile');
            $table->unsignedSmallInteger('results_count')->default(0);
            $table->timestamp('captured_at');
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'captured_at']);
            $table->index(['tracked_keyword_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serp_snapshots');
    }
};
