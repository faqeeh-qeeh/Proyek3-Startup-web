<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('device_anomalies', function (Blueprint $table) {
            // Hanya tambahkan kolom yang belum ada
            if (!Schema::hasColumn('device_anomalies', 'detected_at')) {
                $table->timestamp('detected_at')->nullable()->after('description');
            }
            
            // Ubah nama kolom jika perlu
            if (Schema::hasColumn('device_anomalies', 'score') && !Schema::hasColumn('device_anomalies', 'anomaly_score')) {
                $table->renameColumn('score', 'anomaly_score');
            }
            
            if (Schema::hasColumn('device_anomalies', 'type') && !Schema::hasColumn('device_anomalies', 'anomaly_type')) {
                $table->renameColumn('type', 'anomaly_type');
            }
        });
    }
    
    public function down()
    {
        Schema::table('device_anomalies', function (Blueprint $table) {
            // Kembalikan perubahan jika perlu
            if (Schema::hasColumn('device_anomalies', 'anomaly_score')) {
                $table->renameColumn('anomaly_score', 'score');
            }
            
            if (Schema::hasColumn('device_anomalies', 'anomaly_type')) {
                $table->renameColumn('anomaly_type', 'type');
            }
            
            $table->dropColumn(['detected_at']);
        });
    }
};