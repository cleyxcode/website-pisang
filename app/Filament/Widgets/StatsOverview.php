<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Voucher;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Total orders hari ini
        $todayOrders = Order::today()->count();
        $yesterdayOrders = Order::whereDate('created_at', now()->subDay())->count();
        $todayOrdersTrend = $todayOrders - $yesterdayOrders;

        // Total revenue bulan ini
        $thisMonthRevenue = Order::thisMonth()->paid()->sum('total_amount');
        $lastMonthRevenue = Order::whereMonth('created_at', now()->subMonth()->month)
                                 ->whereYear('created_at', now()->subMonth()->year)
                                 ->paid()->sum('total_amount');
        $revenueTrend = $thisMonthRevenue - $lastMonthRevenue;

        // Pending orders
        $pendingOrders = Order::pending()->count();

        // Product stats
        $totalProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::where('is_active', true)->where('stock', '<=', 5)->count();

        return [
            Stat::make('Pesanan Hari Ini', $todayOrders)
                ->description($todayOrdersTrend >= 0 ? '+' . $todayOrdersTrend . ' dari kemarin' : $todayOrdersTrend . ' dari kemarin')
                ->descriptionIcon($todayOrdersTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($todayOrdersTrend >= 0 ? 'success' : 'danger')
                ->chart([7, 2, 10, 3, 15, 4, $todayOrders]),

            Stat::make('Revenue Bulan Ini', 'Rp ' . number_format($thisMonthRevenue, 0, ',', '.'))
                ->description($revenueTrend >= 0 ? '+Rp ' . number_format($revenueTrend, 0, ',', '.') . ' dari bulan lalu' : '-Rp ' . number_format(abs($revenueTrend), 0, ',', '.') . ' dari bulan lalu')
                ->descriptionIcon($revenueTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueTrend >= 0 ? 'success' : 'danger'),

            Stat::make('Pesanan Pending', $pendingOrders)
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 10 ? 'warning' : 'primary'),

            Stat::make('Produk Aktif', $totalProducts)
                ->description($lowStockProducts > 0 ? $lowStockProducts . ' produk stok menipis' : 'Semua produk stok aman')
                ->descriptionIcon($lowStockProducts > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($lowStockProducts > 0 ? 'warning' : 'success'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}