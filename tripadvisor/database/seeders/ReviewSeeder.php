<?php

namespace Database\Seeders;

use App\Models\Place;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::where('role', 'user')->pluck('id')->all();

        $reviewsMap = [
            'kalemegdan-fortress-beograd-srbija' => [
                ['rating' => 5, 'title' => 'Prelep pogled', 'body' => 'Kalemegdan nudi sjajan pogled na ušće i mnogo istorijskih zanimljivosti.'],
                ['rating' => 4, 'title' => 'Obavezna poseta', 'body' => 'Idealno za šetnju i fotografije, preporuka svakome ko dolazi u Beograd.'],
            ],
            'skadarlija-beograd-srbija' => [
                ['rating' => 5, 'title' => 'Boemski duh', 'body' => 'Autentična atmosfera, muzika uživo i odlična hrana.'],
                ['rating' => 4, 'title' => 'Vredno obilaska', 'body' => 'Malo gužva vikendom, ali vredi doživeti.'],
            ],
            'hotel-moskva-beograd-srbija' => [
                ['rating' => 5, 'title' => 'Klasik Beograda', 'body' => 'Stil, lokacija i doručak su vrhunski.'],
                ['rating' => 4, 'title' => 'Odličan boravak', 'body' => 'Sobe uredne, osoblje ljubazno.'],
            ],
            'manufaktura-restaurant-beograd-srbija' => [
                ['rating' => 5, 'title' => 'Fantastična hrana', 'body' => 'Moderna srpska kuhinja, sjajan izbor.'],
                ['rating' => 4, 'title' => 'Ambijent i usluga', 'body' => 'Odličan ambijent, usluga brza.'],
            ],
            'petrovaradin-fortress-novi-sad-srbija' => [
                ['rating' => 5, 'title' => 'Fenomenalno', 'body' => 'Pogled na Novi Sad je fantastičan, posebno u zalazak sunca.'],
                ['rating' => 4, 'title' => 'Istorija i kultura', 'body' => 'Mnogo kulturnih sadržaja i lepa šetnja.'],
            ],
            'hotel-park-novi-sad-novi-sad-srbija' => [
                ['rating' => 4, 'title' => 'Mirno i uredno', 'body' => 'Dobar doručak i parking, blizu centra.'],
            ],
            'veliki-restaurant-novi-sad-srbija' => [
                ['rating' => 5, 'title' => 'Top preporuka', 'body' => 'Hrana ukusna, porcije obilne, cene korektne.'],
            ],
            'nis-fortress-nis-srbija' => [
                ['rating' => 4, 'title' => 'Lepo šetalište', 'body' => 'Uređene staze i zanimljiva istorija.'],
            ],
            'cair-park-nis-srbija' => [
                ['rating' => 4, 'title' => 'Zelena oaza', 'body' => 'Idealno za relaks šetnju i rekreaciju.'],
            ],
            'new-city-hotel-nis-nis-srbija' => [
                ['rating' => 4, 'title' => 'Prijatno iskustvo', 'body' => 'Čisto i komforno, dobra lokacija.'],
            ],
            'stambolijski-nis-srbija' => [
                ['rating' => 5, 'title' => 'Ukusi juga', 'body' => 'Odlična lokalna kuhinja i usluga.'],
            ],
            'grand-hotel-spa-kopaonik-srbija' => [
                ['rating' => 5, 'title' => 'Spa vrhunski', 'body' => 'Sjajan spa, doručak i osoblje.'],
                ['rating' => 4, 'title' => 'Preporuka', 'body' => 'Sve korektno i uredno.'],
            ],
            'malo-jezero-ski-lift-kopaonik-srbija' => [
                ['rating' => 5, 'title' => 'Skijanje super', 'body' => 'Odlične staze i organizacija.'],
            ],
            'maglic-restaurant-kopaonik-srbija' => [
                ['rating' => 4, 'title' => 'Dobar izbor', 'body' => 'Solidan meni, prijatan ambijent.'],
            ],
            'tornik-ski-center-zlatibor-srbija' => [
                ['rating' => 5, 'title' => 'Sjajne staze', 'body' => 'Dobar izbor za početnike i rekreativce.'],
            ],
            'hotel-mona-zlatibor-zlatibor-srbija' => [
                ['rating' => 4, 'title' => 'Porodično friendly', 'body' => 'Dobar bazen i spa, odlična lokacija.'],
            ],
            'restoran-bajka-zlatibor-srbija' => [
                ['rating' => 5, 'title' => 'Bajkovito', 'body' => 'Veoma ukusna hrana i ljubazno osoblje.'],
            ],
        ];

        foreach ($reviewsMap as $placeSlug => $items) {
            $place = Place::where('slug', $placeSlug)->first();
            if (!$place) {
                $this->command->warn("Place not found for reviews: {$placeSlug}");
                continue;
            }

            foreach ($items as $r) {
                if (empty($userIds)) {
                    continue;
                }
                $uid = $userIds[array_rand($userIds)];

                $exists = Review::where('user_id', $uid)->where('place_id', $place->id)->exists();
                if ($exists) continue;

                Review::create([
                    'user_id' => $uid,
                    'place_id' => $place->id,
                    'rating' => $r['rating'],
                    'title' => $r['title'] ?? null,
                    'body' => $r['body'] ?? null,
                ]);
            }
        }

        $aggregates = Review::selectRaw('place_id, COUNT(*) as cnt, AVG(rating) as avg_rating')
            ->groupBy('place_id')
            ->get()
            ->keyBy('place_id');

        Place::query()->chunkById(200, function ($chunk) use ($aggregates) {
            foreach ($chunk as $p) {
                if (isset($aggregates[$p->id])) {
                    $agg = $aggregates[$p->id];
                    $p->reviews_count = (int) $agg->cnt;
                    $p->rating_avg = round((float) $agg->avg_rating, 2);
                } else {
                    $p->reviews_count = 0;
                    $p->rating_avg = 0.00;
                }
                $p->save();
            }
        });
    }
}