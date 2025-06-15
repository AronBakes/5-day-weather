// In weather-ui/src/App.js

import React, { useState, useEffect } from 'react';
import './App.css';

function App() {
  // --- STATE VARIABLES ---
  // We use state to store data that changes over time.
  const [selectedCity, setSelectedCity] = useState(''); // Stores the currently selected city
  const [forecast, setForecast] = useState(null);       // Stores the forecast data from the API
  const [loading, setLoading] = useState(false);         // Tracks when we are fetching data
  const [error, setError] = useState(null);              // Stores any error messages

  // --- DATA FETCHING ---
  // This `useEffect` hook runs whenever the `selectedCity` state changes.
  useEffect(() => {
    // If no city is selected, do nothing.
    if (!selectedCity) {
      setForecast(null); // Clear any old forecast data
      return;
    }

    const fetchForecast = async () => {
      setLoading(true);
      setError(null);
      setForecast(null);

      try {
        // This URL must match the one your Laravel server is running on.
        // It calls the API route you created earlier.
        const response = await fetch(`http://127.0.0.1:8000/api/forecast/${selectedCity}`);

        if (!response.ok) {
          throw new Error('Data could not be fetched for that city.');
        }

        const data = await response.json();
        setForecast(data); // Save the received data into our state

      } catch (err) {
        setError(err.message); // Save the error message to display it
      } finally {
        setLoading(false); // Stop loading, whether it succeeded or failed
      }
    };

    fetchForecast();
  }, [selectedCity]); // The dependency array: this tells React to re-run the effect ONLY when selectedCity changes.

  // --- RENDERED UI ---
  // This is the JSX that gets rendered to the screen.
  return (
    <div className="App">
      <header className="App-header">
        <h1>5-Day Weather Forecast</h1>
        
        {/* The dropdown menu with the required cities  */}
        <select value={selectedCity} onChange={e => setSelectedCity(e.target.value)}>
          <option value="">Select a City</option>
          <option value="Brisbane">Brisbane</option>
          <option value="Gold Coast">Gold Coast</option>
          <option value="Sunshine Coast">Sunshine Coast</option>
        </select>

        {/* Conditional Rendering: Show messages based on the current state to handle errors gracefully  */}
        {loading && <p>Loading...</p>}
        {error && <p>Error: {error}</p>}
        
        {/* If we have forecast data, display it */}
        {/* The forecast will include the average, maximum and minimum temperatures  */}
        {forecast && (
          <div className="forecast-container">
            <h2>Forecast for {forecast.city_name}, {forecast.country_code}</h2>
            <table>
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Avg Temp</th>
                  <th>Max Temp</th>
                  <th>Min Temp</th>
                </tr>
              </thead>
              <tbody>
                {forecast.forecasts.map(day => (
                  <tr key={day.date}>
                    <td>{day.date}</td>
                    <td>{day.avg_temp}°C</td>
                    <td>{day.max_temp}°C</td>
                    <td>{day.min_temp}°C</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </header>
    </div>
  );
}

export default App;