<?php

namespace App\Observers;

use App\Models\Distribus; // Pastikan namespace model Anda benar
use App\Models\Distribusi;

class DistribusiObserver
{
    /**
     * Handle the Distribus "created" event.
     */
    public function created(Distribusi $distribus): void
    {
        // Saat distribusi baru dibuat, tambah stok barang terkait
        $barang = $distribus->barang;
        if ($barang) {
            $barang->increment('stok', $distribus->jumlah_barang);
        }
    }

    /**
     * Handle the Distribusi "updated" event.
     */
    public function updated(Distribusi $distribus): void
    {
        // Cek jika jumlah barang berubah
        if ($distribus->isDirty('jumlah_barang')) {
            $barang = $distribus->barang;
            if ($barang) {
                // Ambil jumlah lama dan baru
                $jumlahLama = $distribus->getOriginal('jumlah_barang');
                $jumlahBaru = $distribus->jumlah_barang;

                // Hitung selisihnya
                $selisih = $jumlahBaru - $jumlahLama;

                // Update stok dengan selisih tersebut
                $barang->increment('stok', $selisih);
            }
        }
    }

    /**
     * Handle the Distribus "deleted" event.
     */
    public function deleted(Distribusi $distribus): void
    {
        // Saat distribusi dihapus, kurangi stok barang terkait
        $barang = $distribus->barang;
        if ($barang) {
            $barang->decrement('stok', $distribus->jumlah_barang);
        }
    }
}
