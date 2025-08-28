<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    
    protected static ?string $navigationLabel = 'Produk';
     protected static ?string $navigationGroup = 'MANAJEMEN PRODUK';
    protected static ?string $modelLabel = 'Produk';
    
    protected static ?string $pluralModelLabel = 'Produk';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->helperText('URL friendly name (otomatis terisi)'),
                            
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Deskripsi'),
                            ])
                            ->searchable()
                            ->preload(),
                            
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->unique(Product::class, 'sku', ignoreRecord: true)
                            ->helperText('Kosongkan untuk generate otomatis'),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Deskripsi & Gambar')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi Produk')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),
                            
                        Forms\Components\FileUpload::make('images')
                            ->label('Gambar Produk')
                            ->image()
                            ->multiple()
                            ->directory('products')
                            ->visibility('public')
                            ->reorderable()
                            ->maxFiles(5)
                            ->helperText('Maksimal 5 gambar. Gambar pertama akan menjadi gambar utama.')
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('Harga & Stok')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Harga')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->step(500)
                            ->minValue(0),
                            
                        Forms\Components\TextInput::make('stock')
                            ->label('Stok')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                            
                        Forms\Components\TextInput::make('weight')
                            ->label('Berat (gram)')
                            ->numeric()
                            ->suffix('gram')
                            ->step(1)
                            ->minValue(0)
                            ->helperText('Berat produk untuk kalkulasi ongkir'),
                    ])
                    ->columns(3),
                    
                Forms\Components\Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Produk Aktif')
                            ->default(true)
                            ->helperText('Produk tidak aktif tidak akan ditampilkan di frontend'),
                            
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Produk Unggulan')
                            ->default(false)
                            ->helperText('Produk unggulan akan ditampilkan di homepage'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Gambar')
                    ->getStateUsing(function ($record) {
                        return $record->images ? $record->images[0] ?? null : null;
                    })
                    ->defaultImageUrl(url('/images/placeholder.png'))
                    ->circular(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn ($state) => $state > 10 ? 'success' : ($state > 0 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn ($state) => $state . ' pcs'),
                    
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->preload(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua produk')
                    ->trueLabel('Produk aktif')
                    ->falseLabel('Produk tidak aktif'),
                    
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Unggulan')
                    ->placeholder('Semua produk')
                    ->trueLabel('Produk unggulan')
                    ->falseLabel('Produk biasa'),
                    
                Tables\Filters\Filter::make('out_of_stock')
                    ->label('Stok Habis')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '<=', 0))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-mark')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Jadikan Unggulan')
                        ->icon('heroicon-o-star')
                        ->action(fn ($records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
      public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}