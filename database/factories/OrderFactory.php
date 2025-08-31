<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 100, 50000);
        $totalAmount = $quantity * $unitPrice;

        return [
            'user_id' => \App\Models\User::factory(),
            'product_id' => \App\Models\Product::factory(),
            'order_number' => \App\Models\Order::generateOrderNumber(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_amount' => $totalAmount,
            'status' => fake()->randomElement(['pending', 'payment_verification', 'confirmed', 'processing', 'shipped', 'delivered']),
            'shipping_address' => fake()->address(),
            'phone_number' => fake()->phoneNumber(),
            'notes' => fake()->optional()->sentence(),
            'payment_verified_at' => fake()->optional()->dateTimeBetween('-30 days', 'now'),
            'verified_by' => fake()->optional()->randomElement([\App\Models\User::factory(), null]),
            'admin_notes' => fake()->optional()->sentence(),
        ];
    }
}
