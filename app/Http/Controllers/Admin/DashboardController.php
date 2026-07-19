<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Support\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $productCount = Product::query()->count();
        $orderCount = Order::query()->count();
        $newOrdersCount = Order::query()->where('status', OrderStatus::NEW)->count();

        $totalRevenue = (float) Order::query()
            ->where('status', '!=', OrderStatus::CANCELLED)
            ->sum('total');

        $weekStart = now()->subDays(7)->startOfDay();
        $prevWeekStart = now()->subDays(14)->startOfDay();

        $ordersThisWeek = Order::query()->where('created_at', '>=', $weekStart)->count();
        $ordersPrevWeek = Order::query()->whereBetween('created_at', [$prevWeekStart, $weekStart])->count();

        $revenueThisWeek = (float) Order::query()
            ->where('created_at', '>=', $weekStart)
            ->where('status', '!=', OrderStatus::CANCELLED)
            ->sum('total');

        $revenuePrevWeek = (float) Order::query()
            ->whereBetween('created_at', [$prevWeekStart, $weekStart])
            ->where('status', '!=', OrderStatus::CANCELLED)
            ->sum('total');

        $stats = [
            [
                'label' => 'Товаров',
                'value' => number_format($productCount, 0, '.', ' '),
                'trend' => 'В каталоге',
                'icon' => 'products',
            ],
            [
                'label' => 'Заказов',
                'value' => number_format($orderCount, 0, '.', ' '),
                'trend' => $this->trendPercent($ordersThisWeek, $ordersPrevWeek).' за неделю',
                'icon' => 'orders',
            ],
            [
                'label' => 'Новые заказы',
                'value' => number_format($newOrdersCount, 0, '.', ' '),
                'trend' => 'Требуют обработки',
                'icon' => 'customers',
            ],
            [
                'label' => 'Выручка',
                'value' => $this->formatSomoni($totalRevenue),
                'trend' => $this->trendPercent($revenueThisWeek, $revenuePrevWeek).' за неделю',
                'icon' => 'reports',
            ],
        ];

        $chartStart = now()->subDays(6)->startOfDay();
        $ordersByDay = Order::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', $chartStart)
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $key = $date->toDateString();
            $chartLabels[] = $date->format('d.m');
            $chartData[] = (int) ($ordersByDay[$key] ?? 0);
        }

        $statusLabels = OrderStatus::labels();
        $statusColors = [
            OrderStatus::NEW => '#F5A623',
            OrderStatus::CONFIRMED => '#5B3DF5',
            OrderStatus::PROCESSING => '#4A90E2',
            OrderStatus::DELIVERING => '#7B61FF',
            OrderStatus::COMPLETED => '#12C875',
            OrderStatus::CANCELLED => '#E06C75',
        ];

        $statusCounts = Order::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $donutLabels = [];
        $donutData = [];
        $donutColors = [];
        foreach ($statusLabels as $status => $label) {
            $count = (int) ($statusCounts[$status] ?? 0);
            if ($count > 0) {
                $donutLabels[] = $label;
                $donutData[] = $count;
                $donutColors[] = $statusColors[$status] ?? '#999';
            }
        }

        $recentOrders = Order::query()
            ->select(['id', 'order_number', 'customer_name', 'customer_phone', 'total', 'status', 'created_at'])
            ->latest()
            ->limit(8)
            ->get();

        $lowStockProducts = Product::query()
            ->select(['id', 'name', 'slug', 'sku', 'stock', 'main_image'])
            ->where('stock', '<', 10)
            ->orderBy('stock')
            ->limit(8)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'chartLabels',
            'chartData',
            'donutLabels',
            'donutData',
            'donutColors',
            'recentOrders',
            'lowStockProducts',
            'statusLabels',
        ));
    }

    private function trendPercent(float|int $current, float|int $previous): string
    {
        if ($previous <= 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $change = (($current - $previous) / $previous) * 100;
        $prefix = $change >= 0 ? '+' : '';

        return $prefix.number_format($change, 1, '.', '').'%';
    }

    private function formatSomoni(float $amount): string
    {
        return number_format($amount, 0, '.', ' ').' смн';
    }
}
