<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    
    protected static ?string $navigationLabel = 'Voucher';
     protected static ?string $navigationGroup = 'MANAJEMEN PRODUK';
    protected static ?string $modelLabel = 'Voucher';
    
    protected static ?string $pluralModelLabel = 'Voucher';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Voucher')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Voucher')
                            ->required()
                            ->unique(Voucher::class, 'code', ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Contoh: DISKON10, FREEONGKIR')
                            ->helperText('Kode yang akan dimasukkan customer saat checkout')
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Voucher')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Diskon 10% untuk pembelian minimal 50rb')
                            ->columnSpan(1),
                            
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi detail voucher untuk customer')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Pengaturan Diskon')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Tipe Diskon')
                            ->required()
                            ->options([
                                'percentage' => 'Persentase (%)',
                                'fixed' => 'Nominal Tetap (Rp)',
                                'free_shipping' => 'Gratis Ongkir',
                            ])
                            ->live()
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('discount_value')
                            ->label('Nilai Diskon')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->required(fn (Forms\Get $get) => $get('discount_type') !== 'free_shipping')
                            ->hidden(fn (Forms\Get $get) => $get('discount_type') === 'free_shipping')
                            ->prefix(fn (Forms\Get $get) => $get('discount_type') === 'fixed' ? 'Rp' : '')
                            ->suffix(fn (Forms\Get $get) => $get('discount_type') === 'percentage' ? '%' : '')
                            ->helperText(fn (Forms\Get $get) => match($get('discount_type')) {
                                'percentage' => 'Masukkan angka 1-100 (contoh: 10 untuk 10%)',
                                'fixed' => 'Masukkan nominal dalam rupiah',
                                default => '',
                            })
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('maximum_discount')
                            ->label('Maksimal Diskon')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('Rp')
                            ->visible(fn (Forms\Get $get) => $get('discount_type') === 'percentage')
                            ->helperText('Batas maksimal potongan untuk diskon persentase')
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('minimum_amount')
                            ->label('Minimum Pembelian')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('Rp')
                            ->helperText('Minimum total belanja untuk menggunakan voucher (kosongkan jika tidak ada minimum)')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Batasan Penggunaan')
                    ->schema([
                        Forms\Components\TextInput::make('usage_limit')
                            ->label('Total Limit Penggunaan')
                            ->numeric()
                            ->minValue(1)
                            ->helperText('Total maksimal voucher bisa digunakan oleh semua customer (kosongkan untuk unlimited)')
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('usage_limit_per_user')
                            ->label('Limit per User')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required()
                            ->helperText('Maksimal penggunaan per customer')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Periode Berlaku')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Mulai Berlaku')
                            ->helperText('Kosongkan jika langsung aktif')
                            ->columnSpan(1),
                            
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label('Berakhir')
                            ->helperText('Kosongkan jika tidak ada batas waktu')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Voucher Aktif')
                            ->default(true)
                            ->helperText('Voucher tidak aktif tidak bisa digunakan customer'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode voucher disalin!')
                    ->badge()
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Voucher')
                    ->searchable()
                    ->wrap()
                    ->limit(50),
                    
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Tipe')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'percentage' => 'Persentase',
                        'fixed' => 'Nominal',
                        'free_shipping' => 'Gratis Ongkir',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'percentage' => 'success',
                        'fixed' => 'warning',
                        'free_shipping' => 'info',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('formatted_discount')
                    ->label('Nilai Diskon')
                    ->getStateUsing(function ($record) {
                        return $record->formatted_discount;
                    }),
                    
                Tables\Columns\TextColumn::make('minimum_amount')
                    ->label('Min. Belanja')
                    ->money('IDR')
                    ->placeholder('Tidak ada')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('usage_stats')
                    ->label('Penggunaan')
                    ->getStateUsing(function ($record) {
                        $used = $record->used_count;
                        $limit = $record->usage_limit;
                        return $limit ? "{$used}/{$limit}" : $used;
                    })
                    ->badge()
                    ->color(function ($record) {
                        if (!$record->usage_limit) return 'gray';
                        $percentage = ($record->used_count / $record->usage_limit) * 100;
                        return $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                    }),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        return $record->status;
                    })
                    ->badge()
                    ->color(function ($record) {
                        return $record->status_color;
                    }),
                    
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Berakhir')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Tidak ada batas')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('discount_type')
                    ->label('Tipe Diskon')
                    ->options([
                        'percentage' => 'Persentase',
                        'fixed' => 'Nominal',
                        'free_shipping' => 'Gratis Ongkir',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua voucher')
                    ->trueLabel('Voucher aktif')
                    ->falseLabel('Voucher tidak aktif'),
                    
                Tables\Filters\Filter::make('expired')
                    ->label('Kadaluarsa')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('upcoming')
                    ->label('Belum Dimulai')
                    ->query(fn (Builder $query): Builder => $query->where('starts_at', '>', now()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function ($record) {
                        $newVoucher = $record->replicate();
                        $newVoucher->code = $record->code . '-COPY';
                        $newVoucher->used_count = 0;
                        $newVoucher->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Duplikat Voucher')
                    ->modalDescription('Voucher akan diduplikat dengan kode yang ditambahi "-COPY"')
                    ->successNotificationTitle('Voucher berhasil diduplikat'),
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
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'view' => Pages\ViewVoucher::route('/{record}'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
      public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}