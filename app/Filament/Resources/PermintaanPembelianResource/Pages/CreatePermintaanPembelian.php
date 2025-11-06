<?php

namespace App\Filament\Resources\PermintaanPembelianResource\Pages;

use App\Filament\Resources\PermintaanPembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePermintaanPembelian extends CreateRecord
{
    protected static string $resource = PermintaanPembelianResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set 'admin_id' dengan ID user yang sedang login
        $data['admin_id'] = auth()->id();

        return $data;
    }
}
