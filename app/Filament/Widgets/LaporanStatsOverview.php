<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Arr;

class LaporanStatsOverview extends BaseWidget
{
    // Mengambil data filter dari halaman induk dan menghitung statistik
    protected function getStats(): array
    {
        // Menarik data filter dari properti publik 'data' di halaman LaporanKeuangan
        $filters = $this->getPage()->data;

        $tanggalMulai = Arr::get($filters, 'tanggal_mulai');
        $tanggalSelesai = Arr::get($filters, 'tanggal_selesai');

        // Membangun query dengan filter tanggal
        $query = Transaksi::query()
            ->when($tanggalMulai, fn($q) => $q->whereDate('tanggal_transaksi', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn($q) => $q->whereDate('tanggal_transaksi', '<=', $tanggalSelesai));

        // Melakukan kalkulasi
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