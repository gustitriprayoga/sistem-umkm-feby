<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id');

        // Buat 30 transaksi pengeluaran (barang keluar/penjualan)
        for ($i = 0; $i < 30; $i++) {
            // Ambil barang yang punya stok saja
            $barang = Barang::where('stok', '>', 0)->inRandomOrder()->first();

            // Jika tidak ada barang yang punya stok, hentikan seeder
            if (!$barang) {
                $this->command->info('Semua barang habis, seeder transaksi berhenti.');
                break;
            }

            // Jumlah jual tidak boleh lebih dari stok
            $jumlahJual = rand(1, min($barang->stok, 10)); // Jual antara 1-10 barang, tapi tidak lebih dari stok
            $totalHarga = $barang->harga * $jumlahJual;

            Transaksi::create([
                'user_id' => $userIds->random(),
                'kategori_id' => $barang->kategori_id,
                'barang_id' => $barang->id,
                'jumlah_barang' => $jumlahJual,
                'tanggal_transaksi' => now()->subDays(rand(1, 15)),
                'jumlah' => $totalHarga,
                'jenis' => 'pengeluaran',
                'deskripsi' => 'Penjualan ' . $barang->nama_barang,
            ]);

            // --- LOGIKA PENTING: KURANGI STOK ---
            $barang->decrement('stok', $jumlahJual);
        }
    }
}
