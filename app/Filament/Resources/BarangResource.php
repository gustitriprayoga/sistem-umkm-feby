<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube'; // Icon yang lebih sesuai

    protected static ?string $navigationGroup = 'Kelola Barang';

    public static function getEloquentQuery(): Builder
    {
        // Bagian ini sudah benar
        return parent::getEloquentQuery()->with(['latestDistribusi.agen']);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }

    // [1] INI ADALAH BAGIAN YANG SEPENUHNYA DIPERBAIKI
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('nama_barang')
                        ->required()
                        ->maxLength(255),

                    // [2] Form ini harusnya untuk memilih KATEGORI barang
                    Forms\Components\Select::make('kategori_id')
                        ->relationship('kategori', 'nama_kategori')
                        ->searchable()
                        ->preload()
                        ->required(),

                    // [3] Form untuk HARGA dasar barang
                    Forms\Components\TextInput::make('harga')
                        ->label('Harga Jual Satuan')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),

                    // [4] Field STOK sebaiknya hanya bisa dibaca (read-only)
                    // Karena nilainya di-update otomatis oleh Distribusi & Transaksi
                    Forms\Components\TextInput::make('stok')
                        ->numeric()
                        ->readOnly()
                        ->default(0)
                        ->helperText('Stok akan bertambah/berkurang otomatis saat ada distribusi atau transaksi.'),

                    Forms\Components\RichEditor::make('deskripsi')
                        ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        // Bagian ini sudah benar
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nama_barang')->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')->sortable(),
                Tables\Columns\TextColumn::make('latestDistribusi.agen.name')
                    ->label('Penyetor Terakhir')
                    ->default('Toko Hana')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stok')->sortable(),
                Tables\Columns\TextColumn::make('harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
