<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RiwayatTransaksiKaryawan;
use App\Models\Kategori;
use App\Models\Transaksi;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CatatTransaksi extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static string $view = 'filament.pages.catat-transaksi';

    protected static ?string $title = 'Catat Transaksi Harian';

    //--- KONTROL AKSES UNTUK KARYAWAN ---//
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('karyawan');
    }

    // Menambahkan tombol aksi di header halaman
    protected function getHeaderActions(): array
    {
        return [
            // Tombol Aksi untuk BUAT PEMASUKAN
            Action::make('buatPemasukan')
                ->label('Buat Pemasukan')
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->action(function (array $data) {
                    $data['user_id'] = Auth::id(); // Otomatis isi user_id
                    $data['jenis'] = 'pemasukan'; // Otomatis isi jenis
                    Transaksi::create($data);

                    Notification::make()
                        ->title('Pemasukan berhasil dicatat')
                        ->success()
                        ->send();
                })
                ->form([
                    Forms\Components\Select::make('kategori_id')
                        ->label('Kategori Pemasukan')
                        ->options(Kategori::all()->pluck('nama_kategori', 'id')) // Sediakan data secara manual
                        ->searchable()
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_transaksi')
                        ->default(now())
                        ->required(),
                    Forms\Components\TextInput::make('jumlah')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    Forms\Components\Textarea::make('deskripsi')
                        ->columnSpanFull(),
                ]),

            // Tombol Aksi untuk BUAT PENGELUARAN
            Action::make('buatPengeluaran')
                ->label('Buat Pengeluaran')
                ->icon('heroicon-o-minus-circle')
                ->color('danger')
                ->action(function (array $data) {
                    $data['user_id'] = Auth::id(); // Otomatis isi user_id
                    $data['jenis'] = 'pengeluaran'; // Otomatis isi jenis
                    Transaksi::create($data);

                    Notification::make()
                        ->title('Pengeluaran berhasil dicatat')
                        ->success()
                        ->send();
                })
                ->form([
                    Forms\Components\Select::make('kategori_id')
                        ->label('Kategori Pemasukan')
                        ->options(Kategori::all()->pluck('nama_kategori', 'id')) // Sediakan data secara manual
                        ->searchable()
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_transaksi')
                        ->default(now())
                        ->required(),
                    Forms\Components\TextInput::make('jumlah')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    Forms\Components\Textarea::make('deskripsi')
                        ->columnSpanFull(),
                ]),
        ];
    }

    // Menampilkan widget tabel riwayat di bawah header
    protected function getHeaderWidgets(): array
    {
        return [
            RiwayatTransaksiKaryawan::class,
        ];
    }
}