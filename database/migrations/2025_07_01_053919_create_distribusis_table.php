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
        Schema::create('distribusis', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel 'users' untuk ID Agen yang melakukan setoran
            $table->foreignId('agen_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_barang');
            $table->integer('jumlah_barang');
            $table->date('tanggal_setor');
            $table->text('keterangan')->nullable();
            $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusis');
    }
};
