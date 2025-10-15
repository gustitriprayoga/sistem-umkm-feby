<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RiwayatTransaksiKaryawan;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Transaksi;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CatatTransaksiKaryawan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static string $view = 'filament.pages.catat-transaksi-karyawan';
    protected static ?string $title = 'Catat Transaksi';
    protected static ?string $navigationGroup = 'Aktivitas Toko';
    protected static ?int $navigationSort = 1;

    // Hanya Karyawan yang bisa mengakses halaman ini
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('karyawan');
    }

    // Mendefinisikan tombol-tombol aksi di header halaman
    protected function getHeaderActions(): array
    {
        return [
            // [AKSI 1: CATAT PEMASUKAN / PENJUALAN]
            Action::make('catatPemasukan')
                ->label('Catat Pemasukan (Penjualan)')
                ->icon('heroicon-o-plus-circle')
                ->modalWidth('2xl')
                ->action(function (array $data) {
                    try {
                        DB::transaction(function () use ($data) {
                            $barang = Barang::find($data['barang_id']);

                            // 1. Kurangi stok barang
                            $barang->decrement('stok', $data['jumlah_barang']);

                            // 2. Buat catatan transaksi
                            Transaksi::create([
                                'user_id' => Auth::id(),
                                'kategori_id' => $barang->kategori_id,
                                'barang_id' => $data['barang_id'],
                                'jumlah_barang' => $data['jumlah_barang'],
                                'jumlah' => $data['jumlah'], // Total uang
                                'jenis' => 'pemasukan',
                                'tanggal_transaksi' => $data['tanggal_transaksi'],
                                'deskripsi' => 'Penjualan: ' . $barang->nama_barang,
                            ]);
                        });

                        Notification::make()->title('Pemasukan berhasil dicatat!')->success()->send();
                        $this->dispatch('transaksiUpdated');
                    } catch (\Exception $e) {
                        Notification::make()->title('Gagal mencatat pemasukan')->body($e->getMessage())->danger()->send();
                    }
                })
                ->form([
                    Forms\Components\Select::make('barang_id')
                        ->label('Pilih Barang')
                        ->options(Barang::where('stok', '>', 0)->pluck('nama_barang', 'id'))
                        ->searchable()
                        ->required()
                        ->live() // Menggunakan live() agar reaktif
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            $barang = Barang::find($state);
                            if ($barang) {
                                $set('harga_satuan', $barang->harga);
                            }
                        }),

                    Forms\Components\TextInput::make('harga_satuan')
                        ->prefix('Rp')
                        ->readOnly()
                        ->numeric(),

                    Forms\Components\TextInput::make('jumlah_barang')
                        ->label('Jumlah Terjual')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->maxValue(function (Get $get) {
                            $barang = Barang::find($get('barang_id'));
                            return $barang ? $barang->stok : 1;
                        })
                        ->live(debounce: 500) // Debounce agar tidak terlalu sering kalkulasi
                        ->afterStateUpdated(function ($state, Get $get, Forms\Set $set) {
                            $total = $state * $get('harga_satuan');
                            $set('jumlah', $total);
                        }),

                    Forms\Components\TextInput::make('jumlah')
                        ->label('Total Harga')
                        ->prefix('Rp')
                        ->readOnly()
                        ->numeric(),

                    Forms\Components\DatePicker::make('tanggal_transaksi')
                        ->default(now())
                        ->required(),
                ]),

            // [AKSI 2: CATAT PENGELUARAN / BIAYA]
            Action::make('catatPengeluaran')
                ->label('Catat Pengeluaran (Biaya)')
                ->icon('heroicon-o-minus-circle')
                ->color('danger')
                ->action(function (array $data) {
                    $data['user_id'] = Auth::id();
                    $data['jenis'] = 'pengeluaran';
                    Transaksi::create($data);

                    Notification::make()->title('Pengeluaran berhasil dicatat!')->success()->send();
                    $this->dispatch('transaksiUpdated');
                })
                ->form([
                    Forms\Components\TextInput::make('jumlah')
                        ->label('Jumlah Pengeluaran')
                        ->prefix('Rp')
                        ->numeric()
                        ->required(),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi Pengeluaran')
                        ->helperText('Contoh: Bayar listrik, Beli ATK, Biaya kebersihan')
                        ->required(),

                    Forms\Components\Select::make('kategori_id')
                        ->label('Kategori Biaya')
                        ->options(Kategori::all()->pluck('nama_kategori', 'id'))
                        ->searchable()
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal_transaksi')
                        ->default(now())
                        ->required(),
                ]),
        ];
    }

    // Mendaftarkan widget riwayat transaksi
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    // [2] TAMBAHKAN METHOD BARU INI
    // Daftarkan widget Anda di sini
    protected function getFooterWidgets(): array
    {
        return [
            RiwayatTransaksiKaryawan::class,
        ];
    }
}
