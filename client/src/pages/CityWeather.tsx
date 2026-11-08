import { WeatherSearch } from "../components/weather-search/WeatherSearch.tsx";
import { useWeatherOptions } from "../hooks/useWeatherOptions.ts";

export function CityWeather() {
  const options = useWeatherOptions("cities");

  return (
    <WeatherSearch
      entity="city"
      entityId="cityId"
      entityLabel="Város"
      options={options.items}
      optionsLoading={options.loading}
      optionsError={options.error}
    />
  );
}
