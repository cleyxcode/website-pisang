<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentProofResource\Pages;
use App\Models\PaymentProof;
use App\Models\Order;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class PaymentProofResource extends Resource
{
    protected static ?string $model = PaymentProof::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->label('Order')
                    ->options(function () {
                        return Order::query()
                            ->select('id', 'order_number', 'customer_name')
                            ->get()
                            ->mapWithKeys(function ($order) {
                                return [$order->id => "{$order->order_number} - {$order->customer_name}"];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\Select::make('payment_method_id')
                    ->label('Payment Method')
                    ->options(function () {
                        return PaymentMethod::where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\TextInput::make('transfer_amount')
                    ->label('Transfer Amount')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                
                Forms\Components\DateTimePicker::make('transfer_date')
                    ->label('Transfer Date')
                    ->required(),
                
                Forms\Components\TextInput::make('sender_name')
                    ->label('Sender Name')
                    ->required(),
                
                Forms\Components\TextInput::make('sender_account')
                    ->label('Sender Account'),
                
                Forms\Components\FileUpload::make('proof_image')
                    ->label('Proof Image')
                    ->image()
                    ->directory('payment-proofs')
                    ->required(),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Customer Notes')
                    ->rows(3),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),
                
                Forms\Components\DateTimePicker::make('verified_at')
                    ->label('Verified At')
                    ->visible(fn ($get) => $get('status') === 'verified'),
                
                Forms\Components\Textarea::make('admin_notes')
                    ->label('Admin Notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order Number')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order.customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Payment Method')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('transfer_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sender_name')
                    ->label('Sender')
                    ->searchable(),
                
                Tables\Columns\ImageColumn::make('proof_image')
                    ->label('Proof')
                    ->size(50),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ]),
                
                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Transfer Date')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ]),
                
                Tables\Filters\SelectFilter::make('payment_method_id')
                    ->label('Payment Method')
                    ->options(function () {
                        return PaymentMethod::where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('transfer_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $query, $date): Builder => 
                                $query->whereDate('transfer_date', '>=', $date))
                            ->when($data['until'], fn (Builder $query, $date): Builder => 
                                $query->whereDate('transfer_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                // Action untuk verifikasi
                Tables\Actions\Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PaymentProof $record) => $record->status === 'pending')
                    ->action(function (PaymentProof $record) {
                        // Gunakan guard 'web' untuk admin authentication
                        $userId = auth('web')->check() ? auth('web')->id() : null;
                        
                        $record->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                            'verified_by' => $userId,
                        ]);
                        
                        // Update order status
                        if ($record->order) {
                            $record->order->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                            ]);
                        }
                    }),
                
                // Action untuk menolak
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PaymentProof $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function (PaymentProof $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['admin_notes'],
                        ]);
                        
                        // Reset order payment proof flag
                        if ($record->order) {
                            $record->order->update([
                                'has_payment_proof' => false,
                            ]);
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Payment Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('order.order_number')
                            ->label('Order Number'),
                        Infolists\Components\TextEntry::make('order.customer_name')
                            ->label('Customer'),
                        Infolists\Components\TextEntry::make('paymentMethod.name')
                            ->label('Payment Method'),
                        Infolists\Components\TextEntry::make('transfer_amount')
                            ->label('Transfer Amount')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('transfer_date')
                            ->label('Transfer Date')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('sender_name')
                            ->label('Sender Name'),
                        Infolists\Components\TextEntry::make('sender_account')
                            ->label('Sender Account'),
                    ]),
                
                Infolists\Components\Section::make('Proof & Status')
                    ->schema([
                        Infolists\Components\ImageEntry::make('proof_image')
                            ->label('Payment Proof'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'verified' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Customer Notes'),
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->visible(fn ($record) => !empty($record->admin_notes)),
                        Infolists\Components\TextEntry::make('verified_at')
                            ->label('Verified At')
                            ->dateTime()
                            ->visible(fn ($record) => $record->verified_at),
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
            'index' => Pages\ListPaymentProofs::route('/'),
            'create' => Pages\CreatePaymentProof::route('/create'),
            'view' => Pages\ViewPaymentProof::route('/{record}'),
            'edit' => Pages\EditPaymentProof::route('/{record}/edit'),
        ];
    }
}