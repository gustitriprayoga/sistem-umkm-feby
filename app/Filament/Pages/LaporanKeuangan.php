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
    protected static string $view = 'filament.pages.laporan-keuangan';
    protected static ?string $title = 'Laporan Keuangan';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }


    public function mount(): void
    {
        $this->form->fill([
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->endOfMonth(),
        ]);
        $this->data = $this->form->getState();

        // PERUBAHAN: Kirim event saat halaman pertama kali dimuat
        $this->dispatch('updateLaporanFilter', filters: $this->data);
    }

    public function form(Form $form): Form
    {
        // ... (Tidak ada perubahan di sini) ...
        return $form
            ->statePath('data')
            ->schema([
                Section::make('Filter Laporan')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('tanggal_mulai')
                            ->label('Dari Tanggal'),
                        DatePicker::make('tanggal_selesai')
                            ->label('Sampai Tanggal'),
                    ]),
            ]);
    }

    public function submit(): void
    {
        $this->data = $this->form->getState();

        // PERUBAHAN: Kirim event saat filter diterapkan
        $this->dispatch('updateLaporanFilter', filters: $this->data);
    }

    protected function getHeaderWidgets(): array
    {
        return [LaporanStatsOverview::class];
    }

    protected function getFooterWidgets(): array
    {
        return [LaporanTransaksiTable::class];
    }
}