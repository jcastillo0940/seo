<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serp_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serp_snapshot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competitor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('domain');
            $table->string('url', 2048);
            $table->string('title')->nullable();
            $table->unsignedSmallInteger('position');
            $table->boolean('is_own_domain')->default(false);
            $table->decimal('estimated_ctr', 6, 4)->nullable();
            $table->unsignedInteger('estimated_traffic')->nullable();
            $table->timestamps();

            $table->unique(['serp_snapshot_id', 'position']);
            $table->index(['competitor_id', 'position']);
            $table->index(['domain', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serp_results');
    }
};
