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
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel 'kategoris'
            $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->onDelete('set null');
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->integer('stok')->default(0);
            $table->decimal('harga', 15, 2)->comment('Harga jual satuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
