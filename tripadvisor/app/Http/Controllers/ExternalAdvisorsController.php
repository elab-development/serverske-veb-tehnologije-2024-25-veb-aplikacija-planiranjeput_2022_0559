<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExternalAdvisorsController extends Controller
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('services.tripadvisor16');
    }

    protected function setProvider(string $provider): void
    {
        $cfg = config("services.{$provider}");
        if (is_array($cfg) && isset($cfg['base'], $cfg['host'], $cfg['key'])) {
            $this->config = $cfg;
        }
    }

    protected function taGet(string $path, array $query = [])
    {
        $response = Http::withHeaders([
            'X-RapidAPI-Key'  => $this->config['key'],
            'X-RapidAPI-Host' => $this->config['host'],
            'Accept' => 'application/json',
        ])->get($this->config['base'] . $path, $query);

        if ($response->failed()) {
            return response()->json([
                'ok' => false,
                'status' => $response->status(),
                'error' => $response->json(),
            ], $response->status() ?: 500);
        }

        return $response->json();
    }

    protected function wrap($payload, string $key)
    {
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        return response()->json([
            'ok' => true,
            $key => $payload,
        ]);
    }


    public function searchHotelsByLocation16(Request $request)
    {
        $this->setProvider('tripadvisor16');

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'checkIn' => ['sometimes', 'date_format:Y-m-d'],
            'checkOut' => ['sometimes', 'date_format:Y-m-d'],
        ]);

        $resp = $this->taGet('/api/v1/hotels/searchHotelsByLocation', [
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'checkIn' => $validated['checkIn'] ?? null,
            'checkOut' => $validated['checkOut'] ?? null,
        ]);

        return $this->wrap($resp, 'hotels');
    }

    public function attractionsByQueryCom1(Request $request)
    {
        $this->setProvider('tripadvisor_com1');

        $validated = $request->validate([
            'query' => ['required', 'string', 'max:120'],
            "startDate" => ['sometimes', 'date_format:Y-m-d'],
            "endDate" => ['sometimes', 'date_format:Y-m-d'],
        ]);

        $locationsResp = $this->taGet('/auto-complete', [
            'query' => $validated['query'],
        ]);
        if ($locationsResp instanceof JsonResponse) {
            return $locationsResp;
        }

        $geoId = collect($locationsResp['data'] ?? [])->pluck('geoId')->filter()->first();

        if (!$geoId) {
            return response()->json([
                'ok' => false,
                'message' => 'No geoId found for query',
                'locations' => $locationsResp,
            ], 404);
        }

        $resp = $this->taGet('/attractions/search', [
            'geoId' => $geoId,
            'startDate' => $validated['startDate'] ?? null,
            'endDate' => $validated['endDate'] ?? null,
        ]);

        if ($resp instanceof JsonResponse) {
            return $resp;
        }

        return response()->json([
            'ok' => true,
            'query' => $validated['query'],
            'geoId' => $geoId,
            'attractions' => $resp,
        ]);
    }
}