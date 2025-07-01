<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Arr;
// PERUBAHAN: Tambahkan 'use' untuk listener
use Livewire\Attributes\On;

class LaporanStatsOverview extends BaseWidget
{
    // PERUBAHAN: Tambahkan properti untuk menyimpan filter
    public ?array $filters = [];

    // PERUBAHAN: Tambahkan listener untuk event 'updateLaporanFilter'
    #[On('updateLaporanFilter')]
    public function updateFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    protected function getStats(): array
    {
        // PERUBAHAN: Baca dari properti $this->filters
        $tanggalMulai = Arr::get($this->filters, 'tanggal_mulai');
        $tanggalSelesai = Arr::get($this->filters, 'tanggal_selesai');

        $query = Transaksi::query()
            ->when($tanggalMulai, fn($q) => $q->whereDate('tanggal_transaksi', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn($q) => $q->whereDate('tanggal_transaksi', '<=', $tanggalSelesai));

        $totalPemasukan = (clone $query)->where('jenis', 'pemasukan')->sum('jumlah');
        $totalPengeluaran = (clone $query)->where('jenis', 'pengeluaran')->sum('jumlah');
        $keuntungan = $totalPemasukan - $totalPengeluaran;

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalPemasukan))
                ->description('Total pemasukan pada periode ini')
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalPengeluaran))
                ->description('Total pengeluaran pada periode ini')
                ->color('danger'),
            Stat::make('Keuntungan Bersih', 'Rp ' . number_format($keuntungan))
                ->description('Selisih pemasukan dan pengeluaran')
                ->color('info'),
        ];
    }
}