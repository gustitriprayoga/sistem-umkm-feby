<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LaporanTransaksiTable;
use Filament\Pages\Page;

class LaporanTransaksi extends Page
{
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.laporan-transaksi';
    protected static ?string $title = 'Laporan Transaksi';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LaporanTransaksiTable::class,
        ];
    }
}
