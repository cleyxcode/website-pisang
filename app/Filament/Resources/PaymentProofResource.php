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
    protected static ?string $navigationLabel = 'Bukti Pembayaran';
    protected static ?string $navigationGroup = 'PEMBAYARAN';
    // Mengatur judul halaman
    protected static ?string $modelLabel = 'Bukti Pembayaran';
    protected static ?string $pluralModelLabel = 'Bukti Pembayaran';
    
    // Mengatur judul di halaman
    public static function getModelLabel(): string
    {
        return 'Bukti Pembayaran';
    }
    
    public static function getPluralModelLabel(): string
    {
        return 'Bukti Pembayaran';
    }

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->label('Pesanan')
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
                    ->label('Metode Pembayaran')
                    ->options(function () {
                        return PaymentMethod::where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                
                Forms\Components\TextInput::make('transfer_amount')
                    ->label('Jumlah Transfer')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                
                Forms\Components\DateTimePicker::make('transfer_date')
                    ->label('Tanggal Transfer')
                    ->required(),
                
                Forms\Components\TextInput::make('sender_name')
                    ->label('Nama Pengirim')
                    ->required(),
                
                Forms\Components\TextInput::make('sender_account')
                    ->label('Rekening Pengirim'),
                
                Forms\Components\FileUpload::make('proof_image')
                    ->label('Gambar Bukti Pembayaran')
                    ->image()
                    ->directory('payment-proofs')
                    ->required(),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Pelanggan')
                    ->rows(3),
                
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ])
                    ->default('pending')
                    ->required(),
                
                Forms\Components\DateTimePicker::make('verified_at')
                    ->label('Diverifikasi Pada')
                    ->visible(fn ($get) => $get('status') === 'verified'),
                
                Forms\Components\Textarea::make('admin_notes')
                    ->label('Catatan Admin')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Daftar Bukti Pembayaran')
            ->description('Kelola dan verifikasi bukti pembayaran dari pelanggan')
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Nomor Pesanan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('order.customer_name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Metode Pembayaran')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('transfer_amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sender_name')
                    ->label('Pengirim')
                    ->searchable(),
                
                Tables\Columns\ImageColumn::make('proof_image')
                    ->label('Bukti')
                    ->size(50),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Tanggal Transfer')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Menunggu',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ]),
                
                Tables\Filters\SelectFilter::make('payment_method_id')
                    ->label('Metode Pembayaran')
                    ->options(function () {
                        return PaymentMethod::where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\Filter::make('transfer_date')
                    ->label('Tanggal Transfer')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
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
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                
                // Action untuk verifikasi dengan langsung processing
                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Bukti Pembayaran')
                    ->modalDescription('Bukti pembayaran akan diverifikasi dan pesanan akan langsung masuk ke status "Sedang Diproses"')
                    ->modalSubmitActionLabel('Ya, Verifikasi')
                    ->modalCancelActionLabel('Batal')
                    ->visible(fn (PaymentProof $record) => $record->status === 'pending')
                    ->action(function (PaymentProof $record) {
                        // Gunakan guard 'web' untuk admin authentication
                        $userId = auth('web')->check() ? auth('web')->id() : null;
                        
                        $record->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                            'verified_by' => $userId,
                        ]);
                        
                        // Update order status langsung ke processing
                        if ($record->order) {
                            $record->order->update([
                                'status' => 'processing',
                                'paid_at' => now(),
                                'processing_at' => now(),
                            ]);
                        }
                    }),
                
                // Action untuk verifikasi tanpa processing (jika dibutuhkan)
                Tables\Actions\Action::make('verify_only')
                    ->label('Verifikasi Saja')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Verifikasi Pembayaran Saja')
                    ->modalDescription('Bukti pembayaran akan diverifikasi tanpa mengubah status pesanan ke processing')
                    ->modalSubmitActionLabel('Ya, Verifikasi')
                    ->modalCancelActionLabel('Batal')
                    ->visible(fn (PaymentProof $record) => $record->status === 'pending')
                    ->action(function (PaymentProof $record) {
                        // Gunakan guard 'web' untuk admin authentication
                        $userId = auth('web')->check() ? auth('web')->id() : null;
                        
                        $record->update([
                            'status' => 'verified',
                            'verified_at' => now(),
                            'verified_by' => $userId,
                        ]);
                        
                        // Update order status hanya ke paid
                        if ($record->order) {
                            $record->order->update([
                                'status' => 'paid',
                                'paid_at' => now(),
                            ]);
                        }
                    }),
                
                // Action untuk menolak
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Bukti Pembayaran')
                    ->modalDescription('Berikan alasan penolakan bukti pembayaran ini.')
                    ->modalSubmitActionLabel('Ya, Tolak')
                    ->modalCancelActionLabel('Batal')
                    ->visible(fn (PaymentProof $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Alasan Penolakan')
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
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih'),
                ])
                    ->label('Aksi Massal'),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum Ada Bukti Pembayaran')
            ->emptyStateDescription('Bukti pembayaran akan muncul di sini setelah pelanggan mengunggahnya.')
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        Infolists\Components\TextEntry::make('order.order_number')
                            ->label('Nomor Pesanan'),
                        Infolists\Components\TextEntry::make('order.customer_name')
                            ->label('Pelanggan'),
                        Infolists\Components\TextEntry::make('paymentMethod.name')
                            ->label('Metode Pembayaran'),
                        Infolists\Components\TextEntry::make('transfer_amount')
                            ->label('Jumlah Transfer')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('transfer_date')
                            ->label('Tanggal Transfer')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('sender_name')
                            ->label('Nama Pengirim'),
                        Infolists\Components\TextEntry::make('sender_account')
                            ->label('Rekening Pengirim'),
                    ]),
                
                Infolists\Components\Section::make('Bukti & Status')
                    ->schema([
                        Infolists\Components\ImageEntry::make('proof_image')
                            ->label('Bukti Pembayaran'),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'verified' => 'success',
                                'rejected' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Menunggu',
                                'verified' => 'Terverifikasi',
                                'rejected' => 'Ditolak',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('notes')
                            ->label('Catatan Pelanggan'),
                        Infolists\Components\TextEntry::make('admin_notes')
                            ->label('Catatan Admin')
                            ->visible(fn ($record) => !empty($record->admin_notes)),
                        Infolists\Components\TextEntry::make('verified_at')
                            ->label('Diverifikasi Pada')
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
      public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}