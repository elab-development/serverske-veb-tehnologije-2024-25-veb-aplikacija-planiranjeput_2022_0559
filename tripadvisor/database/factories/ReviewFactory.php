<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'place_id' => null,
            'rating' => fake()->numberBetween(1, 5),           
            'title' => fake()->boolean(70) ? fake()->sentence(6) : null, 
            'body' => fake()->boolean(80) ? fake()->paragraphs(2, true) : null,       
        ];
    }
}