<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ForecastCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forecast {cities?*} {--key= : The Weatherbit API key to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the 5-day weather forecast for one or more cities';

    /**
     * Execute the console command.
     */
   public function handle()
    {
        $cities = $this->argument('cities');

        // If no cities are provided, interactively prompt the user.
        if (empty($cities)) {
            $cityInput = $this->ask('Please enter the cities you would like a forecast for (comma-separated)');
            $cities = array_map('trim', explode(',', $cityInput));
        }

        // --- API Key Logic ---
        // 1. Try to get the key from the command-line option first.
        $apiKey = $this->option('key');

        // 2. If the --key option was not provided, fall back to the .env file.
        if (!$apiKey) {
            $apiKey = env('WEATHERBIT_API_KEY');
        }

        // 3. If there's still no key, ask the user for it as a last resort.
        if (!$apiKey) {
            $apiKey = trim($this->ask('API Key not found. Please enter your Weatherbit API key'));
        }

        // 4. If, after all that, we still have no key, then we exit.
        if (!$apiKey) {
            $this->error('No API key provided. Aborting.');
            return Command::FAILURE;
        }

        // --- Forecast Fetching Logic ---
        $this->info('Fetching weather forecasts...');

        $forecasts = [];
        foreach ($cities as $city) {
            if (empty($city)) continue;
            
            try {
                $response = Http::get('https://api.weatherbit.io/v2.0/forecast/daily', [
                    'city' => $city,
                    'key' => $apiKey,
                    'days' => 5,
                ]);

                if ($response->failed()) {
                    throw new \Exception("Could not retrieve forecast for {$city}.");
                }

                $weatherData = $response->json();
                $dailyForecasts = [];
                foreach ($weatherData['data'] as $day) {
                    $dailyForecasts[] = "Avg: {$day['temp']}, Max: {$day['max_temp']}, Low: {$day['min_temp']}";
                }
                $forecasts[] = array_merge([$weatherData['city_name']], $dailyForecasts);

            } catch (\Exception $e) {
                // Both the web application and the console application need to deal with invalid inputs and errors gracefully.
                $this->warn("Warning: Could not fetch forecast for '{$city}'. Please check the city name and try again.");
            }
        }

        if (empty($forecasts)) {
            $this->info('No forecast data to display.');
            return Command::SUCCESS;
        }

        // Shows tabulated data of 5 day forecast.
        $headers = ['City', 'Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'];
        $this->table($headers, $forecasts);

        return Command::SUCCESS;
    }
}