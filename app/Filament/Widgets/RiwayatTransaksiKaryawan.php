<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RiwayatTransaksiKaryawan extends BaseWidget
{
    protected static ?string $heading = 'Riwayat Transaksi Terkini';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Widget ini hanya bisa dilihat oleh Karyawan
        return auth()->user()->hasRole('karyawan');
    }

    // Listener untuk me-refresh tabel secara otomatis setelah ada data baru
    protected $listeners = ['transaksiUpdated' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            // Kueri hanya akan mengambil transaksi yang dibuat oleh karyawan yang sedang login
            ->query(
                Transaksi::query()->where('user_id', auth()->id())->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                // Menampilkan nama barang jika ini adalah pemasukan
                Tables\Columns\TextColumn::make('barang.nama_barang')
                    ->label('Detail')
                    ->default(fn($record) => $record->deskripsi)
                    ->searchable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Uang')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
