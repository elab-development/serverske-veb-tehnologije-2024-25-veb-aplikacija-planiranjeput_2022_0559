<?php

namespace Database\Factories;

use App\Models\Place;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Place>
 */
class PlaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(Place::TYPES);       
        $prefix = match ($type) {
            'restaurant' => fake()->company() . ' Restaurant',           
            'hotel'      => fake()->company() . ' Hotel',           
            default      => fake()->words(2, true) . ' Attraction',            
        };

        $name = $prefix;
        return [
            'destination_id' => null,
            'name' => $name,
            'type' => $type,
            'slug' => Str::slug($name . '-' . fake()->unique()->numerify('####')),            'slug' => Str::slug($name . '-' . fake()->unique()->numerify('####')),
            'address' => fake()->address(),            'address' => fake()->address(),
            'latitude' => fake()->latitude(),            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),            'longitude' => fake()->longitude(),
            'price_level' => fake()->optional(0.8)->numberBetween(0, 4),            'price_level' => fake()->optional(0.8)->numberBetween(0, 4),
            'rating_avg' => 0.00,
            'reviews_count' => 0,
        ];
    }

    public function restaurant(): static
    {
        return $this->state(fn() => ['type' => 'restaurant']);
    }

    public function hotel(): static
    {
        return $this->state(fn() => ['type' => 'hotel']);
    }

    public function attraction(): static
    {
        return $this->state(fn() => ['type' => 'attraction']);
    }
}