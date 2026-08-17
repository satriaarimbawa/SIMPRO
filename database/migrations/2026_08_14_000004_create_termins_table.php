<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spks')->onDelete('cascade');
            $table->integer('no_termin');
            $table->date('tanggal_mulai_kirim')->nullable();
            $table->date('tanggal_akhir_kirim');
            $table->decimal('nilai_termin', 15, 2);
            $table->boolean('kena_ppn')->default(true);
            $table->string('status')->default('belum_kirim');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termins');
    }
};
