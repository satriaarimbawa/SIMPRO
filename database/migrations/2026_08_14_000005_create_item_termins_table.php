<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_termins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termin_id')->constrained('termins')->onDelete('cascade');
            $table->string('nama_barang');
            $table->decimal('jumlah', 12, 2);
            $table->string('satuan');
            $table->string('merk')->nullable()->default('-');
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_termins');
    }
};
