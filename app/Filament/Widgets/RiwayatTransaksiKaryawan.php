<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RiwayatTransaksiKaryawan extends BaseWidget
{
    protected static ?string $heading = 'Riwayat Transaksi Terakhir';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Widget ini hanya bisa dilihat oleh Karyawan
        return auth()->user()->hasRole('karyawan');
    }


    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Query hanya mengambil transaksi milik user yang sedang login
                \App\Models\Transaksi::query()->where('user_id', Auth::id())
            )
            ->defaultSort('created_at', 'desc') // Urutkan berdasarkan terbaru
            ->columns([
                TextColumn::make('user.name')
                    ->label('Nama Karyawan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori.nama_kategori'),
                Tables\Columns\TextColumn::make('jumlah')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(30),
            ]);
    }
}