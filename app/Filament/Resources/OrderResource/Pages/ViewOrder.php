<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\ImageEntry;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pesanan')
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('No. Pesanan')
                            ->badge()
                            ->color('primary'),
                            
                        TextEntry::make('status')
                            ->label('Status')
                            ->getStateUsing(fn ($record) => $record->status_label)
                            ->badge()
                            ->color(fn ($record) => $record->status_color),
                            
                        TextEntry::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->getStateUsing(fn ($record) => $record->payment_method_label)
                            ->badge(),
                            
                        TextEntry::make('created_at')
                            ->label('Tanggal Pesanan')
                            ->dateTime('d M Y H:i'),
                            
                        TextEntry::make('paid_at')
                            ->label('Tanggal Dibayar')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Belum dibayar'),
                            
                        TextEntry::make('shipped_at')
                            ->label('Tanggal Dikirim')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Belum dikirim'),
                    ])
                    ->columns(3),

                Section::make('Informasi Customer')
                    ->schema([
                        TextEntry::make('customer_name')
                            ->label('Nama'),
                            
                        TextEntry::make('customer_email')
                            ->label('Email')
                            ->copyable(),
                            
                        TextEntry::make('customer_phone')
                            ->label('No. Telepon')
                            ->copyable(),
                            
                        TextEntry::make('customer_address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Detail Produk')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                ImageEntry::make('product_image')
                                    ->label('Gambar')
                                    ->height(60)
                                    ->width(60),
                                    
                                TextEntry::make('product_name')
                                    ->label('Nama Produk')
                                    ->weight('bold'),
                                    
                                TextEntry::make('product_sku')
                                    ->label('SKU')
                                    ->badge()
                                    ->color('gray'),
                                    
                                TextEntry::make('product_price')
                                    ->label('Harga Satuan')
                                    ->money('IDR'),
                                    
                                TextEntry::make('quantity')
                                    ->label('Qty')
                                    ->badge()
                                    ->color('info'),
                                    
                                TextEntry::make('total_price')
                                    ->label('Subtotal')
                                    ->money('IDR')
                                    ->weight('bold'),
                            ])
                            ->columns(6)
                            ->contained(false),
                    ]),

                Section::make('Ringkasan Pembayaran')
                    ->schema([
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->money('IDR'),
                            
                        TextEntry::make('voucher_code')
                            ->label('Kode Voucher')
                            ->badge()
                            ->color('success')
                            ->placeholder('Tidak ada voucher'),
                            
                        TextEntry::make('voucher_discount')
                            ->label('Diskon Voucher')
                            ->money('IDR')
                            ->color('success'),
                            
                        TextEntry::make('shipping_cost')
                            ->label('Ongkos Kirim')
                            ->money('IDR'),
                            
                        TextEntry::make('total_amount')
                            ->label('Total Pembayaran')
                            ->money('IDR')
                            ->weight('bold')
                            ->size('lg'),
                    ])
                    ->columns(2),

                Section::make('Catatan')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Catatan Customer')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpan(1),
                            
                        TextEntry::make('admin_notes')
                            ->label('Catatan Admin')
                            ->placeholder('Tidak ada catatan')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}