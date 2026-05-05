<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('domain');
            $table->string('name');
            $table->string('notes')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'domain']);
            $table->index(['project_id', 'last_seen_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitors');
    }
};
