# 5-Day Weather Forecast Application

## Project Overview

This project is a solution for a coding test by R6 Digital that required an application capable of reporting 5-day weather forecasts.

## Features

* **Web UI:** A reactive web interface built using React.
    * [cite_start]Features a dropdown menu with the following pre-defined cities: Brisbane, Gold Coast, and Sunshine Coast. 
    * [cite_start]On selecting a city, the new forecast data is loaded reactively without requiring a page refresh or form submission. 
* **Console Command:** A Laravel Artisan command for retrieving forecasts from the terminal.
    * [cite_start]Can be executed with the format `php artisan forecast {cities?*}`. 
    * [cite_start]Displays a 5-day forecast in a clean, tabulated format. 
    * [cite_start]If no cities are provided as arguments, the command interactively prompts the user for input. 
* [cite_start]**Graceful Error Handling:** Both the web application and the console command are designed to handle invalid inputs and API errors gracefully without crashing. 

## Technologies Used

* **Backend:** Laravel
* **Frontend:** React
* **Weather Data API:** Weatherbit
* **PHP Dependencies:** Composer
* **JavaScript Dependencies:** Node Package Manager (npm)

## Installation Instructions

Follow these instructions to set up the project locally.

1.  **Clone the Repository**
    ```bash
    git clone [https://github.com/AronBakes/5-day-weather.git](https://github.com/AronBakes/5-day-weather.git)
    cd 5-day-weather
    ```

2.  **Backend Setup (Laravel API)**
    ```bash
    # Navigate to the API directory
    cd weather-api

    # Install PHP dependencies
    composer install

    # Create the environment file from the example
    cp .env.example .env

    # Generate the unique application key
    php artisan key:generate

    # IMPORTANT: Open the newly created .env file and add your
    # Weatherbit API key. The key name should be WEATHERBIT_API_KEY.
    WEATHERBIT_API_KEY=your_key_here
    ```

3.  **Frontend Setup (React UI)**
    ```bash
    # Navigate to the UI directory from the root project folder
    cd ../weather-ui

    # Install JavaScript dependencies
    npm install
    ```

## Execution Instructions

To run the full web application, you need to run both the backend and frontend servers simultaneously in two separate terminals.

1.  **Run the Backend Server**
    * In your first terminal, navigate to the backend directory and run the `serve` command:
        ```bash
        # Make sure you are in the weather-api directory
        php artisan serve
        ```
    * The API will now be running at `http://127.0.0.1:8000`. Keep this terminal open.

2.  **Run the Frontend Server**
    * In a **new, separate terminal**, navigate to the frontend directory and run the `start` command:
        ```bash
        # Make sure you are in the weather-ui directory
        npm start
        ```
    * A browser window should automatically open to the web UI at `http://localhost:3000`.

3.  **Run the Console Command**
    * The console command can be run from any terminal. Navigate to the `weather-api` directory.
    * **Run with arguments (uses API Key from `.env` file):**
        ```bash
        php artisan forecast Brisbane "Gold Coast"
        ```
    * **Run in interactive mode (uses API Key from `.env` file):**
        ```bash
        php artisan forecast
        ```
    * **Run with a specific API Key (overrides `.env` file):**
        ```bash
        php artisan forecast Brisbane --key=YOUR_API_KEY_HERE
        ```

## Assumptions and Design Decisions

* **Decoupled Architecture:** I chose to create two separate project folders (weather-api for Laravel and weather-ui for React). This approach makes each part of the application easier to develop, test, and maintain independently.
* **Flexible API Key Handling:** The console command was enhanced to provide flexible API key management. It checks for the key in the following order of priority: 1) A key provided via the `--key=` option. 2) The `WEATHERBIT_API_KEY` value in the `.env` file. 3) As a final fallback, it interactively prompts the user to enter a key if neither of the first two is found.
* **Console Command Flexibility:** The requirements for the Web UI explicitly limit the cities to a pre-defined list. However, the requirements for the console command do not mention such a restriction. Therefore, I made the design decision that the console command should be a more flexible tool capable of fetching a forecast for any city supported by the Weatherbit API.
* [cite_start]**Error Handling Strategy:** To meet the requirement for graceful error handling, `try...catch` blocks were implemented in the backend controller and the console command. In the React frontend, the promise chain from the `fetch` call is used to catch non-200 responses. In all cases, the application displays a user-friendly message rather than crashing.
* **External API Behavior:** The application relies on the Weatherbit API for all forecast data. This includes its behavior for handling ambiguous city names (e.g., inputting "hefe" may return a result for "hefe'li"), which was deemed acceptable for the scope of the console command's more flexible functionality.
