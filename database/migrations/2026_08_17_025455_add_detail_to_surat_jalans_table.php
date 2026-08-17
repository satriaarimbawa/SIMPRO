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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->string('pengemudi')->nullable()->after('tanggal_kirim');
            $table->string('no_polisi')->nullable()->after('pengemudi');
            $table->string('penerima')->nullable()->after('no_polisi');
            $table->text('keterangan')->nullable()->after('penerima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn(['pengemudi', 'no_polisi', 'penerima', 'keterangan']);
        });
    }
};
