<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_page_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->string('page_path', 2048);
            $table->string('page_title')->nullable();
            $table->unsignedInteger('sessions')->default(0);
            $table->unsignedInteger('users')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->string('channel_group')->default('Organic Search');
            $table->timestamps();

            $table->unique(['project_id', 'date', 'page_path', 'channel_group'], 'analytics_page_metrics_unique_scope');
            $table->index(['project_id', 'sessions']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_page_metrics');
    }
};
