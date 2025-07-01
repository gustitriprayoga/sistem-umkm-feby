<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistribusiResource\Pages;
use App\Filament\Resources\DistribusiResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canViewAny(): bool
    {
        return auth()->user()->hasRole('pemilik');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('agen_id')
                    ->relationship('agen', 'name')
                    ->required(),
                Forms\Components\TextInput::make('nama_barang')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jumlah_barang')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_setor')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agen.name')
                    ->label('Nama Agen')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_barang')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_setor')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('keterangan')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDistribusis::route('/'),
            'create' => Pages\CreateDistribusi::route('/create'),
            'edit' => Pages\EditDistribusi::route('/{record}/edit'),
        ];
    }
}
