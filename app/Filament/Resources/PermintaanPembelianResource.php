<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermintaanPembelianResource\Pages;
use App\Models\PermintaanPembelian;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\ToggleButtons; // Pastikan ini ada

class PermintaanPembelianResource extends Resource
{
    protected static ?string $model = PermintaanPembelian::class;

    protected static ?string $navigationGroup = 'Kelola Pesanan';

    protected static ?string $label = 'Request Pembelian';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('agen')) {
            $query->where('agen_id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Request')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('agen_id')
                            ->label('Request ke Agen (Pemasok)')
                            ->options(User::whereHas('roles', fn($q) => $q->where('name', 'agen'))->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_permintaan')
                            ->default(now())
                            ->required(),
                    ]),

                Forms\Components\Section::make('Daftar Barang yang Diminta')
                    ->schema([
                        Forms\Components\Repeater::make('details')
                            ->relationship()
                            ->label('Item Barang')
                            ->columns(3)
                            ->schema([
                                Forms\Components\TextInput::make('nama_barang')
                                    ->required(),
                                Forms\Components\TextInput::make('jumlah')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('satuan')
                                    ->required()
                                    ->placeholder('Contoh: kg, box, liter'),
                            ])
                            ->columnSpanFull()
                            ->defaultItems(1),
                    ]),

                Forms\Components\Textarea::make('catatan_admin')
                    ->label('Catatan untuk Agen')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID Request')
                    ->sortable(),
                Tables\Columns\TextColumn::make('agen.name')
                    ->label('Agen Pemasok')
                    ->searchable()
                    ->hidden(fn() => auth()->user()->hasRole('agen')),
                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Dibuat Oleh (Admin)')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_permintaan')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diminta' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('catatan_agen')
                    ->label('Catatan Agen')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal_permintaan', 'desc')
            ->actions([
                // AKSI UNTUK ADMIN
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->user()->can('update_permintaan::pembelian', $record)),

                // AKSI UNTUK AGEN: VALIDASI STOK PER BARANG
                Tables\Actions\Action::make('updateStatus')
                    ->label('Proses / Validasi Stok')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    // 1. SAAT TOMBOL DITEKAN: Ambil data barang (details) dari database ke dalam form
                    ->mountUsing(function (Forms\ComponentContainer $form, PermintaanPembelian $record) {
                        $form->fill([
                            'status' => $record->status,
                            'catatan_agen' => $record->catatan_agen,
                            // Kita load data 'details' agar muncul di Repeater
                            'details_review' => $record->details->map(function ($item) {
                                return [
                                    'id' => $item->id, // PENTING: ID untuk update nanti
                                    'nama_barang' => $item->nama_barang,
                                    'jumlah' => $item->jumlah,
                                    'satuan' => $item->satuan,
                                    'status_barang' => $item->status_barang,
                                    'catatan_barang' => $item->catatan_barang,
                                ];
                            })->toArray(),
                        ]);
                    })
                    ->form([
                        Forms\Components\Section::make('Validasi Ketersediaan Barang')
                            ->schema([
                                // 2. REPEATER KHUSUS UNTUK REVIEW (Tidak bisa tambah/hapus baris)
                                Forms\Components\Repeater::make('details_review')
                                    ->label('Daftar Permintaan Barang')
                                    ->schema([
                                        // Hidden ID untuk referensi update
                                        Forms\Components\Hidden::make('id'),

                                        // Info Barang (Disabled / Read Only)
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('nama_barang')
                                                ->disabled()
                                                ->dehydrated(false),
                                            Forms\Components\TextInput::make('jumlah')
                                                ->label('Qty')
                                                ->disabled()
                                                ->inlineLabel()
                                                ->suffix(fn($get) => $get('satuan'))
                                                ->dehydrated(false),
                                        ])->columns(2),

                                        // Input Status Agen
                                        Forms\Components\Group::make([
                                            // MENGGUNAKAN TOGGLE BUTTONS (FIX ERROR SELECT::COLORS)
                                            ToggleButtons::make('status_barang')
                                                ->label('Ketersediaan')
                                                ->options([
                                                    'ada' => 'Ada',
                                                    'kosong' => 'Kosong',
                                                ])
                                                ->colors([
                                                    'ada' => 'success',
                                                    'kosong' => 'danger',
                                                ])
                                                ->icons([
                                                    'ada' => 'heroicon-o-check',
                                                    'kosong' => 'heroicon-o-x-mark',
                                                ])
                                                ->inline()
                                                ->default('diajukan')
                                                ->required()
                                                ->live(),

                                            Forms\Components\TextInput::make('catatan_barang')
                                                ->label('Alasan (Jika Kosong)')
                                                ->placeholder('Stok habis / Discontinue')
                                                ->visible(fn(Forms\Get $get) => $get('status_barang') === 'kosong')
                                                ->required(fn(Forms\Get $get) => $get('status_barang') === 'kosong'),
                                        ])->columns(1),
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->columns(1)
                            ]),

                        Forms\Components\Section::make('Konfirmasi Akhir')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Update Status Keseluruhan')
                                    ->options([
                                        'diproses'   => 'Diproses (Sebagian/Semua Ada)',
                                        'selesai'    => 'Selesai (Barang Dikirim)',
                                        'dibatalkan' => 'Tolak Semua (Batalkan Order)',
                                    ])
                                    ->required(),

                                Forms\Components\Textarea::make('catatan_agen')
                                    ->label('Catatan Tambahan (Opsional)')
                            ])
                    ])
                    // 3. LOGIKA PENYIMPANAN DATA
                    ->action(function (PermintaanPembelian $record, array $data) {
                        // A. Update Status Utama
                        $record->update([
                            'status' => $data['status'],
                            'catatan_agen' => $data['catatan_agen'],
                        ]);

                        // B. Loop dan Update Status PER ITEM
                        foreach ($data['details_review'] as $itemData) {
                            $detail = \App\Models\DetailPermintaanPembelian::find($itemData['id']);

                            if ($detail) {
                                $detail->update([
                                    'status_barang' => $itemData['status_barang'],
                                    'catatan_barang' => $itemData['catatan_barang'] ?? null,
                                ]);
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Validasi stok berhasil disimpan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn() => auth()->user()->hasRole('agen')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermintaanPembelians::route('/'),
            'create' => Pages\CreatePermintaanPembelian::route('/create'),
            'edit' => Pages\EditPermintaanPembelian::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = auth()->id();
        return $data;
    }
}
