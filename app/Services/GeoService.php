<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoService
{
    public static function getLocationFromAddress(string $address): ?array
    {
        $response = Http::withHeaders([
            'User-Agent' => env('HTTP_CLIENT_USER_AGENT'),
        ])->get('https://nominatim.openstreetmap.org/search', [
                    'q'      => $address,
                    'format' => 'json',
                    'limit'  => 1,
                ]);

        if ($response->failed()) {
            return null;
        }

        $data = $response->json()[0] ?? null;

        return $data ? [
            'latitude'     => (float) $data['lat'],
            'longitude'    => (float) $data['lon'],
            'display_name' => $data['display_name'],
        ] : null;
    }

    public static function getAddressFromCoordinates(float $lat, float $lng): ?string
    {
        $response = Http::withHeaders([
            'User-Agent' => env('HTTP_CLIENT_USER_AGENT'),
        ])->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat'    => $lat,
                    'lon'    => $lng,
                    'format' => 'json',
                ]);

        if ($response->successful()) {
            return $response->json()['display_name'] ?? null;
        }

        return null;
    }
}
