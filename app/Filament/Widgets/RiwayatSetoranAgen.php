<?php

namespace App\Filament\Widgets;

use App\Models\Distribusi;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RiwayatSetoranAgen extends BaseWidget
{
    public static function canView(): bool
    {
        // Widget ini hanya bisa dilihat oleh Karyawan
        return auth()->user()->hasRole('agen');
    }

    protected static ?string $heading = 'Riwayat Setoran Saya';

    protected int | string | array $columnSpan = 'full';

    // [1] Tambahkan listener untuk me-refresh tabel
    protected $listeners = ['setoranUpdated' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            // [2] INI BAGIAN PALING PENTING: Filter data hanya untuk user yang login
            ->query(
                Distribusi::query()->where('agen_id', auth()->id())->latest('tanggal_setor')
            )
            ->columns([
                Tables\Columns\TextColumn::make('barang.nama_barang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_barang')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_setor')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }
}
