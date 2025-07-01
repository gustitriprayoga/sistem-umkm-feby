<?php

namespace Database\Seeders;

use App\Models\Distribusi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DistribusiSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua ID user dengan role 'Agen'
        $agenIds = User::whereHas('roles', fn ($query) => $query->where('name', 'Agen'))->pluck('id');

        $daftarBarang = ['Beras 5kg', 'Minyak Goreng 2L', 'Gula Pasir 1kg', 'Kopi Sachet', 'Mie Instan Dus'];

        for ($i = 0; $i < 50; $i++) { // Membuat 50 data distribusi
            Distribusi::create([
                'agen_id' => $faker->randomElement($agenIds),
                'nama_barang' => $faker->randomElement($daftarBarang),
                'jumlah_barang' => $faker->numberBetween(5, 50),
                'tanggal_setor' => $faker->dateTimeBetween('-6 months', 'now'),
                'keterangan' => 'Setoran rutin',
            ]);
        }
    }
}