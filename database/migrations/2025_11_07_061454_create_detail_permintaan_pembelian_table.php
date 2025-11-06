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
        Schema::create('detail_permintaan_pembelian', function (Blueprint $table) {
            $table->id();

            // Merujuk ke tabel "request" utamanya
            $table->foreignId('permintaan_pembelian_id')
                ->constrained('permintaan_pembelian') // Merujuk ke tabel 'permintaan_pembelian'
                ->onDelete('cascade');

            $table->string('nama_barang'); // Nama bahan baku/barang, misal: "Biji Kopi Robusta"
            $table->integer('jumlah');
            $table->string('satuan', 50); // Misal: "kg", "liter", "box", "pack"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_permintaan_pembelian');
    }
};
