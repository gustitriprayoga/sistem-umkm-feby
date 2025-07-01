<?php

namespace App\Filament\Pages;

// use App\Filament\Widgets\RiwayatSetoranAgen as WidgetsRiwayatSetoranAgen;
use App\Filament\Widgets\RiwayatSetoranAgen;
use App\Models\Distribusi;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SetorBarang extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string $view = 'filament.pages.setor-barang';

    protected static ?string $title = 'Setor Barang Distribusi';

    //--- KONTROL AKSES UNTUK AGEN ---//
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('agen');
    }

    // Menambahkan tombol aksi di header halaman
    protected function getHeaderActions(): array
    {
        return [
            // Tombol Aksi untuk SETOR BARANG
            Action::make('setorBarangBaru')
                ->label('Setor Barang Baru')
                ->icon('heroicon-o-plus-circle')
                ->action(function (array $data) {
                    // Otomatis isi agen_id dengan ID user yang sedang login
                    $data['agen_id'] = Auth::id();
                    Distribusi::create($data);

                    Notification::make()
                        ->title('Barang berhasil disetor')
                        ->success()
                        ->send();
                })
                ->form([
                    Forms\Components\TextInput::make('nama_barang')
                        ->required()
                        ->label('Nama Barang'),
                    Forms\Components\TextInput::make('jumlah_barang')
                        ->required()
                        ->numeric()
                        ->label('Jumlah'),
                    Forms\Components\DatePicker::make('tanggal_setor')
                        ->default(now())
                        ->required(),
                    Forms\Components\Textarea::make('keterangan')
                        ->columnSpanFull(),
                ]),
        ];
    }

    // Menampilkan widget tabel riwayat di bawah header
    protected function getHeaderWidgets(): array
    {
        return [
            RiwayatSetoranAgen::class,
        ];
    }
}