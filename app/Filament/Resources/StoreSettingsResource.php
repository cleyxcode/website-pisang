<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreSettingsResource\Pages;
use App\Models\StoreSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StoreSettingsResource extends Resource
{
    protected static ?string $model = StoreSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationLabel = 'Pengaturan Toko';
    
    protected static ?string $navigationGroup = 'PENGATURAN';
    
    protected static ?string $modelLabel = 'Pengaturan Toko';
    
    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Toko')
                    ->description('Nama toko dan lokasi toko pada peta')
                    ->schema([
                        Forms\Components\TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Toko Keripik Pisang')
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Lokasi Toko')
                    ->description('Klik pada peta di bawah untuk menentukan lokasi toko atau masukkan koordinat secara manual')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('-3.6954281')
                                    ->helperText('Contoh: -3.6954281')
                                    ->reactive()
                                    ->afterStateUpdated(fn () => null),
                                
                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step(0.00000001)
                                    ->placeholder('128.1814822')
                                    ->helperText('Contoh: 128.1814822')
                                    ->reactive()
                                    ->afterStateUpdated(fn () => null),
                            ]),
                        
                        // Instruksi cara mendapatkan koordinat
                        Forms\Components\Placeholder::make('instructions')
                            ->label('Cara Mendapatkan Koordinat')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="text-sm space-y-2">
                                    <p><strong>Cara 1: Melalui OpenStreetMap</strong></p>
                                    <ol class="list-decimal list-inside space-y-1 text-gray-600">
                                        <li>Buka <a href="https://www.openstreetmap.org" target="_blank" class="text-primary-600 hover:underline">OpenStreetMap.org</a></li>
                                        <li>Cari lokasi toko Anda</li>
                                        <li>Klik kanan pada lokasi toko</li>
                                        <li>Pilih "Show address"</li>
                                        <li>Salin koordinat yang muncul</li>
                                    </ol>
                                    
                                    <p class="mt-4"><strong>Cara 2: Melalui Google Maps</strong></p>
                                    <ol class="list-decimal list-inside space-y-1 text-gray-600">
                                        <li>Buka <a href="https://www.google.com/maps" target="_blank" class="text-primary-600 hover:underline">Google Maps</a></li>
                                        <li>Cari lokasi toko Anda</li>
                                        <li>Klik kanan pada lokasi</li>
                                        <li>Koordinat akan muncul di bagian atas</li>
                                        <li>Klik untuk menyalin</li>
                                    </ol>
                                </div>
                            ')),
                        
                        // Preview Map (hanya tampil saat edit)
                        Forms\Components\Placeholder::make('map_preview')
                            ->label('Preview Lokasi')
                            ->visible(fn ($record) => $record && $record->latitude && $record->longitude)
                            ->content(fn ($record) => $record && $record->latitude && $record->longitude 
                                ? new \Illuminate\Support\HtmlString('
                                    <div class="rounded-lg overflow-hidden border border-gray-300">
                                        <iframe 
                                            width="100%" 
                                            height="400" 
                                            frameborder="0" 
                                            scrolling="no" 
                                            marginheight="0" 
                                            marginwidth="0" 
                                            src="https://www.openstreetmap.org/export/embed.html?bbox=' . ($record->longitude - 0.01) . '%2C' . ($record->latitude - 0.01) . '%2C' . ($record->longitude + 0.01) . '%2C' . ($record->latitude + 0.01) . '&amp;layer=mapnik&amp;marker=' . $record->latitude . '%2C' . $record->longitude . '">
                                        </iframe>
                                        <br/>
                                        <div class="bg-gray-50 p-3 text-center">
                                            <a href="' . $record->open_street_map_url . '" target="_blank" class="text-primary-600 hover:underline text-sm font-medium">
                                                📍 Buka di OpenStreetMap (Tab Baru)
                                            </a>
                                        </div>
                                    </div>
                                ')
                                : 'Masukkan koordinat untuk melihat preview peta'
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('store_name')
                    ->label('Nama Toko')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('latitude')
                    ->label('Latitude')
                    ->numeric(8)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Longitude')
                    ->numeric(8)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_map')
                    ->label('Lihat Peta')
                    ->icon('heroicon-o-map-pin')
                    ->color('success')
                    ->url(fn ($record) => $record->open_street_map_url, shouldOpenInNewTab: true)
                    ->visible(fn ($record) => $record->latitude && $record->longitude),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStoreSettings::route('/'),
        ];
    }
    
    public static function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StoreLocationMap::class,
        ];
    }
    
    public static function canCreate(): bool
    {
        // Hanya bisa ada 1 record
        return StoreSettings::count() === 0;
    }
}