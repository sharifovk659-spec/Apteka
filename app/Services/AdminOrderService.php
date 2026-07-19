<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Support\OrderStatus;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AdminOrderService
{
    public function __construct(
        private readonly StockMovementService $stockMovements,
    ) {}

    public function updateStatus(Order $order, string $status, ?int $userId = null): Order
    {
        if (! in_array($status, OrderStatus::all(), true)) {
            throw new RuntimeException('Недопустимый статус заказа.');
        }

        if ($status === OrderStatus::CANCELLED) {
            return $this->cancel($order, $userId);
        }

        $order->update(['status' => $status]);

        return $order->fresh();
    }

    public function cancel(Order $order, ?int $userId = null): Order
    {
        if ($order->status === OrderStatus::CANCELLED) {
            throw new RuntimeException('Заказ уже отменён.');
        }

        if ($order->stock_returned_at !== null) {
            throw new RuntimeException('Товары по этому заказу уже возвращены на склад.');
        }

        return DB::transaction(function () use ($order, $userId) {
            $order->load('items');

            $hasSales = \App\Models\StockMovement::query()
                ->where('reference_type', 'order')
                ->where('reference_id', $order->id)
                ->where('type', 'sale')
                ->exists();

            if ($hasSales && ! $this->stockMovements->hasReturnFor('order', $order->id)) {
                $productIds = $order->items->pluck('product_id')->all();

                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($order->items as $item) {
                    $product = $products->get($item->product_id);

                    if (! $product) {
                        continue;
                    }

                    $product->increment('stock', $item->quantity);
                    $product->refresh();

                    $this->stockMovements->recordReturn(
                        $product,
                        $item->quantity,
                        'order',
                        $order->id,
                        "Возврат по отмене заказа {$order->order_number}",
                        $userId,
                    );
                }
            }

            $order->update([
                'status' => OrderStatus::CANCELLED,
                'stock_returned_at' => now(),
            ]);

            return $order->fresh(['items']);
        });
    }
}
