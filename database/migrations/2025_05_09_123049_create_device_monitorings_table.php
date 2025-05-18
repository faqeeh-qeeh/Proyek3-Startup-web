<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('device_monitorings', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('device_id')->constrained('client_devices');
//             $table->float('voltage');
//             $table->float('current');
//             $table->float('power');
//             $table->float('energy');
//             $table->float('frequency');
//             $table->float('power_factor');
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('device_monitorings');
//     }
// };
