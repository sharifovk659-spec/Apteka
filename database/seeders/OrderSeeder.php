<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\OrderStatus;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::query()->get();

        if ($products->isEmpty()) {
            return;
        }

        $customers = [
            ['name' => 'Алишер Каримов', 'phone' => '+998901234567', 'email' => 'alisher@test.local'],
            ['name' => 'Мадина Рахимова', 'phone' => '+998909876543', 'email' => 'madina@test.local'],
            ['name' => 'Жасур Тошматов', 'phone' => '+998977001122', 'email' => null],
            ['name' => 'Дилафруз Назарова', 'phone' => '+998935551122', 'email' => 'dilafruz@test.local'],
            ['name' => 'Бахром Сидиков', 'phone' => '+998991122334', 'email' => 'bahrom@test.local'],
            ['name' => 'Нигора Юлдашева', 'phone' => '+998903334455', 'email' => 'nigora@test.local'],
            ['name' => 'Шохрух Мирзаев', 'phone' => '+998911223344', 'email' => null],
            ['name' => 'Зарина Абдуллаева', 'phone' => '+998936667788', 'email' => 'zarina@test.local'],
            ['name' => 'Илхом Хакимов', 'phone' => '+998944556677', 'email' => 'ilhom@test.local'],
            ['name' => 'Камола Эргашева', 'phone' => '+998955778899', 'email' => 'kamola@test.local'],
        ];

        $statuses = [
            OrderStatus::NEW,
            OrderStatus::CONFIRMED,
            OrderStatus::PROCESSING,
            OrderStatus::DELIVERING,
            OrderStatus::COMPLETED,
            OrderStatus::CANCELLED,
        ];

        foreach ($customers as $index => $customer) {
            $itemsCount = random_int(1, 4);
            $selectedProducts = $products->random(min($itemsCount, $products->count()));
            $subtotal = 0;
            $orderItems = [];

            foreach ($selectedProducts as $product) {
                $quantity = random_int(1, 3);
                $lineTotal = round($product->price * $quantity, 2);
                $subtotal += $lineTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'total' => $lineTotal,
                ];
            }

            $deliveryPrice = $index % 2 === 0 ? 15000 : 0;
            $total = $subtotal + $deliveryPrice;
            $status = $statuses[$index % count($statuses)];

            $order = Order::query()->create([
                'order_number' => 'SM-DEMO-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'customer_name' => $customer['name'],
                'customer_phone' => $customer['phone'],
                'customer_email' => $customer['email'],
                'address' => 'г. Душанбе, тестовый адрес доставки №'.($index + 1),
                'delivery_type' => $deliveryPrice > 0 ? 'courier' : 'pickup',
                'payment_method' => ['cash', 'card', 'alif', 'dushanbe_city'][$index % 4],
                'subtotal' => $subtotal,
                'delivery_price' => $deliveryPrice,
                'total' => $total,
                'status' => $status,
                'stock_returned_at' => $status === OrderStatus::CANCELLED ? now() : null,
                'comment' => $index % 3 === 0 ? 'Тестовый заказ для демонстрации админ-панели.' : null,
            ]);

            foreach ($orderItems as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    ...$item,
                ]);
            }
        }
    }
}
