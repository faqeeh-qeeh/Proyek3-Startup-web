<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel untuk menyimpan model machine learning
        Schema::create('ml_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'anomaly_detection', 'clustering'
            $table->text('parameters'); // JSON config
            $table->binary('model_data'); // Serialized model
            $table->timestamps();
        });

        // Tabel untuk menyimpan hasil deteksi anomali
        Schema::create('device_anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('client_devices');
            $table->foreignId('monitoring_id')->constrained('device_monitoring');
            $table->float('score'); // Skor anomali (0-1)
            $table->string('type'); // Jenis anomali
            $table->text('description')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();
        });

        // Tabel untuk klasifikasi perangkat
        Schema::create('device_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('client_devices');
            $table->string('category'); // 'industrial', 'household'
            $table->float('confidence'); // Tingkat keyakinan (0-1)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    // public function down(): void
    // {
    //     Schema::dropIfExists('anomaly_detection_tables');
    // }
        public function down()
    {
        Schema::dropIfExists('ml_models');
        Schema::dropIfExists('device_anomalies');
        Schema::dropIfExists('device_classifications');
    }
};
