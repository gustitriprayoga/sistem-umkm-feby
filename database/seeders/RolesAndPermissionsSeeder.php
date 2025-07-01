<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Jalankan seeder database.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat Roles
        Role::create(['name' => 'pemilik', 'guard_name' => 'web']);
        Role::create(['name' => 'karyawan', 'guard_name' => 'web']);
        Role::create(['name' => 'agen', 'guard_name' => 'web']);
    }
}