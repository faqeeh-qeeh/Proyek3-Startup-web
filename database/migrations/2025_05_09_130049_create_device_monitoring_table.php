<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('device_monitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('client_devices');
            $table->float('voltage');
            $table->float('current');
            $table->float('power');
            $table->float('energy');
            $table->float('frequency');
            $table->float('power_factor');
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            $table->index('device_id');
            $table->index('recorded_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('device_monitoring');
    }
};