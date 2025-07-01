<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class LaporanTransaksiTable extends BaseWidget
{
    protected static ?string $heading = 'Detail Transaksi';

    // --- PERUBAHAN DI SINI: Tambahkan baris ini ---
    protected int | string | array $columnSpan = 'full';

    public ?array $filters = [];

    #[On('updateLaporanFilter')]
    public function updateFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    public function table(Table $table): Table
    {
        $filters = $this->filters;
        $tanggalMulai = Arr::get($filters, 'tanggal_mulai', 'awal');
        $tanggalSelesai = Arr::get($filters, 'tanggal_selesai', 'akhir');
        $fileName = "laporan-keuangan-{$tanggalMulai}-sampai-{$tanggalSelesai}";

        return $table
            ->query(
                Transaksi::query()
                    ->when($tanggalMulai, fn($q) => $q->whereDate('tanggal_transaksi', '>=', $tanggalMulai))
                    ->when($tanggalSelesai, fn($q) => $q->whereDate('tanggal_transaksi', '<=', $tanggalSelesai))
            )
            ->headerActions([
                ExportAction::make()
                    ->label('Export Excel')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename($fileName)
                            ->withColumns([
                                Column::make('tanggal_transaksi')->heading('Tanggal Transaksi'),
                                Column::make('jenis')->heading('Jenis'),
                                Column::make('kategori.nama_kategori')->heading('Kategori'),
                                Column::make('jumlah')->heading('Jumlah'),
                                Column::make('deskripsi')->heading('Deskripsi'),
                                Column::make('user.name')->heading('Dicatat Oleh'),
                            ])
                    ])
            ])
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