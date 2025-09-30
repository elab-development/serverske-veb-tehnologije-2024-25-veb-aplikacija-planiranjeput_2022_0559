<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'name' => 'Beograd',
                'country' => 'Srbija',
                'region' => 'Centralna Srbija',
                'slug' => 'beograd-srbija',
                'description' => 'Glavni grad Srbije, poznat po noćnom životu, istoriji i ušću Save u Dunav.'
            ],
            [
                'name' => 'Novi Sad',
                'country' => 'Srbija',
                'region' => 'Vojvodina',
                'slug' => 'novi-sad-srbija',
                'description' => 'Grad kulture na Dunavu, dom Petrovaradinske tvrđave i festivala EXIT.'
            ],
            [
                'name' => 'Niš',
                'country' => 'Srbija',
                'region' => 'Jugoistočna Srbija',
                'slug' => 'nis-srbija',
                'description' => 'Jedan od najstarijih gradova na Balkanu, poznat po tvrđavi i bogatoj istoriji.'
            ],
            [
                'name' => 'Kopaonik',
                'country' => 'Srbija',
                'region' => 'Raška',
                'slug' => 'kopaonik-srbija',
                'description' => 'Najveći ski centar u Srbiji, poznat po zimskim sportovima i planinskom turizmu.'
            ],
            [
                'name' => 'Zlatibor',
                'country' => 'Srbija',
                'region' => 'Zapadna Srbija',
                'slug' => 'zlatibor-srbija',
                'description' => 'Planinski centar za odmor tokom cele godine, sa bogatom ponudom smeštaja i sadržaja.'
            ],
        ];

        foreach ($destinations as $d) {
            Destination::updateOrCreate(['slug' => $d['slug']], $d);
        }
    }
}