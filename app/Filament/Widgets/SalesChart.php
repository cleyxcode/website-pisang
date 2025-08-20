<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Penjualan (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Get sales data for last 7 days
        $salesData = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as orders')
            ->where('created_at', '>=', now()->subDays(7))
            ->whereIn('status', ['paid', 'processing', 'shipped', 'delivered'])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Generate labels for last 7 days
        $labels = [];
        $revenue = [];
        $orderCounts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d M');
            
            $dayData = $salesData->where('date', $date->format('Y-m-d'))->first();
            $revenue[] = $dayData ? (float) $dayData->total : 0;
            $orderCounts[] = $dayData ? $dayData->orders : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $revenue,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $orderCounts,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            if (context.dataset.label === "Revenue (Rp)") {
                                label += "Rp " + new Intl.NumberFormat("id-ID").format(context.parsed.y);
                            } else {
                                label += context.parsed.y + " pesanan";
                            }
                            return label;
                        }'
                    ]
                ]
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Tanggal'
                    ]
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue (Rp)'
                    ],
                    'ticks' => [
                        'callback' => 'function(value) {
                            return "Rp " + new Intl.NumberFormat("id-ID").format(value);
                        }'
                    ]
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Pesanan'
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }
}