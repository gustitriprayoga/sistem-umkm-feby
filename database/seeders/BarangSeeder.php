<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua ID kategori yang ada
        $kategoriIds = Kategori::pluck('id');

        if ($kategoriIds->isEmpty()) {
            $this->command->info('Tidak ada Kategori ditemukan, silakan jalankan KategoriSeeder terlebih dahulu.');
            return;
        }

        $barangs = [
            ['nama' => 'Sabun Mandi Lifebuoy', 'harga' => 4000],
            ['nama' => 'Shampo Pantene', 'harga' => 18000],
            ['nama' => 'Pasta Gigi Pepsodent', 'harga' => 8500],
            ['nama' => 'Sikat Gigi Formula', 'harga' => 5000],
            ['nama' => 'Minyak Goreng Sania 2L', 'harga' => 35000],
            ['nama' => 'Beras Ramos 5kg', 'harga' => 65000],
            ['nama' => 'Gula Pasir Gulaku 1kg', 'harga' => 17000],
            ['nama' => 'Kopi Kapal Api Special', 'harga' => 14000],
            ['nama' => 'Teh Celup Sariwangi', 'harga' => 6000],
            ['nama' => 'Susu Kental Manis Frisian Flag', 'harga' => 11000],
        ];

        foreach ($barangs as $barang) {
            Barang::create([
                'nama_barang' => $barang['nama'],
                'kategori_id' => $kategoriIds->random(),
                'deskripsi' => 'Deskripsi untuk ' . $barang['nama'],
                'harga' => $barang['harga'],
                'stok' => 0, // Stok awal kita set 0, akan diisi oleh DistribusiSeeder
            ]);
        }
    }
}
