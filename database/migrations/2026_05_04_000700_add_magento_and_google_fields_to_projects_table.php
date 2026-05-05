<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('magento_base_url')->nullable()->after('url');
            $table->string('magento_store_code')->nullable()->after('magento_base_url');
            $table->string('magento_website_code')->nullable()->after('magento_store_code');
            $table->timestamp('magento_last_synced_at')->nullable()->after('last_synced_at');
            $table->string('ga4_property_id')->nullable()->after('google_property_type');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'magento_base_url',
                'magento_store_code',
                'magento_website_code',
                'magento_last_synced_at',
                'ga4_property_id',
            ]);
        });
    }
};
