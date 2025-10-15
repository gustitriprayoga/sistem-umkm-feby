<?php

namespace App\Filament\Widgets;

use App\Models\Distribusi;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LaporanDistribusiTable extends BaseWidget
{
    protected static ?string $heading = 'Data Laporan Distribusi';
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        // Hanya izinkan pengguna dengan role 'pemilik' yang bisa melihat widget ini.
        // Pastikan nama role 'pemilik' sudah sesuai dengan yang ada di database Anda.
        return auth()->user()->hasRole('pemilik');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Distribusi::query())
            ->defaultSort('tanggal_setor', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('barang.nama_barang')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('agen.name')->label('Agen Penyetor')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('jumlah_barang')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('harga_satuan')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('total_harga')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('tanggal_setor')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('barang_id')->label('Filter Barang')->relationship('barang', 'nama_barang')->searchable()->preload(),
                SelectFilter::make('agen_id')->label('Filter Agen')->relationship('agen', 'name')->searchable()->preload(),
                Tables\Filters\Filter::make('tanggal_setor')
                    ->form([
                        DatePicker::make('dari_tanggal'),
                        DatePicker::make('sampai_tanggal')->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari_tanggal'], fn(Builder $query, $date): Builder => $query->whereDate('tanggal_setor', '>=', $date))
                            ->when($data['sampai_tanggal'], fn(Builder $query, $date): Builder => $query->whereDate('tanggal_setor', '<=', $date));
                    })
            ]);
    }
}
