<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TransaksiSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID'); // Menggunakan data Faker Indonesia

        // Ambil ID Karyawan dan Kategori
        $userIds = User::whereHas('roles', fn ($query) => $query->where('name', 'Karyawan'))->pluck('id');
        $kategoriIds = Kategori::pluck('id');

        for ($i = 0; $i < 100; $i++) { // Membuat 100 data transaksi
            Transaksi::create([
                'user_id' => $faker->randomElement($userIds),
                'kategori_id' => $faker->randomElement($kategoriIds),
                'tanggal_transaksi' => $faker->dateTimeBetween('-1 year', 'now'),
                'jumlah' => $faker->numberBetween(10000, 500000),
                'jenis' => $faker->randomElement(['pemasukan', 'pengeluaran']),
                'deskripsi' => $faker->sentence(3),
            ]);
        }
    }
}