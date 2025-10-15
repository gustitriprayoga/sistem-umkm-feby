<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LaporanStatsOverview;
use App\Filament\Widgets\LaporanTransaksiTable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class LaporanKeuangan extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.laporan-keuangan';
    protected static ?string $title = 'Laporan Keuangan';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }

    public function mount(): void
    {
        // Default tetap ke bulan ini
        $this->setFilterBulanan();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Section::make('Filter Laporan')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tanggal_mulai')
                            ->label('Dari Tanggal')
                            ->live()
                            ->afterStateUpdated(fn() => $this->submit()),
                        DatePicker::make('tanggal_selesai')
                            ->label('Sampai Tanggal')
                            ->live()
                            ->afterStateUpdated(fn() => $this->submit()),
                    ]),
            ]);
    }

    public function submit(): void
    {
        $this->data = $this->form->getState();
        $this->dispatch('updateLaporanFilter', filters: $this->data);
    }

    // --- [1] METODE BARU UNTUK TOMBOL FILTER CEPAT ---

    public function setFilterHarian(): void
    {
        $this->form->fill([
            'tanggal_mulai' => now()->today(),
            'tanggal_selesai' => now()->today(),
        ]);
        $this->submit();
    }

    public function setFilterMingguan(): void
    {
        $this->form->fill([
            'tanggal_mulai' => now()->startOfWeek(),
            'tanggal_selesai' => now()->endOfWeek(),
        ]);
        $this->submit();
    }

    public function setFilterBulanan(): void
    {
        $this->form->fill([
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->endOfMonth(),
        ]);
        $this->submit();
    }

    public function setFilterTahunan(): void
    {
        $this->form->fill([
            'tanggal_mulai' => now()->startOfYear(),
            'tanggal_selesai' => now()->endOfYear(),
        ]);
        $this->submit();
    }
    // --- AKHIR METODE BARU ---


    protected function getHeaderWidgets(): array
    {
        return [
            LaporanStatsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            LaporanTransaksiTable::class,
        ];
    }
}
