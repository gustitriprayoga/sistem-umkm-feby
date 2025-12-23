<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermintaanPembelianResource\Pages;
use App\Models\PermintaanPembelian;
use App\Models\DetailPermintaanPembelian; // Pastikan Model ini di-import
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;

class PermintaanPembelianResource extends Resource
{
    protected static ?string $model = PermintaanPembelian::class;

    protected static ?string $navigationGroup = 'Kelola Pesanan';

    protected static ?string $label = 'Request Pembelian';

    public static function canCreate(): bool
    {
        return auth()->user()->hasRole(['pemilik', 'karyawan']);
    }

    // Filter Query: Agen hanya melihat datanya sendiri
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('agen')) {
            $query->where('agen_id', auth()->id());
        }

        return $query;
    }

    // FORM UTAMA (Tampilan saat Create / Edit / View oleh Admin)
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
                            ->required()
                            ->disabled(fn($record) => $record && $record->status !== 'diminta'), // Kunci jika sudah diproses

                        Forms\Components\DatePicker::make('tanggal_permintaan')
                            ->default(now())
                            ->required()
                            ->disabled(fn($record) => $record && $record->status !== 'diminta'),
                    ]),

                Forms\Components\Section::make('Daftar Barang yang Diminta')
                    ->schema([
                        Forms\Components\Repeater::make('details')
                            ->relationship()
                            ->label('Item Barang')
                            ->columns(3)
                            ->schema([
                                // --- KOLOM INPUT ASLI (Admin) ---
                                Forms\Components\TextInput::make('nama_barang')
                                    ->required()
                                    ->disabled(fn($record) => $record && $record->permintaanPembelian->status !== 'diminta'),

                                Forms\Components\TextInput::make('jumlah')
                                    ->numeric()
                                    ->required()
                                    ->disabled(fn($record) => $record && $record->permintaanPembelian->status !== 'diminta'),

                                Forms\Components\TextInput::make('satuan')
                                    ->required()
                                    ->placeholder('Contoh: kg, box, liter')
                                    ->disabled(fn($record) => $record && $record->permintaanPembelian->status !== 'diminta'),

                                // --- KOLOM HASIL VALIDASI (TAMPILAN READ-ONLY UNTUK ADMIN) ---
                                // Group ini hanya muncul jika status request bukan lagi 'diminta' (sudah diproses agen)
                                Forms\Components\Group::make([
                                    ToggleButtons::make('status_barang')
                                        ->label('Respon Agen')
                                        ->options([
                                            'diajukan' => 'Menunggu',
                                            'ada' => 'Ada / Tersedia',
                                            'kosong' => 'Kosong / Ditolak',
                                        ])
                                        ->colors([
                                            'diajukan' => 'gray',
                                            'ada' => 'success',
                                            'kosong' => 'danger',
                                        ])
                                        ->icons([
                                            'diajukan' => 'heroicon-o-clock',
                                            'ada' => 'heroicon-o-check-circle',
                                            'kosong' => 'heroicon-o-x-circle',
                                        ])
                                        ->inline()
                                        ->default('diajukan')
                                        ->disabled() // Admin & Agen di view ini hanya Read-Only
                                        ->dehydrated(false),

                                    Forms\Components\TextInput::make('catatan_barang')
                                        ->label('Alasan Penolakan')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->visible(fn(Get $get) => $get('status_barang') === 'kosong'),
                                ])
                                    ->columnSpanFull()
                                    // Logika Visible: Tampil jika record ada DAN status utamanya bukan 'diminta'
                                    ->visible(
                                        fn($record) =>
                                        $record &&
                                            $record->permintaanPembelian &&
                                            $record->permintaanPembelian->status !== 'diminta'
                                    ),
                            ]),
                    ]),

                Forms\Components\Section::make('Catatan Akhir')
                    ->schema([
                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Catatan Awal (Dari Admin)')
                            ->columnSpanFull()
                            ->disabled(fn($record) => $record && $record->status !== 'diminta'),

                        Forms\Components\Textarea::make('catatan_agen')
                            ->label('Balasan / Catatan dari Agen')
                            ->columnSpanFull()
                            ->disabled() // Read Only di form utama
                            ->visible(fn($record) => ($record && $record->catatan_agen) || ($record && $record->status !== 'diminta')),
                    ]),
            ]);
    }

    // TABLE UTAMA
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('agen.name')
                    ->label('Agen Pemasok')
                    ->searchable()
                    ->hidden(fn() => auth()->user()->hasRole('agen')), // Sembunyikan kolom agen bagi si agen sendiri
                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Dibuat Oleh')
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
                    ->limit(30),
            ])
            ->defaultSort('tanggal_permintaan', 'desc')
            ->actions([
                // 1. TOMBOL LIHAT (Untuk melihat detail request & respon agen)
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail'),

                // 2. TOMBOL EDIT (Hanya Admin, jika status masih 'diminta')
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->user()->can('update_permintaan::pembelian', $record) && $record->status === 'diminta'),

                // 3. TOMBOL PROSES (KHUSUS AGEN - INI LOGIKA VALIDASINYA)
                Tables\Actions\Action::make('updateStatus')
                    ->label('Proses / Validasi Stok')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    // A. LOAD DATA SAAT MODAL DIBUKA
                    ->mountUsing(function (Forms\ComponentContainer $form, PermintaanPembelian $record) {
                        $form->fill([
                            'status' => $record->status,
                            'catatan_agen' => $record->catatan_agen,
                            // Load detail barang ke repeater sementara 'details_review'
                            'details_review' => $record->details->map(function ($item) {
                                return [
                                    'id' => $item->id,
                                    'nama_barang' => $item->nama_barang,
                                    'jumlah' => $item->jumlah,
                                    'satuan' => $item->satuan,
                                    'status_barang' => $item->status_barang ?? 'diajukan', // Default agar tidak error
                                    'catatan_barang' => $item->catatan_barang,
                                ];
                            })->toArray(),
                        ]);
                    })
                    // B. DEFINISI FORM DALAM MODAL
                    ->form([
                        Forms\Components\Section::make('Validasi Ketersediaan Barang')
                            ->schema([
                                Forms\Components\Repeater::make('details_review')
                                    ->label('Daftar Permintaan Barang')
                                    ->schema([
                                        // Hidden ID sangat penting untuk referensi update nanti
                                        Forms\Components\Hidden::make('id'),

                                        // Info Barang (Read Only)
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('nama_barang')->disabled()->dehydrated(false),
                                            Forms\Components\TextInput::make('jumlah')->disabled()->inlineLabel()->suffix(fn($get) => $get('satuan'))->dehydrated(false),
                                        ])->columns(2),

                                        // Input Validasi Agen
                                        Forms\Components\Group::make([
                                            ToggleButtons::make('status_barang')
                                                ->label('Ketersediaan')
                                                ->options([
                                                    'diajukan' => 'Menunggu',
                                                    'ada' => 'Ada',
                                                    'kosong' => 'Kosong',
                                                ])
                                                ->colors([
                                                    'diajukan' => 'gray',
                                                    'ada' => 'success',
                                                    'kosong' => 'danger',
                                                ])
                                                ->icons([
                                                    'diajukan' => 'heroicon-o-clock',
                                                    'ada' => 'heroicon-o-check',
                                                    'kosong' => 'heroicon-o-x-mark',
                                                ])
                                                ->inline()
                                                ->default('diajukan')
                                                ->required()
                                                ->live(), // Wajib Live

                                            Forms\Components\TextInput::make('catatan_barang')
                                                ->label('Alasan (Wajib jika Kosong)')
                                                ->placeholder('Contoh: Stok habis')
                                                ->visible(fn(Get $get) => $get('status_barang') === 'kosong')
                                                ->required(fn(Get $get) => $get('status_barang') === 'kosong'),
                                        ])->columns(1),
                                    ])
                                    ->addable(false)     // Agen tidak boleh nambah barang
                                    ->deletable(false)   // Agen tidak boleh hapus barang
                                    ->reorderable(false) // Agen tidak boleh ubah urutan
                                    ->columns(1)
                            ]),

                        Forms\Components\Section::make('Konfirmasi Akhir')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('Update Status Keseluruhan')
                                    ->options([
                                        'diproses' => 'Diproses',
                                        'selesai' => 'Selesai',
                                        'dibatalkan' => 'Tolak Semua',
                                    ])
                                    ->required(),
                                Forms\Components\Textarea::make('catatan_agen')
                                    ->label('Catatan Tambahan'),
                            ])
                    ])
                    // C. EKSEKUSI PENYIMPANAN DATA
                    ->action(function (PermintaanPembelian $record, array $data) {
                        // 1. Update Status Utama
                        $record->update([
                            'status' => $data['status'],
                            'catatan_agen' => $data['catatan_agen'] ?? null,
                        ]);

                        // 2. Loop dan Update Detail Barang
                        if (!empty($data['details_review'])) {
                            foreach ($data['details_review'] as $itemData) {
                                // Pastikan ID ada
                                if (!empty($itemData['id'])) {
                                    $detail = DetailPermintaanPembelian::find($itemData['id']);

                                    if ($detail) {
                                        // Reset catatan jadi null jika status berubah jadi 'ada'/'diajukan'
                                        $catatan = ($itemData['status_barang'] === 'kosong')
                                            ? ($itemData['catatan_barang'] ?? null)
                                            : null;

                                        // Update ke database
                                        $detail->update([
                                            'status_barang' => $itemData['status_barang'],
                                            'catatan_barang' => $catatan,
                                        ]);
                                    }
                                }
                            }
                        }

                        Notification::make()
                            ->title('Validasi berhasil disimpan')
                            ->success()
                            ->send();
                    })
                    // Tombol ini hanya muncul untuk role 'agen'
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
