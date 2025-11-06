<?php

namespace App\Filament\Resources\PermintaanPembelianResource\Pages;

use App\Filament\Resources\PermintaanPembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanPembelian extends EditRecord
{
    protected static string $resource = PermintaanPembelianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
