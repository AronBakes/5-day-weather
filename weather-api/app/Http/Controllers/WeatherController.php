<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    /**
     * Fetch the 5-day forecast for a given city.
     */
    public function getForecast(string $city): JsonResponse
    {
        $apiKey = env('WEATHERBIT_API_KEY');

        if (!$apiKey) {
            return response()->json(['error' => 'Weather API key is not configured.'], 500);
        }

        $response = Http::get('https://api.weatherbit.io/v2.0/forecast/daily', [
            'city' => $city,
            'key' => $apiKey,
            'days' => 5,
        ]);

        if ($response->failed() || $response->json() === null) {
            return response()->json(['error' => 'Could not retrieve forecast for the specified city.'], 404);
        }
        
        $weatherData = $response->json();
        
        $processedData = [
            'city_name' => $weatherData['city_name'],
            'country_code' => $weatherData['country_code'],
            'forecasts' => [],
        ];

        foreach ($weatherData['data'] as $day) {
            $processedData['forecasts'][] = [
                'date' => $day['valid_date'],
                'avg_temp' => $day['temp'],
                'max_temp' => $day['max_temp'],
                'min_temp' => $day['min_temp'],
            ];
        }

        return response()->json($processedData);
    }
}