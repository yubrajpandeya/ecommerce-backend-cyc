<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => \App\Models\Category::factory(),
            'name' => $name = fake()->unique()->productName(),
            'slug' => str()->slug($name),
            'description' => fake()->optional()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 500),
            'stock' => fake()->numberBetween(0, 500),
            'is_active' => true,
            'is_featured' => fake()->boolean(15),
            'is_upcoming' => fake()->boolean(20),
            'available_from' => fake()->optional()->dateTimeBetween('now', '+3 months'),
        ];
    }
}
