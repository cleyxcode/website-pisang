<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationLabel = 'Metode Pembayaran';
    
    protected static ?string $modelLabel = 'Metode Pembayaran';
    
    protected static ?string $pluralModelLabel = 'Metode Pembayaran';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Metode Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: BCA, Dana, GoPay')
                            ->columnSpan(2),
                            
                        Forms\Components\Select::make('type')
                            ->label('Jenis')
                            ->options([
                                'bank' => 'Bank Transfer',
                                'ewallet' => 'E-Wallet',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('account_number')
                            ->label('Nomor Rekening/Akun')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Masukkan nomor rekening atau nomor e-wallet')
                            ->helperText('Nomor rekening bank atau nomor HP untuk e-wallet')
                            ->columnSpan(2),
                            
                        Forms\Components\TextInput::make('account_name')
                            ->label('Nama Pemilik')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Nama sesuai rekening/akun')
                            ->columnSpan(1),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Instruksi Pembayaran')
                    ->schema([
                        Forms\Components\RichEditor::make('instructions')
                            ->label('Petunjuk Transfer')
                            ->placeholder('Contoh: Transfer ke rekening di atas, lalu kirim bukti transfer via WhatsApp ke 0812-3456-7890')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'bulletList',
                                'orderedList',
                                'link',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pengaturan Lainnya')
                    ->schema([
                        Forms\Components\FileUpload::make('icon')
                            ->label('Logo/Icon')
                            ->image()
                            ->directory('payment-methods')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->helperText('Upload logo bank atau e-wallet (max 1MB)')
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Semakin kecil, semakin atas urutannya')
                            ->columnSpan(1),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Metode pembayaran yang tidak aktif tidak akan ditampilkan')
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->label('Icon')
                    ->circular()
                    ->defaultImageUrl(url('/images/payment-placeholder.png')),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('type_label')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn ($record) => $record->type === 'bank' ? 'info' : 'success'),
                    
                Tables\Columns\TextColumn::make('account_number')
                    ->label('No. Rekening/Akun')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor berhasil disalin!')
                    ->formatStateUsing(fn ($record) => $record->formatted_account_number),
                    
                Tables\Columns\TextColumn::make('account_name')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'bank' => 'Bank Transfer',
                        'ewallet' => 'E-Wallet',
                    ]),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua metode')
                    ->trueLabel('Metode aktif')
                    ->falseLabel('Metode tidak aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                
                Tables\Actions\Action::make('preview_instructions')
                    ->label('Lihat Instruksi')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->modalContent(fn (PaymentMethod $record) => view('filament.pages.payment-instructions-preview', compact('record')))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
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
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'view' => Pages\ViewPaymentMethod::route('/{record}'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}