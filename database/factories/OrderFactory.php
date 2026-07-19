<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 800);
        $deliveryPrice = fake()->randomElement([0, 15000, 25000]);

        return [
            'order_number' => 'SB-'.fake()->unique()->numerify('######'),
            'customer_name' => fake()->name(),
            'customer_phone' => '+998'.fake()->numerify('#########'),
            'customer_email' => fake()->optional()->safeEmail(),
            'address' => fake()->address(),
            'delivery_type' => fake()->randomElement(['courier', 'pickup']),
            'payment_method' => fake()->randomElement(['cash', 'card', 'online']),
            'subtotal' => $subtotal,
            'delivery_price' => $deliveryPrice,
            'total' => $subtotal + $deliveryPrice,
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            'comment' => fake()->optional()->sentence(),
        ];
    }
}
