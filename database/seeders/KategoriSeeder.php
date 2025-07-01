<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        Kategori::create(['nama_kategori' => 'Penjualan Harian', 'deskripsi' => 'Pemasukan dari penjualan barang harian.']);
        Kategori::create(['nama_kategori' => 'Pembelian Sembako', 'deskripsi' => 'Pengeluaran untuk stok sembako.']);
        Kategori::create(['nama_kategori' => 'Biaya Operasional', 'deskripsi' => 'Pengeluaran untuk listrik, air, dll.']);
        Kategori::create(['nama_kategori' => 'Gaji Karyawan', 'deskripsi' => 'Pengeluaran untuk pembayaran gaji.']);
        Kategori::create(['nama_kategori' => 'Perlengkapan Toko', 'deskripsi' => 'Pengeluaran untuk membeli perlengkapan toko.']);
    }
}