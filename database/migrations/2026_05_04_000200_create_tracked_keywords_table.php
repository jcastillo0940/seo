<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracked_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->string('country_code', 2)->default('US');
            $table->string('language_code', 5)->default('es');
            $table->string('device', 20)->default('mobile');
            $table->string('search_intent', 20)->nullable();
            $table->unsignedTinyInteger('priority')->default(3);
            $table->string('source', 30)->default('manual');
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'keyword', 'country_code', 'language_code', 'device'], 'tracked_keywords_unique_scope');
            $table->index(['project_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracked_keywords');
    }
};
