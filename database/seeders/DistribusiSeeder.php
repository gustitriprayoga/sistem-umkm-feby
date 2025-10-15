<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Distribus;
use App\Models\Distribusi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistribusiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agenIds = User::pluck('id');
        $barangs = Barang::all();

        if ($agenIds->isEmpty() || $barangs->isEmpty()) {
            $this->command->info('Tidak ada User (agen) atau Barang ditemukan.');
            return;
        }

        // Buat 20 data distribusi (barang masuk)
        for ($i = 0; $i < 20; $i++) {
            $barang = $barangs->random();
            $jumlahMasuk = rand(10, 50); // Jumlah barang yang masuk antara 10 - 50

            Distribusi::create([
                'agen_id' => $agenIds->random(),
                'barang_id' => $barang->id,
                'jumlah_barang' => $jumlahMasuk,
                'harga_satuan' => $barang->harga,
                'total_harga' => $barang->harga * $jumlahMasuk,
                'tanggal_setor' => now()->subDays(rand(1, 30)),
                'keterangan' => 'Stok masuk dari seeder.',
            ]);

            // --- LOGIKA PENTING: TAMBAHKAN STOK ---
            $barang->increment('stok', $jumlahMasuk);
        }
    }
}
