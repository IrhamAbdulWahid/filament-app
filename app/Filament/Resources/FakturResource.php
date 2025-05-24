<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FakturResource\Pages;
use App\Models\FakturModel;
use App\Models\BarangModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{Section, Repeater, TextInput, DatePicker, Select};
use Filament\Tables\Columns\TextColumn;

class FakturResource extends Resource
{
    protected static ?string $model = FakturModel::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Faktur';
    protected static ?string $pluralModelLabel = 'Faktur';
    protected static ?string $modelLabel = 'Faktur';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Faktur')
                    ->schema([
                        TextInput::make('kode_faktur')->label('Kode Faktur')->required()->maxLength(50),
                        DatePicker::make('tanggal_faktur')->label('Tanggal Faktur')->required(),
                        TextInput::make('kode_customer')->label('Kode Customer')->maxLength(50),
                        Select::make('customer_id')->label('Customer')->relationship('customer', 'nama_customer')->required(),
                        TextInput::make('ket_faktur')->label('Keterangan'),
                    ])
                    ->columns(2),

                Section::make('Detail Faktur')
                    ->schema([
                        Repeater::make('detail')
                            ->relationship('detail')
                            ->label('Barang')
                            ->schema([
                                Select::make('barang_id')
                                    ->label('Barang')
                                    ->relationship('barang', 'nama_barang')
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (!empty($state)) {
                                            $barang = BarangModel::find($state);
                                            if ($barang) {
                                                $set('nama_barang', $barang->nama_barang);
                                                $set('harga', $barang->harga);
                                            }
                                        }
                                    }),

                                TextInput::make('nama_barang')
                                    ->label('Nama Barang')
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('harga')
                                    ->numeric()
                                    ->required()
                                    ->label('Harga')
                                    ->reactive(),

                                TextInput::make('qty')
                                    ->numeric()
                                    ->required()
                                    ->label('Qty')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                        $harga = $get('harga') ?? 0;
                                        $subtotal = $state * $harga;
                                        $set('hasil_qty', $subtotal);
                                        $set('subtotal', $subtotal);
                                    }),

                                TextInput::make('diskon')
                                    ->numeric()
                                    ->label('Diskon'),

                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->label('Subtotal')
                                    ->disabled()
                                    ->dehydrated(),

                                TextInput::make('hasil_qty')
                                    ->numeric()
                                    ->label('Qty x Harga')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel('Tambah Barang'),
                    ]),

                Section::make('Total dan Charge')
                    ->schema([
                        TextInput::make('total')->numeric()->required(),
                        TextInput::make('nominal_charge')->numeric(),
                        TextInput::make('charge')->numeric(),
                        TextInput::make('total_final')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->label('Total Akhir (otomatis)'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_faktur')->searchable(),
                TextColumn::make('tanggal_faktur')->date(),
                TextColumn::make('kode_customer')->label('Kode Cust'),
                TextColumn::make('customer.nama_customer')->label('Nama Customer'),
                TextColumn::make('total')->money('IDR'),
                TextColumn::make('charge')->money('IDR'),
                TextColumn::make('total_final')->label('Total Akhir')->money('IDR'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFakturs::route('/'),
            'create' => Pages\CreateFaktur::route('/create'),
            'edit' => Pages\EditFaktur::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total_final'] = ($data['total'] ?? 0) + ($data['charge'] ?? 0);
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['total_final'] = ($data['total'] ?? 0) + ($data['charge'] ?? 0);
        return $data;
    }
}
