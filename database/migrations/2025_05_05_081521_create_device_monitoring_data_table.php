<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     public function up()
//     {
//         Schema::create('device_monitoring_data', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('device_id')->constrained('client_devices');
//             $table->decimal('voltage', 8, 2);
//             $table->decimal('current', 8, 2);
//             $table->decimal('power', 8, 2);
//             $table->decimal('energy', 8, 2);
//             $table->decimal('frequency', 8, 2);
//             $table->decimal('pf', 8, 2);
//             $table->timestamps();
//         });
//     }

//     public function down()
//     {
//         Schema::dropIfExists('device_monitoring_data');
//     }
// };