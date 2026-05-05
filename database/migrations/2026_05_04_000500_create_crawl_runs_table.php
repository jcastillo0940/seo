<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawl_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('source', 30)->default('internal');
            $table->string('status', 30)->default('queued');
            $table->unsignedInteger('pages_crawled')->default(0);
            $table->unsignedInteger('issue_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('summary')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['project_id', 'finished_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawl_runs');
    }
};
