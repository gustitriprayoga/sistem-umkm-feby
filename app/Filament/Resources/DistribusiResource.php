<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistribusiResource\Pages;
use App\Models\Barang; // [1] Import model Barang
use App\Models\Distribusi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DistribusiResource extends Resource
{
    protected static ?string $model = Distribusi::class;

    // protected static ?string $navigationIcon = 'heroicon-o-truck'; // Ganti icon agar lebih relevan

    protected static ?string $navigationGroup = 'Kelola Distribusi';

    // [2] Eager load relasi untuk performa yang lebih baik di tabel
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['agen', 'barang']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('agen_id')
                        ->relationship('agen', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    // [3] GANTI TextInput 'nama_barang' menjadi Select 'barang_id'
                    Forms\Components\Select::make('barang_id')
                        ->relationship('barang', 'nama_barang')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive() // Membuat field ini reaktif
                        ->afterStateUpdated(function ($state, callable $set) {
                            // Ambil data barang berdasarkan ID yang dipilih
                            $barang = Barang::find($state);
                            if ($barang) {
                                // Set nilai harga_satuan secara otomatis
                                $set('harga_satuan', $barang->harga);
                            }
                        }),

                    // [4] Tambahkan field untuk harga satuan dan total
                    Forms\Components\TextInput::make('harga_satuan')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->reactive() // Reaktif untuk kalkulasi total
                        // Nonaktifkan field ini agar tidak bisa diubah manual
                        ->disabled(),

                    Forms\Components\TextInput::make('jumlah_barang')
                        ->required()
                        ->numeric()
                        ->reactive() // Reaktif untuk kalkulasi total
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            // Kalkulasi total harga otomatis: jumlah * harga satuan
                            $set('total_harga', $state * $get('harga_satuan'));
                        }),

                    Forms\Components\TextInput::make('total_harga')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->disabled(), // Nonaktifkan field ini

                    Forms\Components\DatePicker::make('tanggal_setor')
                        ->required()
                        ->default(now()),

                    Forms\Components\Textarea::make('keterangan')
                        ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agen.name')
                    ->label('Nama Agen')
                    ->searchable()
                    ->sortable(),

                // [5] Tampilkan nama barang dari relasi
                Tables\Columns\TextColumn::make('barang.nama_barang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_barang')
                    ->numeric()
                    ->sortable(),

                // [6] Tampilkan harga dengan format mata uang
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_setor')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(), // Tambahkan aksi delete
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDistribusis::route('/'),
            'create' => Pages\CreateDistribusi::route('/create'),
            'edit' => Pages\EditDistribusi::route('/{record}/edit'),
        ];
    }
}
