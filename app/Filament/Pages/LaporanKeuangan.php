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

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static string $view = 'filament.pages.laporan-keuangan';
    protected static ?string $title = 'Laporan Keuangan';

    // Properti publik untuk menyimpan state filter
    public ?array $data = [];

    // Fungsi untuk membatasi akses hanya untuk 'Pemilik'
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }

    // Mengatur nilai default filter saat halaman pertama kali dibuka
    public function mount(): void
    {
        $this->form->fill([
            'tanggal_mulai' => now()->startOfMonth(),
            'tanggal_selesai' => now()->endOfMonth(),
        ]);

        $this->data = $this->form->getState();
    }

    // Mendefinisikan elemen-elemen form filter
    public function form(Form $form): Form
    {
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

    // Fungsi yang dijalankan saat tombol "Terapkan Filter" ditekan
    public function submit(): void
    {
        $this->data = $this->form->getState();
    }

    // Mendaftarkan widget statistik (atas)
    protected function getHeaderWidgets(): array
    {
        return [
            LaporanStatsOverview::class,
        ];
    }

    // Mendaftarkan widget tabel (bawah)
    protected function getFooterWidgets(): array
    {
        return [
            LaporanTransaksiTable::class,
        ];
    }
}