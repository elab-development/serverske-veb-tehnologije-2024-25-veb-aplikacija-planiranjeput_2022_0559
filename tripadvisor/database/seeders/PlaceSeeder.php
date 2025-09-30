<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Place;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PlaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $byDestination = [
            'beograd-srbija' => [
                ['name' => 'Kalemegdan Fortress', 'type' => 'attraction', 'address' => 'Kalemegdan bb', 'price_level' => null],
                ['name' => 'Skadarlija', 'type' => 'attraction', 'address' => 'Skadarska', 'price_level' => null],
                ['name' => 'Hotel Moskva', 'type' => 'hotel', 'address' => 'Bulevar kralja Aleksandra 2', 'price_level' => 3],
                ['name' => 'Manufaktura Restaurant', 'type' => 'restaurant', 'address' => 'Kralja Petra 13-15', 'price_level' => 2],
            ],
            'novi-sad-srbija' => [
                ['name' => 'Petrovaradin Fortress', 'type' => 'attraction', 'address' => 'Tvrđava Petrovaradin', 'price_level' => null],
                ['name' => 'Hotel Park Novi Sad', 'type' => 'hotel', 'address' => 'Novosadskog sajma 35', 'price_level' => 3],
                ['name' => 'Veliki Restaurant', 'type' => 'restaurant', 'address' => 'Nikole Pašića 24', 'price_level' => 2],
            ],
            'nis-srbija' => [
                ['name' => 'Niš Fortress', 'type' => 'attraction', 'address' => 'Tvrđava bb', 'price_level' => null],
                ['name' => 'Čair Park', 'type' => 'attraction', 'address' => 'Čair', 'price_level' => null],
                ['name' => 'New City Hotel Niš', 'type' => 'hotel', 'address' => 'Vojvode Mišića 7', 'price_level' => 2],
                ['name' => 'Stambolijski', 'type' => 'restaurant', 'address' => 'Kazandžijsko sokače', 'price_level' => 2],
            ],
            'kopaonik-srbija' => [
                ['name' => 'Grand Hotel & Spa', 'type' => 'hotel', 'address' => 'Kopaonik centar', 'price_level' => 4],
                ['name' => 'Malo jezero Ski Lift', 'type' => 'attraction', 'address' => 'Ski centar', 'price_level' => null],
                ['name' => 'Maglić Restaurant', 'type' => 'restaurant', 'address' => 'Kopaonik centar', 'price_level' => 3],
            ],
            'zlatibor-srbija' => [
                ['name' => 'Tornik Ski Center', 'type' => 'attraction', 'address' => 'Tornik', 'price_level' => null],
                ['name' => 'Hotel Mona Zlatibor', 'type' => 'hotel', 'address' => 'Miladina Pećinara 26', 'price_level' => 3],
                ['name' => 'Restoran Bajka', 'type' => 'restaurant', 'address' => 'Jezero, Zlatibor', 'price_level' => 2],
            ],
        ];

        foreach ($byDestination as $destSlug => $places) {
            $dest = Destination::where('slug', $destSlug)->first();

            if (!$dest) {
                $this->command->warn("Destination not found: {$destSlug}. Skipping its places.");
                continue;
            }

            foreach ($places as $p) {
                Place::updateOrCreate(
                    ['slug' => Str::slug($p['name'] . '-' . $dest->slug)],
                    [
                        'destination_id' => $dest->id,
                        'name' => $p['name'],
                        'type' => $p['type'],
                        'slug' => Str::slug($p['name'] . '-' . $dest->slug),
                        'address' => $p['address'] ?? null,
                        'latitude' => null,
                        'longitude' => null,
                        'price_level' => $p['price_level'],
                        'rating_avg' => 0.00,
                        'reviews_count' => 0,
                    ]
                );
            }
        }
    }
}