<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermintaanPembelianResource\Pages;
use App\Models\PermintaanPembelian;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PermintaanPembelianResource extends Resource
{
    protected static ?string $model = PermintaanPembelian::class;

    protected static ?string $navigationGroup = 'Kelola Pesanan';

    protected static ?string $label = 'Request Pembelian';

    /**
     * INI ADALAH LOGIKA UTAMA UNTUK AGEN
     * Memodifikasi kueri utama. Jika yang login adalah 'agen',
     * maka hanya tampilkan request yang ditujukan untuknya.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('agen')) {
            $query->where('agen_id', auth()->id());
        }

        // Admin/Pemilik akan melihat semua.
        return $query;
    }

    /**
     * Form ini digunakan oleh ADMIN untuk membuat request
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Request')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('agen_id')
                            ->label('Request ke Agen (Pemasok)')
                            // Mengambil data user yang HANYA memiliki peran 'agen'
                            ->options(User::whereHas('roles', fn($q) => $q->where('name', 'agen'))->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_permintaan')
                            ->default(now())
                            ->required(),
                        // 'admin_id' akan diisi secara otomatis di bawah
                    ]),

                Forms\Components\Section::make('Daftar Barang yang Diminta')
                    ->schema([
                        Forms\Components\Repeater::make('details') // 'details' adalah nama relasi
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
                    // Sembunyikan kolom ini jika yang login adalah Agen (karena pasti dirinya sendiri)
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
                Tables\Columns\TextColumn::make('details_count')
                    ->label('Jumlah Item')
                    ->counts('details')
                    ->sortable(),
            ])
            ->defaultSort('tanggal_permintaan', 'desc')
            ->actions([
                // AKSI UNTUK ADMIN: Bisa edit full request
                Tables\Actions\EditAction::make()
                    // Hanya tampil jika user BISA 'update' (diatur di Shield)
                    ->visible(fn($record) => auth()->user()->can('update_permintaan::pembelian', $record)),

                // AKSI UNTUK AGEN: Hanya bisa ubah status
                Tables\Actions\Action::make('updateStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-check-circle')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'diproses' => 'Diproses',
                                'selesai' => 'Selesai',
                            ])
                            ->default(fn($record) => $record->status)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update($data);
                    })
                    // Hanya tampil jika yang login adalah AGEN
                    ->visible(fn() => auth()->user()->hasRole('agen')),

                Tables\Actions\ViewAction::make(), // Tampil untuk semua
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermintaanPembelians::route('/'),
            'create' => Pages\CreatePermintaanPembelian::route('/create'),
            // 'view' => Pages\ViewPermintaanPembelian::route('/{record}'),
            'edit' => Pages\EditPermintaanPembelian::route('/{record}/edit'),
        ];
    }

    /**
     * Logika untuk mengisi 'admin_id' secara otomatis saat membuat.
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        // Set pembuat request adalah user yang sedang login
        $data['admin_id'] = auth()->id();
        return $data;
    }
}
