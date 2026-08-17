<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekonsiliasi_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termin_id')->constrained('termins')->onDelete('cascade');
            $table->decimal('nilai_diterima', 15, 2);
            $table->decimal('selisih', 15, 2);
            $table->text('catatan_selisih')->nullable();
            $table->string('status_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekonsiliasi_pembayarans');
    }
};
