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
//         Schema::create('device_sensor_data', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('device_id')->constrained('client_devices');
//             $table->decimal('voltage', 8, 2);
//             $table->decimal('current', 8, 2);
//             $table->decimal('power', 8, 2);
//             $table->decimal('energy', 8, 2);
//             $table->decimal('frequency', 8, 2);
//             $table->decimal('power_factor', 8, 2);
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('device_sensor_data');
//     }
// };
