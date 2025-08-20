<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    
    protected static ?string $navigationLabel = 'Pesanan';
    
    protected static ?string $modelLabel = 'Pesanan';
    
    protected static ?string $pluralModelLabel = 'Pesanan';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pesanan')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('No. Pesanan')
                            ->disabled()
                            ->columnSpan(1),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status Pesanan')
                            ->options([
                                'pending' => 'Menunggu Pembayaran',
                                'paid' => 'Sudah Dibayar',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'delivered' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                                'expired' => 'Kadaluarsa',
                            ])
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Customer')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Customer')
                            ->required()
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('No. Telepon')
                            ->required()
                            ->columnSpan(1),
                            
                        Forms\Components\Textarea::make('customer_address')
                            ->label('Alamat')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Pembayaran')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Diskon')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Ongkos Kirim')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->columnSpan(1),
                            
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'midtrans' => 'Otomatis (Midtrans)',
                                'manual' => 'Manual (Transfer)',
                            ])
                            ->disabled()
                            ->columnSpan(1),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Customer')
                            ->disabled()
                            ->rows(2)
                            ->columnSpan(1),
                            
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Catatan Admin')
                            ->rows(2)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('total_items')
                    ->label('Items')
                    ->getStateUsing(fn ($record) => $record->total_items)
                    ->badge()
                    ->color('gray')
                    ->suffix(' item'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->getStateUsing(fn ($record) => $record->payment_method_label)
                    ->badge()
                    ->color(fn ($record) => $record->payment_method === 'midtrans' ? 'info' : 'warning'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(fn ($record) => $record->status_label)
                    ->badge()
                    ->color(fn ($record) => $record->status_color),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu Pembayaran',
                        'paid' => 'Sudah Dibayar',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        'expired' => 'Kadaluarsa',
                    ]),
                    
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'midtrans' => 'Otomatis (Midtrans)',
                        'manual' => 'Manual (Transfer)',
                    ]),
                    
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('mark_paid')
                    ->label('Tandai Dibayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                        
                        // Update voucher usage if any
                        if ($record->voucher) {
                            $record->voucher->incrementUsage();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Yakin pesanan ini sudah dibayar?'),
                    
                Tables\Actions\Action::make('mark_shipped')
                    ->label('Tandai Dikirim')
                    ->icon('heroicon-o-truck')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->canBeShipped())
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'shipped',
                            'shipped_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation(),
                    
                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Order $record) => $record->canBeCancelled())
                    ->action(function (Order $record) {
                        $record->update(['status' => 'cancelled']);
                        
                        // Restore product stock
                        foreach ($record->items as $item) {
                            $item->product->increment('stock', $item->quantity);
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pesanan')
                    ->modalDescription('Yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('mark_shipped')
                        ->label('Tandai Dikirim')
                        ->icon('heroicon-o-truck')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->canBeShipped()) {
                                    $record->update([
                                        'status' => 'shipped',
                                        'shipped_at' => now(),
                                    ]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'pending')->count() > 0 ? 'warning' : null;
    }
}