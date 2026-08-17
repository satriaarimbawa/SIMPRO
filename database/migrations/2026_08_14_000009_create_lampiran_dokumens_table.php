<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lampiran_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termin_id')->constrained('termins')->onDelete('cascade');
            $table->string('jenis_dokumen');
            $table->string('nama_file');
            $table->string('file');
            $table->date('tanggal_unggah');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lampiran_dokumens');
    }
};
