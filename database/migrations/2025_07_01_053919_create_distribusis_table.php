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
            // Menghubungkan ke tabel 'users' untuk ID Agen
            $table->foreignId('agen_id')->constrained('users')->onDelete('cascade');

            // --- INI BAGIAN YANG PALING PENTING ---
            // Menghubungkan ke tabel 'barangs' menggunakan foreign key
            // Kolom 'nama_barang' HARUS DIHAPUS.
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');

            $table->integer('jumlah_barang');
            $table->decimal('harga_satuan', 15, 2); // Menggunakan decimal, bukan integer
            $table->decimal('total_harga', 15, 2);
            $table->date('tanggal_setor');
            $table->text('keterangan')->nullable();
            $table->timestamps();
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
