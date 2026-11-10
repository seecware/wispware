<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consumption_logs', function (Blueprint $table) {
            $table->macAddress('isp_mac_origin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_logs', function (Blueprint $table) {
            $table->dropColumn('isp_mac_origin');
        });
    }
};
