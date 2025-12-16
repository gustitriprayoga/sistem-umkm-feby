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
       Schema::table('detail_permintaan_pembelian', function (Blueprint $table) {
        // Status per barang: 'diajukan' (default), 'ada' (diterima), 'kosong' (ditolak)
        $table->enum('status_barang', ['diajukan', 'ada', 'kosong'])->default('diajukan');

        // Catatan per barang jika kosong (misal: "Barang discontinue")
        $table->string('catatan_barang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_permintaan_pembelian', function (Blueprint $table) {
        $table->dropColumn(['status_barang', 'catatan_barang']);
        });
    }
};
