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
        Schema::create('infrastructure_devices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('brand', 50);
            $table->string('model', 50);
            $table->string('serial_number', 50)->unique();
            $table->char('mac_address',17)->unique();
            $table->string('status', 30);
            $table->string('comment', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infrastructure_devices');
    }
};
