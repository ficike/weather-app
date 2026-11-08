import { WeatherSearch } from "../components/weather-search/WeatherSearch.tsx";
import { useWeatherOptions } from "../hooks/useWeatherOptions.ts";

export function RegionWeather() {
  const options = useWeatherOptions("regions");

  return (
    <WeatherSearch
      entity="region"
      entityId="region"
      entityLabel="Régió"
      options={options.items}
      optionsLoading={options.loading}
      optionsError={options.error}
    />
  );
}
