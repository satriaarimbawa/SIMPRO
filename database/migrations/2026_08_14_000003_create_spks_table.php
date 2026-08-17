<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->string('no_spk')->unique();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->string('nama_dinas');
            $table->string('kabupaten');
            $table->string('npwp_dinas')->nullable();
            $table->text('alamat_dinas')->nullable();
            $table->string('nama_ppk');
            $table->string('jabatan_ppk')->nullable();
            $table->date('tanggal_spk');
            $table->integer('jumlah_termin');
            $table->string('file_spk_asli')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spks');
    }
};
