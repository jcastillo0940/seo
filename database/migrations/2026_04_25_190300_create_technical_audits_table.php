<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technical_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('performance_score');
            $table->unsignedTinyInteger('seo_score');
            $table->longText('json_raw_data')->nullable();
            $table->timestamp('audited_at');
            $table->timestamps();

            $table->index(['project_id', 'audited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technical_audits');
    }
};
