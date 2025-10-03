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
}