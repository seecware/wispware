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
            $table->renameColumn('consumption_gb', 'consumption_bytes');
        });

        Schema::table('consumption_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('consumption_bytes')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consumption_logs', function (Blueprint $table) {
            $table->decimal('consumption_bytes', 12, 4)->change();
            $table->renameColumn('consumption_bytes', 'consumption_gb');
        });
    }
};
