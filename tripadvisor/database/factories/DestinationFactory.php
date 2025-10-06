<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->city();        $city = fake()->city();
        $country = fake()->country();        $country = fake()->country();
        $slug = Str::slug($city . '-' . $country);

        return [
            'name' => $city,
            'country' => $country,
            'region' => fake()->boolean(70) ? fake()->state() : null,            
            'slug' => $slug,
            'description' => fake()->paragraphs(2, true),           
        ];
    }
}