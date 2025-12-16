<?php

namespace App\Filament\Resources\BarangResource\Pages;

use App\Filament\Resources\BarangResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;

    // Kita menonaktifkan form standar Resource dan menggantinya dengan form khusus halaman ini
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Input Banyak Barang')
                    ->description('Anda dapat menambahkan lebih dari satu barang sekaligus.')
                    ->schema([
                        // Menggunakan Repeater untuk input berulang
                        Forms\Components\Repeater::make('items')
                            ->label('Daftar Barang')
                            ->schema([
                                // --- Mulai: Field dari BarangResource Anda ---
                                Forms\Components\TextInput::make('nama_barang')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\Select::make('kategori_id')
                                    ->relationship('kategori', 'nama_kategori')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([ // Opsional: Agar bisa tambah kategori langsung
                                        Forms\Components\TextInput::make('nama_kategori')->required(),
                                    ]),

                                Forms\Components\TextInput::make('harga')
                                    ->label('Harga Jual Satuan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),

                                Forms\Components\TextInput::make('stok')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Stok awal.'),
                                
                                Forms\Components\RichEditor::make('deskripsi')
                                    ->columnSpanFull(),
                                // --- Selesai: Field ---
                            ])
                            ->columns(2)
                            ->defaultItems(1) // Munculkan 1 form di awal
                            ->grid(2) // Opsional: Tampilan grid agar rapi
                            ->addActionLabel('Tambah Barang Lain'),
                    ])
            ]);
    }

    // Fungsi ini menangani logika penyimpanan ke database
    protected function handleRecordCreation(array $data): Model
    {
        // $data['items'] berisi array dari semua barang yang diinput di Repeater
        $items = $data['items'];

        $record = null;

        // Gunakan Transaksi Database agar aman (jika satu gagal, semua batal)
        DB::transaction(function () use ($items, &$record) {
            foreach ($items as $item) {
                // Simpan setiap item sebagai record baru
                $record = static::getModel()::create($item);
            }
        });

        // Kembalikan record terakhir (diperlukan oleh Filament untuk redirect)
        return $record;
    }

    // Setelah simpan, arahkan kembali ke tabel list barang (bukan view barang)
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}