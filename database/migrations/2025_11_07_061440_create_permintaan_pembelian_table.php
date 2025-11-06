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
        Schema::create('permintaan_pembelian', function (Blueprint $table) {
            $table->id();

            // Siapa yang membuat request (Admin/Pengelola)
            $table->foreignId('admin_id')
                ->constrained('users') // Merujuk ke tabel 'users'
                ->onDelete('cascade');

            // Request ini ditujukan untuk siapa (Agen)
            $table->foreignId('agen_id')
                ->constrained('users') // Merujuk ke tabel 'users'
                ->onDelete('cascade');

            $table->date('tanggal_permintaan');
            $table->enum('status', ['diminta', 'diproses', 'selesai', 'dibatalkan'])
                ->default('diminta');
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_pembelian');
    }
};
