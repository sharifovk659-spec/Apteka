<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        private readonly CartService $cart,
        private readonly SettingService $settings,
        private readonly StockMovementService $stockMovements,
    ) {}

    public function create(array $data): Order
    {
        $this->cart->syncWithStock();

        if ($this->cart->isEmpty()) {
            throw new InvalidArgumentException('Корзина пуста.');
        }

        $cartItems = $this->cart->items();
        $deliveryPrice = $data['delivery_type'] === 'courier' ? $this->settings->deliveryPrice() : 0.0;
        $subtotal = round((float) $cartItems->sum('line_total'), 2);
        $minOrder = $this->settings->minOrderAmount();

        if ($minOrder > 0 && $subtotal < $minOrder) {
            throw new InvalidArgumentException('Минимальная сумма заказа: '.number_format($minOrder, 0, '.', ' ').' смн.');
        }

        return DB::transaction(function () use ($data, $cartItems, $deliveryPrice, $subtotal) {
            $productIds = $cartItems->pluck('product.id')->all();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($cartItems as $item) {
                $quantity = (int) $item['quantity'];

                if ($quantity < 1) {
                    throw new InvalidArgumentException('Количество товара должно быть не меньше 1.');
                }

                $product = $products->get($item['product']->id);

                if (! $product || ! $product->is_active || $product->status !== 'published') {
                    throw new InvalidArgumentException("Товар «{$item['product']->name}» недоступен.");
                }

                if ($quantity > $product->stock) {
                    throw new InvalidArgumentException(
                        "Недостаточно товара «{$product->name}». Доступно: {$product->stock} шт."
                    );
                }
            }

            $customer = Customer::query()->updateOrCreate(
                ['phone' => $data['customer_phone']],
                [
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'] ?? null,
                ],
            );

            $order = Order::query()->create([
                'customer_id' => $customer->id,
                'order_number' => $this->generateOrderNumber(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'customer_email' => $data['customer_email'] ?? null,
                'address' => $data['address'],
                'delivery_type' => $data['delivery_type'],
                'payment_method' => $data['payment_method'],
                'subtotal' => $subtotal,
                'delivery_price' => $deliveryPrice,
                'total' => round($subtotal + $deliveryPrice, 2),
                'status' => OrderStatus::NEW,
                'comment' => $data['comment'] ?? null,
            ]);

            foreach ($cartItems as $item) {
                $product = $products->get($item['product']->id);
                $quantity = (int) $item['quantity'];
                $price = (float) $product->price;
                $lineTotal = round($price * $quantity, 2);

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $lineTotal,
                ]);

                $product->decrement('stock', $quantity);
                $product->refresh();

                $this->stockMovements->recordSale(
                    $product,
                    $quantity,
                    'order',
                    $order->id,
                    "Списание по заказу {$order->order_number}",
                );
            }

            $this->cart->clear();

            return $order->load('items');
        });
    }

    private function generateOrderNumber(): string
    {
        do {
            $number = 'SM-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Order::query()->where('order_number', $number)->exists());

        return $number;
    }
}
