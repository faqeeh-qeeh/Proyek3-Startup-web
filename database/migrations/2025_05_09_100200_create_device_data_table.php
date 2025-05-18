<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up()
//     {
//         Schema::create('device_data', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('device_id')->constrained('client_devices');
//             $table->float('voltage')->nullable();
//             $table->float('current')->nullable();
//             $table->float('power')->nullable();
//             $table->float('energy')->nullable();
//             $table->float('frequency')->nullable();
//             $table->float('pf')->nullable();
//             $table->timestamps();
//         });
//     }
//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('device_data');
//     }
// };
