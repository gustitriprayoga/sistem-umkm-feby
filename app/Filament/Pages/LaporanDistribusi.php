<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LaporanDistribusiTable;
use Filament\Pages\Page;

class LaporanDistribusi extends Page
{
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.laporan-distribusi';
    protected static ?string $title = 'Laporan Distribusi';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LaporanDistribusiTable::class,
        ];
    }
}
