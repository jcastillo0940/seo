<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('google_property_id');
            $table->string('google_property_type')->default('sc-domain');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'google_property_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
