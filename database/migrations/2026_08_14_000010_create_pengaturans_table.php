<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->integer('ambang_pengingat_hari')->default(7);
            $table->decimal('tarif_ppn_persen', 5, 2)->default(11);
            $table->decimal('tarif_pph_persen', 5, 2)->default(1.5);
            $table->date('berlaku_sejak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
