<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        // Membuat User Pemilik
        $pemilik = User::create([
            'name' => 'Feby Rahayu Putri',
            'email' => 'pemilik@hana.com',
            'password' => Hash::make('pemilik123'),
        ]);
        $pemilik->assignRole('pemilik');

        // Membuat User Karyawan
        $karyawan = User::create([
            'name' => 'Zola Karyawan',
            'email' => 'karyawan@hana.com',
            'password' => Hash::make('karyawan123'),
        ]);
        $karyawan->assignRole('karyawan');

        $agen = User::create([
            'name' => 'Agen Hana',
            'email' => 'agen@hana.com',
            'password' => Hash::make('agen123'),
        ]);
        $agen->assignRole('agen');

        // Membuat beberapa User Agen menggunakan factory
        User::factory(5)->create()->each(function ($user) {
            $user->assignRole('Agen');
        });
    }
}