<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->unsignedBigInteger('external_id');
            $table->string('url', 2048);
            $table->string('slug')->nullable();
            $table->string('name');
            $table->string('status', 20)->default('active');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->boolean('is_indexable')->default(true);
            $table->unsignedInteger('product_count')->default(0);
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'type', 'external_id']);
            $table->index(['project_id', 'type']);
            $table->index(['project_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_pages');
    }
};
