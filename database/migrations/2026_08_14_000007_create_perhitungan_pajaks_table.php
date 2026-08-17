<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perhitungan_pajaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termin_id')->constrained('termins')->onDelete('cascade');
            $table->decimal('dpp', 15, 2);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph', 15, 2);
            $table->decimal('nilai_tagihan', 15, 2);
            $table->decimal('tarif_ppn_persen', 5, 2);
            $table->decimal('tarif_pph_persen', 5, 2);
            $table->text('uraian')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perhitungan_pajaks');
    }
};
