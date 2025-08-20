<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Order;

class RecentOrders extends BaseWidget
{
    protected static ?string $heading = 'Pesanan Terbaru';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('No. Pesanan')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('total_items')
                    ->label('Items')
                    ->getStateUsing(fn ($record) => $record->total_items . ' item')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
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
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false);
    }
}