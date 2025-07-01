<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RiwayatSetoranAgen extends BaseWidget
{
    protected static ?string $heading = 'Riwayat Setoran Agen';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Widget ini hanya bisa dilihat oleh Karyawan
        return auth()->user()->hasRole('agen');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Query hanya mengambil data distribusi milik Agen yang sedang login
                \App\Models\Distribusi::query()->where('agen_id', Auth::id())
            )
            ->defaultSort('created_at', 'desc') // Urutkan berdasarkan terbaru
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Agen')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_barang')
                    ->label('Jumlah'),
                Tables\Columns\TextColumn::make('tanggal_setor')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(40),
            ]);
    }
}
