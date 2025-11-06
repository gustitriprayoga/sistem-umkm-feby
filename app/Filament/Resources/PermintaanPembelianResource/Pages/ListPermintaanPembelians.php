<?php

namespace App\Filament\Resources\PermintaanPembelianResource\Pages;

use App\Filament\Resources\PermintaanPembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanPembelians extends ListRecords
{
    protected static string $resource = PermintaanPembelianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
