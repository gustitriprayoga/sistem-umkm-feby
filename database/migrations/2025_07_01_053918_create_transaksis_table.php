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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel 'users' untuk mencatat siapa yang menginput
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Menghubungkan ke tabel 'kategori'
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->date('tanggal_transaksi');
            $table->decimal('jumlah', 15, 2); // Jumlah uang
            $table->enum('jenis', ['pemasukan', 'pengeluaran']); // Jenis transaksi
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
