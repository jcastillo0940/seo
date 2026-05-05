<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keyword_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');
            $table->float('position');
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('impressions')->default(0);
            $table->date('date')->index();
            $table->timestamps();

            $table->index(['project_id', 'date']);
            $table->index(['project_id', 'keyword']);
            $table->unique(['project_id', 'keyword', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_metrics');
    }
};
