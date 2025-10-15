<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LaporanTransaksiTable extends BaseWidget
{
    protected static ?string $heading = 'Rincian Transaksi';
    protected int | string | array $columnSpan = 'full';

    // Properti untuk filter tanggal
    public ?string $tanggalMulai = null;
    public ?string $tanggalSelesai = null;

    public static function canView(): bool
    {
        // Hanya izinkan pengguna dengan role 'pemilik' yang bisa melihat widget ini.
        // Pastikan nama role 'pemilik' sudah sesuai dengan yang ada di database Anda.
        return auth()->user()->hasRole('pemilik');
    }

    // Listener untuk event dari halaman Laporan Keuangan
    protected $listeners = ['updateLaporanFilter' => 'applyDateFilter'];

    public function mount(): void
    {
        $this->tanggalMulai = now()->startOfMonth()->toDateString();
        $this->tanggalSelesai = now()->endOfMonth()->toDateString();
    }

    public function applyDateFilter(array $filters): void
    {
        $this->tanggalMulai = $filters['tanggal_mulai'] ?? $this->tanggalMulai;
        $this->tanggalSelesai = $filters['tanggal_selesai'] ?? $this->tanggalSelesai;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaksi::query()
                    ->when($this->tanggalMulai, fn($q) => $q->whereDate('tanggal_transaksi', '>=', Carbon::parse($this->tanggalMulai)))
                    ->when($this->tanggalSelesai, fn($q) => $q->whereDate('tanggal_transaksi', '<=', Carbon::parse($this->tanggalSelesai)))
            )
            ->defaultSort('tanggal_transaksi', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('jenis')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('deskripsi')->searchable(),
                Tables\Columns\TextColumn::make('jumlah')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Dicatat Oleh')->sortable(),
                Tables\Columns\TextColumn::make('tanggal_transaksi')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('jenis')->options(['pemasukan' => 'Pemasukan', 'pengeluaran' => 'Pengeluaran']),
                SelectFilter::make('user_id')->label('Dicatat Oleh')->relationship('user', 'name')->searchable()->preload(),
            ]);
    }
}
