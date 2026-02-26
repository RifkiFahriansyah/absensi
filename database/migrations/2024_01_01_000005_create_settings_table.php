<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('office_lat', 10, 7)->default(-6.2000000);
            $table->decimal('office_long', 10, 7)->default(106.8166670);
            $table->integer('radius_meter')->default(75);
            $table->time('work_start')->default('08:00');
            $table->integer('late_tolerance_minutes')->default(15);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
