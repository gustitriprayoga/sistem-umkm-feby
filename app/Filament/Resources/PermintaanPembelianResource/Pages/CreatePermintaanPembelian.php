<?php

namespace App\Filament\Resources\PermintaanPembelianResource\Pages;

use App\Filament\Resources\PermintaanPembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePermintaanPembelian extends CreateRecord
{
    protected static string $resource = PermintaanPembelianResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set 'admin_id' dengan ID user yang sedang login
        $data['admin_id'] = auth()->id();

        return $data;
    }

    // 1. Kita override form bawaan agar bisa input banyak Request sekaligus
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Buat Banyak Request')
                    ->description('Buat permintaan ke beberapa agen sekaligus.')
                    ->schema([
                        // REPEATER LUAR: Daftar Permintaan (Surat Jalan)
                        Forms\Components\Repeater::make('requests')
                            ->label('Daftar Permintaan Pembelian')
                            ->schema([
                                // --- Bagian Header Request (Agen & Tanggal) ---
                                Forms\Components\Group::make([
                                    Forms\Components\Select::make('agen_id')
                                        ->label('Tujuan Agen')
                                        ->options(User::whereHas('roles', fn($q) => $q->where('name', 'agen'))->pluck('name', 'id'))
                                        ->searchable()
                                        ->required(),
                                    
                                    Forms\Components\DatePicker::make('tanggal_permintaan')
                                        ->default(now())
                                        ->required(),
                                ])->columns(2),

                                // REPEATER DALAM: Daftar Barang per Request
                                Forms\Components\Repeater::make('details') 
                                    ->label('Item Barang')
                                    ->schema([
                                        Forms\Components\TextInput::make('nama_barang')
                                            ->required(),
                                        Forms\Components\TextInput::make('jumlah')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('satuan')
                                            ->required()
                                            ->placeholder('kg, box, dll'),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(1),

                                Forms\Components\Textarea::make('catatan_admin')
                                    ->label('Catatan')
                                    ->rows(2),
                            ])
                            ->addActionLabel('Tambah Request ke Agen Lain')
                            ->defaultItems(1)
                    ])
            ]);
    }

    // 2. Kita override cara penyimpanannya karena struktur datanya kompleks (Nested)
    protected function handleRecordCreation(array $data): Model
    {
        // Ambil data dari Repeater Luar
        $requests = $data['requests'];
        
        $lastRecord = null;

        // Gunakan Transaksi Database agar aman
        DB::transaction(function () use ($requests, &$lastRecord) {
            foreach ($requests as $requestData) {
                
                // A. Simpan Data Bapak (PermintaanPembelian)
                // Kita harus memisahkan data 'details' (anak) dari data bapak
                $parentData = collect($requestData)->except('details')->toArray();
                
                // Masukkan admin_id secara manual (karena kita bypass mutateFormDataBeforeCreate standar)
                $parentData['admin_id'] = auth()->id();
                $parentData['status'] = 'diminta'; // Default status

                $permintaan = static::getModel()::create($parentData);

                // B. Simpan Data Anak (DetailPermintaanPembelian)
                if (isset($requestData['details'])) {
                    // Filament Repeater mengembalikan array, kita loop untuk simpan ke relasi
                    foreach ($requestData['details'] as $detailItem) {
                        $permintaan->details()->create($detailItem);
                    }
                }

                $lastRecord = $permintaan;
            }
        });

        return $lastRecord;
    }

    // 3. Redirect ke halaman List setelah selesai
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
