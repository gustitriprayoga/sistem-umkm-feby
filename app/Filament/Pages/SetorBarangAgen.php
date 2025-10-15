<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\RiwayatSetoranAgen;
use App\Models\Barang;
use App\Models\Distribusi;
use App\Models\Kategori; // [1] IMPORT MODEL KATEGORI
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SetorBarangAgen extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static string $view = 'filament.pages.setor-barang-agen';
    protected static ?string $title = 'Setor Barang';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('agen');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('setorBarangBaru')
                ->label('Setor Barang Baru')
                ->icon('heroicon-o-plus-circle')
                ->modalWidth('2xl')
                ->action(function (array $data) {
                    // Logika action ini sudah benar dan tidak perlu diubah
                    try {
                        DB::transaction(function () use ($data) {
                            $barang = Barang::firstOrCreate(
                                ['nama_barang' => $data['nama_barang']],
                                [
                                    'kategori_id' => $data['kategori_id'],
                                    'harga' => $data['harga_jual'],
                                    'stok' => 0,
                                ]
                            );

                            Distribusi::create([
                                'agen_id' => Auth::id(),
                                'barang_id' => $barang->id,
                                'jumlah_barang' => $data['jumlah_setor'],
                                'harga_satuan' => $data['harga_beli_satuan'],
                                'total_harga' => $data['harga_beli_satuan'] * $data['jumlah_setor'],
                                'tanggal_setor' => $data['tanggal_setor'],
                                'keterangan' => $data['keterangan'],
                            ]);
                        });

                        Notification::make()->title('Barang berhasil disetor')->success()->send();
                        $this->dispatch('setoranUpdated');
                    } catch (\Exception $e) {
                        Notification::make()->title('Terjadi Kesalahan')->body($e->getMessage())->danger()->send();
                    }
                })
                ->form([
                    Forms\Components\Section::make('Detail Barang')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('nama_barang')
                                ->required()
                                ->maxLength(255)
                                ->label('Nama Barang Baru'),

                            // [2] INI ADALAH PERUBAHAN UTAMA
                            Forms\Components\Select::make('kategori_id')
                                ->label('Pilih Kategori Barang')
                                ->options(Kategori::all()->pluck('nama_kategori', 'id')) // Ambil data secara manual
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('harga_jual')
                                ->required()
                                ->numeric()
                                ->prefix('Rp')
                                ->label('Harga Jual Satuan (Untuk Toko)'),
                        ]),

                    Forms\Components\Section::make('Detail Setoran')
                        ->columns(2)
                        ->schema([
                            Forms\Components\TextInput::make('harga_beli_satuan')
                                ->required()
                                ->numeric()
                                ->prefix('Rp')
                                ->label('Harga Beli Satuan (Modal)'),

                            Forms\Components\TextInput::make('jumlah_setor')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->label('Jumlah Barang Disetor'),

                            Forms\Components\DatePicker::make('tanggal_setor')
                                ->default(now())
                                ->required(),

                            Forms\Components\Textarea::make('keterangan')
                                ->columnSpanFull(),
                        ])
                ]),
        ];
    }

    // Mendaftarkan widget riwayat transaksi
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [
            RiwayatSetoranAgen::class,
        ];
    }
}
