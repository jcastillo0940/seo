<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawl_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crawl_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('url', 2048);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->string('title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('h1')->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->string('robots_directives')->nullable();
            $table->boolean('is_indexable')->default(true);
            $table->boolean('is_in_sitemap')->default(false);
            $table->unsignedInteger('internal_links_count')->default(0);
            $table->unsignedInteger('images_without_alt_count')->default(0);
            $table->unsignedInteger('word_count')->default(0);
            $table->json('issues')->nullable();
            $table->timestamps();

            $table->index(['crawl_run_id', 'status_code']);
            $table->index(['project_id', 'is_indexable']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawl_pages');
    }
};
