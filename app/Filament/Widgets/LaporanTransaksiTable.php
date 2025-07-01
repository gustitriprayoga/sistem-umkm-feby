<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Arr;
// PERUBAHAN: Tambahkan 'use' untuk listener
use Livewire\Attributes\On;

class LaporanTransaksiTable extends BaseWidget
{
    protected static ?string $heading = 'Detail Transaksi';

    // PERUBAHAN: Tambahkan properti untuk menyimpan filter
    public ?array $filters = [];

    // PERUBAHAN: Tambahkan listener untuk event 'updateLaporanFilter'
    #[On('updateLaporanFilter')]
    public function updateFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    public function table(Table $table): Table
    {
        // PERUBAHAN: Baca dari properti $this->filters
        $tanggalMulai = Arr::get($this->filters, 'tanggal_mulai');
        $tanggalSelesai = Arr::get($this->filters, 'tanggal_selesai');

        return $table
            ->query(
                Transaksi::query()
                    ->when($tanggalMulai, fn($q) => $q->whereDate('tanggal_transaksi', '>=', $tanggalMulai))
                    ->when($tanggalSelesai, fn($q) => $q->whereDate('tanggal_transaksi', '<=', $tanggalSelesai))
            )
            ->defaultSort('tanggal_transaksi', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_transaksi')->date()->label('Tanggal'),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')->label('Kategori'),
                Tables\Columns\TextColumn::make('deskripsi'),
                Tables\Columns\TextColumn::make('jenis')->badge()->color(fn(string $state) => match($state){
                    'pemasukan' => 'success',
                    'pengeluaran' => 'danger'
                }),
                Tables\Columns\TextColumn::make('jumlah')->money('IDR')->alignEnd(),
            ]);
    }
}