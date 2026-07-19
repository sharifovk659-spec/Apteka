<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderIndexRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\AdminOrderService;
use App\Support\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(
        private readonly AdminOrderService $orders,
    ) {}

    public function index(OrderIndexRequest $request): View
    {
        $filters = $request->filters();

        $orders = Order::query()
            ->when($filters['search'], function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'], fn (Builder $q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => $filters,
            'statuses' => OrderStatus::labels(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'customer']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => OrderStatus::labels(),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orders->updateStatus($order, $request->validated('status'), $request->user()?->id);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Статус заказа обновлён.');
    }
}
