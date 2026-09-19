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
        Schema::create('device_management_infos', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('dev_id')
                ->unique()
                ->constrained('infrastructure_devices');
            $table->char('ip_management_address', 15);
            $table->string('default_management_page', 50);
            $table->string('default_username', 50);
            $table->string('default_password', 50);
            $table->string('default_ssid', 50);
            $table->string('default_ssid_pw',50);
            $table->string('notes', 200);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_management_infos');
    }
};
